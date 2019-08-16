<?php
@include_once(DIRNAME(__FILE__) .'/globals.php');
$data = array_merge($_GET, $_POST);
$data['act'] = isset($data['act']) && is_string($data['act']) ? strtolower(trim($data['act'])) : false; 
if($ir['married']) {
echo'You are already married.';
$h->endpage();
exit;
}
switch($data['act']) {
case'accept': Accept(); break;
case'decline': Decline(); break;
default: index(); break;
}
function index()
{
global $data, $ir, $db, $h;
if(isset($data['userto'])) { 
$data['userto'] = abs(@intval($data['userto']));
$db->query(sprintf("INSERT INTO `proposals` VALUES('', %u, %d, '%s')", $ir['userid'], $data['userto'], addslashes(strip_tags($data['comment']))));
$i = $db->insert_id();
$p = $db->fetch_row($db->query(sprintf("SELECT * FROM `proposals` WHERE ID=%d", $i)));
event_add($data['userto'], $ir['username'].' has proposed to you, with the message of:<br />'.stripslashes($data['comment']).'<br /> click <a href="propose.php?act=accept&ID='.$p['ID'].'">Here to accept</a> or <a href="propose.php?act=decline&ID='.$p['ID'].'">Here to decline</a>.');
$uname = mysql_result($db->query(sprintf("SELECT username FROM users WHERE userid=%u", $data['userto'])), 0,0 );
echo'You have proposed to '.$uname.'.';
$h->endpage();
}
else { 
?>
<form action="" method="post">
User's ID to propose to: <input type="text" STYLE='color: black;  background-color: white;' name="userto" value="" maxlength="5" /><br />
Comment: <input type="text" name="comment" STYLE='color: black;  background-color: white;' value="" size="40" /><br />
<input type="submit" STYLE='color: black;  background-color: white;' value="Propose!" />
</form>
<?php
$h->endpage();
}
}
function Accept()
{
global $data, $ir, $db, $h;
if(!isset($data['ID'])) {
echo'No proposal selected.';
$h->endpage();
exit;
}
$p = $db->query(sprintf("SELECT * FROM `proposals` WHERE ID=%d", $data['ID']));
if(!$db->num_rows($p)) {
echo'No proposal selected.';
$h->endpage();
exit;
}
$p = $db->fetch_row($p);
if(($p['Proposed_ID'] || $p['Proposer_ID']) != $ir['userid']) {
echo'No proposal selected.';
$h->endpage();
exit;
}
$db->query(sprintf("UPDATE users SET married=%u WHERE userid=%d", $p['Proposer_ID'], $p['Proposed_ID']));
$db->query(sprintf("UPDATE users SET married=%u WHERE userid=%d", $p['Proposed_ID'], $p['Proposer_ID']));
event_add($p['Proposer_ID'], $ir['username'].' has accepted your proposal, you are now married.');
$uname = mysql_result($db->query(sprintf("SELECT username FROM users WHERE userid=%u", $p['Proposer_ID'])), 0,0 );
$db->query(sprintf("DELETE FROM `proposals` WHERE `ID`=%d", $data['ID']));
echo'You accepted '.$uname.'\'s proposal.';
$h->endpage();
}
function Decline()
{
global $data, $ir, $db, $h;
if(!isset($data['ID'])) {
echo'No proposal selected.';
$h->endpage();
exit;
}
$p = $db->query(sprintf("SELECT * FROM `proposals` WHERE ID=%d", $data['ID']));
if(!$db->num_rows($p)) {
echo'No proposal selected.';
$h->endpage();
exit;
}
$p = $db->fetch_row($p);
if(($p['Proposed_ID'] || $p['Proposer_ID']) != $ir['userid']) {
echo'No proposal selected.';
$h->endpage();
exit;
}
event_add($p['Proposer_ID'], $ir['username'].' has declined your proposal, sorry.');
$uname = mysql_result($db->query(sprintf("SELECT username FROM users WHERE userid=%u", $p['Proposer_ID'])), 0,0 );
$db->query(sprintf("DELETE FROM `proposals` WHERE `ID`=%d", $data['ID']));
echo'You declined '.$uname.'\'s proposal.';
$h->endpage();
}
