<?php 
error_reporting(E_ALL ^ E_NOTICE); 
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

$set = array();
$settq = $db->query("SELECT conf_name, conf_value FROM settings");
while($r=$db->fetch_row($settq)) {
	$set[$r['conf_name']]=$r['conf_value'];
}

// Read the post from PayPal and add 'cmd' 
$req = 'cmd=_notify-validate'; 
if(function_exists('get_magic_quotes_gpc')) 
{  
	$get_magic_quotes_exits = true; 
} 
foreach ($_POST as $key => $value) 
// Handle escape characters, which depends on setting of magic quotes 
{  
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1){  
		$value = urlencode(stripslashes($value)); 
	} else { 
		$value = urlencode($value); 
	} 
	$req .= "&$key=$value"; 
} 

require_once('../donation_functions.php');

$dset = getSettings();

if($dset['paypal_sandbox'] == 1) {
	$sandbox = '.sandbox';
}

$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n"; 
$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; 
$fp = fsockopen ('ssl://www'.$sandbox.'.paypal.com', 443, $errno, $errstr, 30); 

function record_error($error) {
	global $db, $set, $req, $dset;
	if($dset['paypal_debug'] == 1) {
		$db->query("INSERT INTO donate_errors (errorText, errorTime) VALUES('".$error."', '".time()."')");
	} else {
		$admins = $db->query("SELECT user_level, userid FROM users WHERE user_level = 2");
		while($a = $db->fetch_row($admins)) {
			event_add($a['userid'],'<b>Donation System Error</b><br />A recent error has occured within the donation system. Please enable debugging to get a more in-depth log of what went wrong.<br /><br />
			<u>Error Details:</u><br />
			'.$error);
		}	
	}
}
 
$item_name = (isset($_POST['item_name']) ? $_POST['item_name'] : '');
$item_number = (isset($_POST['item_number']) ? $_POST['item_number'] : '');
$payment_status = (isset($_POST['payment_status']) ? $_POST['payment_status'] : '');
$payment_amount = (isset($_POST['mc_gross']) ? $_POST['mc_gross'] : ''); 
$payment_currency = (isset($_POST['mc_currency']) ? $_POST['mc_currency'] : '');
$txn_id = (isset($_POST['txn_id']) ? $_POST['txn_id'] : '');
$receiver_email = (isset($_POST['receiver_email']) ? $_POST['receiver_email'] : '');
$payer_email = (isset($_POST['payer_email']) ? $_POST['payer_email'] : '');

if($dset['paypal_debug'] == 1) {
	if($_POST['txn_type'] == 'subscr_signup') {
		$text .= '<span style="color: darkred;">Warning! This is s subcription signup page, So the log is invalid but could be helpful if something isn\'t working.</span><br /><br />';
	}
	$text .= "<b>IPN POST Vars from Paypal:</b>\n";
	foreach ($_POST as $key=>$value) {
	   $text .= "$key=$value\n";
	}

	$fw=fopen('donate_log.txt','a');
	fwrite($fw, $text . "\n\n"); 
	event_add(1,nl2br($text).'<br />
	<small>You have recieved this because you have PayPal debugging enabled.</small>');

	fclose($fw);  // close file	
}
 
if (!$fp) { 
	record_error('#0 FP Error');
	exit;
} else { 
fputs ($fp, $header . $req); 
while (!feof($fp)) { 
	$res = fgets ($fp, 1024); 
	if (strcmp ($res, "VERIFIED") == 0) { 
		
		if($_POST['txn_type'] == 'subscr_signup') {
			//User created subscription. Don't really need to do anything with this.
			exit;
		}
		
		
		if($payment_status != 'Completed') {
			record_error('#0 Payment is not complete. $payment_status = '.$payment_status);
			exit;
		}
		if(!$item_number) { // Make sure the item number is set.
			record_error('#1 Item Number not present from IPN.');
			exit;	
		}
		
		if($set['paypal'] != $receiver_email) { // Make sure the payment is going to the correct PayPal account.
			record_error('#2 Payment Email changed.');
			exit;	
		}
		
		if($payment_currency != 'USD') { // Make sure the user is paying in the correct currency.
			record_error('#3 User is not paying in US dollars.');
			exit;	
		}
		
		$select = $db->query("SELECT * FROM `donate_log` WHERE donate_id = ".$item_number);
		if(!$db->num_rows($select)) { // Make sure the purchase exists in our database.
			record_error('#4 The package the user is buying dosen\'t exist in the database.');
			exit;	
		}
		
		$pack_data = $db->fetch_row($select);
		
		if(centsToDollar($pack_data['donate_totalprice']) != $payment_amount) { // Make sure the price is correct.
			record_error('#5 The price for the total items was incorrect.');
			exit;	
		}
		if($payment_type == 'echeck') { // The user is paying with an eCheck. Thus halt the payment till it clears.
			record_error('#6 User is paying with an eCheck. So item was not instantly credited.');
			
			//            -- Status ID's --      //
			//    1  =   Complete                //
			//    2  =   Canceled                //
			//    3  =   eCheck                  //
			//    4  =   Subscription Payment    //
			
			$db->query("UPDATE donate_log SET donate_status = 3 WHERE donate_id = ".$item_number);
			exit;	
		}
		if($_POST['txn_type'] != 'subscr_payment' && $pack_data['donate_sub'] == 1) {
			
			record_error('#6 Using is setting up a subscription on a non subscribable pack.');	
			exit;
			
		}
								
		// -- Credit users donator packs -- //
		
		$part = explode('|',$pack_data['donate_packs']);
		foreach($part as $pd) {
			$pda = explode('_',$pd);
			creditPack($pda[0],$pack_data['donate_for'],$pda[1]);	
		}
		
		if($pack_data['donate_for'] != $pack_data['donate_buyer']) {
			event_add($pack_data['donate_for'],'The donation packs that were purchased for you by '.getUsername($pack_data['donate_buyer']).' have been credited to your account. Enjoy!');
			event_add($pack_data['donate_buyer'],'The donation packs you purchased for '.getUsername($pack_data['donate_for']).' have been credited to their account. Enjoy!');
		}
			
		event_add($pack_data['donate_for'],$dset['success_message']);
		
		$db->query("UPDATE donate_log SET donate_status = ".($_POST['txn_type'] == 'subscr_payment' ? 4 : 1).", donate_paymentid = '".$txn_id."' WHERE donate_id = ".$item_number);
		
		if($_POST['txn_type'] == 'subscr_payment') {
			$db->query('INSERT INTO donate_log (donate_id, donate_time, donate_totalprice, donate_packs, donate_buyer, donate_for, donate_gateway, donate_status, donate_sub, donate_paymentid) VALUES ('.$item_number.'.'.$txn_id.', '.time().', '.$payment_amount.', \''.$pack_data['donate_packs'].'\', '.$pack_data['donate_buyer'].', '.$pack_data['donate_for'].', \'paypal_sub\', 4, 1, '.$txn_id.')');
		} 
		
		// -- Credit users donator packs -- //	
		
	} else if (strcmp ($res, "INVALID") == 0) { 
		record_error('#7 Manual run detected.');
	}	 
} 
fclose ($fp); 
}
?>