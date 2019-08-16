<?php

session_start();
if(get_magic_quotes_gpc() == 0)
{
foreach($_POST as $k => $v)
{
  $_POST[$k]=addslashes($v);
}
foreach($_GET as $k => $v)
{
  $_GET[$k]=addslashes($v);
}
}
require "global_func.php";
if($_SESSION['loggedin']==0) { header("Location: login.php");exit; }
$userid=$_SESSION['userid'];
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
$is=$db->query("SELECT u.*,us.* FROM users u LEFT JOIN userstats us ON u.userid=us.userid WHERE u.userid=$userid");
$ir=$db->fetch_row($is);
$q=$db->query("SELECT * FROM votes WHERE userid=$userid AND list='trpg'");
if($db->num_rows($q))
{

print "You have already voted at TOPRPG today!";

}
else
{
$db->query("INSERT INTO votes values ($userid,'trpg')");
$db->query("UPDATE users SET money=money+500 WHERE userid=$userid");
header("Location:http://www.toprpgames.com/vote.php?idno=2896");
exit;
}
?>

