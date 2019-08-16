<?php
$macropage="vipgym.php";
require "globals.php";
if($ir['donatordays'] == 0)
{
print "This feature is for donators only.";
$h->endpage();
exit;

}



if($ir['hospital']) { die("<prb>This page cannot be accessed while in hospital.</prb>"); }
$statnames=array(
'Strength' => 'strength',
'Agility' => 'agility',
'Guard' => 'guard',
'Labour' => 'labour');
$_POST['amnt']=abs((int) $_POST['amnt']);
if($ir['jail']) { die("<prb>This page cannot be accessed while in jail.</prb>"); }
print "
<center'>
<h1>Donator Gym</h1><br><h2>Double EXP + Stats</h2>";

if($_POST['stat'] && $_POST['amnt'])
{
  $stat=$statnames[$_POST['stat']];
  if(!$stat)
  {
    die("<prb><br>This stat cannot be trained.</prb>");
  }
  if($_POST['amnt'] > $ir['energy'])
  {
    print("<prb><font color='red'>You do not have enough energy to train that much.</font></prb>");
  }
  else
  {
    $gain=0;
    for($i=0; $i<$_POST['amnt']; $i++)
    {
     $gain+=rand(1,1.25)*(($ir['will']/0.15);
      $ir['will']-=rand(1,15);
      if($ir['will'] < 0) { $ir['will']=0; }
    }
    if($ir['jail']) { $gain/=2; }
    $db->query("UPDATE `userstats` SET `{$stat}` = `{$stat}` + $gain WHERE `userid` = $userid");
    $db->query("UPDATE `users` SET `will` = {$ir['will']}, energy = energy - {$_POST['amnt']} WHERE `userid` = $userid");
    $inc=$ir[$stat]+$gain;
    $inc2=$ir['energy']-$_POST['amnt'];
    if($stat=="strength")
    {
      print "You begin lifting some weights.<br />
      You have gained {$gain} strength by doing {$_POST['amnt']} sets of weights.<br />
      You now have {$inc} strength and {$inc2} energy left.";
    }
    elseif($stat=="agility")
    {
      print "You begin running on a treadmill.<br />
      You have gained {$gain} agility by doing {$_POST['amnt']} minutes of running.<br />
      You now have {$inc} agility and {$inc2} energy left.";
    }
    elseif($stat=="guard")
    {
      print "You jump into the pool and begin swimming.<br />
      You have gained {$gain} guard by doing {$_POST['amnt']} minutes of swimming.<br />
      You now have {$inc} guard and {$inc2} energy left.";
    }
    elseif($stat=="labour")
    {
      print "You walk over to some boxes filled with gym equipment and start moving them.<br />
      You have gained {$gain} labour by moving {$_POST['amnt']} boxes.<br />
      You now have {$inc} labour and {$inc2} energy left.";
    }
    print "<hr />";
    $ir['energy']-=$_POST['amnt'];
    $ir[$stat]+=$gain;
  }
}
$ir['strank']=get_rank($ir['strength'],'strength');
$ir['agirank']=get_rank($ir['agility'],'agility');
$ir['guarank']=get_rank($ir['guard'],'guard');
$ir['labrank']=get_rank($ir['labour'],'labour');
print "Choose the stat you want to train and the times you want to train it.<br />
You can train up to {$ir['energy']} times.<hr />
<form action='vipgym.php' method='post'>
Stat: <select type='dropdown' name='stat'>
<option style='color:red;' value='Strength'>Strength (Have {$ir['strength']}, Ranked {$ir['strank']})
<option style='color:blue;' value='Agility'>Agility (Have {$ir['agility']}, Ranked {$ir['agirank']})
<option style='color:green;' value='Guard'>Guard (Have {$ir['guard']}, Ranked {$ir['guarank']})
<option style='color:brown;' value='Labour'>Labour (Have {$ir['labour']}, Ranked {$ir['labrank']})
</select><br><br>
Times to train: <input type='text' STYLE='color: black;  background-color: white;' name='amnt' value='{$ir['energy']}' /><br><br>
<input type='submit' STYLE='color: black;  background-color: white;' value='Train' /></form></center>";
$h->endpage();
?>