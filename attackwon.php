<?php

$atkpage=1;
include "globals.php";
$_GET['ID']=abs((int) $_GET['ID']);
$_SESSION['attacking']=0;
$ir['attacking']=0;
$db->query("UPDATE users SET attacking=0 WHERE userid=$userid");
$od=$db->query("SELECT * FROM users WHERE userid={$_GET['ID']}");
if($_SESSION['attackwon'] != $_GET['ID'])
{
die ("Cheaters don't get anywhere.");
}
if($db->num_rows($od))
{
$r=$db->fetch_row($od);
$gq=$db->query("SELECT * FROM gangs WHERE gangID={$r['gang']}");
$ga=$db->fetch_row($gq);
if($r['hp'] == 1)
{
print "What a cheater you are.";
}
else
{
$stole=round($r['money']/(rand(200,10000)/10));$stole2=round($r['crystals']/(rand(10,1000)/10));$expgain=rand(1,3);
print "

<div id='mainOutput' style='text-align: center; color: green;  width: 600px; border: 1px solid #222222; height: 100px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

You beat {$r['username']}!!<br />
You knock {$r['username']} on the floor a few times to make sure he is unconscious, then open his wallet, snatch \$$stole and $stole2 crystals and run home happily.<br>You gained $expgain EXP";
$hosptime=rand(20,40)+floor($ir['level']/8);

$db->query("UPDATE users SET exp=exp+$expgain,money=money+$stole,crystals=crystals+$stole2 WHERE userid=$userid");
$db->query("UPDATE users SET hp=1,money=money-$stole,crystals=crystals-$stole2,hospital=$hosptime,hospreason='Mugged by <a href=\'viewuser.php?u={$userid}\'>{$ir['username']}</a>' WHERE userid={$r['userid']}");
event_add($r['userid'],"<a href='viewuser.php?u=$userid'>{$ir['username']}</a> mugged you and stole \$$stole and $stole2 crystals.",$c);
$atklog=mysql_escape_string($_SESSION['attacklog']);
$db->query("INSERT INTO attacklogs VALUES('',$userid,{$_GET['ID']},'won',unix_timestamp(),$stole,'$atklog');");
$chk_one = $db->query(sprintf("SELECT * FROM `battle_members` WHERE `bmemberUser` = '%u'", $ir['userid']));
$chk_two = $db->query(sprintf("SELECT * FROM `battle_members` WHERE `bmemberUser` = '%u'", $r['userid']));
   if (mysql_num_rows($chk_one) AND mysql_num_rows($chk_two))
    {
      $score = rand(12, 24);
      $db->query(sprintf("UPDATE `battle_members` SET `bmemberScore` = `bmemberScore` - '%d', `bmemberLosses` = `bmemberLosses` + '1' WHERE `bmemberUser` = '%u'", $score, $r['userid']));
      $db->query(sprintf("UPDATE `battle_members` SET `bmemberScore` = `bmemberScore` + '%d', `bmemberWins` = `bmemberWins` + '1' WHERE `bmemberUser` = '%u'", $score, $ir['userid']));
      echo '<br/><br/>You have added '.$score.' points to the score on the battle ladder, well done.<br />';
    }
$_SESSION['attackwon']=0;
$warq=$db->query("SELECT * FROM gangwars WHERE (warDECLARER={$ir['gang']} AND warDECLARED={$r['gang']}) OR (warDECLARED={$ir['gang']} AND warDECLARER={$r['gang']})");
if ($db->num_rows($warq) > 0)
{
$war=$db->fetch_row($warq);
$db->query("UPDATE gangs SET gangRESPECT=gangRESPECT-3 WHERE gangID={$r['gang']}");
$ga['gangRESPECT']-=3;
$db->query("UPDATE gangs SET gangRESPECT=gangRESPECT+3 WHERE gangID={$ir['gang']}");
print "<br />You earned 3 respect for your gang!";

}
//Gang Kill
if ($ga['gangRESPECT']<=0 && $r['gang'])
{
$db->query("UPDATE users SET gang=0 WHERE gang={$r['gang']}");

$db->query("DELETE FROM gangs WHERE gangRESPECT<='0'");
$db->query("DELETE FROM gangwars WHERE warDECLARER={$ga['gangID']} or warDECLARED={$ga['gangID']}");
}
$npcs=array(
);

if($r['user_level']==0)
{
$q=$db->query("SELECT * FROM challengebots WHERE cb_npcid={$r['userid']}");
if ($db->num_rows($q)) {
$cb=$db->fetch_row($q);
$qk=$db->query("SELECT * FROM challengesbeaten WHERE userid=$userid AND npcid={$r['userid']}");
if(!$db->num_rows($qk))
{
$m=$cb['cb_money'];
$db->query("UPDATE users SET money=money+$m WHERE userid=$userid");
print "<br /> You gained \$$m for beating the challenge bot {$r['username']}";
$db->query("INSERT INTO challengesbeaten VALUES($userid, {$r['userid']})");
}
}
}


}
}
else
{
print "You beat Mr. non-existant! Haha, pwned!";
}
$h->endpage();
?>
