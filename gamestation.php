<?php
include "globals.php";
switch($_GET['action'])
{
case 'maxdirtbike': max_dirt_bike(); break;
case 'pool': pool(); break;
case 'ewoks': ewoks(); break;
case 'poker': poker(); break;
default: gamestation_index(); break;
}
function gamestation_index()
{
global $ir,$c,$userid;
print"
 <div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'><font color=purple> Game Station</font></h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>";
print"
<table width=100% border=3 bordercolor=silver class='table'><tr><th><font color=gold>Game Name</font></th><th><font color=gold>Description</font></th>";
print"<tr><td><a href='gamestation.php?action=maxdirtbike'>Max Dirt Bike</a></td><td>Max Dirt Bike! Click on the name to play</td>";
print"<tr><td><a href='gamestation.php?action=pool'>Pool</a></td><td>8 Ball Pool Multiplayer! Click on the name to play</td>";
print"<tr><td><a href='gamestation.php?action=ewoks'>Ewoks</a></td><td>Blast the crap out the of cute little teddies from Star Wars. Click on the name  to play</td>";
print"<tr><td> <a href='gamestation.php?action=poker'>Poker</a> </td><td>Poker, a card game played by millions. Click on the name  to play</td>";
print"</table></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div> ";
}
function max_dirt_bike()
{
global $ir, $c,$userid;
print "

 <div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'><font color=purple> <font color=purple>Max Dirt Bike</font></font></h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>


";
print "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='700' height='600'>
<param name='movie' value='http://1cup1coffee.com/fl/maxdirtbike.swf'><param name='quality' value='high'>
<embed src='http://1cup1coffee.com/fl/maxdirtbike.swf'  width='660' height='600' align='center' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'></embed>
</object></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div> <br><br><a href='gamestation.php'>Back to Game Station</a> ";
}
function pool()
{
global $ir,$c,$userid;
print "


 <div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'><font color=purple> <font color=purple>8 Ball Pool Multiplayer</font</font></h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>


";
print"		<div id='miniclip-game-embed' data-game-name='8-ball-pool-multiplayer' data-theme='1' data-width='650' data-height='420'></div></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div><br><br><a href='gamestation.php'>Back to Game Station</a>";
}

function ewoks()
{
global $ir,$c,$userid;
print "



 <div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'><font color=purple> <font color=purple>Ewoks</font</font></h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>


";
print "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='500' height='500'>
<param name='movie' value='images/ewoks.swf'><param name='quality' value='high'>
<embed src='images/ewoks.swf' width=500 height=500 align='center' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'></embed>
</object></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div><br><br><a href='gamestation.php'>Back to Game Station</a>";
}
function poker()
{
global $ir,$c,$userid;
print "


 <div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'><font color=purple> <font color=purple>Poker</font</font></h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>

";
print "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='500' height='500'>
<param name='movie' value='images/poker.swf'><param name='quality' value='high'>
<embed src='images/poker.swf' width=500 height=500 align='center' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'></embed>
</object></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div><br><br><a href='gamestation.php'>Back to Game Station</a>";

}
?>