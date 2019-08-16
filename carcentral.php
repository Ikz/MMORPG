<?php
 include "globals.php";
print "<h3>Car Central</h3><br>
<b>Diplaying the last 20 finished races:</b><br />
<table width='95%' class=table><tr><th>Race Participants</th><th>Results</th></tr>";
$q=$db->query("SELECT * FROM race_results ORDER BY rrID DESC LIMIT 20");
while($r=$db->fetch_row($q))
{
print "<tr><td>{$r['rrCHALLENGER']} Vs {$r['rrCHALLENGED']}</td><td> <a href='viewrace.php?race={$r['rrID']}'>View</td></tr></a>";
}
print "<br>

<b>Current Tournaments:</b><br />
<font color='red'>No tournaments going on at present.</font>";

$h->endpage();
?>