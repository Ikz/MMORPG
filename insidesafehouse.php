<?php
include "globals.php";
if ($ir['safehouse'] == 0)
{
print "Error, you are not in a Safe House";
}
print "<b>Inside The <span class='highlight'>Safe</span> <span class='highlight'>House</span></b><br><br>
 
 
<font size=3><font color=red><a href='leavesafehouse.php'>Leave <span class='highlight'>Safe</span> <span class='highlight'>House</span><a/></font></font><br><br>
 
 
<i>The <span class='highlight'>Safe</span> <span class='highlight'>House</span> Rules</i><br>
 
<ul>- You can't travel - You can't commit crimes - You can't be attacked or attack";
?>