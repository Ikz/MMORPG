<?php    

include "globals.php";
$tresder=(int) rand(100,999);
global $db, $ir, $userid, $h, $db;
$cityname = $db->fetch_single($db->query("SELECT cityname FROM cities WHERE cityid = ".$ir['location']));
$citycount = $db->fetch_single($db->query("SELECT COUNT(*) FROM users WHERE location = ".$ir['location'])); 
if($ir['jail'] or $ir['hospital']) { print "This page cannot be accessed while in jail or hospital.";

$h->endpage(); 
exit; 
}
print "


<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h3 style='padding-top:10px;'>You are currently exploring <font color='FF7E00'>$cityname!</font></h3><h5>There are $citycount people in $cityname</h5></div>
<div><img src='images/info_right.jpg' alt='' /></div>

</div>
<div class='generalinfo_simple'>";
if($ir['location']==1) { print "<center><img src=images/denver.jpg width='500' height='300' alt='City' title='$cityname' /></center><br>";}
if($ir['location']==2) { print "<center><img src=images/spain.jpg width='500' height='300' alt='City' title='$cityname' /></center><br>";}
if($ir['location']==3) { print "<center><img src=images/lasvegas.jpg width='500' height='350' alt='City' title='$cityname' /></center><br>";}
if($ir['location']==4) { print "<center><img src=images/dubai.jpg width='500' height='300' alt='City' title='$cityname' /></center><br>";}
if($ir['location']==5) { print "<center><img src=images/argentina.jpg width='500' height='350' alt='City' title='$cityname' /></center><br>";}
if($ir['location']==6) { print "<center><img src=images/bahamas.png width='500' height='300' alt='City' title='$cityname' /></center><br>";}
if($ir['location']==7) { print "<center><img src=images/usa.jpg width='500' height='324' alt='City' title='$cityname' /></center><br>";}
print "<table class='table' width='85%'>

<tr valign='middle'>
<font color='000000'><th width='33%'><img src='images/basket.png' alt='shop' /> Shops</th>
<th width='34%'><img src='images/building.png' alt='bis' /> Businesses</th>
<th width='33%'><img src='images/coins.png' alt='casino' /> Casino</th></font>
</tr>
<tr style='height: 100%;'>
<td valign='top'>
<a href='shops.php'>City Shops</a><br />
<a href='playershops.php'>Player Shops</a><br /> 
<a href='itemmarket.php'>Item Market</a><br />
<a href='cmarket.php'>Crystal Market</a><br />
<a href='carmarket.php'>Car Market</a><br />
</td>
<td valign='top'>
<a href='travel.php'>Travel Agency</a><br />
<a href='estate.php'>Real Estate</a><br />
<a href='bank.php'>City Bank</a><br>
<a href='crystallocker.php'>Crystal Locker</a><br>
<a href='caryard.php'>Car Yard</b></a><br />";
if($ir['level'] >= 5)
{
print "
<a href='cyberbank.php'>Cyber Bank</a><br />";
}
print "</td>
<td valign='top'>  
<a href='slotsmachine.php?tresde=$tresder'>Slots Machine</a><br />
<a href='magicslots.php'>Magic Slots</a><br />
<a href='roulette.php?tresde=$tresder'>Roulette</a><br />
<a href='lucky.php'>Lucky Boxes</a><br>
<a href='pick3.php'>Pick Three</a><br>
<a href='C5050.php'>50/50</a>";
if($ir['level'] >= 5)
{
print "<br />
<a href='horsing.php'>Horse Racing</a><br />";
}
print "</tr>

<tr>
<th width='33%'><img src='images/door.png' alt='life' /> Your Life</th>
<th width='34%'><img src='images/sport_soccer.png' alt='act' /> Mysterious</th>
<th width='33%'><img src='images/building_link.png' alt='head' /> Headquarters</th>
</tr>

<tr style='height: 100%;'>
<td valign='top'>
<a href='viewuser.php?u={$ir['userid']}'>Your Profile</a><br />
<a href='garage.php'>Your Garage</a><br />";

$checkforshop=$db->query("select * from usershops where userid=$userid");
if(mysql_num_rows($checkforshop)!=0)
{
print"<a href='myshop.php'>Your Shop</a> <br/>";
}
print"
<a href='inventory.php'>Inventory</a><br />
<a href='mailbox.php'>Mailbox</a><br />
<a href='polling.php'>Polls</a><br />
<a href='forums.php'>Forums</a><br />
<a href='gamestation.php'>Game Station</a><br />
<a href='marriage.php'>Marriage</a><br>
<a href='propose.php'>Propose</a>
</td>
<td valign='top'>
<a href='battle_ladder.php'>Battle Ladder</a><br /> 
<a href='battletent.php'>Battle Tent</a><br />
<a href='hitmanagency.php'>Hitman Agency</a><br />
<a href='whorehouse.php'>Brothel</a><br />    
<a href='crystaltemple.php'>Crystal Temple</a><br />
<a href='streets.php'>Search Streets</a><br />
<a href='hourly.php'>Hourly Reward</a><br />
<a href='safehouse.php'>Safe House</a><br />
</td>
<td valign='top'>
<a href='stats.php'>Game Stats</a><br />
<a href='stafflist.php'>{$set['game_name']} Staff</a><br />
<a href='halloffame.php'>Hall of Fame</a><br />
<a href='usersonline.php'>Users Online</a><br />
<a href='userlist.php'>User List</a><br />
<a href='preport.php'>Player Report</a><br />
<a href='fedjail.php'>Federal Jail</a><br />
<a href='attacklist.php'>Player Attack List</a><br />
<a href='cityusers.php'>Players In Your City</a><br />
</tr>

<tr>
<th width='33%'><img src='images/information.png' alt='info' /> Information</th>
<th width='34%'><img src='donator.gif' alt='act' /> Donators Only</th>
<th width='33%'><img src='images/user_suit.png' alt='gang' /> Gang</th>
</tr>

<tr style='height: 100%;'>
<td valign='top'>
<a href='helptutorial.php'>Tutorial</a><br />
<a href='gamerules.php'>Rules</a><br />
<a href='support.php'>Support Desk</a><br />
<a href='http://www.diamond-design.co.uk'>Diamond Designs</a><br />
</td>
<td valign='top'>
<a href='donatorbank.php'>Donator Bank</a><br />
<a href='lockers.php'>Daily Lockers</a><br />
<a href='daily_vip_bonus.php'>Daily Donator Bonus</a><br />
<a href='friendslist.php'>Friends List</a><br />
<a href='blacklist.php'>Black List</a><br />
</td>
<td valign='top'>
<a href='gangs.php'>Gang List</a><br />
<a href='gangs.php?action=gang_wars'>Gang Wars</a><br />
<a href='yourgang.php'>Your Gang</a><br />";

print "
</td></tr></table> </div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>


This is your referal link: http://{$domain}/signup.php?ref=$userid <br><br />
Every signup from this link earns you five valuable crystals!";
$h->endpage();
?>

