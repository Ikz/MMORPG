<?php

include "sglobals.php";
switch($_GET['site_mode'])
{
case 'maintain': maintenance_mode_on(); break;
case 'maintainoff': maintenance_mode_off(); break;
case 'maintaindo': maintenance_do_on(); break;
case 'maintaindeact': maintenance_do_off(); break;
case 'maintainpage': maintenance_do_page(); break;
case 'maintainpageacc': maintenance_do_page_now(); break;
default: print "Error: This script requires an action."; break;
}
function maintenance_mode_on()
{
global $db,$ir, $maint, $c;
if($ir['user_level'] != 2)
{
die("<h1><center>Hmmm this page is clean maybe you should not be here</h1></center>");
}
$q = $db->query("SELECT offline, online, reason, maintpage FROM maintenance where offline=Offline");
$t = $db->fetch_row($q);
echo'Current site Status = <font size=2>'.$t['online'].''.$t['offline'].'</font><br />';
echo'Current Page Closed = <font size=2>'.$t['maintpage'].'</font>';
echo'<br /><br /><br />';
print "<h3>Turn Site Maintenance On</h3><br />
<form action='staff_maintenance.php?site_mode=maintaindo' method='post'>
<input type='hidden' name='online' value='Online' /> 
<input type='submit' STYLE='color: black;  background-color: white;' value='Reopen Page/Site' /></form><br /><br />Or Give Reason For Closure <br /><br />
<form action='staff_maintenance.php?site_mode=maintaindeact' method='post'>
<input type='hidden' name='offline' value='Offline'/> 
<textarea rows='6' cols='40' name='reason'></textarea><br /><br />
Expected Time to Re-Open site to Public:<br />
Hours: <input type='text' name='hour' STYLE='color: black;  background-color: white;' maxlength='2' style='width:20px;'/> <br />
Minutes: <input type='text' name='minute' STYLE='color: black;  background-color: white;' maxlength='2' style='width:20px;'/><br />
<input type='hidden' name='timenow' > <br /><br /> 
<input type='submit' value='Close Site' STYLE='color: black;  background-color: white;'/></form>
<h3>Close a Particular Page</h3><br />
<form action='staff_maintenance.php?site_mode=maintainpageacc' method='post'>
<input type='hidden' name='online' value='Online' /> <br /><br />Reason For Page Closure <br /><br />
<form action='staff_maintenance.php?site_mode=maintaindeact' method='post'>
<input type='hidden' name='offline' value='Offline'/> 
<textarea rows='6' cols='40' name='reason'></textarea><br /><br />
Expected Time to Re-Open site to Public:<br />
Hours: <input type='text' name='hour' STYLE='color: black;  background-color: white;' maxlength='2' style='width:20px;'/> <br />
Minutes: <input type='text' name='minute' STYLE='color: black;  background-color: white;' maxlength='2' style='width:20px;'/><br />
<input type='hidden' name='timenow' > 
Close a Page: <input type='text' STYLE='color: black;  background-color: white;' name='maintpage' maxlength='100' style='width:100px;'/><small> [Leave Blank to Close Every Page]</small><br /><br /> 
<input type='submit' STYLE='color: black;  background-color: white;' value='Close Page' /></form>";
}
function maintenance_do_on()
{
global $db,$ir, $c, $maint, $reason;
if($ir['user_level'] != 2)
{
die("<h1><center>Hmmm this page is clean maybe you should not be here</h1></center>");
}
if($maint['online'] = 'Online') {
    //$db->query("TRUNCATE TABLE maintenance;");
    $db->query("UPDATE maintenance SET offline='', online='{$_POST['online']}', reason='', maintpage='<b>No Pages Are being Worked On</b>'");
    $db->query("UPDATE users SET offline=0");
    $db->query("UPDATE users SET maintpage=''");
print "<h2>Mode Changed Successfully</h2><br /><br />Site is now <b>'{$_POST['online']}'</b>";
if($t['reason'] = '') {
    print"<b>No Reason was given</b>";
    die();
    }
}
}
function maintenance_do_off()
{
global $db,$ir, $c, $maint, $reason;
if($ir['user_level'] != 2)
{
die("<h1><center>Hmmm this page is clean maybe you should not be here</h1></center>");
}
if($maint['offline'] = 'Offline') {
    //$db->query("UPDATE maintenance SET  offline='{$_POST['offline']}', online='', reason='{$_POST['reason']}', hour='{$_POST['hour']}', minute='{$_POST['minute']}', unix_timestamp()");
    $db->query("TRUNCATE TABLE maintenance;");
    $db->query("UPDATE users SET offline=1 WHERE user_level=1 ");
    $db->query("INSERT INTO maintenance VALUES('{$_POST['offline']}', '', '{$_POST['reason']}', '{$_POST['hour']}', '{$_POST['minute']}', NOW(), '<b> All Pages are closed to Public View </b>')");
    $db->query("UPDATE users SET maintpage='' WHERE user_level=1 ");
print "<h2>Mode Changed Successfully</h2><br /><br />Site is now <b>'{$_POST['offline']}' $offline</b> <br /><br />For the following Reason: <br /> <br /><b>'{$_POST['reason']}'</b><br /><br />Expected Downtime = {$_POST['hour']} hour(s) and {$_POST['minute']} minute(s)";
echo "<br />".date('g:i:s a');
if($t['timenow'] > 0)
{
$ll=time()-$t['timenow'];
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
echo ''.$_POST['timenow'].''.$str2.'';
if($t['reason'] = '') {
    print"<b>No Reason was given</b>";
    die();
    }
    }
    }
    function maintenance_do_page_now()
{
global $db,$ir, $c, $maint, $reason;
if($ir['user_level'] != 2)
{
die("<h1><center>Hmmm this page is clean maybe you should not be here</h1></center>");
}
if($maint['offline'] = 'Offline') {
    //$db->query("UPDATE maintenance SET  offline='{$_POST['offline']}', online='', reason='{$_POST['reason']}', hour='{$_POST['hour']}', minute='{$_POST['minute']}', unix_timestamp()");
    $db->query("TRUNCATE TABLE maintenance;");
    $db->query("UPDATE users SET offline=0 WHERE user_level=1 ");
    $db->query("INSERT INTO maintenance VALUES('', '{$_POST['online']}', '{$_POST['reason']}', '{$_POST['hour']}', '{$_POST['minute']}', NOW(), '{$_POST['maintpage']}')");
    $db->query("UPDATE users SET maintpage='{$_POST['maintpage']}'");
print "<h2>Mode Changed Successfully</h2><br /><br />Current Page <b>'{$_POST['maintpage']}' </b> Has been Closed <br /><br />For the following Reason: <br /> <br /><b>'{$_POST['reason']}'</b><br /><br />Expected Downtime = {$_POST['hour']} hour(s) and {$_POST['minute']} minute(s)";
}
}
$h->endpage();
?>