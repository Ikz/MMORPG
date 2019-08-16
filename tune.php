<?php
include_once "globals.php";
function power($num1, $num2)
{
return pow($num1, $num2);
}
print "<h3>Tuning Shop</h3>";
if(!$_GET['id']) { print "Invalid Usage"; $h->endpage(); exit(); }
$q=$db->query("SELECT cp.*,ct.* FROM cars_playercars cp LEFT JOIN cars_types ct ON ct.carID=cp.cpcCAR WHERE cp.cpcID={$_GET['id']} AND cp.cpcPLAYER=$userid");
if($db->num_rows($q) == 0) { print "Invalid Car"; $h->endpage(); exit(); }
$r=$db->fetch_row($q);
$cost['acc']=$r['carACC']*power($r['cpcACCLV']+1,4)*($r['cpcACCLV']*$r['carACC']*10);
$cost['han']=$r['carHAN']*power($r['cpcHANLV']+1,4)*($r['cpcHANLV']*$r['carHAN']*10);
$cost['spd']=$r['carSPD']*power($r['cpcSPDLV']+1,4)*($r['cpcSPDLV']*$r['carSPD']*10);
$cost['shd']=$r['carSHD']*power($r['cpcSHDLV']+1,4)*($r['cpcSHDLV']*$r['carSHD']*10);
if($_GET['buy'])
{
if($_GET['buy'] != "acc" && $_GET['buy'] != "han" && $_GET['buy'] != "spd" && $_GET['buy'] != "shd") { print "Abusers suck."; $h->endpage(); exit(); }
$upgr_cost=$cost[$_GET['buy']];
if($ir['money'] < $upgr_cost) { print "You don't have enough money to tune this stat."; $h->endpage(); exit(); }
$db->query("UPDATE users SET money=money-{$upgr_cost} WHERE userid=$userid", $c);
$stat="cpc".strtoupper($_GET['buy'])."LV";
$db->query("UPDATE cars_playercars SET $stat=$stat+1 WHERE cpcID={$_GET['id']}");
print "Car tuned!
&gt; <a href='tune.php?id={$_GET['id']}'>Tune some more</a>";
}
else
{
foreach($cost as $k => $v)
{
$costf[$k]='$'.number_format($v);
}
$acc=$r['cpcACCLV']*$r['carACC'];
$han=$r['cpcHANLV']*$r['carHAN'];
$spd=$r['cpcSPDLV']*$r['carSPD'];
$shd=$r['cpcSHDLV']*$r['carSHD'];
print "Current Stats for your {$r['carNAME']}<br><br>
<table class='table' width='90%'><tr> <th>Stat</th> <th>Amount</th> <th>Cost To Tune</th> <th>Tune</th></tr>
<tr> <td>Acceleration</td> <td>Lv{$r['cpcACCLV']} ($acc)</td> <td>{$costf['acc']}</td> <td><a href='tune.php?id={$_GET['id']}&buy=acc'>Tune</a></td></tr>
<tr><td>Speed</td> <td>Lv{$r['cpcSPDLV']} ($spd)</td> <td>{$costf['spd']}</td> <td><a href='tune.php?id={$_GET['id']}&buy=spd'>Tune</a></td></tr>
 <tr> <td>Handling</td> <td>Lv{$r['cpcHANLV']} ($han)</td> <td>{$costf['han']}</td> <td><a href='tune.php?id={$_GET['id']}&buy=han'>Tune</a></td></tr>
 <tr> <td>Shield</td> <td>Lv{$r['cpcSHDLV']} ($shd)</td>  <td>{$costf['shd']}</td> <td><a href='tune.php?id={$_GET['id']}&buy=shd'>Tune</a></td></tr>
</table><br><a href='garage.php'>Back To Garage</a>";
}
$h->endpage();
?>