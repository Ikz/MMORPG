<?php
include 'globals.php';
     
    // simple array of the user levels, would be better in globals.php or something
    $levels = array(
        0 => 'NPC',
        1 => 'Player', 
        2 => 'Admin', 
        3 => 'Secretary',
        4 => 'Assistant'
    );
 
// create a username function to format the username correctly
// parse the id, name & user_level
// using each of the classes in css each name can be formatted acordingly
function username($id, $name, $user_level,$nolink = false) {
    global $levels;
    return ($nolink == true) ?
        '<a href="<span class="highlight">viewuser</span>.php?u='.$id.'" class="'.$levels[$user_level].'">'.$name.' ['.$id.']</a>' :
        '<span class="'.$levels[$user_level].'">'.$name.' ['.$id.']</span>';
 
}

$money=money_formatter($r['money']);
$crystals=money_formatter($r['crystals'],''); 
$m = $db->fetch_row($db->query(sprintf("SELECT u.userid, u.username, u.money, u.crystals, u.bankmoney, u.cybermoney, u.maxwill, u.married, u.marriedwill, us.*, h.hNAME, h.hWILL
FROM users AS u 
LEFT JOIN userstats AS us ON u.userid=us.userid 
LEFT JOIN houses AS h ON u.maxwill=h.hWILL
WHERE u.userid=%u", $r['married'])));
$_GET['u'] = isset($_GET['u']) && ctype_digit($_GET['u']) ? abs((int) $_GET['u']) : 0;
if(!empty($_GET['u']) && $_GET['u'] > 0) {
     
    // get the generic sql resultset
    $sql = sprintf(
        'SELECT u.*,us.*,c.*,h.*,g.*,f.*,COUNT(r.refREFER) AS ref_count
         FROM users u LEFT JOIN userstats us ON u.userid=us.userid 
         LEFT JOIN cities c ON u.location=c.cityid 
         LEFT JOIN houses h ON u.maxwill=h.hWILL 
         LEFT JOIN gangs g ON g.gangID=u.gang 
         LEFT JOIN fedjail f ON f.fed_userid=u.userid 
         LEFT JOIN referals r ON r.refREFER = u.userid 
         WHERE u.userid = %u;',
        $_GET['u']   
    );
    // query the results and if the resultset is empty then error 
    $result = $db->query($sql);
    if($db->num_rows($result) == 0) {
        echo
        '<h2>Error</h2>
        <p>Sorry but we could not find this user you are looking for. 
        Please try again another time</p>';
         
        $h->endpage();
        die();
    }
     
    // get the results ready to display
    $r = $db->fetch_row($result);
	if($r['laston'] > 0)
{
$la=time()-$r['laston'];
$unit="seconds";
if($la >= 60)
{
$la=(int) ($la/60);
$unit="minutes";
}
if($la >= 60)
{
$la=(int) ($la/60);
$unit="hours";
if($la >= 24)
{
$la=(int) ($la/24);
$unit="days";
}
}
$str="$la $unit ago";
}
else
{
$str="--";
}
if($r['last_login'] > 0)
{
$ll=time()-$r['last_login'];
$unit2="seconds";
if($ll >= 60)
{
$ll=(int) ($ll/60);
$unit2="minutes";
}
if($ll >= 60)
{
$ll=(int) ($ll/60);
$unit2="hours";
if($ll >= 24)
{
$ll=(int) ($ll/24);
$unit2="days";
}
}
$str2="$ll $unit2 ago";
}
else
{
$str2="--";
}
 
} else {
 
    // error so just redirect the page
    // more than likely just decided to type in the filename directly
    header('Location:index.php');
 
}
?>
 
<style type="text/css">
 
.Admin { color: lime; }
 
 
div#profilewrap {
    width: 550px;
}
div#generalinfo {
    padding: 0px 10px 10px 0px;
    text-align: left;
    font-size: 1.4em;
    width: 300px;
    float: left;
}
div#additionalinfo {
    float: right;
    width: 200px;
}
div#additionalinfo div#photo {
    background: #FFA500;
}
div#additionalinfo ul#links {
    padding: 0;
}
div#additionalinfo ul#links li {
    list-style: none;
    padding: 0;
    margin: 0;
}
div#additionalinfo ul#links li a {
    display: block;
    text-align: center;
    background: #f5f5f5;
    border: 1px solid #999;
    font-size: 1.1em;
    padding: 8px;
    margin: 4px 0px;
    text-shadow: 1px 1px 1px #fff;
    filter: dropshadow(color=#fff, offx=1, offy=1);
     
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;

/* CSS3 ELEMENTS: GRADIENT BACKGROUND */
 
    background-image: linear-gradient(bottom, rgb(230,230,230) 50%, rgb(245,245,245) 100%);
    background-image: -o-linear-gradient(bottom, rgb(230,230,230) 50%, rgb(245,245,245) 100%);
    background-image: -moz-linear-gradient(bottom, rgb(230,230,230) 50%, rgb(245,245,245) 100%);
    background-image: -webkit-linear-gradient(bottom, rgb(230,230,230) 50%, rgb(245,245,245) 100%);
    background-image: -ms-linear-gradient(bottom, rgb(230,230,230) 50%, rgb(245,245,245) 100%);
     
    background-image: -webkit-gradient(
        linear,
        left bottom,
        left top,
        color-stop(0.5, rgb(230,230,230)),
        color-stop(1, rgb(245,245,245))
    );

}
div#additionalinfo ul#links li a:hover {
    background: #ccc;
    text-shadow: 0;
    filter: 0;      
}
div#staffinfo {
    text-align: center;
    padding: 24px;
    margin: 24px 48px;
    background: #f5f5f5;
}
div#staffinfo div#info {
    width: 350px;
    float:center;
}
div#staffinfo div#notes {
    float:center; 
    width: 350px;
}
div#staffinfo div#notes textarea {
    display: block;
    width: 316px;
    height: 10em;
    border: 1px solid #ccc;
    padding: 12px;
}
 
 
span.vlabel {
    clear: left;
    float: left;
    display: block;
    width: 45%;
    margin-bottom: 6px;
}
span vv {
    clear: right;
    float: left;
    margin-bottom: 6px;
}
 
