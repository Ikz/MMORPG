<?php

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
if(!$_GET['password']) { die("<font color='red'>$rrerr10</font>"); }
if(strlen($_GET['password']) < 4) { die("<font color='red'>$rrerr11a</font>"); }
$un=$_GET['password'];
$q=$db->query("SELECT * FROM users WHERE login_name='$un' OR username='$un'");
if($db->num_rows($q)) { die("<font color='red'>$rrerr12</font>"); }
print "<font color='green'>$rrerr13</font>";
?>
