<?php 

error_reporting(0);

class headers {
function startheaders() {  
global $ir, $set;
global $_CONFIG;
define("MONO_ON", 1);
$db=new database;
$db->configure($_CONFIG['hostname'],
$_CONFIG['username'],
$_CONFIG['password'],
$_CONFIG['database'],
$_CONFIG['persistent']);
$db->connect();
$c=$db->connection_id;
$set=array();
$settq=$db->query("SELECT * FROM settings");
while($r=$db->fetch_row($settq))
{
$set[$r['conf_name']]=$r['conf_value'];
}
$q=$db->query("SELECT userid FROM users");
$membs=$db->num_rows($q);
$q=$db->query("SELECT userid FROM users WHERE bankmoney>-1");
$banks=$db->num_rows($q);
$q=$db->query("SELECT userid FROM users WHERE gender='Male'");
$male=$db->num_rows($q);
$q=$db->query("SELECT userid FROM users WHERE gender='Female'");
$fem=$db->num_rows($q);
$money=money_formatter($ir['money']);
$crystals=money_formatter($ir['crystals'],'');
$cn=0;
// Users Online , Counts Users Online In Last 30 minutes                                                                           
$q=$db->query("SELECT * FROM users WHERE laston>unix_timestamp()-30*60 ORDER BY laston DESC");
$online=$db->num_rows($q);
$ec=$ir['new_events'];
$mc=$ir['new_mail'];

$ids_checkpost=urldecode($_SERVER['QUERY_STRING']);
if(eregi("[\'|'/'\''<'>'*'~'`']",$ids_checkpost) || strstr($ids_checkpost,'union') || strstr($ids_checkpost,'java') || strstr($ids_checkpost,'script') || strstr($ids_checkpost,'substring(') || strstr($ids_checkpost,'ord()')){

$passed=0;
echo "<center> <font color=red> Hack attempt <br/>WARNING!!! <br/>

Malicious Code Detected! The staff has been notified.</font></center>"; 
event_add(1,"  <a href='viewuser.php?u={$ir['userid']}'>  <font color=red> ".$ir['username']."</font> </a>  <b> Tried to use [".$_SERVER['SCRIPT_NAME']."{$ids_checkpost}].. ",$c); 
$h->endpage();
exit;

} 


echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{$set['game_name']}</title>
<meta name="keywords" content="RPG, Online Games, Online Mafia Game, Stock, Market, Ikz, Diamond, Designs" />
<meta name="description" content="{$set['game_name']}" />
<meta name="author" content="Diamond Designs" />
<meta name="copyright" content="Copyright GTA Mobster. Designed & Powered By: Diamond Designs" />
<link rel="SHORTCUT ICON" href="favicon.ico" />
<script src="js/jquery-1.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/styledd.css" type="text/css" />
<link rel="stylesheet" href="css/styleold.css" type="text/css" />
<script src="http://static.miniclipcdn.com/js/game-embed.js"></script>

<script type="text/javascript" src="js/header.js"></script>
<style type="text/css">
.boston a{
background:url(images/boston.jpg) no-repeat;
}

.boston a:hover{
background:url(images/boston_hover.jpg) no-repeat;
}
</style>
<!--<script type="text/javascript">
$(document).ready(function(){
$.get("userstatajax.php",function(res){
if(res)
{
var resarray = res.split('||||||');
$('.profile_mid').html(resarray[0]);
$('#points_money').html(resarray[1]);
}
});
});
</script>-->
</head>
<body id="sub" class="yui-skin-sam">

<div id="pagecontainer">
<!-- Header Part Starts -->

<div class="headerpart">

<div class="onlinegame"></div>
<div class="toplist">

</div>
</div>

<!-- //Header Part End -->  

<!-- Inner Page Top Starts -->

<div class="innertopbg">
<div class="toprow1">
<div class="toprow1_col1">
<div class="needbtn"></div>        
<div class="top_leftbtn">
<div class="leftbtn1"> 


</div>
<div class="leftbtn2"> 

</div>

</div>
</div>

<div class="toprow1_col2">

<div class="tot_txt"><a href="userlist.php" style="color:#fff;">Total Mobsters:&nbsp;&nbsp;<span>$membs</span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="usersonline.php" style="color:#fff;">Online Now:&nbsp;&nbsp;<span>$online</span></a></div>
<div class="messagepart">
<div class="message_txt"><a href="mailbox.php" style="color:#fff;"><span>[$mc]</span> Messages</a></div>

<div class="event_txt"><a href="events.php" style="color:#fff;"><span>[$ec]</span> Events</a></div> </div>  <br/>
<div class="messagepart" id="points_money">
<div class="point_txt">Crystals:&nbsp;<span> $crystals </span><br/></div>
<div class="gold_txt">Money:&nbsp;<span>$money</span></div>

</div>              
</div>
</div>

<!-- Menu Part Starts -->
<div class="toprow2">
<div><img src="images/menu_left.png" alt="" /></div>
<div class="menu_md">
<ul>
<li class="ihome"><a href="index.php"></a></li>

<li class="gym"><a href="donate.php">&nbsp;</a></li>
<li class="news"><a href="voting.php">&nbsp;</a></li>
<li class="forum"><a href="forums.php">&nbsp;</a></li>
<li class="explore"><a href="explore.php">&nbsp;</a></li>
<li class="protect"><a href="bodyguard.php">&nbsp;</a></li>
<li class="logout"><a href="logout.php">&nbsp;</a></li>                            
</ul>                        
</div>
<div><img src="images/menu_right.png" alt="" /></div>
</div>            
<!-- //Menu Part End -->

</div>
<!-- //Inner Page Top End -->
<div class="toprow2">
<div><img src="images/menu_left.png" alt="" /></div>
<div class="menu_md">
<center><font color='#FFFFFF' size="3">Welcome to <b>GTA Mobster</b></font></center>
<marquee><font size='3'><a href='newspaper.php' style='color:#fff;'><b>Latest News:</b></a> {$set['news']}</font></marquee>

</font></center></div><div><img src="images/menu_right.png" alt="" /></div>
</div>  </div> 
<br/> <br/> <br/><br/>    

<div class="gymbg">
<div id="centercontainer">

<div id="centermaincontainer">

<!-- Center Part Starts -->
                    <div class="icenterpart"><div class="icolumn1">



EOF;
}
function userdata($ir,$lv,$fm,$cm,$dosessh=1)
{
global $db,$c,$userid, $set;
$IP = $_SERVER['REMOTE_ADDR'];
$IP=addslashes($IP);
$IP=mysql_real_escape_string($IP);
$IP=strip_tags($IP);
$db->query("UPDATE users SET laston=unix_timestamp(),lastip='$IP' WHERE userid=$userid");
$_GET['ID'] = abs(@intval($_GET['ID']));
$_GET['reply'] = abs(@intval($_GET['reply']));


if(!$ir['email'])
{
global $domain;
die ("<body>Your account may be broken. Please mail admin@gta-mobster.com stating your username and player ID.");
}
if($dosessh && ($_SESSION['attacking'] || $ir['attacking']))
{
print "<CENTER><P><b><font color=red>You lost 1 EXP for running from the fight.</font></b></P></CENTER> <br/><br/>";
$db->query("UPDATE users SET exp=99,attacking=0 WHERE userid=$userid");
$_SESSION['attacking']=0;
}
$enperc=(int) ($ir['energy']/$ir['maxenergy']*100);
$wiperc=(int) ($ir['will']/$ir['maxwill']*100);
$experc=(int) ( $ir['exp']/$ir['exp_needed']*100);
$brperc=(int) ($ir['brave']/$ir['maxbrave']*100);
$hpperc=(int) ($ir['hp']/$ir['maxhp']*100);
$enopp=100-$enperc;
$wiopp=100-$wiperc;
$exopp=100-$experc;
$bropp=100-$brperc;
$hpopp=100-$hpperc;
$d="";
$u=$ir['username'];
if($ir['donatordays']) { $u = "<font color=aqua>{$ir['username']}</font>";$d="<img src='donator.gif' alt='Donator: {$ir['donatordays']} Days Left' title='Donator: {$ir['donatordays']} Days Left' />"; }

$gn=""; 
global $staffpage;

$bgcolor = 'FFFFFF';     

include "travellingglobals.php";

if($ir['fedjail'])
{
$q=$db->query("SELECT * FROM fedjail WHERE fed_userid=$userid");
$r=$db->fetch_row($q);
die(" <br /><br /><br /><br /><br /> <CENTER><P> <b><font color=red size=+1>You have been put in the {$set['game_name']} Federal Jail for {$r['fed_days']} day(s).<br /> <br />
Reason: {$r['fed_reason']}</font></b> </P></CENTER> </body></html>"); 
}



if(file_exists('ipbans/'.$IP))
{
die("<br /><br /><br /><br /><br /><CENTER><P><b><font color=red size=+1>Your IP has been banned from {$set['game_name']}, there is no way around this.</font></b></P></CENTER></body></html>");
}

print <<<OUT

<!-- Begin Main Content -->     





<table width="950" border="0" align="center" cellpadding="0" cellspacing="0">    
<tr valign="top">
<td width="181"><table width="181" border="0" cellspacing="0" cellpadding="0">
<tr><td class="text2 height:40px;"><table width="181" border="0" cellspacing="0" cellpadding="0">
</table></table>

<!-- Side Panel -->



<div class="profilepart" id="profilepart">
<div class="profiletxt"><p><img src="images/profile_txt.png" alt="" /></p></div>
<div class="profile_mid">
<div class="profile_con">
<div class="gunimg"><img alt='User Pic' src='{$ir['display_pic']} ' width='75' height='75' /></div>
<div class="guntxt">


<style="color=#00ffff"><b><a href='viewuser.php?u={$ir['userid']}'> $gn{$u} </a> [{$ir['userid']}]$d</b><br/>
<font color=#000000><b>Money:</b></font> {$fm} <a href="bank.php" style="color:#0000FF;">[Bank]</a><br/>
<font color=#000000><b>Level:</b></font> {$ir['level']} <br/>
<font color=#000000><b>Crystals:</b></font> {$cm} <a href="crystallocker.php" style="color:#0000FF;">[Locker]<a/><br/>
<font color=#000000><b>Credits:</b></font> <font color=#FFFFFF>{$ir['credits']}</font><br/>
</div>



</div>                  
<div class="energypart">


<font color=#000000><b>Energy:</b></font> <font color=#FFFFFF>{$enperc}% <a href='crystaltemple.php?spend=refill'><font color='green'>[Refill Energy]</font></a><br />
<img src=bar_left.gif height=13><img src=bargreen.gif width=$enperc height=13><img src=barred.gif width=$enopp height=13><img src=bar_fil_end.gif height=13><br />
<font color=#000000><b>Will:</b></font> <font color=#FFFFFF>{$wiperc}% </font><br />
<img src=bar_left.gif height=13><img src=barblue.gif width=$wiperc height=13><img src=barred.gif width=$wiopp height=13><img src=bar_fil_end.gif height=13><br />
<font color=#000000><b>Brave:</b></font> <font color=#FFFFFF>{$ir['brave']}/{$ir['maxbrave']} </font><br />
<img src=bar_left_purp.gif height=13><img src=barpurple.gif width=$brperc height=13><img src=barred.gif width=$bropp height=13><img src=bar_fil_end.gif height=13><br />
<font color=#000000><b>EXP:</b></font> <font color=#FFFFFF>{$experc}%</font><br />
<img src=bar_left.gif height=13><img src=bargreen.gif width=$experc height=13><img src=barred.gif width=$exopp height=13><img src=bar_fil_end.gif height=13><br />
<font color=#000000><b>Health:</b></font> <font color=#FFFFFF>{$hpperc}% </font><br />
<img src=bar_left.gif height=13><img src=bargreen.gif width=$hpperc height=13><img src=barred.gif width=$hpopp height=13><img src=bar_fil_end.gif height=13><br />

</div>
</div><div><img src="images/profile_btm.png" alt="" /></div>    
</div>
<!-- Links -->
OUT;
}
function menuarea()
{
include "mainmenu.php";
global $ir,$c;
$bgcolor = '000000';
print '</td>
<td width="2" class="linegrad" bgcolor="#'.$bgcolor.'">&nbsp;</td><td width="80%"  bgcolor="#'.$bgcolor.'" valign="top"><center>';

if($ir['hospital'])
{
print "<font color='red'><b>NOTE:</b></font> You are currently in hospital for {$ir['hospital']} minutes.<br/><br />";
}
if($ir['jail'])
{
print "<font color='red'><b>NOTE:</b></font> You are currently in jail for {$ir['jail']} minutes.<br/><br />";
}

if($ir['traveltime'] > 0)
{
print "<font color = 'red' /><b>Travelling for <b>{$ir['traveltime']} minutes</b>.</font><br /><br />";
} 

if($ir['bguard'] >0)
{
print "<font color='green'><b>NOTE:</b></font> Your Bodyguard is protecting you for {$ir['bguard']} more minutes.<br/><br/>";
}


//-- Finding items query
$cityname = $db->fetch_single($db->query("SELECT cityname FROM cities WHERE cityid = ".$ir['location']));
$fia=(int) rand(1,1000000);
$fib=(int) rand(1,1000000);
if($fia == $fib)
{
$iq=$db->query("SELECT * FROM items WHERE itmbuyable=1 ORDER BY rand() LIMIT 1",$c);
$r=$db->fetch_row($iq);
$item=$r['itmid'];
$userid=$ir['userid'];
$db->query("INSERT INTO inventory VALUES ('', $item, $userid, 1)",$c);
event_add($userid,"While passing through $cityname, you found a {$r['itmname']}.<br> Congratulations!");
}

if($ir['hourlyReward']==0)
{
print "<font color='green'><b>NOTE:</b></font> You can claim your hourly reward. <a href='hourly.php'>Click here to claim it!</a><br/><br/>";}

if($ir['offline'] > 0)
{
$q=$db->query("SELECT offline, online, reason, hour, minute, timenow FROM maintenance where offline=Offline AND NOW()");
$t=$db->fetch_row($q);
echo'<h1>Currently Closed for Maintenance</h1>';
echo'<br><font size=3>'.$t['reason'].'</font><br><br> Site will be back online in '.$t['hour'].' Hour(s) And '.$t['minute'].' Minute(s)<br><br>Time from when Site was Placed in Maintenance mode at <b>'.$t[timenow].'</b>';
echo "<br><br>Time Now: <b>".date('g:i:s a')."</b>";
die();
}

}
function smenuarea()
{
include "smenu.php";
global $ir,$c;
$bgcolor = '000000';
print '</td><td width="2" class="linegrad" bgcolor="#'.$bgcolor.'"> &nbsp; </td><td width="80%"  bgcolor="#'.$bgcolor.'" valign="top"><center>';
}
function endpage()
{
global $db;

//  Do Not Remove Designed & Powered By Diamond Designs without permission.

 // However, if you would like to use the script without the powered by links you may do so by purchasing a Copyright removal license for a very low fee.  

include "footer.php";


}    
} 
?>
