<?php
@ob_start("ob_gzhandler");
require_once('gateway/donation_functions.php');

if(isset($_POST['action'])) {
	
	session_start();
	if(!isset($_SESSION['userid'])) {
		header("Location: login.php");
		exit;	
	}
	include "config.php";
	global $_CONFIG;
	define("MONO_ON", 1);
	require "class/class_db_{$_CONFIG['driver']}.php";
	require_once('global_func.php');
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
	
	function benefit_dropdown() {
		global $db;	
		echo '<div id="benefit" class="benefit_box">
			'.benefitList().' 
			<select name="action">
				<option value="+">+</option>
				<option value="-">-</option>
				<option value="=">=</option>
			</select>
			<input type="text" name="qty" STYLE="color: black;  background-color: white;" size="3" value="0" />
		</div>';
		exit;
	}
	function item_staff_dropdown() {
		global $db, $c;	
		echo '<div id="item" class="item_box">
			'.item_dropdown($c, 'item_id').' x<input type="text" STYLE="color: black;  background-color: white;" name="qty" size="3" value="0" />
		</div>';
		exit;
	}
	
	function setActive() {
		global $db, $c;	
		if(isset($_POST['gateway'])) {
			$set_to = abs((int) $_POST['set_to']);
			$query = $db->query("UPDATE donate_settings SET donate_value = ".$set_to." WHERE donate_name = '".$_POST['gateway']."_active'");
			if($query) {
				echo 1;
			} else {
				echo 2;	
			}
			exit;
		} 
		else if(isset($_POST['pack_id'])) {
			$set_to = abs((int) $_POST['set_to']);
			$query = $db->query("UPDATE donate SET pack_active = ".($set_to == 1 ? 1 : 0)." WHERE pack_id = ".abs((int) $_POST['pack_id']));
			if($query) {
				echo 1;
			} else {
				echo 2;	
			}
			exit;
		} else {
			echo 2;	
			exit;
		}
	}
	
	function view() {
		global $db,$set;
		$select_query = $db->query("SELECT d.pack_id,d.pack_name, d.pack_benefits, d.pack_items, d.pack_price, d.pack_sub, d.pack_active FROM donate d ORDER BY pack_price");
		?>
        <script type="text/javascript">
		function setActive(pack_id,active,ele) {
			$.ajax({
				type: "POST",
				url: 'staff_donation.php',
				data: {'action': 'set_active','pack_id': pack_id,'set_to': active},
				success: function(data) {
					if(data == '1') {
						$('#pack_' + pack_id).toggleClass("unactive",1000);
					}
				}
			});	
		}
		$('[name=active]').click(function() {
		  if($(this).attr('checked') == true) {
			  setActive($(this).val(),1,this);	
		  } else {
			  setActive($(this).val(),0,this);		
		  }
		});
		</script>
        <?php
		echo '<font color="black"><h3>View Packages</h3>
        <p>Below you can view all the packages which you currently have within your system. Simply click on a package to edit it.</font></p>
        <table width="100%" cellspacing="1" cellpadding="3" border="0" class="table tr_select">
        	<tr>
				<th>Active</th>
            	<th width="20%">Name</th>
                <th width="35%">Benefits</th>
                <th width="35%">Items</th>
                <th width="10%">Cost</th>
            </tr>';
			if(!$db->num_rows($select_query)) {
				echo '<tr>
					<td colspan="5" style="text-align: center;">You currently have no packages, Please add some.</td>
				</tr>';	
			} else {
				while($pack = $db->fetch_row($select_query)) {
					echo '<tr '.($pack['pack_active'] == 0 ? 'class="unactive"' : '').' id="pack_'.$pack['pack_id'].'">
						<td style="text-align: center;"><input type="checkbox" id="check" name="active" value="'.$pack['pack_id'].'"'.($pack['pack_active'] == 1 ? 'checked="checked"' : '').' ></td>
						<td onclick="editPack(\''.$pack['pack_id'].'\');">'.$pack['pack_name'].'</td>
						<td onclick="editPack(\''.$pack['pack_id'].'\');">'.unserializeBenefits($pack['pack_benefits'],1).'</td>
						<td onclick="editPack(\''.$pack['pack_id'].'\');">'.(isset($pack['pack_items']) ? unserializeItems($pack['pack_items']) : '').'</td>
						<td onclick="editPack(\''.$pack['pack_id'].'\');" style="text-align: center;">$'.centsToDollar($pack['pack_price']).'</td>
					</tr>';	
				}
			}
        echo '</table>';
	}
	function create() {
		global $db, $set, $c;
		
		if(isset($_POST['edit'])) {
			$editId = abs((int) $_POST['edit']);
			$check = $db->query("SELECT * FROM `donate` WHERE pack_id = ".$_POST['edit']);
			if(!$db->num_rows($check)) {
				echo '<span style="color: darkred;">The pack you\'re attempting to edit doesn\'t exist.</span>';
			} else {
				$data = $db->fetch_row($check);	
				$edit_page = true;
			}
		}
		
		echo '<h3>'.(isset($edit_page) ? 'Editing '.$data['pack_name'] : 'Create new Package').'</h3>
		<p><font color="black">Please fill in <b>all</b> the fields listed below and follow any instruction given on that particular field.</p>
		<span id="create_return"></span>
		<form action="javascript:void(0);" method="post" id="create" onsubmit="createPack('.(isset($edit_page) ? $data['pack_id'] : '').');">
		<table width="100%" cellpadding="3">
			<tr valign="top">
				<td width="50%">
					Pack Name:<br />
					<small>Make it sound exciting, and explanatory to what it is.</small><br />
					<input type="text" name="pack_name" STYLE="color: black;  background-color: white; width: 95%;" '.(isset($edit_page) ? 'value="'.$data['pack_name'].'"' : '').' />
					<br /><br />
					Pack Benefits:<br />
					<small>The first column is the field of the user you wish to change. The second is the operator you wish to use to change it. The third is the value you want to modify it by or to. Leaving the third value as 0 will mean it won\'t be added as a benefit.</small><br />';
					if(isset($edit_page)  && $data['pack_benefits']) {
						$benefit_data = unserialize($data['pack_benefits']);
						foreach($benefit_data as $bd) {
							echo '
							<div id="benefit" class="benefit_box">
								'.benefitList($bd[0]).' 
								<select name="action">>
									<option value="+" '.($bd[1] == '+' ? 'selected="selected"' : '').'>+</option>
									<option value="-" '.($bd[1] == '-' ? 'selected="selected"' : '').'>-</option>
									<option value="=" '.($bd[1] == '=' ? 'selected="selected"' : '').'>=</option>
								</select>
								<input type="text" name="qty" size="3" STYLE="color: black;  background-color: white;" value="'.$bd[2].'" />
							</div>';	
						}
					} else {
						echo '
						<div id="benefit" class="benefit_box">
							'.benefitList().' 
							<select name="action">
								<option value="+">+</option>
								<option value="-">-</option>
								<option value="=">=</option>
							</select>
							<input type="text" name="qty" size="3" STYLE="color: black;  background-color: white;" value="0" />
						</div>';
					}
					echo '<a href="javascript:void(0);" onclick="addBenefit();" class="add_benefit">+ add benefit</a>
					<br /><br />
					Subscription:<br />
					<small>Check the box to set this pack as a subscription</small><br />
					<input type="checkbox" name="sub" onclick="checkChecked(this);" '.(isset($edit_page) ? ($data['pack_sub'] > 0 ? 'checked="checked"' : '') : '').' />Package Subscription
					<div id="sub_days_box" '.(isset($edit_page) ? ($data['pack_sub'] > 0 ? '' : 'style="display: none;"') : 'style="display: none;"').'>
						<br />
						Subscription Days:<br />
						<small>The amount of days between the payment being taken.</small><br />
						<input type="text" name="sub_days" value="'.($data['pack_sub'] > 0 ? $data['pack_sub'] : 30).'" STYLE="color: black;  background-color: white;" size="5" />
					</div>
				</td>
				<td width="50%">
					Pack Price:<br />
					<small>Ex. <b>5.00</b> would be $5.00</small><br />
					<input type="text" name="pack_price" style="color: black;  background-color: white; width: 95%;" '.(isset($edit_page) ? 'value="'.centsToDollar($data['pack_price']).'"' : '').' /><br />
					<br />
					Pack Items:<br />
					<small>The first column is the item and the second is the quantify of the item. We reccomend that you don\'t add the phyiscal packs which are created when a pack is made in the system. The system will handle the crediting of the item if you have the system turned onto item crediting.</small>';
					if(isset($edit_page) && $data['pack_items']) {
						$item_data = unserialize($data['pack_items']);
						foreach($item_data as $id) {
							echo '<div id="item" class="item_box">
								'.item_dropdown($c,'item_id',$id[0]).' x<input type="text" STYLE="color: black;  background-color: white;" name="qty" size="3" value="'.$id[1].'" />
							</div>';	
						}
					} else {
						echo '<div id="item" class="item_box">
							'.item_dropdown($c,'item_id').' x<input type="text" STYLE="color: black;  background-color: white;" name="qty" size="3" value="0" />
						</div>';
					}
					echo '<a href="javascript:void(0);" onclick="addItem();" class="add_item">+ add item</font></a><br /><br />
					<input type="submit" STYLE="color: black;  background-color: white;" value="'.(isset($edit_page) ? 'Save Donator Pack!' : 'Create new Donator Pack!').'" />
				</td>
			</tr>
		</table>
		</form>
		</div>
		';	
	}
	function createPack() {
		global $db, $c;
		$_POST['edit'] = isset($_POST['edit']) && $_POST['edit'] != 'undefined' ? $_POST['edit'] : 0;
		$formData = (isset($_POST['form_data']) ? $_POST['form_data'] : '');
		$data = explode('&',$formData);
		foreach($data as $d) {
			$new_data = explode('=',$d);
			$donate_data[$new_data[0]] = mysql_real_escape_string($new_data[1]);	
		}
		if(isset($_POST['benefit_data']) && $_POST['benefit_data'] != '') {
			$benefitData = (isset($_POST['benefit_data']) ? $_POST['benefit_data'] : '');
			$bdata = explode('|',$benefitData);
			$benefit = '';
			foreach($bdata as $b) {
				$split_data = explode(',',$b);
				$benefit[] = $split_data;
			}
		}
		if(isset($_POST['item_data']) && $_POST['item_data'] != '') {
			$itemData = (isset($_POST['item_data']) ? $_POST['item_data'] : '');
			$idata = explode('|',$itemData);
			$item = '';
			foreach($idata as $i) {
				$split_data = explode(',',$i);
				$items[] = $split_data;
			}
		}
		$required_fields = array('pack_name','pack_price');
		$error = 0;
		foreach($required_fields as $r) {
			$error+= ($donate_data[$r] == '' || !isset($donate_data[$r]) ? 1 : 0);
		}
		if($error >= 1) {
			echo '<span style="color: darkred;">You are required to fill in all fields. Please go back and try again.</span><br /><br />';
			exit;	
		} 
		if(isset($donate_data['sub']) && $donate_data['sub_days'] < 1) {
			echo '<span style="color: darkred;">The subscription pack is required to have atleast one day.</span><br /><br />';
			exit;	
		}
		if(!preg_match('^\$?([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(.[0-9][0-9])$^',$donate_data['pack_price'])) {
			echo '<span style="color: darkred;">The packs price must be formatted correctly. <small>(Eg. 5.00)</small></span><br /><br />';
		}
		
		//Create package
		
		if(isset($_POST['edit']) && $_POST['edit'] != '' && $_POST['edit'] != 'undefined') {
			
			$db->query("UPDATE `donate` SET
						pack_name = '".urldecode($donate_data['pack_name'])."',
						pack_benefits = '".(isset($benefit) ? serialize($benefit) : '')."',
						pack_items = '".(isset($items) ? serialize($items) : '')."',
						pack_price = '".dollarToCents($donate_data['pack_price'])."',
						pack_sub = '".(isset($donate_data['sub']) ? abs((int) $donate_data['sub_days']) : 0)."'
						WHERE pack_id = ".abs((int) $_POST['edit']));
						
			$get_item_id = $db->query("SELECT pack_item_id FROM donate WHERE pack_id = ".abs((int) $_POST['edit']));
			if($db->num_rows($get_item_id)) {
				$pack_item_id = $db->fetch_row($get_item_id);
				$db->query("UPDATE items SET itmname = '".urldecode($donate_data['pack_name'])."' WHERE itmid = ".$pack_item_id['pack_item_id']);
			}
								
			echo '<span style="color: darkgreen;">The edits to your donation pack have been applied.</span><br /><br />';
			
		} else {
			
			$dset = getSettings();
			
			// Create the item for the pack.
			$item_insert = $db->query("INSERT INTO `items` (itmtype, itmname, itmdesc, itmbuyable) VALUES('".$dset['item_type']."', '".urldecode($donate_data['pack_name'])."', 'Donator Pack', 0)");
			$item_id = mysql_insert_id();
		
			$db->query("INSERT INTO `donate` (pack_name, pack_benefits, pack_items, pack_item_id, pack_price, pack_sub, pack_active) VALUES ('".urldecode($donate_data['pack_name'])."','".(isset($benefit) ? serialize($benefit) : '')."','".(isset($items) ? serialize($items) : '')."',".$item_id.",".dollarToCents($donate_data['pack_price']).",".(isset($donate_data['sub']) ? abs((int) $donate_data['sub_days']) : 0).", 0)");
						
			echo '<span style="color: darkgreen;">Your new donation pack has been created and added to the system.</span><br /><br />
			<span style="color: darkred;">You will need to activate the pack before it will appear on the sites donation page.</span><br /><br />';
		
		}
		
		//Create package
	}
	
	function settings() {
		global $db, $c, $userid, $set;
		
		$dset = getSettings();
		
		echo '<font color="black"><h3>Donation Settings</h3>
		<p>Here you are able to modify any changeable aspect of the donation system.</p>
		<form action="javascript:void(0);" method="post" id="settings" onsubmit="saveSettings();">
		<span id="settings_return"></span>
		<table width="100%" cellpadding="3">
			<tr valign="top">
				<td width="50%">
					System Open:<br />
					<small>Ability to disable the whole donation system.</small><br />
					<select name="donations_open">
						<option value="0" '.($dset['donations_open'] == 0 ? 'selected="selected"': '').'>Closed</option>
						<option value="1" '.($dset['donations_open'] == 1 ? 'selected="selected"': '').'>Open</option>
					</select>
					<br /><br />
					Item Type:<br />
					<small>The item type that the donator packs will be given, this feature still needs to be set even if you\'re not using it. It will of been automatically set on install but if you recieve errors you are able to change it.</small><br />
					'.itemtype_dropdown($c,'item_type',$dset['item_type']).'
					<br /><br />
					Maxium Purchase:<br />
					<small>The maximum amount a user can purchase at once. Should be formatted properly (Ex. 500.00) (0.00: None)</small><br />
					$<input type="text" name="max_purchase" STYLE="color: black;  background-color: white;" value="'.$dset['max_purchase'].'" size="10" />
					<br /><br />
					Mininum Purchase:<br />
					<small>The minimum amount a user can purchase at once. Should be formatted properly (Ex. 500.00) (0.00: None)</small><br />
					$<input type="text" name="min_purchase" STYLE="color: black;  background-color: white;" value="'.$dset['min_purchase'].'" size="10" />
				</td>
				<td width="50%">
					Credit Type:<br />
					<small>This is how you would like the donator packs to be given to the user. Instant credit means as soon as the user has paid all the aspects of their purchase will be instantly transfered onto their account. Item credit means the user will be given items in accordance to the pack(s) they purchase.</small><br />
					<select name="credit_type">
						<option value="0" '.($dset['credit_type'] == 0 ? 'selected="selected"': '').'>Instant Credit</option>
						<option value="1" '.($dset['credit_type'] == 1 ? 'selected="selected"': '').'>Item Credit</option>
					</select>
					<br /><br />
					Promotion:<br />
					<small>Set this to the % you want to have of all packages. Set to 0 to disable.</small><br />
					<input type="text" STYLE="color: black;  background-color: white;" name="discount" value="'.$dset['discount'].'" size="5" />%
					<br /><br />
					Currency Code:<br />
					<small>The currency code you wish to use for payments. View a full list <a href="http://www.xe.com/iso4217.php" target="_blank">here</a>.</small><br />
					<input type="text" name="currency" STYLE="color: black;  background-color: white;" value="'.$dset['currency'].'" size="3" />
					<br /><br />
					Currency Symbol:<br />
					<small>The symbol to display before the price of a pack.</small><br />
					<input type="text" STYLE="color: black;  background-color: white;" name="currency_symbol" value="'.$dset['currency_symbol'].'" size="1" />
					<br /><br />
					<span style="color: darkred;">Remember! To change parameters for certain gateways you must visit the gateway page.</span></font>
					<br /><br />
					<input type="submit" STYLE="color: black;  background-color: white;" value="Save Settings" />
				</td>
			</tr>
		</table>
		</form>
		';	
	}
	function save_settings() {
		global $db;
		
		$dset = getSettings();
		
		$data = explode('&',$_POST['data']);
		foreach($data as $d) {
			$save = explode('=',$d);
			if(isset($dset[$save[0]]) || $save[0] == 'paypal_email') {
				if(($save[0] == 'min_purchase' || $save[0] == 'max_purchase') && !preg_match('^\$?([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(.[0-9][0-9])$^',$save[1])) {
					echo '<span style="color: darkred;">The payment area\'t must have the correct formatting. <small>(Eg. 5.00)</small></span><br /><br />';
					$error = 1;
					break;
				} else if($save[0] == 'paypal_email') {
					$db->query("UPDATE settings SET conf_value = '".mysql_real_escape_string(urldecode($save[1]))."' WHERE conf_name = 'paypal'");
					break;
				} else if($save[0] == 'item_type' && $dset['item_type'] != $save[0]) {
					$db->query("UPDATE items SET itmtype = ".$save[1]." WHERE itmtype = ".$dset['item_type']);
					$db->query("UPDATE donate_settings SET donate_value = '".mysql_real_escape_string(urldecode($save[1]))."' WHERE donate_name = '".$save[0]."'");				
				} else {
					$db->query("UPDATE donate_settings SET donate_value = '".mysql_real_escape_string(urldecode($save[1]))."' WHERE donate_name = '".$save[0]."'");
				}
			}
		}
		
		if(!isset($error)) {
			echo '<span style="color: darkgreen;">All settings have been succesfully saved.</span><br /><br />';
		}
		
	}
	function gateways() {
		global $db;
		
		$dset = getSettings();
		
		?>
        <script type="text/javascript">
		function setActive(gateway,active,ele) {
			$.ajax({
				type: "POST",
				url: 'staff_donation.php',
				data: {'action': 'set_active','gateway': gateway,'set_to': active},
				success: function(data) {
					if(data == '1') {
						$('#' + gateway).toggleClass("unactive",1000);
					}
				}
			});	
		}
		$('[name=active]').click(function() {
		  if($(this).attr('checked') == true) {
			  setActive($(this).val(),1,this);	
		  } else {
			  setActive($(this).val(),0,this);		
		  }
		});
		</script>
        <?php
		
		echo '<font color="black"><h3>Payment Gateways</h3>
		<p>Here you can manage the different ways a user can pay for their purchase. You may click on a gateway to edit any settings it has.</font></p>
		<table width="100%" cellspacing="1" cellpadding="3" border="0" class="table tr_select">
			<tr>
				<th style="text-align: center;">Active</th>
				<th width="40%">Gateway Name</th>
				<th width="10%" style="text-align: center;">Version</th>
				<th width="20%">Update avalible?</th>
				<th width="20%">Developer</th>
			</tr>';
		foreach(list_gateways() as $gate) {
			$gate_data = load_gateway($gate);
			echo '<tr '.(isset($dset[$gate.'_active']) && $dset[$gate.'_active'] == 0 ? 'class="unactive"' : (!isset($dset[$gate.'_active']) ? 'class="install"' : '')).' id="'.$gate.'">
				<td style="text-align: center;"><input type="checkbox" name="active" value="'.$gate.'" value="0" '.(isset($dset[$gate.'_active']) && $dset[$gate.'_active'] == 1 ? 'checked="checked"' : '').' '.(!isset($dset[$gate.'_active']) ? 'disabled="disabled"' : '').' /></td>
				<td onclick="gatewaySettings(\''.$gate.'\');">'.$gate_data['gateway_name'].'</td>
				<td onclick="gatewaySettings(\''.$gate.'\');" style="text-align: center;">V'.$gate_data['gate_version'].'</td>
				<td onclick="gatewaySettings(\''.$gate.'\');">';
					
					if(!isset($dset[$gate.'_active'])) {
						
						echo '<span style="color: orange;">Click to Install</span>';
						
					} else {
						
						$get_file = @file_get_contents($gate_data['update_check']);
						if($get_file == false) {
							echo '<span style="color: darkred;">No update server</span>';	
						} else {
							if($get_file != $gate_data['gate_version']) {
								echo '<span style="color: darkgreen;">Update Avalible</span>';	
							} else {
								echo 'Latest version.';	
							}
						}
						
					}
										
				echo '</td>
				<td><a href="'.$gate_data['support'].'" target="_blank">'.$gate_data['developer'].'</a></td>
			</tr>';				
		}
		echo '</table>';
	}
	function gateway_settings() {
		global $db, $userid, $set;
		if(isset($_POST['gateway'])) {
			
			if(in_array($_POST['gateway'], list_gateways())) {
				$gate_data = load_gateway($_POST['gateway']);
				
				if(file_exists('gateway/'.$_POST['gateway'].'/'.$gate_data['settings_file'])) {
					
					require_once('gateway/'.$_POST['gateway'].'/'.$gate_data['settings_file']);
					
				} else {
					echo '<h3>Gateway Error</h3>
					<p>The gateways setting file is missing, please make sure all files were uploaded.</p>';		
				}
				
			} else {
				echo '<h3>Gateway Error</h3>
				<p>That gateway dosen\'t currently exist.</p>';		
			}
			
		} else {
			echo '<h3>Gateway Error</h3>
			<p>This gateway has no settings.</p>';	
		}
	}
	
	function view_donations() {
		global $db, $ir, $h;
		
		$dset = getSettings();
		
		echo '<font color="black"><h3>View Donations</h3>
		<p>Here you are able to view all the recent donations which have been paid into the game.</font></p>
		<table width="100%" cellspacing="1" cellpadding="3" border="0" class="table tr_select">
			<tr>
				<th width="20%">Time</th>
				<th width="10%" style="text-align: center;">Price</th>
				<th width="25%">Packs</th>
				<th width="10%">Buyer</th>
				<th width="10%">For</th>
				<th>Payment</th>
			</tr>';
			$data = $db->query("SELECT * FROM donate_log WHERE donate_status != 0 ORDER BY donate_time DESC");
			if(!$db->num_rows($data)) {
				echo '<tr>
					<td colspan="6" style="text-align: center;">
						There are no transactions available to view.
					</td>
				</tr>';	
			}
			$total_made = 0;
			while($da = $db->fetch_row($data)) {
				echo '<tr>
					<td>'.date ('F j, g:i a',$da['donate_time']).'</td>
					<td style="text-align: center;">'.$dset['currency_symbol'].centsToDollar($da['donate_totalprice']).'</td>
					<td>';
					
					$part = explode('|',$da['donate_packs']);
					$packs = '';
					foreach($part as $pd) {
						$pda = explode('_',$pd);
						$select = $db->query("SELECT pack_name FROM donate WHERE pack_id = ".$pda[0]);
						if($db->num_rows($select)) {
							$pack = $db->fetch_row($select);
							echo $pack['pack_name'].' [x'.$pda[1].']<br />';	
						}
					}	
					
					echo '</td>
					<td><a href="viewuser.php?u='.$da['donate_buyer'].'">'.getUsername($da['donate_buyer']).'</a> ['.$da['donate_buyer'].']</td>
					<td><a href="viewuser.php?u='.$da['donate_for'].'">'.getUsername($da['donate_for']).'</a> ['.$da['donate_for'].']</td>
					<td>';
					
					if($da['donate_status'] == 0) {
						echo '<span style="color: darkred;">Pending</span>';	
					} else if($da['donate_status'] == 1) {
						echo '<span style="color: darkgreen;">Complete</span><br />
						<b>Gateway:</b> <small>'.$da['donate_gateway'].'</small><br />
						<b>Payment ID:</b> <small>'.$da['donate_paymentid'].'</small>';	
						$total_made += $da['donate_totalprice'];
					} else if($da['donate_status'] == 2) {
						echo '<span style="color: darkred;">Canceled</span>';	
					} else if($da['donate_status'] == 3) {
						echo '<span style="color: orange;">eCheck</span>';	
					} else if($da['donate_status'] == 4) {
						echo '<span style="color: darkgreen;">Subscription</span><br />
						<b>Payment ID:</b> <small>'.$da['donate_paymentid'].'</small>';
						$total_made += $da['donate_totalprice'];
					}
					echo '</td>';
			}
		echo '<tr>
			<th style="text-align: right;">Total Made:</th>
			<td style="text-align: center;">'.$dset['currency_symbol'].centsToDollar($total_made).'</td>
			<td colspan="4"><small>This is only the complete payments.</small></td>
		</table>';
	}
	
	function error() {
		echo '<h3>Page Error</h3>
		<p>The page you\'re trying to load couldn\'t be located. Please try again.</p>';	
	}
	
	switch($_POST['action']) {
		case 'view':view();break;	
		case 'create':create();break;
		case 'gateways':gateways();break;
		case 'gateway_settings':gateway_settings();break;
		case 'settings':settings();break;
		case 'save_settings':save_settings();break;
		case 'view_donations':view_donations();break;
		
		case 'create_pack':createPack();break;
		case 'set_active':setActive();break;
		
		case 'benefit_dropdown':benefit_dropdown();break;
		case 'item_dropdown':item_staff_dropdown();break;
		default:error();break;
	}
	exit;
}
require_once('sglobals.php');

function dumpDatabase() {
	global $db;
	
	$table[] = "CREATE TABLE IF NOT EXISTS `donate` (
	  `pack_id` int(11) NOT NULL AUTO_INCREMENT,
	  `pack_name` varchar(255) NOT NULL,
	  `pack_benefits` text NOT NULL,
	  `pack_items` text NOT NULL,
	  `pack_item_id` int(11) NOT NULL,
	  `pack_price` int(11) NOT NULL,
	  `pack_sub` int(11) NOT NULL,
	  `pack_active` int(1) NOT NULL,
	  PRIMARY KEY (`pack_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$table[] = "CREATE TABLE IF NOT EXISTS `donate_errors` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `errorId` int(11) NOT NULL,
	  `errorText` text NOT NULL,
	  `errorTime` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$table[] = "CREATE TABLE IF NOT EXISTS `donate_log` (
	  `donate_id` int(6) NOT NULL,
	  `donate_time` int(11) NOT NULL,
	  `donate_totalprice` int(11) NOT NULL,
	  `donate_packs` text NOT NULL,
	  `donate_buyer` int(11) NOT NULL,
	  `donate_for` int(11) NOT NULL,
	  `donate_paymentid` varchar(255) NOT NULL,
	  `donate_gateway` varchar(255) NOT NULL,
	  `donate_status` int(1) NOT NULL,
	  `donate_sub` int(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`donate_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	
	$table[] = "CREATE TABLE IF NOT EXISTS `donate_settings` (
	  `donate_name` text NOT NULL,
	  `donate_value` text NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	
	foreach($table as $t) {
		$db->query($t) or die('<b>MySQL Databse insertion error:</b><p>An error has occured when the system attempted to dump the various database information into the table. Please check the error below.<br />'.mysql_error());	
	}	
}
function installDonation() {
	global $db,$c,$userid,$ir,$h;
	// Just to make it simple to install the modification.
	
	$settings_required = array('credit_type','donations_open','item_type','max_purchase','min_purchase','discount','currency','currency_symbol','success_message');
	$default = array(0,1,0,'0.00','0.00',0,'USD','$',mysql_real_escape_string('The donation pack(s) you\'ve just purchased have been credited to your account. The update should be instant and you should be able to use the benefits instantly.'));
	$dset = getSettings();
	$count = 0;
	
	foreach($settings_required as $setting) {
		if(!isset($dset[$setting])) {
			if($setting == 'item_type') {
				$db->query("INSERT INTO `itemtypes` (itmtypename) VALUES ('Donator Pack')");
				$default[$count] = mysql_insert_id();
			}
			$db->query("INSERT INTO `donate_settings` VALUES('".$setting."','".$default[$count]."')");
			++$count;	
		}
	}
	
}

$tables = array('donate','donate_errors','donate_log','donate_settings');

$check = 0;
foreach($tables as $ct) {	
	if(!tableExists($ct)) {
		dumpDatabase();
		++$check;
		break;
	}
}
if($check >= 1) {
	header("Location: staff_donation.php");
	exit;	
}

$dset = getSettings();

if($dset == '') {
	//If the system is not installed, or if parts of the installation are missing or corrupt. Re-install the system.
	installDonation();
	
	echo '<div style="text-align: left;">
		<h3>System Installed</h3>
		<p>The system has been fully installed and should now be fully functional providing there are no errors above. Please click the link below to access the system.<br />
		<br />
		&gt; <a href="staff_donation.php">Access Staff System</a>
	</div>';
	
	$h->endpage();
	
	exit;
}

?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<style type="text/css">
#donate {
	width: 700px;
}
#donate_content {
	width: 670px;	
	background: white;
	border: 1px solid black;
	margin-top: -8px;
	text-align: left;
	padding: 15px;

}
#donate_content h3 {
	margin-top: 0;
	padding-top: 0;	
}
.donate_menu {
	list-style: none;	
	text-align: left;
	margin: 0;
	padding: 0;
	height: 34px;
}
.donate_menu li {	
	display: inline;
}
.donate_menu li a {
	display: inline-table;
	padding: 5px;
	border: 1px solid black;
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0, rgb(209,209,209)),
		color-stop(1, rgb(251,251,251))
	);
	background-image: -moz-linear-gradient(
		center bottom,
		rgb(209,209,209) 0%,
		rgb(251,251,251) 100%
	);
	cursor: pointer;
	-webkit-transition: 0.5s;	
	border-bottom: none;
}
.donate_menu li a:hover {
	padding-bottom: 2px;
}
.tr_select tr {
	cursor: pointer;
	-webkit-transition: 0.5s;		
}
.tr_select tr:hover {
	opacity: 0.9;	
}
.unactive td {
	background: #ff7f7f;
	color: #cc0000;
}
.unactive td a {
	color: #cc0000;	
}
.install td {
	background: #ffc17f;
	color: #cc6900;
}
.install td a {
	color: #cc6900;	
}
#settings_return {
	display: none;	
}
</style>
<script type="text/javascript">
function changePage(new_page) {
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': new_page},
		success: function(data) {
			$('#donate_content .content').slideUp(500, function() { $(this).html(data).slideDown(500); });
		}
	});
}
function editPack(pack_id) {
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': 'create','edit': pack_id},
		success: function(data) {
			$('#donate_content .content').slideUp(500, function() { $(this).html(data).slideDown(500); });
		}
	});	
}
function gatewaySettings(gateway) {
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': 'gateway_settings', 'gateway': gateway},
		success: function(data) {
			$('#donate_content .content').slideUp(500, function() { $(this).html(data).slideDown(500); });
		}
	});
}
function saveSettings() {
	var form_data = $('#settings').serialize();
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': 'save_settings', 'data': form_data},
		success: function(data) {
			$('#settings_return').html(data).fadeIn(500);
			setTimeout('$(\'#settings_return\').fadeOut(500)',3000);
		}
	});	
}
function addBenefit() {
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': 'benefit_dropdown'},
		success: function(data) {
			$('.add_benefit').before(data);
		}
	});
}
function addItem() {
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': 'item_dropdown'},
		success: function(data) {
			$('.add_item').before(data);
		}
	});
}
function checkChecked(ele) {
	if($(ele).attr('checked')) {
		$('#sub_days_box').slideDown(500);
	} else {
		$('#sub_days_box').slideUp(500);	
	}
}
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
function createPack(edit) {
	var benefitData = '';
	$('.benefit_box').each(function() {
		var qty = $(this).children('[name=qty]').val();
		var action = $(this).children('[name=action]').val();
		var benefit = $(this).children('[name=benefits]').val();
		if(qty >= 1) {
			benefitData += benefit + ',' + action + ',' + qty + '|';
		}
		
	});
	var itemData = '';
	$('.item_box').each(function() {
		var itemId = $(this).children('[name=item_id]').val();
		var qty = $(this).children('[name=qty]').val();
		if(qty >= 1) {
			itemData += itemId + ',' + qty + '|';
		}
		
	});
	var formData = $('#create').serialize();
	benefitData = rtrim(benefitData,'|');
	itemData = rtrim(itemData,'|');
	
	$.ajax({
		type: "POST",
		url: 'staff_donation.php',
		data: {'action': 'create_pack','form_data': formData,'benefit_data': benefitData,'item_data': itemData,'edit': edit},
		success: function(data) {
			$('#create_return').html(data);
		}
	});
	
	return false;
}
</script>

<br />
<div style="text-align: left;width: 700px;">
    <h3><font color="white">Donation System Control Panel</h3>
    <p>Here you are able to control every controllable aspect of the donation system. </font></p>
</div>
<div id="donate">
    <ul class="donate_menu">
        <li><a href="javascript:void(0);" onclick="changePage('view');">View Packages</a></li>
        <li><a href="javascript:void(0);" onclick="changePage('create');">Create new Package</a></li>
        <li><a href="javascript:void(0);" onclick="changePage('gateways');">Gateways</a></li>
        <li><a href="javascript:void(0);" onclick="changePage('view_donations');">View Donations</a></li>
        <li class="last"><a href="javascript:void(0);" onclick="changePage('settings');">Settings</a></li>
    </ul>
    <div id="donate_content">
    	<div class="content">
		<h3><font color="black">Overview</h3>
        <p>Here you can view general information about various different area's of how your game is performing.
        </font></p></div>
    </div>
</div>

<div class="height_control"></div>

<?php
$h->endpage();
?>