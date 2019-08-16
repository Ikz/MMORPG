<?php
 include "globals.php";
$ycq=$db->query("SELECT pc.*,t.* FROM cars_playercars pc LEFT JOIN cars_types t ON pc.cpcCAR=t.carID WHERE pc.cpcPLAYER=$userid");
while($r=$db->fetch_row($ycq))
{
$cars[]=$r;
}
if($ir['userid'] <> 1)
{
die("&gt;_&gt;");
}
switch($_GET['action'])
{
default:
index();
break;
}
function index()
{
global $ir, $c, $userid, $h, $cars;
}
$h->endpage();
?>