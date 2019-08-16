<?php
include "globals.php";
if($ir['user_level'] == 2 || $ir['user_level'] == 3 || $ir['user_level'] == 5)
{
$q=$db->query("SELECT staffnotes FROM users WHERE userid={$_POST['ID']}");
$old=addslashes($db->fetch_single($q));
$db->query("UPDATE users SET staffnotes='{$_POST['staffnotes']}' WHERE userid='{$_POST['ID']}'");
$db->query("INSERT INTO staffnotelogs VALUES ('', $userid, {$_POST['ID']}, unix_timestamp(), '$old', '{$_POST['staffnotes']}')");
print "User notes updated!<br />
<a href='viewuser.php?u={$_POST['ID']}'>&gt; Back To Profile</a>";
}
else
{
print "You violent scum.";
}
$h->endpage();
?>

