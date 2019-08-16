<?php
include "globals.php";
print "

<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'> Voting</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>


Here you may vote for {$set['game_name']} at various RPG toplists and be rewarded.<br />
<a href='http://apexwebgaming.com/in/5757'>Vote at APEX (no reward)</a><br />
<a href='votetwg.php'>Vote at TWG (20% energy restore)</a><br />
<a href='votetrpg.php'>Vote at TOPRPG ($500)</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";

$h->endpage();
?>
