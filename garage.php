<?php
 include "globals.php";
if(!$_GET['st'] ) { $_GET['st']=0; }
$start=abs((int) $_GET['st']);
$cpp=20;
print "<h3>Your Garage</h3><br />
&gt; <a href='caryard.php'>Buy A Car (Car Yard)</a><br />
&gt; <a href='carmarket.php'>Buy A Car (Car Market)</a><br />
&gt; <a href='sellcar.php'>Sell Car (On The Car Market)</a><br><br /><h3>Your Cars</h3>";
$q=$db->query("SELECT cpcPLAYER FROM cars_playercars WHERE cpcPLAYER=$userid");
$cars=mysql_num_rows($q);
$pages=ceil($cars/$cpp);
print "Pages: ";
for($i=1; $i<=$pages; $i++)
{
$st=($i-1)*$cpp;
if($st == $start)
{
print "<b>$i</b>&nbsp;";
}
else
{
print "<a href='garage.php?st=$st'>$i</a>&nbsp;";
}
}
print "<br />
<table width=95% class=table border=6> <tr style='background:#cc9966'><th>Image</th><th>Name</th><th>Description</th><th>Acceleration</th> <th>Handling</th><th>Speed</th><th>Shield</th><th>In Challenge?</th><th>Tune</th></tr>";
$q=$db->query("SELECT cp.*, ct.* FROM cars_playercars cp LEFT JOIN cars_types ct ON cp.cpcCAR=ct.carID WHERE cp.cpcPLAYER=$userid LIMIT $start, $cpp");
$count=0;
$cars=array();
while($r=$db->fetch_row($q))
{
$count++;
$acc=$r['cpcACCLV']*$r['carACC'];
$han=$r['cpcHANLV']*$r['carHAN'];
$spd=$r['cpcSPDLV']*$r['carSPD'];
$shd=$r['cpcSHDLV']*$r['carSHD'];
$q2=$db->query("SELECT * FROM challenges WHERE chCHRCAR={$r['cpcID']} AND chSTATUS='open'");
if(mysql_num_rows($q2) == 1) { $challenge="<font color='red'>Yes</font>"; } else { $challenge="<font color='green'>No</font>"; }
print "<tr><td><a href='{$r['carPIC']}' target='_blank'><img src='{$r['carPIC']}' width='100' height='70' alt='{$r['carNAME']}' title='{$r['carNAME']}'></a></td><td>{$r['carNAME']} </td> <td>{$r['carDESC']}</td> <td>Lv{$r['cpcACCLV']} ($acc)</td>  <td>Lv{$r['cpcHANLV']} ($han)</td> <td>Lv{$r['cpcSPDLV']} ($spd)</td> <td>Lv{$r['cpcSHDLV']} ($shd)</td><td>$challenge</td> <td><a href='tune.php?id={$r['cpcID']}'>Tune</a></td> </tr>";
$cars[$r['cpcID']]="{$r['carNAME']} - {$r['cpcACCLV']}/{$r['cpcHANLV']}/{$r['cpcSPDLV']}/{$r['cpcSHDLV']}";
}
if($count == 0) { print "<tr><th colspan=9>No Cars In Your Garage</th></tr>"; }
print "</table>";
$totalraces=$ir['cars_races_won']+$ir['cars_races_lost'];
$races_highstakes=$ir['cars_won']+$ir['cars_lost'];
if($ir['cars_races_income'] > 0)
{
$income = '<font color="green">$'.number_format($ir['cars_races_income'])."</font>";
}
else if($ir['cars_races_income'] == 0)
{
$income='$0';
}
else
{
$income = '<font color="red">-$'.number_format(abs($ir['cars_races_income']))."</font>";
}
print "<br />

