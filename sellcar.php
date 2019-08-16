<?php
 include "globals.php";

print "<h3>Sell a Car</h3>
<br>
<table class=table width=95% border=6> <tr style='background:#cc9966'> <th>Image</th> <th>Name</th> <th>Description</th> <th>Acceleration</th> <th>Handling</th> <th>Speed</th> <th>Shield</th> <th>Sell</th> </tr>";
$q=$db->query("SELECT cp.*, ct.* FROM cars_playercars cp LEFT JOIN cars_types ct ON cp.cpcCAR=ct.carID WHERE cp.cpcPLAYER=$userid", $c);
$count=0;
$cars=array();
while($r=$db->fetch_row($q))
{
$count++;
$acc=$r['cpcACCLV']*$r['carACC'];
$han=$r['cpcHANLV']*$r['carHAN'];
$spd=$r['cpcSPDLV']*$r['carSPD'];
$shd=$r['cpcSHDLV']*$r['carSHD'];
print "<tr><td><a href='{$r['carPIC']}' target='_blank'><img src='{$r['carPIC']}' width='100' height='70' alt='{$r['carNAME']}' title='{$r['carNAME']}'></a></td><td>{$r['carNAME']}</td> <td>{$r['carDESC']}</td> <td>Lv{$r['cpcACCLV']} ($acc)</td>  <td>Lv{$r['cpcHANLV']} ($han)</td> <td>Lv{$r['cpcSPDLV']} ($spd)</td> <td>Lv{$r['cpcSHDLV']} ($shd)</td> <td><a href='carmadd.php?ID={$r['cpcID']}'>Add To Market</a></td> </tr>";
$cars[$r['cpcID']]="{$r['carNAME']} - {$r['cpcACCLV']}/{$r['cpcHANLV']}/{$r['cpcSPDLV']}/{$r['cpcSHDLV']}";
}
if($count == 0) { print "<tr><th colspan=8>No Cars In Your Garage to sell</th></tr>"; }
print "</table><br><a href='garage.php'>Back To Garage</a>";

$h->endpage();
?>