<?php

include "globals.php";
if($_GET['action'] == "cancel")
{
print "You have cancelled your donation. Please donate later...";
}
else if($_GET['action'] == "done")
{
if(!$_GET['tx'])
{
die ("Get a life.");
}
print "Thank you for your payment to {$set['game_name']}. Your transaction has been completed, and a receipt for your purchase has been emailed to you. You may log into your account at <a href='http://www.paypal.com'>www.paypal.com</a> to view details of this transaction. Your donator pack should be credited within a few minutes, if not, contact an admin for assistance.";
}
$h->endpage();
?>
