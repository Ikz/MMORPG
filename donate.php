<?php

// -- Functions -- //

require_once('gateway/donation_functions.php');

// -- Functions -- //

if(isset($_POST['checkout']) || isset($_GET['payment']) || isset($_GET['username']) || isset($_GET['subscribe'])) {
	session_start();
	if(!isset($_SESSION['userid'])) {
		header("Location: login.php");
		exit;	
	}
	include "config.php";
	global $_CONFIG;
	define("MONO_ON", 1);
	require "class/class_db_{$_CONFIG['driver']}.php";
	$db=new database;
	$db->configure($_CONFIG['hostname'],
	 $_CONFIG['username'],
	 $_CONFIG['password'],
	 $_CONFIG['database'],
	 $_CONFIG['persistent']);
	$db->connect();
	$c=$db->connection_id;
	
	$set=array();
	$settq=$db->query("SELECT * FROM settings");
	while($r=$db->fetch_row($settq)) {
		$set[$r['conf_name']]=$r['conf_value'];
	}
	
	if(isset($_GET['username'])) {
		$user_select = $db->query("SELECT username FROM users WHERE userid=".abs((int) $_GET['username']));	
		if($db->num_rows($user_select)) {
			$user_data = $db->fetch_row($user_select);
			echo $user_data['username'];	
		} else {
			echo 'null';	
		}
		exit;
	}
	
	$user_select = $db->query("SELECT username FROM users WHERE userid=".$_SESSION['userid']);
	if($db->num_rows($user_select)) {
		$ir = $db->fetch_row($user_select);	
	} else {
		unset($_SESSION['userid']);
		header("Location: login.php");
		exit;	
	}
	
	if(isset($_GET['subscribe']) && in_array('paypal', list_gateways())) {
		
		$select = $db->query("SELECT pack_price, pack_sub FROM donate WHERE pack_id = ".$db->escape($_GET['subscribe']));
		if($db->num_rows($select)) {
			$pack_data = $db->fetch_row($select);
			
			$price = $pack_data['pack_price'];
			$days = $pack_data['pack_sub'];
			
			require_once('gateway/paypal/subscribe.php');
			exit;
			
		} else {
			echo 'An error has occured.';	
		}
			
	}
	
	if(isset($_GET['payment']) && isset($_GET['gateway'])) {
		
		if(in_array($_GET['gateway'], list_gateways())) {
			$data = load_gateway($_GET['gateway']);
			if($data['log_data']) {
				require_once('gateway/'.$_GET['gateway'].'/'.$data['log_data']);	
			} else {
				header("Location: donate.php?error=".urlencode('We are currently having issues with this checkout gateway. Please try another'));
				exit;
			}
			exit;	
		} else {
			header("Location: donate.php?error=".urlencode('We are currently having issues with this checkout gateway. Please try another'));
			exit;
		}
		
	}
	
	?>
    <script type="text/javascript">
	$(document).ready(function() {
		$('#userTo').click(function() {
			if($(this).children('[name=userTo]').length == 0) {
				$(this).html('<input type="text" name="userTo" size="3" value="<?php echo (abs((int) $_COOKIE['user_for']) ? abs((int) $_COOKIE['user_for']) : $ir['userid']); ?>" style="font-size: 12px;font-family: Tahoma;padding:0;margin:0;text-align: center;" />');	
				$('[name=userTo]').focus();
			}
		});
		$('#userTo').keypress(function(e){
			if(e.which == 13){
				var username = $('[name=userTo]').val();
				$.ajax({
				   type: "GET",
				   url: "donate.php",
				   cache: false,
				   data: "username=" + username,
				   success: function(data){
					 if(data == 'null') {
						alert('That user dosen\'t exist.'); 
						 $('[name=userTo]').focus();
					 } else {
						 $.cookie("user_for", username);
						 $('#userTo').html(data);
					 }
				  }
				 });
			}
		});
	});
	</script>
    <?php
	
	$dset = getSettings();
	
	echo '<h3>Checkout</h3>
	<div style="float: left;width: 70%;">
		Please confirm these are the packages you wanted to purchase.
	</div>
	<div id="donate_for" style="float: right;width: 25%;text-align: right;margin-right: 2px;">
		Donating for: [<span id="userTo">';
		
		if(isset($_COOKIE['user_for'])) {
			$user_select = $db->query("SELECT username FROM users WHERE userid=".abs((int) $_COOKIE['user_for']));	
			if($db->num_rows($user_select)) {
				$user_data = $db->fetch_row($user_select);
				echo $user_data['username'];	
			} else {
				unset($_COOKIE['user_for']);
			}
		} else {
			echo $ir['username'];	
		}
		
		echo '</span>]
	</div>
	<br /><br /><br>';
	
	$packs = explode('|',$_COOKIE['cartData']);
	if(count($packs) >= 1) {
		echo '<table width="100%" cellspacing="1" cellpadding="2" border="0" class="table">
			<tr>
				<th>Pack Name</th>
				<th style="text-align: center;" border="1">Qty</th>
				<th style="text-align: center;" border="1">Price each</th>
				<th style="text-align: center;" border="1">Total Price</th>
			</tr>';	
			$total_price = 0;
			$cart_data = ''; //Use this so all the data gets verified if it gets tampared.
	}
	foreach($packs as $pack) {
		list($pack_id, $pack_qty) = explode(',',$pack);
		if($pack_qty > 0) {
			$select = $db->query('SELECT pack_name, pack_price FROM donate WHERE pack_id = '.abs((int) $pack_id));
			$pack_data = $db->fetch_row($select);
			echo '<tr>
				<td>'.$pack_data['pack_name'].'</td>
				<td style="text-align: center;">'.$pack_qty.'</td>
				<td style="text-align: center;">'.$dset['currency_symbol'].''.centsToDollar($pack_data['pack_price']).'</td>
				<td style="text-align: center;">'.$dset['currency_symbol'].''.centsToDollar($pack_qty*$pack_data['pack_price']).'</td>
			</tr>';
			$total_price += $pack_qty*$pack_data['pack_price'];
			$cart_data .= $pack_id.'.'.$pack_qty.'|';
		}
	}
	if(count($packs) >= 1) {
		echo '<tr>
			<th colspan="3" style="text-align: right;"> Total Price:&nbsp;&nbsp;&nbsp;</th>
			<td style="text-align: center;">'.$dset['currency_symbol'].''.centsToDollar($total_price).'</td>
		</tr>';
		
		if(isset($dset['discount']) && $dset['discount'] != 0) {
			echo '<th colspan="3" style="text-align: right;"> Discount:&nbsp;&nbsp;&nbsp;</th>
			<td style="text-align: center;">-'.$dset['currency_symbol'].''.centsToDollar($total_price/100*$dset['discount']).'</td>
		</tr>
		<tr>';	
			$total_price = $total_price - ($total_price/100*$dset['discount']);
			echo '<th colspan="3" style="text-align: right;"> New Total Price:&nbsp;&nbsp;&nbsp;</th>
			<td style="text-align: center;">'.$dset['currency_symbol'].''.centsToDollar($total_price).'</td>
		</tr>';	
		}
		
		echo '</table>';
		
		$_SESSION['cart_data'] = rtrim($cart_data,'|');
		$_SESSION['total_price'] = $total_price;
	}
	
	if($dset['min_purchase'] != '0.00' && dollarToCents($dset['min_purchase']) > $total_price) {
		echo '<br /><br /><div style="text-align: center;">There is a minimum spend of '.$dset['currency_symbol'].''.$dset['min_purchase'].' per transaction.</div><br />';	
	} else if($dset['max_purchase'] != '0.00' && dollarToCents($dset['max_purchase']) < $total_price) {
		echo '<br /><br /><div style="text-align: center;">There is a maximum spend of '.$dset['currency_symbol'].''.$dset['max_purchase'].' per transaction.</div><br />';
	} else {
	
		// -- Payment procedure -- //
		/* This is to help when we release new payment addons */
		
		$gateways = list_gateways();
		
		echo '<br />';
		
		foreach($gateways as $gate) {
			$data = load_gateway($gate);
			if($data['button_file'] && $dset[$gate.'_active'] == 1) {
				require_once('gateway/'.$gate.'/'.$data['button_file']);
				$gate = 1;	
			}
		}	
		if($gate <= 0) {
			echo 'There are currently no payment systems available. Please inform an administrator.';	
		}
	
	}
	
	// -- Payment procedure -- //
	exit;
} else {
	require_once('globals.php');
}
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script type="text/javascript">
jQuery.cookie = function (key, value, options) {
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);
        if (value === null || value === undefined) {
            options.expires = -1;
        }
        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
        value = String(value);
        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '',
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.height() - 150 ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}
function addCart(packageId) {
	if($('#' + packageId + 'qty').length == 1) {
		var ele = $('#' + packageId + 'qty');
		var cents = $('#' + packageId + 'cents');
		var totalqty = $('#totalqty');
		var totalcents = $('#totalcents');
		totalcents.html(parseInt(totalcents.html())+parseInt(cents.html()));
		ele.html(parseInt(ele.html())+1);
		totalqty.html(checkTotalQty());
		updateTotalPrice();
		if(ele.html() > 0) {
			var minus = $('#' + packageId + 'minus');
			minus.html('<br /><a href="javascript:void(0);" onclick="removeCart('+ packageId +');" class="minus">[ - ]</a>');
		}
	}
}
function removeCart(packageId) {
	if($('#' + packageId + 'qty').length == 1) {
		var ele = $('#' + packageId + 'qty');
		var cents = $('#' + packageId + 'cents');
		var totalqty = $('#totalqty');
		var totalcents = $('#totalcents');
		totalcents.html(parseInt(totalcents.html())-parseInt(cents.html()));
		ele.html(parseInt(ele.html())-1);
		totalqty.html(checkTotalQty());
		updateTotalPrice();
		if(ele.html() <= 0) {
			ele.html(0);
			totalqty.html(0);
			var minus = $('#' + packageId + 'minus');
			minus.html('');
		}
	}	
}
function resetCart() {
	var totalcents = $('#totalcents');
	var totalqty = $('#totalqty');
	$('.qty').each(function() {
		$(this).html(0);
	});	
	$('.minus').each(function() {
		$(this).html('');
	});	
	totalcents.html(0);
	totalqty.html(0);
	updateTotalPrice();
}
function checkTotalQty() {
	var total_packs = 0;
	$('.qty').each(function() {
		total_packs = total_packs + parseInt($(this).html());
	});
	return total_packs;
}
function updateTotalPrice() {
	var totalcents = $('#totalcents');
	var totalcost = $('#totalcost');
	totalcost.html(centsToDollar(parseInt(totalcents.html())));
}
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
function recordTotals() {
	var finalData = '';
	$('.qty').each(function() {
		if($(this).html() > 0) {
			finalData+= $(this).attr('id').split('qty').join('');
			finalData+= ',';
			finalData+= $(this).html();
			finalData+= '|';
		}
	});	
	if(rtrim(finalData,'|')) {
		$.cookie("cartData", rtrim(finalData,'|'));
		showCheckout();
	} else {
		alert('You must have atleast one package in your cart to continue to the checkout.');	
	}
}
function showCheckout() {
	$.ajax({
	   type: "POST",
	   url: "donate.php",
	   cache: false,
	   data: "checkout=true",
	   success: function(data){
         $('#confirm_popup').html(data).fadeIn(500);
		 $('#confirm_popup_back').fadeIn(500);
      }
	 });	
}
function centsToDollar(cents) {
	return (cents/100).toFixed(2);
}
$(document).ready(function() {
	$('#confirm_popup').center();
	$('#confirm_popup_back,#popup_close').click(function() {
		$(this).fadeOut(500);
		$('#confirm_popup').fadeOut(500);	
	});
});
$(document).ready(function() {
		$('#userTo').click(function() {
			if($(this).children('[name=userTo]').length == 0) {
				$(this).html('<input type="text" name="userTo" size="3" value="<?php echo (abs((int) $_COOKIE['user_for']) ? abs((int) $_COOKIE['user_for']) : $ir['userid']); ?>" style="color: black;  background-color: white; font-size: 12px;font-family: Tahoma;padding:0;margin:0;text-align: center;" />');
				$('[name=userTo]').focus();
			}
		});
		$('#userTo').keypress(function(e){
			if(e.which == 13){
				var username = $('[name=userTo]').val();
				$.ajax({
				   type: "GET",
				   url: "donate.php",
				   cache: false,
				   data: "username=" + username,
				   success: function(data){
					 if(data == 'null') {
						alert('That user dosen\'t exist.'); 
						 $('[name=userTo]').focus();
					 } else {
						 $.cookie("user_for", username);
						 $('#userTo').html(data);
					 }
				  }
				 });
			}
		});
	});
