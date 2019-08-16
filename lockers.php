<?php

include "globals.php";


if($ir['donatordays'] == 0)
{
print "This feature is for donators only.";
$h->endpage();
exit;

}//Check to see if player is in Jail or Hospital
if($ir['jail'] or $ir['hospital']) { print "This page cannot be accessed while in jail or hospital.";

$h->endpage(); 
exit; 
}
print "


<h1>Daily Lockers</h1>

";
if($_GET['open'])
{
if($ir['lockers'] >= 15)
{
    
print "

<div id='mainOutput' style='text-align: center; color: white;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

Sorry, you have already opened 15 Lockers today. Come back tomorrow.<br><a href='index.php'><b>Go Home</b></a>";
$h->endpage(); 
exit;

}
if($ir['money'] < 500)
{
print"

<div id='mainOutput' style='text-align: center; color: white;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

Sorry, it costs \$500 to open a Locker. Come back when you have enough.";
$h->endpage(); 
exit;

}
$num=rand(1, 5);
$db->query("UPDATE users SET lockers=lockers+1, money=money-500 WHERE userid=$userid");
$db->query("UPDATE users SET lockersdone=lockersdone+1 WHERE userid=$userid");
$ir['money']-=500;
switch($num)
{
case 1:
$tokens=rand(1,5);
print "

<div id='mainOutput' style='text-align: center; color: green;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>


You have gained {$tokens} crystals.  

";
$db->query("UPDATE users SET crystals=crystals+{$tokens} WHERE userid={$userid}");
break;
case 2:
$money=rand(330, 3300);
print "

<div id='mainOutput' style='text-align: center; color: green;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

You have gained \${$money} 

";
$db->query("UPDATE users SET money=money+{$money} WHERE userid={$userid}");
break;
case 3:
$hosp=rand(10,25);
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>


You end up in hospital for {$hosp} mins.  

";
$db->query("UPDATE users SET hospital=hospital+{$hosp}, hospreason='Hit by a locker bomb' WHERE userid={$userid}");
event_add($ir['userid'],"You were hit by a locker bomb.<br>You end up in hospital for {$hosp} mins!");
break;
case 4:
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

You found nothing!


";
break;
}
print "

<br> </br>
<b><a href='lockers.php?open=1'><font color='green'>Open another!</font></a></b> | 
<b><a href='explore.php'><font color='red'>Enough! Back to Town</font></a></b>

";
}
else
{
print "

<div id='mainOutput' style='text-align: center; color: white;  width: 550px; border: 1px solid #222222; height: 90px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

<font color=gold size=3>Welcome to GTA Mobster Daily Lockers.<br> Each day you can pop by and open 15 Lockers.<br><br />
<b><a href='lockers.php?open=1'><font color='green'>Open Locker</font></a> </b> |
<b><a href='explore.php'><font color='red'>Maybe Later</font></a></b><br /><br />";
}
$h->endpage();
?>
