<?php
 include "globals.php";
print "<h3>Car Yard</h3><br />";
$car=abs((int) $_GET['car']);
if($car)
{
$q=$db->query("SELECT * FROM cars_types WHERE carID={$car} and carBUYABLE=1");
if(mysql_num_rows($q) == 0)
{
die("Invalid Car");
}
$r=$db->fetch_row($q);
if($r['carCOST'] > $ir['money'])
{
die("You do not have enough money to buy this car.");
}$db->query("UPDATE users SET money=money-{$r['carCOST']},cars_owned=cars_owned+1 where userid=$userid");
$db->query("INSERT INTO cars_playercars VALUES('', $userid, $car, 1, 1, 1, 1)");
print "You bought a {$r['carNAME']}!<br />
&gt; <a href='caryard.php'>Back to Car Yard</a><br />
&gt; <a href='garage.php'>Go To Your Garage</a><br />";
}
else
{
print "<table width=95% border=6 class=table> <tr style='background:#cc9966'> <th>Image</th><th>Name</th><th>Description</th><th>Base Acceleration</th><th>Base Handling</th><th>Base Speed</th><th>Base Shield</th><th>Price</th><th>Buy</th></tr>";
$q=$db->query("SELECT * FROM cars_types WHERE carBUYABLE=1 ORDER BY carCOST", $c);
while($r=$db->fetch_row($q))
{
$price='$'.number_format($r['carCOST']);
print "<tr><td><a href='{$r['carPIC']}' target='_blank'><img src='{$r['carPIC']}' width='100' height='70' alt='{$r['carNAME']}' title='{$r['carNAME']}'></a></td><td>{$r['carNAME']}</td> <td>{$r['carDESC']}</td> <td>{$r['carACC']}</td> <td>{$r['carHAN']}</td><td>{$r['carSPD']}</td> <td>{$r['carSHD']}</td> <td>$price</td> <td><a href='caryard.php?car={$r['carID']}'>Buy</a></td> </tr>";
}
print "</table><br><a href='garage.php'>Back To Garage</a>";
}
$h->endpage();
?>