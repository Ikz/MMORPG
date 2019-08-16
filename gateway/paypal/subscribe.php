<?php

// -- PayPal subscribe.php -- //

require_once('gateway/donation_functions.php');

$dset = getSettings();

$name = $set['game_name'].' Subscription ['.$ir['username'].']';
$paypalemail = $set['paypal'];
$item_number = mt_rand(111111,999999);
$user_for = $_SESSION['userid'];
$cart_data = abs((int) $_GET['subscribe']).'_1';

// -- Debug Information -- //

if($dset['paypal_sandbox'] == 1) {
	$sandbox = '.sandbox';
}

// -- Debug Information -- //

$site_url = str_replace('donate.php?subscribe='.$_GET['subscribe'], '', getAddress());

// -- Log the data -- //

$db->query('INSERT INTO donate_log (donate_id, donate_time, donate_totalprice, donate_packs, donate_buyer, donate_for, donate_gateway, donate_status, donate_sub) VALUES ('.$item_number.', '.time().', '.$db->escape($price).', \''.$db->escape($cart_data).'\', '.$_SESSION['userid'].', '.$user_for.', \'paypal_sub\', 0, 1)');

// -- Log the data -- //

header('Location: https://www'.$sandbox.'.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&business='.$paypalemail.'&currency_code=USD&no_shipping=1&a3='.centsToDollar($price).'&p3='.$days.'&t3=D&src=1&sra=1&item_name='.$name.'&item_number='.$item_number.'&notify_url='.$site_url.'/gateway/paypal/verify.php&return='.$site_url.'donate.php?paid='.$id.'&cancel_return='.$site_url.'donate.php?cancel=true');


?>