<?php 
include "globals.php"; 
if ($ir['safehouse'] == 1)
{
print "You are already protected by guards!<br>[<a href='insidesafehouse.php'>Go Inside</a>]";
}
if ($ir['safehouse'] == 0)
{
print "<h2>Safe House</h2>
  
<br><b>Rent a Safe House<br>NOTE: You can't be attacked or attack nor commit crimes or travel</b><br><br>
 
 
 
  
<table width=100% class='table' border=1>  
<tr> 
<td> 
Safe House
</td> 
<td> 
$10000
</td> 
<td> 
<a href='safehouse.php?spend=safehouse'>Rent</a> 
</td> 
</table>"; 
if($_GET['spend'] == 'safehouse') 
{ 
if($ir['money'] <10000) 
{ 
print "You don't have enough money!"; 
} 
else
{ 
if($ir['money'] >9999) 
{ 
print "You are now protected by guards!
  
<a href='index.php'>Go Home</a><br>
  
<a href='insidesafehouse.php'>Go inside and read the rules</a>"; 
$db->query("UPDATE users SET money=money-10000,safehouse=1 WHERE userid=$userid",$c); 
} 
} 
} 
}
$h->endpage(); 
?>