</script>
<style type="text/css">
	#subscribe_button {
		display: block;
		text-align: center;
		font-size: 12px;
		margin: 10px;
		padding-top: 5px;
		height: 19px;
		background: url('subscribe_back.jpg') repeat-x;
		-webkit-border-radius: 15px;
		-moz-border-radius: 15px;
		border-radius: 15px;
		border: 1px solid #ff9933;
		color: #003366;
		cursor: pointer;
		opacity: 0.8;
		-webkit-transition-duration: 0.5s;
	}
	#subscribe_button:hover {
		opacity: 1;	
	}
	#confirm_popup {
		position: fixed;
		width: 570px;
		background: white;	
		z-index: 1001;
		-moz-box-shadow: 0px 0px 15px #000;
		-webkit-box-shadow: 0px 0px 15px #000;
		box-shadow: 0px 0px 15px #000;
		text-align: left;
		padding: 15px;
		display: none;
	}
	#confirm_popup_back {
		position: fixed;
		left: 0;
		right: 0;
		top: 0;
		bottom: 0;
		background: black;	
		z-index: 1000;
		opacity: 0.7;
		filter: alpha(opacity = 70);
		zoom: 1;
		display: none;
	}
	.error {
		color: #D8000C;
		background-color: #FFBABA;
		padding: 8px;
		margin: 8px;
		width: 92%;
		text-align: center;
		border: 1px solid;
	}
