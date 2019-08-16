<?php 

include "globals.php";
global $h,$ir;
echo "<h2>Welcome to Hourly Rewards</h3>";
echo "<p>Once an hour you may come here to collect a reward</p>"; 
if($_POST['valid']=='yes' && isset($_POST['bet'] )){if($ir['hourlyReward'] > 0){echo "You cannot refresh this page.";
$h->endpage();
exit;
}
else
{
$hourlyMoney = $ir['level']*rand(100,300);
$hourlyPoints = rand(1,10); 
echo "You have earned $".$hourlyMoney." + ".$hourlyPoints." Crystals for playing this hour.";
$db->query("UPDATE users SET main = main +{$hourlyMoney},second = second+{$hourlyPoints},hourlyReward = 60 WHERE userid={$ir['userid']}");
$_POST['valid']='no';
$db->query("UPDATE users SET crystals=crystals+$hourlyPoints WHERE userid=$userid");
$db->query("UPDATE users SET money=money+$hourlyMoney WHERE userid=$userid");
$h->endpage();
exit;}
}
else
{
if($ir['hourlyReward']==0){echo "<br><p><form method='post'><input type='hidden' name='valid' value='yes' /><input type='submit' STYLE='color: black;  background-color: white;' name='bet' value='Claim Your Reward' class='btn'></form></p>";}
else{$wait= $ir['hourlyReward'];
echo"You must wait <font color=red>".$wait."</font> minutes before you can claim your next reward.";}}?>
