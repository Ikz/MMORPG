<?php

include "globals.php";
if($_POST['report'])
{
$_POST['player']=abs((int) $_POST['player']);
$db->query("INSERT INTO preports VALUES('',$userid,{$_POST['player']},'{$_POST['report']}')");
print "Report processed!";
}
else
{
print "

<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'>Player Report</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>


Know of a player that's breaking the rules? Don't hesitate to report them. Reports are kept confidential.<br /><br />
<form action='preport.php' method='post'>
Player's ID: <input type='text' STYLE='color: black;  background-color: white;' name='player' value='{$_GET['ID']}' /><br /> <br />
What they've done: <br /> <br />
<textarea rows='7' cols='40' name='report'>{$_GET['report']}</textarea><br />
<input type='submit' STYLE='color: black;  background-color: white;' value='Send Report' /></form></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
}
$h->endpage();
?>