</style>
<?php

$dset = getSettings();

if(isset($_GET['use_item'])) {
	
	$item_id = abs((int) $_GET['use_item']);
	
	$check = $db->query("SELECT iv.inv_userid, iv.inv_qty, iv.inv_itemid,i.itmtype,i.itmid FROM inventory iv LEFT JOIN items i ON iv.inv_itemid=i.itmid WHERE iv.inv_id=".$item_id." AND iv.inv_userid=".$userid);
	if($db->num_rows($check)) {
		
		$item_data = $db->fetch_row($check);
		if($item_data['itmtype'] == $dset['item_type']) {
			
			$get_pack = $db->query("SELECT pack_id,pack_benefits,pack_items FROM donate WHERE pack_item_id = ".$item_data['itmid']);
			if($db->num_rows($get_pack)) {
				
				$pack_data = $db->fetch_row($get_pack);
				$getBenefits = @unserialize($pack_data['pack_benefits']);
				$getItems = @unserialize($pack_data['pack_items']);
				
				if($getBenefits != false) {
					$user_stats = array('strength','agility','guard','labour','IQ');
					foreach($getBenefits as $ben) {
						$db->query("UPDATE ".(in_array($ben[0],$user_stats) ? 'userstats' : 'users')." SET ".$ben[0]." = ".(isset($ben[1]) && ($ben[1] == '+' || $ben[1] == '-') ? $ben[0].' '.$ben[1].' '.$ben[2] : $ben[2])." WHERE userid = ".$userid);	
					}
				}
				if($getItems != false) {
					foreach($getItems as $item) {				
						item_add($userid, $item[0], $item[1]);
					}
				}
				
				item_remove($userid, $item_data['itmid'], 1);	
				
				echo '<h3>Donator Pack Used!</h3>
				You have succesfully used the donator pack! All the benefits it gives you have been placed upon your account.';	
					
			} else {
				echo '<h3>Error</h3>
				The donation pack no longer exists.';	
			}
				
		} else {
			echo '<h3>Error</h3>
			This item is not a donator pack and cannot be used within this system.';		
		}
		
	} else {
		echo '<h3>Error</h3>
		The item you are trying to use dosen\'t exist or it is not yours.';	
	}
	
	echo '<br /><br />
	&gt; <a href="inventory.php">Back to Inventory</a>';
	
	$h->endpage();
	exit;
		
}