.well {
    color: #300;
    text-shadow: 1px 1px 1px #999;
    filter: dropshadow(color=#999, offx=1, offy=1);
    margin-bottom: 12px;
}
.alert {
    background: #ffc;
    padding: 0px 12px 6px 12px;
    border: 1px solid #fc3;
    margin-bottom: 12px;
    text-shadow: 1px 1px 1px #fff;
    filter: dropshadow(color=#fff, offx=1, offy=1);
}
.warning {
    background: #fcc;
    padding: 0px 12px 6px 12px;
    border: 1px solid #933;
    margin-bottom: 12px;
    color: #933;    
    text-shadow: 1px 1px 1px #fff;
    filter: dropshadow(color=#fff, offx=1, offy=1);
}
.alert p, .warning p {margin: 6px 0;}
.alert p.b, .warning p.b {margin: 12px 0;color:#600;}
 
 
.pageTitle{font-size:24px;}
.b{font-weight: 600;}
.hdr{font-weight:bold;}
.push {margin-top:12px;margin-bottom:12px;}
.clear{clear:both;}
</style>
 

 
<h2><?php if($r['donatordays']) { $r['username'] = "<font color=aqua>{$r['username']}</font>";$d="<img src='donator.gif' alt='Donator: {$r['donatordays']} Days Left' title='Donator: {$r['donatordays']} Days Left' />";} 
print "

{$r['username']} <small>[{$r['userid']}]</small> $d";  ?></h2>
<div id="profilewrap"><br>
    <div id="generalinfo">
        <? if($r['user_level'] != 1) : ?>
<span class="vlabel">User Level:</span><span class="vv"> Admin</span><br class="clear" />
                <span class="vlabel">Duties:</span><span class="vv"><? echo $r['duties']; ?></span><br class="clear" />
        <? endif; ?>
         <br>
         
        <? if($r['fedjail']): ?>
            <div class="warning">
                <p class="b">Fed Jail</p>
                 <p>This user is in federal jail for <? echo number_format($r['fed_days']); ?> days.</p>
                <? if($r['fed_reason']): ?><p><? echo $r['fed_reason']; ?></p><? endif; ?>
            </div>
        <? endif; ?>
        <? if($r['hospital'] || $r['jail']): ?>
            <div class="alert">
                <? if($r['hospital']): ?>
                    <p class="b">Hospitalised</p>
                    <p class="b">This user is in hospital for <? echo $r['hospital']; ?> minutes.</p>
                    <? if($r['hospreason']): ?><p class="b"><? echo $r['hospreason']; ?></p><? endif; ?>
                <? endif; ?>
                <? if($r['jail']): ?>
                    <p class="b">Jailed</p>
                    <p class="b">This user is in jail for <? echo $r['jail']; ?> minutes.</p>
                    <? if($r['jail_reason']): ?><p class="b"><? echo $r['jail_reason']; ?></p><? endif; ?>
                <? endif; ?>
                 
            </div>
        <? endif; ?>
         
         
        <span class="vlabel push hdr"><font color="#FF7E00">Information</font></span><br class="clear" />
         
        <span class="vlabel">Gender:</span><span class="vv"><? echo $r['gender']; ?></span><br class="clear" />
        <span class="vlabel">Days Old:</span><span class="vv"><? echo $r['daysold']; ?></span><br class="clear" />
        <span class="vlabel">Joined:</span><span class="vv"><? echo date('jS F Y',$r['signedup']); ?></span><br class="clear" />
        <span class="vlabel">Last Active:</span><span class="vv"><? echo $str; ?></span><br class="clear" />
        <span class="vlabel">Location:</span><span class="vv"><? echo $r['cityname']; ?> [<a href='travel.php'>Travel</a>]</span><br class="clear" />
         
         
        
         
        <span class="vlabel">Level</span><span class="vv"><? echo $r['level']; ?></span><br class="clear" />
        <span class="vlabel">Health</span><span class="vv"><? echo $r['hp'] .'/'. $r['maxhp']; ?></span><br class="clear" />
        <span class="vlabel">Money</span><span class="vv"><? echo $r['money']; ?></span><br class="clear" />
        <span class="vlabel">Crystals</span><span class="vv"><? echo $r['crystals']; ?></span><br class="clear" />
        <span class="vlabel">Gang</span><span class="vv"><? 
            echo ($r['gang']) ? '<a href="gangs.php?action=view&ID='.$r['gang'].'">'.$r['gangNAME'].'</a>' : 'N/A'; 
        ?></span><br class="clear" />
         
         
        <? if($r['hID'] > 1) { $housepic="{$r['hNAME']}<img src='{$r['hPIC']}' width='137' height='72' alt='{$r['hNAME']}' title='{$r['hNAME']}'>";} ?>
		<? if($r['hID'] == 1) { $nohouse="Homeless";} ?>
         
        <span class="vlabel">Property:</span><span class="vv"><? print "$housepic"; ?><? print "$nohouse"; ?></span><br class="clear" />
        <span class="vlabel">Friends:</span><span class="vv"><? echo $r['friend_count']; ?></span><br class="clear" />
        <span class="vlabel">Enemies:</span><span class="vv"><? echo $r['enemy_count']; ?></span><br class="clear" />  
        <span class="vlabel">Referals:</span><span class="vv"><? echo $r['ref_count']; ?></span><br class="clear" />
		<span class="vlabel">Married To:</span><span class="vv"><? if($r['married'] > 0)
{ $married="<a href='viewuser.php?u={$m['userid']}'>{$m['username']} [{$m['userid']}]</a>";}
print"$married";
if($r['married'] == 0)
{ $notmarried="Nobody";}
print"$notmarried"; ?></span><br class="clear" />
<span class="vlabel">Rating:</span><span class="vv"><a href='rating.php?ID=<? echo $r['userid']; ?>&action=goodrating'><img src='images/thumbsup.gif' title='Give user good rating'></a> <strong><? echo $r['ratings']; ?></strong>  <a href='rating.php?ID=<? echo $r['userid']; ?>&action=badrating'><img src='images/thumbsdown.gif' title='Give user bad rating'></a><br />
    </div>
    <div id="additionalinfo">
            <img src="<? echo $r['display_pic'] ? $r['display_pic'] : 'noimg.png'; ?>" width="168" height="140" title="Profile Image" alt="Profile Image" /><br><? if($r['donatordays'] > 0)
{ $respected="<img src='images/respect_txtbg.png' alt='Respected Donator' title='Respected Donator' />";} 
print "

$respected"; ?>
        <ul id="links">
        <? if($r['userid'] != $userid) : ?>
            <? if($ir['mailban'] == 0 && $r['mailban'] == 0): ?>
                <li><a href="mailbox.php?action=compose&ID=<? echo $r['userid']; ?>" style='color:#000;'>Message</a></li>
                <li><a href="contactlist.php?action=add&ID=<? echo $r['userid']; ?>" style='color:#000;'>Add To Contact List</a></li>
            <? endif; ?>
            <li><a href="sendcrys.php?ID=<? echo $r['userid']; ?>" style='color:#000;'>Send Crystals</a></li>
            <li><a href="sendcash.php?ID=<? echo $r['userid']; ?>" style='color:#000;'>Send Money</a></li>
            <? if($r['jail'] == 0 && $r['hospital'] == 0 && $r['fedjail'] == 0): ?>
                <li><a href="attack.php?ID=<? echo $r['userid']; ?>" style='color:#000;'>Attack</a></li>
                <li><a href="burnhouse.php?ID=<? echo $r['userid']; ?>" style='color:#000;'>Arson</a></li>
            <? endif; ?>
            <? if($ir['donatordays'] > 0): ?>
                <li><a href="friendslist.php?action=add&ID=<? echo $r['userid']; ?>" style='color:#000;'>Add Friend</a></li>
                <li><a href="blacklist.php?action=add&ID=<? echo $r['userid']; ?>" style='color:#000;'>Add Enemy</a></li>
            <? endif; ?>
            <? if($ir['user_level'] > 1): ?>
                <li><a href="jailuser.php?userid=<? echo $r['userid']; ?>" style='color:#000;'>Jail</a></li>
                <li><a href="mailban.php?userid=<? echo $r['userid']; ?>" style='color:#000;'>Mail Ban</a></li>
            <? endif; ?>
        <? endif; ?>  
        </ul>
    </div>
    <br class="clear" />
 
</div>
 
<? if(in_array($ir['user_level'], array(2,3,4,5)) && $userid != $r['userid']): ?>
    <div id="staffinfo">
        <div id="info">
            <span class="vlabel" style='color:#000;'>Last Hit</span><span class="vv" style='color:#000;'><? echo $r['lastip'] .' ('. @gethostbyaddr($r['lastip']).')'; ?></span><br class="clear" />
            <span class="vlabel" style='color:#000;'>Last Login</span><span class="vv" style='color:#000;'><? echo $r['lastip_login'] .' ('. @gethostbyaddr($r['lastip_login']).')'; ?></span><br class="clear" />
            <span class="vlabel" style='color:#000;'>Signup</span><span class="vv" style='color:#000;'><? echo $r['lastip_signup'] .' ('. @gethostbyaddr($r['lastip_signup']).')'; ?></span><br class="clear" />
        <br></div>
        <div id="notes">
            <form action="staffnotes.php" method="post">
                <textarea name="staffnotes"><? echo $r['staffnotes']; ?></textarea>
                <input type="hidden" name="ID" value="<? echo $r['userid']; ?>" />
                <p><input type="submit" STYLE='color: black;  background-color: white;' value="Change" /></p>
            </form>
        </div>
        <br class="clear" />
    </div> 
<? endif; ?>
 
<? $h->endpage(); ?>
