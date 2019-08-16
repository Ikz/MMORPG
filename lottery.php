<?php
include("./globals.php");



if($ir['user_level'] == 1)
{
print "



<h1>MOD OFFLINE</h1><br><h3><font color='FF7E00'><b>Lottery Mod</b></font> is currently offline.<br>Problem: Fails to give out money.<br>Message <a href='viewuser.php?u=156'><b>Ikz [156]</b></a> if you have any questions.</h3>";
$h->endpage();exit;

}

if($ir['user_level'] > 1);
$cost = 100;
if(!$_GET['a'])
{
$sql = sprintf("SELECT * FROM `lottery`");
$query = $db->query($sql);
$row = $db->fetch_row($query);
       echo sprintf("' 
<font color = red size = 5>[b]
                         Welcome to the National Jackpot. You currently have %d 
                         tickets and are able to purchase upto %d tickets per week.  
                         Each ticket costs \$%d. The jackpot is \$%u", $ir['lottery'], $tick, 
                         $cost,$row['jackpot']);
 
       echo'             
 
                        <form action = "lottery.php?a=buy" method = "post">
                        <input type = "submit" value = "Buy a Lottery Ticket">
                        </form>';
}
if($_GET['a'] == buy)
{
if($ir['money'] < 100)
{
die("You don't have enough cash");
}$tick = 100;
$tickq = $db->query("SELECT * FROM `lottery` WHERE `userid` = '$ir['userid']'");
if($db->num_rows($tickq) > $tick)
{
die('You can only purchase ' .$tick. ' tickets maximum');
}
 
      echo' 
 
                       You purchased a lottery ticket';
 
$sql = sprintf("UPDATE `users` SET `lottery` = `lottery` + %d, `money` = `money` - %d WHERE `userid` = (%u)",
1,
$cost,
$userid);
 
$db->query($sql);
 
$sql1 = sprintf("INSERT INTO `lottery` (id,userid, amount) VALUES (%d,%u, %d)",
'',$userid, $cost);
 
$db->query($sql1);
 
$sql2 = sprintf("UPDATE `lottery` SET `jackpot` = `jackpot` + %d",
$cost);
 
$db->query($sql2);
}
 
$h->endpage();
?>