<?php
include ("./config.php");
global $_CONFIG;
if($_GET['code'] != $_CONFIG['code']) { die(""); }
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
include("./global_func.php");
$sql = $db->query("SELECT * FROM `lottery`");
$rows = $db->num_rows($sql);
$row = $db->fetch_row($sql);
$winner = rand(1,$rows);
$winnerq = sprintf("SELECT `userid` FROM `lottery` WHERE `id` = %d",
$winner);
$winnerq1 = $db->query($winnerq);
$user = $db->fetch_row($winnerq1);
 
$credit = sprintf("UPDATE `users` SET `money` = `money` + %d WHERE `userid` = (%u)",
$row['jackpot'],
$user['userid']);
event_add($user['userid'],"You won the weekly lottery and were credited \${$row['jackpot']}",$c);  
 
$db->query("UPDATE `users` SET `lottery` = 0");
$db->query("TRUNCATE TABLE `lottery`");
 
?>