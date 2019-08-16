<?php
 include "globals.php";
$_GET['ID'] = abs((int) $_GET['ID']);
$_GET['price'] = make_bigint( $_GET['price']);
$q=$db->query("SELECT * FROM challenges WHERE chCHRCAR={$_GET['ID']} AND chSTATUS='open'");
if($db->num_rows($q) >0) { die("You are already challenging someone with this car."); }
if($_GET['price'])
{
$q1=$db->query("SELECT * FROM carmarket WHERE cmADDER={$userid}");
if($db->num_rows($q1) >= 3) { die("You can only put up to 3 listings on the car market at a time. Please remove some to add some more."); }
$q=$db->query("SELECT iv.*,i.* FROM cars_playercars iv LEFT JOIN cars_types i ON iv.cpcCAR=i.carID WHERE cpcID={$_GET['ID']} and cpcPLAYER=$userid");
if($db->num_rows($q)==0)
{
print "Invalid Car ID";
}
else
{
$r=$db->fetch_row($q);$db->query("INSERT INTO carmarket VALUES ('','{$r['cpcCAR']}',{$r['cpcACCLV']}, {$r['cpcHANLV']}, {$r['cpcSPDLV']}, {$r['cpcSHDLV']}, $userid,{$_GET['price']})");$db->query("DELETE FROM cars_playercars WHERE cpcID={$_GET['ID']}");
$db->query("INSERT INTO imarketaddlogs VALUES ( '', {$r['cpcCAR']}, {$_GET['price']}, {$r['cpcID']}, $userid, unix_timestamp(), '{$ir['username']} added a {$r['carNAME']} to the car market for \${$_GET['price']}')");

print "{$r['carNAME']} added to market.";

}
}
else
{
$q=$db->query("SELECT iv.*,i.* FROM cars_playercars iv LEFT JOIN cars_types i ON iv.cpcCAR=i.carID WHERE cpcID={$_GET['ID']} and cpcPLAYER=$userid");
if($db->num_rows($q)==0)
{
print "Invalid Car ID";
}
else
{
$r=$db->fetch_row($q);
print "<h3>Adding a car to the car market</h3><br>

You can only put up to 3 listings on the car market at a time. Please remove some to add some more if you have 3 already.
<form action='carmadd.php' method='get'>
<input type='hidden' name='ID' value='{$_GET['ID']}' />
<br>
<br>
Price: \$<input type='text' STYLE='color: black;  background-color: white;' name='price' value='0' /><br>
<br>

<input type='submit' STYLE='color: black;  background-color: white;' value='Add' /></form><br><a href='garage.php'>Back To Garage</a>";


}
}
$h->endpage();
?>