<table width=90% class=table border=6>
<th colspan='2'>Your Driver's Record</th><tr>
<td width='45%'>Cars Owned</td>
<td width='45%'>{$ir['cars_owned']}</td>
</tr>
<tr>
<td>Cars Won In Races</td>
<td>{$ir['cars_won']}</td>
</tr>
<tr>
<td>Cars Lost In Races</td>
<td>{$ir['cars_lost']}</td>
</tr>
<tr>
<td>Challenges Sent</td>
<td>{$ir['cars_challs_sent']}</td>
</tr>
<tr>
<td>Challenges Accepted</td>
<td>{$ir['cars_challs_accpt']}</td>
</tr>
<tr>
<td>Challenges Declined</td>
<td>{$ir['cars_challs_decln']}</td>
</tr>
<tr>
<td>Races Won</td>
<td>{$ir['cars_races_won']}</td>
</tr>
<tr>
<td>Races Lost</td>
<td>{$ir['cars_races_lost']}</td>
</tr>
<tr>
<td>Total Races</td>
<td>$totalraces</td>
</tr>
<tr>
<td>Total Income From Betted Races</td>
<td>$income</td>
</tr>
<tr>
<td>Friendly Races</td>
<td>{$ir['cars_races_friendly']}</td>
</tr>
<tr>
<td>Betted Races</td>
<td>{$ir['cars_races_betted']}</td>
</tr>
<tr>
<td>High-Stakes Races</td>
<td>{$races_highstakes}</td>
</tr>
</table>";
print "<br />
<h3>Pending Challenges To You</h3>
<table class=table border='1' width='90%'><tr> <th>Challenger</th> <th>When Sent?</th> <th>View</th> </tr>";
$q=$db->query("SELECT c.*,u.* FROM challenges c LEFT JOIN users u ON c.chCHR=u.userid WHERE chCHD=$userid AND chSTATUS = 'open'", $c);
if(mysql_num_rows($q) == 0)
{
print "<tr><td colspan='4'><center>No Pending Challenges</center></td></tr>";
}
else
{
while($r=$db->fetch_row($q))
{
print "<tr><td>{$r['username']}</td> <td>".date('F j Y, g:i:s a', $r['chTIME'])."</td> <td><a href='viewchallenge.php?id={$r['chID']}'>View</a></td></tr>";
}
}
print "</table>";
print "<br />
<h3>Pending Challenges Sent By You</h3>
<table border='1' width='90%' class=table><tr> <th>Challenged</th> <th>When Sent?</th> <th>Cancel</th> </tr>";
$q=$db->query("SELECT c.*,u.* FROM challenges c LEFT JOIN users u ON c.chCHD=u.userid WHERE chCHR=$userid AND chSTATUS = 'open'");
if(mysql_num_rows($q) == 0)
{
print "<tr><td colspan=4><center>No Pending Sent Challenges</center></td></tr>";
}
else
{
while($r=$db->fetch_row($q))
{
print "<tr><td>{$r['username']}</td> <td>".date('F j Y, g:i:s a', $r['chTIME'])."</td> <td><a href='cancelchallenge.php?id={$r['chID']}'>Cancel</a></td></tr>";
}
}
print "</table>";
if($count > 0)
{
print "<br />
<h3 name=\"challenge\">Challenge Someone To A Race</h3><br><br>";
foreach($cars as $k => $v)
{
if($ir['userid'] == 241)
{
print $k." = ".$v."<br>";
}
}
print "
<form action='makechallenge.php' method='post'>
Player ID To Challenge: <input type='text' STYLE='color: black;  background-color: white;' name='id' value='".$_GET["selectprouser"]."' /><br><br>

Type: <select name='type' type='dropdown'><option>Friendly</option> <option>Betted</option> <option>High-Stakes</option></select><br><br>Car to Use: <select name='car' type='dropdown'>";
foreach($cars as $k => $v)
{
if($_GET['selectprocar'] == $k)
$selected = 'selected';
else
$selected = "youwant = \"battlefield 1942 and battlefield 2\"";
print "<option value='$k' $selected>$v</option>";
}
print "</select><br><br>
Bet (if Betted Race): $<input type='text' STYLE='color: black;  background-color: white;' name='bet' value='0' /><br><br>
<input type='submit' value='Send Challenge' STYLE='color: black;  background-color: white;'/></form>";
}
$h->endpage();
?>