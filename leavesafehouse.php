<?php
include "globals.php";
if(!$_GET['spend'])
{
print "<b><a href='leavesafehouse.php?spend=leave'>Leave the Safe House</a></b><br>(<font color=red>No going back, though you can buy another Safe House</font>)
";
}
else
{
if($_GET['spend'] == 'leave')
{
if($ir['safehouse'] == 0)
{
print "You are not in a Safe House, how can you leave? Dumbass!";
}
else
{
if($ir['safehouse'] == 1)
{
print "You have left the Safe House, we hope our services were helpful.<br>
 
<a href='index.php'>Go Home</a>";
$db->query("UPDATE users SET safehouse=safehouse-1 WHERE userid=$userid",$c);
}
}
}
}
$h->endpage();
?>