if($dset['donations_open'] != 1) {
	echo '<h3>Donations are currently offline</h3>
	<p>Donations are currently offline. Please check back later to see if the system has been put back online.</p>';
	$h->endpage();
	exit;	
}

if(isset($_GET['paid'])) {
	echo '<h3>Thank you!</h3>
	<p>Your purchase has been completed and is currently prcoessing. You should be credited within 5 minutes and if you are not please contact an adminstrator.</p>
	<p>Thanks for your custom.</p>';
	$h->endpage();
	exit;
}

// -- Confirmation Popup -- //

echo '<div id="confirm_popup">
	Loading information...	
</div>
<div id="confirm_popup_back"></div>';

// -- Confirmation Popup -- //
$sub_heads = $db->query('SELECT pack_name FROM donate WHERE pack_sub > 0 AND pack_active = 1 ORDER BY pack_price');
if($db->num_rows($sub_heads) && in_array('paypal',list_gateways())) {
echo '<h3>Subscriptions</h3>
<p>You can subscribe to our donation system to receive donator days and other benefits every month!</p>

<table width="95%" cellspacing="1" cellpadding="2" border="0" class="table">
	<tr>';
		while($sub = $db->fetch_row($sub_heads)) {
			echo '<th width="'.(100/$db->num_rows($sub_heads)).'" style="text-align: left;">
				'.$sub['pack_name'].'
			</th>';
		}
	echo '</tr>
	<tr valign="top">';
		$sub_content = $db->query('SELECT pack_id, pack_benefits, pack_items, pack_price, pack_sub FROM donate WHERE pack_sub > 0 AND pack_active = 1  ORDER BY pack_price');
		while($sub = $db->fetch_row($sub_content)) {
			echo '<td style="position: relative;text-align: left;">
				'.unserializeBenefits($sub['pack_benefits']).'
				'.unserializeItems($sub['pack_items']).'
				<a href="donate.php?subscribe='.$sub['pack_id'].'" alt="Purchase Now!">
					<div id="subscribe_button">
						'.$dset['currency_symbol'].''.centsToDollar($sub['pack_price']).' every '.$sub['pack_sub'].' day'.($sub['pack_sub'] > 1 ? 's' : '').'
					</div>
				</a>
			</td>';
		}
	echo '</tr>
</table>';
}

