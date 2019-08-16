<?php  

global $db,$c,$ir, $set;$cityname = $db->fetch_single($db->query("SELECT cityname FROM cities WHERE cityid = ".$ir['location']));
$hc=$set['hospital_count'];
$jc=$set['jail_count'];
$ec=$ir['new_events'];
$mc=$ir['new_mail'];
if($ir['hospital'])
{
print "

<div class='navipart'>
<div class='navitop'><p>
<img src='images/navi_txt.png' alt='' />
</p></div>

<div class='navi_mid'><ul>



<li> <a class='link1' href='index.php'>Home</a></li>
<li> <a class='link1' href='shops.php'>Medical Shop</a></li>
<li> <a class='link1' href='hospital.php'>Hospital [$hc]</a></li> 
<li> <a class='link1' href='inventory.php'>Inventory</a></li>";
}
elseif($ir['jail'])
{

print "

<div class='navipart'>
<div class='navitop'><p>
<img src='images/navi_txt.png' alt='' />
</p></div>

<div class='navi_mid'><ul>


<li><a class='link1' href='jail.php'>Jail [$jc]</a></li><li>
<a class='link1' href='inventory.php'>Inventory</a></li>";
}
else
{
print "


<div class='navipart'>
<div class='navitop'><p>
<img src='images/navi_txt.png' alt='' />
</p></div>

<div class='navi_mid'><ul>


<li><a class='link1' href='index.php'>Home</a></li><li>
<a class='link1' href='inventory.php'>Inventory</a></li>";
}
if($ec > 0) { print "<li> <a class='link1' href='events.php'><font color='FF7E00'>Events [$ec]</font></a></li>"; }
else { print "<li> <a class='link1' href='events.php'>Events [0]</a></li>"; }
if($mc > 0) { print "<li> <a class='link1' href='mailbox.php'><font color='FF7E00'>Mailbox [$mc]</font></a></li>"; }
else { print "<li> <a class='link1' href='mailbox.php'>Mailbox [0]</a></li>"; }
if($ir['new_announcements'])
{
print "<li> <a class='link1' href='announcements.php'><font color='FF7E00'>Announcements [{$ir['new_announcements']}]</font></a></li>";
}
else
{
print "<li> <a class='link1' href='announcements.php'>Announcements [0]</a></li>";
}


if($ir['jail'] and !$ir['hospital'])
{
print "<li> <a class='link1' href='gym.php'>Jail Gym</a></li>
<li> <a class='link1' href='hospital.php'>Hospital [$hc]</a></li>";
}
else if (!$ir['hospital'])
{
print "<li> <a class='link1' href='explore.php'>$cityname</a></li>
<li> <a class='link1' href='gym.php'>Gym</a></li>
<li> <a class='link1' href='criminal.php'>Crimes</a></li>
<li> <a class='link1' href='job.php'>Your Job</a></li>

<li> <a class='link1' href='realstock.php'>Stock Market</a></li>
<li> <a class='link1' href='education.php'>Local School</a></li>
<li> <a class='link1' href='hospital.php'>Hospital [$hc]</a></li>
<li> <a class='link1' href='jail.php'>Jail [$jc]</a></li>";
}
else
{
print "<li> <a class='link1' href='jail.php'>Jail [$jc]</a></li>";
}
print "<li> <a class='link1' href='forums.php'>Forums</a></li>";

print "
<li> <a class='link1' href='newspaper.php'>Newspaper</a></li>
<li> <a class='link1' href='search.php'>Search</a></li>";


if($ir['jail'] )
{
print "

</div>
<div><img src='images/navi_btm.png' alt='' /></div>
</div>  

";
}


if(!$ir['jail'] )
{
print "<li> <a class='link1' href='yourgang.php'>Your Gang</a></li>


<li> <a class='link1' href='garage.php'>Your Garage</a></li></div><div><img src='images/navi_btm.png' alt='' /></div>
</div>  


";
}


if($ir['user_level'] > 1)
{
print "


<div class='navipart'>
<div class='navitop'><p>
<img src='images/staff_links.png' alt='' /> 
</p></div>

<div class='navi_mid'><ul>
<li> <a class='link1' href='staff.php'>Staff Panel</a></li>
</div>
<div><img src='images/navi_btm.png' alt='' /></div>
</div>    


";

}


print "

<div class='navipart'>
<div class='navitop'><p>
<img src='images/staff_online.png' alt='' />
</p></div>
<div class='navi_mid'> 
";
$q=$db->query("SELECT * FROM users WHERE laston>(unix_timestamp()-30*60) AND user_level>1 ORDER BY userid ASC");
while($r=$db->fetch_row($q))
{
$la=time()-$r['laston'];
$unit="secs";
if($la >= 60)
{
$la=(int) ($la/60);
$unit="mins";
}
if($la >= 60)
{
$la=(int) ($la/60);
$unit="hours";
if($la >= 24)
{
$la=(int) ($la/24);
$unit="days";
}
}
print "<li><a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [$la $unit]</li>";
}

print"</div>

<div><img src='images/navi_btm.png' alt='' /></div>
</div>  ";

if($ir['donatordays'])
{
print "


<div class='navipart'>
<div class='navitop'><p>
<img src='images/donators_only.png' alt='' />
</p></div>
<div class='navi_mid'><ul> 

<li> <a class='link1' href='donatorbank.php'>Donator Bank</a></li><li> <a class='link1' href='lockers.php'>Daily Lockers</a></li>



<li> <a class='link1' href='daily_vip_bonus.php'>Daily Donator Bonus</a></li><li> <a class='link1' href='friendslist.php'>Friends List</a></li>
<li> <a class='link1' href='blacklist.php'>Black List</a></li>

</div>

<div><img src='images/navi_btm.png' alt='' /></div>
</div>  


";
}
print "


<div class='navipart'>
<div class='navitop'><p>
<img src='images/other_links.png' alt='' />
</p></div>
<div class='navi_mid'><ul> 


<li> <a class='link1' href='preferences.php'>Preferences</a></li>
<li> <a class='link1' href='preport.php'>Player Report</a></li>
<li>  <a class='link1' href='helptutorial.php'>Help Tutorial</a></li>
<li> <a class='link1' href='gamerules.php'>Game Rules</a></li>
<li> <a class='link1' href='viewuser.php?u={$ir['userid']}'>My Profile</a></li>
<li> <a class='link1' href='logout.php'>Logout</a></li>
<li><a class='link1' href='http://www.diamond-design.co.uk'>Diamond Designs</a></li>


</div>

<div><img src='images/navi_btm.png' alt='' /></div>
</div>  


" ; 



?>
