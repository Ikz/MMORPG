<?php

include "globals.php";
$_GET['ID'] = abs((int) $_GET['ID']);
if(!$_GET['ID'])
{
die ("Incorrect usage of file.");
}
$q=$db->query("SELECT * FROM oclogs WHERE oclID={$_GET['ID']}");
$r=$db->fetch_row($q);
print "Here is the detailed view on this crime.<br />
<b>Crime:</b> {$r['ocCRIMEN']}<br />
<b>Time Executed:</b> ".date('F j, Y, g:i:s a',$r['ocTIME'])."<br />
{$r['oclLOG']}<br /><br />
<b>Result:</b> {$r['oclRESULT']}<br />
<b>Money Made:</b> \${$r['oclMONEY']}";
$h->endpage();
?>
