<?php

session_start();
$sessid=$_SESSION['userid'];
$atk=$_SESSION['attacking'];

if($_SESSION['attacking'])
{
print "You lost all your EXP for running from the fight.<br />";
include "config.php";
include "language.php";
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
$db->query("UPDATE users SET exp=0,attacking=0 WHERE userid=$sessid");
$_SESSION['attacking']==0;
session_unset();
session_destroy();
die("<a href='login.php'>Continue to login...</a>");
}
session_unset();
session_destroy();
header("Location: login.php");

?>
