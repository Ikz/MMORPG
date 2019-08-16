<?php

$macropage="docrime.php?c={$_GET['c']}";
include "globals.php";
if($ir['jail'] or $ir['hospital']) { print "This page cannot be accessed while in jail or hospital.";

$h->endpage(); 
exit; 
}
$_GET['c']=abs((int) $_GET['c']);
if(!$_GET['c'])
{
print "Invalid crime";
}
else
{
$q=mysql_query("SELECT * FROM crimes WHERE crimeID={$_GET['c']}",$c);
$r=mysql_fetch_array($q);
if($ir['brave'] < $r['crimeBRAVE'])
{
print "

<style type='text/css'>
.style1 {
    color: #FF0000;
}
</style>


<body class='style1'>

You do not have enough Brave to perform this crime. 

";
}
else
{
$ec="\$sucrate=".str_replace(array("LEVEL","CRIMEXP","EXP","WILL","IQ"), array($ir['level'], $ir['crimexp'], $ir['exp'], $ir['will'], $ir['IQ']),$r['crimePERCFORM']).";";
eval($ec);
print $r['crimeITEXT'];
$ir['brave']-=$r['crimeBRAVE'];
mysql_query("UPDATE users SET brave={$ir['brave']} WHERE userid=$userid",$c);
if(rand(1,100) <= $sucrate)
{
$find=array("{money}","{crystals}","{item}");
$replace=array($r['crimeSUCCESSMUNY'],$r['crimeSUCCESSCRYS'],$r['crimeSUCCESSITEM']);
print str_replace($find,$replace,$r['crimeSTEXT']);
$ir['money']+=$r['crimeSUCCESSMUNY'];
$ir['crystals']+=$r['crimeSUCCESSCRYS'];
$ir['exp']+=(int) ($r['crimeSUCCESSMUNY']/8);
mysql_query("UPDATE users SET money={$ir['money']}, crystals={$ir['crystals']}, exp={$ir['exp']},crimexp=crimexp+{$r['crimeXP']} WHERE userid=$userid",$c);
if($r['crimeSUCCESSITEM'])
{
  item_add($userid, $r['crimeSUCCESSITEM'], 1);
}
}
else
{
if(rand(1, 2) == 1)
{
print $r['crimeFTEXT'];
}
else
{
  print $r['crimeJTEXT'];
  $db->query("UPDATE `users` SET `jail` = '$r[crimeJAILTIME]', `jail_reason` = '$r[crimeJREASON]' WHERE `userid` = '$userid'");
}
}

print "<br /><a href='docrime.php?c={$_GET['c']}'>Try Again</a><br />
<a href='criminal.php'>Crimes</a>";
}
}


$h->endpage();
?>