echo '<h3>Donations</h3>
<p>Here you are able to make donations towards GTA Mobster. Please make sure you read the terms before purchasing a pack.</p>
<p>You are currently donating for <u><span id="userTo">';
		
		if(isset($_COOKIE['user_for'])) {
			$user_select = $db->query("SELECT username FROM users WHERE userid=".abs((int) $_COOKIE['user_for']));	
			if($db->num_rows($user_select)) {
				$user_data = $db->fetch_row($user_select);
				echo $user_data['username'];	
			} else {
				unset($_COOKIE['user_for']);
			}
		} else {
			echo $ir['username'];	
		}
		
		echo '</span></u> click on the name to change it, and then hit enter to confirm it.</p><br>';
if(isset($_GET['error'])) {
	echo '<div class="error">
		'.$_GET['error'].'
	</div>';	
}
if(isset($dset['discount']) && $dset['discount'] != 0) {
	echo '<b>We\'re currently having a special offer on all packages! You can enjoy any of our packages at '.$dset['discount'].'% off!</b><br /><br />';	
}
echo '<table width="95%" cellspacing="1" cellpadding="1" border="0" class="table">
	<tr>
		<th width="20%">Name</th>
		<th width="27.5%">Benefits</th>
		<th width="27.5%">Items</th>
		<th width="15%">Price</th>
		<th width="6%%">In Cart</th>
		<th width="4%"></th>
	</tr>';
	$select = $db->query('SELECT pack_id, pack_name, pack_benefits, pack_items, pack_price FROM donate WHERE pack_sub = 0 AND pack_active = 1 ORDER BY pack_price');
	if(!$db->num_rows($select)) {
		echo '<tr>
			<td colspan="6" style="text-align: center;">Sorry, there are currently no packs available for purchase.</td>
		</tr>';		
	} else {
		while($pack = $db->fetch_row($select)) {
			echo '<tr>
				<td>'.$pack['pack_name'].'</td>
				<td style="text-align: left;">'.unserializeBenefits($pack['pack_benefits']).'</td>
				<td style="text-align: left;">'.unserializeItems($pack['pack_items']).'</td>
				<td style="text-align: center;">';
				
				if(isset($dset['discount']) && $dset['discount'] != 0) {
					
					$new_price = $pack['pack_price'] - ($pack['pack_price']/100*$dset['discount']);					
					echo '<s>'.$dset['currency_symbol'].''.centsToDollar($pack['pack_price']).'</s> <span id="'.$pack['pack_id'].'cents" style="display: none;">'.$new_price.'</span><br />
					NOW! '.$dset['currency_symbol'].''.centsToDollar($new_price);
					
				} else {
				
					echo $dset['currency_symbol'].centsToDollar($pack['pack_price']).' <span id="'.$pack['pack_id'].'cents" style="display: none;">'.$pack['pack_price'].'</span>';
				
				}
				echo '</td>
				<td style="text-align: center;" id="'.$pack['pack_id'].'qty" class="qty">0</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="addCart('.$pack['pack_id'].');" title="Add to Cart">[+]</a><span id="'.$pack['pack_id'].'minus"></span></td>
			</tr>';						
		}
	}
	echo '
	<tr>
		<th colspan="3" style="text-align: right;">Total Price:&nbsp;&nbsp;&nbsp;</td>
		<td style="text-align: center;">'.$dset['currency_symbol'].'<span id="totalcost">0.00</span> <span id="totalcents" style="display: none;">0</span></td>
		<td style="text-align: center;" id="totalqty">0</td>
		<td style="text-align: center;"><a href="javascript:void(0);" onclick="resetCart();" title="Reset">[R]</a></td>
	</tr>
	<tr>
		<td colspan="3"></td>
		<td colspan="3" style="text-align: center;">
			<input type="submit" STYLE="width: 97%; color: black; background-color: white;" value="Continue to Checkout" onclick="recordTotals();">
		</td>
	</tr>
</table>';

$h->endpage();
?>