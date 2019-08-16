<?php 

include "globals.php";
print "

<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'> Crystal Locker</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>

";
if($ir['crystalbank']>-1)
{
switch($_GET['action'])
{
case "deposit":
deposit();
break;

case "withdraw":
withdraw();
break;

default:
index();
break;
}

}
else
{
if(isset($_GET['buy']))
{
if($ir['crystals']>9)
{
print "Congratulations, you bought a crystal locker for 10 crystals!<br />
<a href='crystallocker.php'>Start using my locker</a>";
$db->query("UPDATE users SET crystals=crystals-10,crystalbank=0 WHERE userid=$userid");
}
else
{
print "You do not have enough crystals to buy a locker.
<a href='explore.php'>Back to town...</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div> ";
}
}
else
{
print "Buy a locker today, just 10 crystals!<br />
<a href='crystallocker.php?buy'>&gt; Yes, sign me up!</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div> ";
}
}
function index()
{
global $db,$ir,$c,$userid,$h;
$interest= $ir['crystalbank']/100*1 ;
$interests=crystal_formatter($interest);
$balance=crystal_formatter($ir['crystalbank']);
print " \n<b>You currently have $balance crystals in your Crystal Locker.</b><br><br />
<table width=100% class = table border=1> <tr><th>Deposit</th><th>Withdraw</th></tr><tr>
<td>
It will cost you 1% of the crystals you deposit. The maximum fee is 1,000 crystals.<form action='crystallocker.php?action=deposit' method='post'>
Amount: <input type='text' STYLE='color: black;  background-color: white;' name='deposit' value='{$ir['crystals']}' /><br />
<input type='submit' STYLE='color: black;  background-color: white;' value='Deposit' /></form></td> <td>
<b>Withdraw</b><br />
It will cost you nothing for the withdrawal of crystals. You can withdraw as many times as you want.<form action='crystallocker.php?action=withdraw' method='post'>
Amount: <input type='text' STYLE='color: black;  background-color: white;' name='withdraw' value='{$ir['crystalbank']}' /><br />
<input type='submit' STYLE='color: black;  background-color: white;' value='Withdraw' /></form></td> </tr> </table></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div> ";
}
function deposit()
{
global $db,$ir,$c,$userid,$h;
$_POST['deposit']=abs((int) $_POST['deposit']);
if($_POST['deposit'] > $ir['crystals'])
{
print "You do not have enough crystals to deposit this amount.</div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div></a>";
}
else
{
$fee=ceil($_POST['deposit']*1/100);
$fees= crystal_formatter($fee); 
if($fee > 1000) { $fee=1000; }
$gain=$_POST['deposit']-$fee;
$gains= crystal_formatter($gain);
$balance= $ir['crystalbank'] + $gain;
$balances= crystal_formatter($balance);    
$db->query("UPDATE users SET crystalbank=crystalbank+$gain, crystals=crystals-{$_POST['deposit']} where userid=$userid");
print "You hand over {$_POST['deposit']} crystals to be deposited, <br />
after the fee is taken ($fees crystals), $gains crystals are added to your account. <br />
<b>You now have $balances crystals in your Crystal Locker.</b><br />
<a href='crystallocker.php'> Back</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
}
}
function withdraw()
{
global 
$db,$ir,$c,$userid,$h;
$_POST['withdraw']=abs((int) $_POST['withdraw']);
if($_POST['withdraw'] > $ir['crystalbank'])

{
print "You do not have enough banked crystals to withdraw this amount.</div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div></a>";
}

else {
    

$withdraw=$_POST['withdraw'];
$withdraws=crystal_formatter($withdraw); 

$balance= $ir['crystalbank'] - $_POST['withdraw'];
$balances= crystal_formatter($balance);

$db->query("UPDATE users SET crystalbank=crystalbank-$withdraw, crystals=crystals+$withdraw where userid=$userid");
print "You ask to withdraw $withdraws crystals, <br />
The teller hands $withdraws crystals to you. <br />
<b>You now have $balances crystals in your Crystal Locker.</b><br />
<a href='crystallocker.php'> Back</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div></a>";

}
}
$h->endpage();
?>
