<script type="text/javascript">
function savePayPal() {
	var form_data = $('#paypal_set').serialize();
	$.ajax({
		type: "POST",
		url: 'gateway/paypal/settings.php',
		data: {'action': 'save_settings', 'data': form_data},
		success: function(data) {
			$('#settings_return').html(data).fadeIn(500);
			setTimeout('$(\'#settings_return\').fadeOut(500)',3000);
		}
	});	
}
</script>
<?php

if($_POST['action'] == 'save_settings') {
	
	if(!in_array('donation_functions.php',get_included_files())) {
		require_once('../donation_functions.php');
	}
	
	include "../../config.php";
	global $_CONFIG;
	define("MONO_ON", 1);
	require "../../class/class_db_{$_CONFIG['driver']}.php";
	require_once('../../global_func.php');
	$db=new database;
	$db->configure($_CONFIG['hostname'],
	 $_CONFIG['username'],
	 $_CONFIG['password'],
	 $_CONFIG['database'],
	 $_CONFIG['persistent']);
	$db->connect();
	$c=$db->connection_id;
	
	$dset = getSettings();
	
	$data = explode('&',$_POST['data']);
	foreach($data as $d) {
		$save = explode('=',$d);
		if($save[0] == 'paypal_email') {
		  $db->query("UPDATE settings SET conf_value = '".mysql_real_escape_string(urldecode($save[1]))."' WHERE conf_name = 'paypal'");  
		}
		if(isset($dset[$save[0]])) {
		  $db->query("UPDATE donate_settings SET donate_value = '".mysql_real_escape_string(urldecode($save[1]))."' WHERE donate_name = '".$save[0]."'");
		}
	}	
	echo '<span style="color: darkgreen;">All settings have been succesfully saved.</span><br /><br />';
	exit;
}

if(!in_array('donation_functions.php',get_included_files())) {
	require_once('gateway/donation_functions.php');
}

$dset = getSettings();

$settings_required = array('paypal_active','paypal_debug','paypal_ipn_active','paypal_sandbox');
$default = array(1,0,1,0);
$count = 0;

foreach($settings_required as $setting) {
	if(!isset($dset[$setting])) {
		$db->query("INSERT INTO `donate_settings` VALUES('".$setting."',".$default[$count].")");
		$edit = 1;
		++$count;	
	}
}

$dset = getSettings();

echo '<h3>PayPal Gateway Settings</h3>
'.(isset($edit) ? '<span style="color: darkgreen;">The PayPal Gateway has been succesfully installed. You can edit the settings below.</span>' : '').'
<p>Here you are able to make modifications to the PayPal gateway.</p>
<span id="settings_return"></span>
<form action="javascript:void(0);" method="post" id="paypal_set" onsubmit="savePayPal();">
	<table width="100%" cellpadding="3">
		<tr valign="top">
			<td width="50%">
				PayPal Email:<br />
				<small>The email you have associated with your PayPal account.</small><br />
				<input type="text" name="paypal_email" value="'.$set['paypal'].'" style="width: 95%;" />
				<br /><br />
				Debugging:<br />
				<small>If something is going wrong with the functioning you can debug the script and see what\'s going wrong.</small><br />
				<select name="paypal_debug">
					<option value="1" '.($dset['paypal_debug'] == 1 ? 'selected="selected"' : '').'>On</option>
					<option value="0" '.($dset['paypal_debug'] == 0 ? 'selected="selected"' : '').'>Off</option>
				</select>
			</td>
			<td>
				IPN Activated:<br />
				<small>This allows users to be automatically credited when they purchase something. This will mean you have to manually accept payments.</small><br />
				<select name="paypal_ipn_active">
					<option value="1" '.($dset['paypal_ipn_active'] == 1 ? 'selected="selected"' : '').'>Active</option>
					<option value="0" '.($dset['paypal_ipn_active'] == 0 ? 'selected="selected"' : '').'>Inactive</option>
				</select>
				<br /><br />
				Sandbox Mode:<br />
				<small>Uses PayPal\'s sandbox system to test the system, only activate if you know what you\'re doing!</small><br />
				<select name="paypal_sandbox">
					<option value="1" '.($dset['paypal_sandbox'] == 1 ? 'selected="selected"' : '').'>On</option>
					<option value="0" '.($dset['paypal_sandbox'] == 0 ? 'selected="selected"' : '').'>Off</option>
				</select>
				<br /><br />
				<input type="submit" value="Save Settings" />
			</td>
		</tr>
	</table>
</form>

';

?>