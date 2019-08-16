<?php
 include "globals.php";
$id=abs((int) $_GET['id']);
$q=mysql_query("SELECT ch.*, cp.*, ct.*, u1.username as challenger, u2.username as challenged FROM challenges ch LEFT JOIN cars_playercars cp ON ch.chCHRCAR=cp.cpcID LEFT JOIN cars_types ct ON ct.carID=cp.cpcCAR LEFT JOIN users u1 ON ch.chCHR=u1.userid LEFT JOIN users u2 ON ch.chCHD=u2.userid WHERE ch.chID={$id} AND (ch.chCHR=$userid OR ch.chCHD=$userid)", $c) or die(mysql_error());
if(mysql_num_rows($q) == 0) { die("Invalid Challenge"); }
$r=mysql_fetch_array($q);
print "<h2>Challenge From {$r['challenger']} to {$r['challenged']}</h2><hr />
Type: <h3><font color=red>{$r['chTYPE']}</font></h3><br />";
if($r['chTYPE'] == "Betted") { $bet='$'.number_format($r['chBET']); print "Bet: $bet<br />"; }
print "Challengers Car: {$r['carNAME']}<br />
Status: {$r['chSTATUS']}<br />";
if(($userid == $r['chCHD'] or $userid == 1) and $r['chSTATUS'] == "open")
{
$q=mysql_query("SELECT cp.*, ct.* FROM cars_playercars cp LEFT JOIN cars_types ct ON cp.cpcCAR=ct.carID WHERE cp.cpcPLAYER=$userid", $c);
$cars=array();
while($r=mysql_fetch_array($q))
{
$cars[$r['cpcID']]="{$r['carNAME']} - {$r['cpcACCLV']}/{$r['cpcHANLV']}/{$r['cpcSPDLV']}/{$r['cpcSHDLV']}";
}
print "<hr />
<h3>Manage This Challenge</h3>
<b>Accept It:</b><br />
<form action='acceptchallenge.php' method='post'>
Car To Use: <select name=car type=dropdown>";
foreach($cars as $k => $v)
{
print "<option value='$k'>$v</option>";
}
print "</select><br />
<input type='hidden' name='id' value='$id'>
<input STYLE='color: black;  background-color: white;' type='submit' value='Accept Challenge' /></form><br />
<b>Decline Challenge:</b><br />
&gt; <a href='declinechallenge.php?id={$id}'>Click Here</a>";
}
$h->endpage();
?>