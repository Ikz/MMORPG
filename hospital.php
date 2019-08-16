<?php

include "globals.php";
global $ir;
print "


<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'> Hospital</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>
<table class='tablee'><tr><td><center><img src='/images/hospital.jpg' /></td></tr></table><br>
<table width='75%' class=\"table\" border=\"0\" cellspacing=\"1\"><tr bgcolor=gray><th>Name</th> <th>Level</th> <th>Time</th><th>Reason</th></tr>";
if ($ir['hospital']) {
    echo '<a href="hospitalPayOut.php">Pay $10,000 to leave hospital</a><br><br>';
}
$q=$db->query("SELECT u.*,c.* FROM users u LEFT JOIN gangs c ON u.gang=c.gangID WHERE u.hospital > 0 ORDER BY u.hospital DESC",$c);
while($r=$db->fetch_row($q))
{
print "\n<tr><td>{$r['gangPREFIX']} <a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]</td><td>
{$r['level']}</td><td>{$r['hospital']} minutes</td><td>{$r['hospreason']}</td></tr>";
}
print "</table></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
$h->endpage();
?>
