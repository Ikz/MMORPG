<?php
include "globals.php";
$query=$db->query("SELECT `userid`, `username`, `userpass`, `email`, `signedup` FROM users WHERE userid > 1");
while ($r=$db->fetch_row($query))
{
$link = mysql_connect("localhost", "gtamobx1_ikz", "b9mafia786");
mysql_select_db("gtamobx1_vb", $link) or die(mysql_error());
mysql_query("INSERT INTO gtamobx1_vb.`vbuser` (username,passworddate,email,password) VALUES
('{$r['username']}','{$r['signedup']}','{$r['email']}', '{$r['userpass']}')") or die(mysql_error());
mysql_close($link);
}
print "Members inserted correctly";
?>