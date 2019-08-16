<?php

// -- PayPal log_purchase.php -- //

require_once('gateway/donation_functions.php');

$dset = getSettings();

$name = $set['game_name'].' Donation ['.$ir['username'].']';
$paypalemail = $set['paypal'];
$item_number = mt_rand(111111,999999);
$user_for = (abs((int) $_COOKIE['user_for']) ? abs((int) $_COOKIE['user_for']) : $_SESSION['userid']);
$cart_data = str_replace(',','_',$_COOKIE['cartData']); //Due to MySQL errors.

// -- Debug Information -- //

if($dset['paypal_sandbox'] == 1) {
	$sandbox = '.sandbox';
}

// -- Debug Information -- //

$site_url = str_replace('donate.php?payment=true&gateway=paypal', '', getAddress());

// -- Log the data -- //

$db->query('INSERT INTO donate_log (donate_id, donate_time, donate_totalprice, donate_packs, donate_buyer, donate_for, donate_gateway, donate_status) VALUES ('.$item_number.', '.time().', '.$db->escape($_SESSION['total_price']).', \''.$db->escape($cart_data).'\', '.$_SESSION['userid'].', '.$user_for.', \'paypal\', 0)');

// -- Log the data -- //

header('Location: https://www'.$sandbox.'.paypal.com/cgi-bin/webscr?cmd=_xclick&item_name='.$name.'&item_number='.$item_number.'&quantity=1&amount='.centsToDollar($_SESSION['total_price']).'&business='.$paypalemail.'&currency_code=USD&no_shipping=0&no_note=0&return='.$site_url.'donate.php?paid=true'.($dset['paypal_ipn_active'] == 1 ? '&notify_url='.$site_url.'/gateway/paypal/verify.php' : '').'&cancel_return='.$site_url.'donate.php?cancel=true');


?>