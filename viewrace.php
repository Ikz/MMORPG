<?php
 include "globals.php";
$race=abs((int) $_GET['race']);
$q=$db->query("SELECT * FROM race_results WHERE rrID={$race}");
if($db->num_rows($q)==0) { die("Invalid Usage"); }
$r=$db->fetch_row($q);
print "<h3>Race Between {$r['rrCHALLENGER']} and {$r['rrCHALLENGED']}</h3><hr />
Challenger: {$r['rrCHALLENGER']} (Used: {$r['rrCHRCAR']})<br />
Challenged: {$r['rrCHALLENGED']} (Used: {$r['rrCHDCAR']})<br />
Type: {$r['rrTYPE']}<br />";
if($r['rrTYPE'] == "Betted") { print "Bet: \${$r['rrBET']}<br />"; }
print "Winner: {$r['rrWINNER']}<br />
Result: {$r['rrNOTES']}<br /><hr />
&gt; <a href='garage.php'>Go To Your Garage</a>";

$h->endpage();
?>