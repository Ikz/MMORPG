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
$q=$db->query("SELECT * FROM votes WHERE userid=$userid AND list='twg'");
if($db->num_rows($q))
{

print "You have already voted at TWG today!";

}
else
{
$db->query("INSERT INTO votes values ($userid,'twg')");
$db->query("UPDATE users SET energy=energy+maxenergy/5 WHERE userid=$userid");
$db->query("UPDATE users SET energy=maxenergy WHERE energy>maxenergy");
header("Location:http://www.topwebgames.com/in.asp?id=7772");
exit;
}
?>

