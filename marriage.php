<?php
$housequery=1;
@include_once(DIRNAME(__FILE__) .'/globals.php');
if($ir['married']==0) {
echo'You are not married.';
$h->endpage();
exit;
}

//Get the married persons data
$m = $db->fetch_row($db->query(sprintf("SELECT u.userid, u.username, u.money, u.crystals, u.bankmoney, u.cybermoney, u.maxwill, u.marriedwill, us.*, h.hNAME, h.hWILL
FROM users AS u 
LEFT JOIN userstats AS us ON u.userid=us.userid 
LEFT JOIN houses AS h ON u.maxwill=h.hWILL
WHERE u.userid=%u", $ir['married'])));

$data = array_merge($_GET, $_POST); //Not using $_REQUEST because it includes $_COOKIE
$data['act'] = isset($data['act']) && is_string($data['act']) ? strtolower(trim($data['act'])) : false; 
$array = array('moneyget', 'moneysend', 'bankget', 'banksend', 'crystalget', 'crystalsend', 'cyberget', 'cybersend', 'movein', 'moveout', 'view_requests', 'divorce', 'request', 'index', ''); //Small array for actions.
if(!in_array($data['act'], $array)) {
echo'<big>Error!</big><br />
There seems to be an error, please go back and try again.';
$h->endpage();
exit;
}
switch($data['act']) {
case'moneyget': Get_Money(); break;
case'moneysend': Give_Money(); break;
case'bankget': Get_Bank(); break;
case'banksend': Give_Bank(); break;
case'crystalget': Get_Crystal(); break;
case'crystalsend': Give_Crystal(); break; 
case'cyberget': Get_Cyber(); break;
case'cybersend': Give_Cyber(); break; 
case'movein': Move_In(); break;
case'moveout': Move_Out(); break;
case'view_requests': View_Requests(); break;
case'divorce': Divorce(); break;
case'request': Request(); break;
default: Marriage(); break;
}
function Money_UnFormatter($number) {
$number = str_replace(array(',','$','+','-'), '', $number);
if(!is_numeric($number)) {
return false;
exit;
}
return $number;
}
function Marriage()
{
global $ir, $db, $m, $h;
$requests = $db->num_rows($db->query(sprintf("SELECT ID FROM `marriage_requests` WHERE (User_To=%u)", $ir['userid'])));
echo'<h3>You are married to <a href="viewuser.php?u='.$m['userid'].'"><u>'.$m['username'].'</u></a> ['.number_format($m['userid']).']</h3>';
$m['cybermoney'] = ($m['cybermoney'] ==  -1) ? 'No Account' : money_formatter($m['cybermoney']);
$ir['cybermoney'] = ($ir['cybermoney'] ==  -1) ? 'No Account' : money_formatter($ir['cybermoney']);
$m['bankmoney'] = ($m['bankmoney'] ==  -1) ? 'No Account' : money_formatter($m['bankmoney']);
$ir['bankmoney'] = ($ir['bankmoney'] ==  -1) ? 'No Account' : money_formatter($ir['bankmoney']);
?>
<table width="90%" class="table">
<tr style="text-align: center;">
<th width="20%">&nbsp;</th>
<th>You</th>
<th><?php echo $m['username'] ?>'s</th>
</tr>
<tr style="text-align: center;">
<td style="text-align: center;"><big>Money</big><br /> <a href="?act=moneysend">Send</a> || <a href="?act=moneyget">Request</a></td><td><?php echo money_formatter($ir['money']) ?></td> <td><?php echo money_formatter($m['money']) ?></td>
</tr>
<tr style="text-align: center;">
<td><big>Crystals</big><br /> <a href="?act=crystalsend">Send</a> || <a href="?act=crystalget">Request</a></td><td><?php echo number_format($ir['crystals']) ?></td> <td><?php echo number_format($m['crystals']) ?></td>
</tr>
<tr style="text-align: center;">
<td><big>Bank Money</big><br /> <a href="?act=banksend">Send</a> || <a href="?act=bankget">Request</a></td><td><?php echo $ir['bankmoney'] ?></td> <td><?php echo $m['bankmoney'] ?></td>
</tr>
<tr style="text-align: center;">
<td><big>Cyber Money</big><br /> <a href="?act=cybersend">Send</a> || <a href="?act=cyberget">Request</a></td><td><?php echo $ir['cybermoney'] ?></td> <td><?php echo $m['cybermoney'] ?></td>
</tr>
<tr style="text-align: center;">
<td><big>Houses</big><br />
<small>(
<?php if($ir['maxwill'] > $m['maxwill']) { echo'Can\'t move in.'; }
elseif(($ir['marriedwill'] == $m['maxwill'] || $ir['maxwill'] == $m['maxwill']) && ($ir['maxwill'] || $m['maxwill']) != 100) { echo'You are living together.)<br /> ( <a href="?act=moveout">Leave</a>'; }
elseif($ir['maxwill'] < $m['maxwill']) { echo'<a href="?act=movein">Move in</a>'; }
elseif($ir['marriedwill'] == 0 && $m['marriedwill'] ==0 ) {echo'Can\'t move in';}
?> )</small></td><td><?php echo $ir['hNAME'] ?><br /><small>(<?php echo $ir['maxwill'] ?> will)</small></td><td><?php echo $m['hNAME'] ?><br /><small>(<?php echo $m['maxwill'] ?> will)</small></td></tr>
<tr style="text-align: center;">
<td colspan="3"><a href="?act=view_requests">View Requests(<?php echo number_format($requests) ?>)</a> || <a href="?act=divorce">Get a Divorce</a></td>
</tr>
</table>
<br />
<br />
<table width="90%" class="table">
<tr style="text-align: center;">
<th width="15%">&nbsp;</th>
<th>Yours</th>
<th><?php echo $m['username'] ?>'s</th>
</tr>
<tr style="text-align: center;">
<td>Strength</td><td><?php echo number_format($ir['strength']) ?></td> <td><?php echo number_format($m['strength']) ?></td>
</tr>
<tr style="text-align: center;">
<td>Agility</td><td><?php echo number_format($ir['agility']) ?></td> <td><?php echo number_format($m['agility']) ?></td>
</tr>
<tr style="text-align: center;">
<td>Guard</td><td><?php echo number_format($ir['guard']) ?></td> <td><?php echo number_format($m['guard']) ?></td>
</tr style="text-align: center;">
<tr style="text-align: center;">
<td>Labour</td><td><?php echo number_format($ir['labour']) ?></td> <td><?php echo number_format($m['labour']) ?></td>
</tr>
</table>
<?php
$h->endpage();
}
function Get_Money()
{
global $data, $ir, $db, $m, $h;
if(isset($data['money'])) {
$data['money'] = Money_UnFormatter($data['money']);
$data['money'] = abs(@intval($data['money']));
$data['reasonwhy'] = strip_tags(addslashes($data['reasonwhy']));
if($data['money'] > $m['money']) {
echo'Your partner doesnt have this much money.';
$h->endpage();
exit;
}
$db->query(sprintf("INSERT INTO `marriage_requests` VALUES('', %u, %u, '%s', %d, 0, 0, 0)", $m['userid'], $ir['userid'], $data['reasonwhy'], $data['money']));
event_add($m['userid'], "Your partner has requested ".money_formatter($data['money'])." from your hand please accept/decline this in the marriage requests.");
echo'You have requested '.money_formatter($data['money']).' from your partner.';
exit;
}
?>
<span style="font-weight: bold;">Requesting Money</span><br />
You can request up to: <?php echo money_formatter($m['money']) ?><br />
<form action="" method="post">
<input type="hidden" name="act" value="moneyget">
Ammout to request: <input type="text" name="money" value="<?php echo money_formatter($m['money']) ?>" /><br />
Reason why: <textarea name="reasonwhy"></textarea><br />
<input type="submit" value="Request!" />
</form>
<?php
$h->endpage();
}
function Get_Bank()
{
global $data, $ir, $db, $m, $h;
if($m['bankmoney'] == -1) {
echo'Your partner doesnt have a bank account.';
$h->endpage();
exit;
}
if($ir['bankmoney'] == -1) {
echo'You don\'t have a bank account.';
$h->endpage();
exit;
}
if(isset($data['money'])) {
$data['money'] = Money_UnFormatter($data['money']);
$data['money'] = abs(@intval($data['money']));
$data['reasonwhy'] = strip_tags(addslashes($data['reasonwhy']));
if($data['money'] > $m['bankmoney']) {
echo'Your partner doesnt have this much money in the bank.';
$h->endpage();
exit;
}
$db->query(sprintf("INSERT INTO `marriage_requests` VALUES('', %d, %d, '%s', 0, 0, 0, %d)", $m['userid'], $ir['userid'], $data['reasonwhy'], $data['money']));
event_add($m['userid'], "Your partner has requested ".money_formatter($data['money'])." from your bank please accept/decline this in the marriage requests.");
echo'You have requested '.money_formatter($data['money']).' from your partner.';
exit;
}
?>
<span style="font-weight: bold;">Requesting Bank-money</span><br />
You can request up to: <?php echo money_formatter($m['bankmoney']) ?><br />
<form action="" method="post">
<input type="hidden" name="act" value="bankget">
Ammout to request: <input type="text" name="money" value="<?php echo money_formatter($m['bankmoney']) ?>" /><br />
Reason why: <textarea name="reasonwhy"></textarea><br />
<input type="submit" value="Request!" />
</form>
<?php
$h->endpage();
}
function Get_Crystal()
{
global $data, $ir, $db, $m, $h;
if(isset($data['crystals'])) {
$data['crystals'] = abs(@intval($data['crystals']));
$data['reasonwhy'] = strip_tags(addslashes($data['reasonwhy']));
$db->query(sprintf("INSERT INTO `marriage_requests` VALUES('', %d, %d, '%s', 0, %d, 0, 0)", $m['userid'], $ir['userid'], $data['reasonwhy'], $data['crystals']));
event_add($m['userid'], "Your partner has requested ".number_format($data['crystals'])." crystals please accept/decline this in the marriage requests.");
echo'You have requested '.number_format($data['crystals']).' crystals from your partner.';
exit;
}
?>
<span style="font-weight: bold;">Requesting Crystals</span><br />
You can request up to: <?php echo number_format($m['crystals']) ?> crystals<br />
<form action="" method="post">
<input type="hidden" name="act" value="crystalget">
Ammout to request: <input type="text" name="crystals" value="<?php echo number_format($m['crystals']) ?>" /><br />
Reason why: <textarea name="reasonwhy"></textarea><br />
<input type="submit" value="Request!" />
</form>
<?php
$h->endpage();
}
function Get_Cyber()
{
global $data, $ir, $db, $m, $h;
if($m['cybermoney'] == -1) {
echo'Your partner doesnt have a cyber bank account.';
$h->endpage();
exit;
}
if($ir['cybermoney'] == -1) {
echo'You don\'t have a cyber bank account.';
$h->endpage();
exit;
}
if(isset($data['money'])) {
$data['money'] = Money_UnFormatter($data['money']);
$data['money'] = abs(@intval($data['money']));
$data['reasonwhy'] = strip_tags(addslashes($data['reasonwhy']));
if($data['money'] > $m['cybermoney']) {
echo'Your partner doesnt have this much money in the cyber bank.';
$h->endpage();
exit;
}
$db->query(sprintf("INSERT INTO `marriage_requests` VALUES('', %d, %d, '%s', 0, 0, %d, 0)", $m['userid'], $ir['userid'], $data['reasonwhy'], $data['money']));
event_add($m['userid'], "Your partner has requested ".money_formatter($data['money'])." from your cyber bank please accept/decline this in the marriage requests.");
echo'You have requested '.money_formatter($data['money']).' from your partner.';
exit;
}
?>
<span style="font-weight: bold;">Requesting Cyber-money</span><br />
You can request up to: <?php echo money_formatter($m['cybermoney']) ?><br />
<form action="" method="post">
<input type="hidden" name="act" value="cyberget">
Ammout to request: <input type="text" name="money" value="<?php echo money_formatter($m['cybermoney']) ?>" /><br />
Reason why: <textarea name="reasonwhy"></textarea><br />
<input type="submit" value="Request!" />
</form>
<?php
$h->endpage();
}
function View_Requests()
{
global $db, $ir, $m, $h;
echo'<span style="font-weight: bold;">Viewing Requests</span><br />
<table class="table" width="80%">
<tr>
<th width="20%">Amount wanted</th>
<th>Reason</th>
<th width="22%">Links</th>
</tr>';
$Requests = $db->query(sprintf("SELECT * FROM `marriage_requests` WHERE User_To=%d", $ir['userid']));
if($db->num_rows($Requests) == 0) {
echo'<tr style="text-align: center; text-weight: bold;"><td colspan="4">You have no requests</td></tr>';
$h->endpage();
exit;
}
$type="";
$format="";
$amout="";
$reason="";
while($ma = $db->fetch_row($Requests)) {
if($ma['Money'] != 0) { $amount = money_formatter($ma['Money']).' Money'; }
if($ma['Crystals'] > 0) { $amount = number_format($ma['Crystals']).' Crystals'; }
if($ma['Cyber_Money'] > 0)  { $amount = money_formatter($ma['Cyber_Money']).' Cyber money'; }
if($ma['Bank_Money'] > 0)  { $amount = money_formatter($ma['Bank_Money']).' Bank money'; }
$reason = stripslashes($ma['Reason']);
?>
<tr>
<td style="text-align: center;"><?php echo $amount ?></td>
<td style="text-align: center;"><?php echo $reason ?></td>
<td style="text-align: center;"><a href="?act=request&action=accept&ID=<?php echo $ma['ID'] ?>">Accept</a> &nbsp;&nbsp;&nbsp;&nbsp; || &nbsp;&nbsp;&nbsp;&nbsp; <a href="?act=request&action=decline&ID=<?php echo $ma['ID'] ?>">Decline</a></td>
</tr>
<?php
}
echo'</table>';
$h->endpage();
}
function Request()
{
global $data, $db, $ir, $m, $h;
if(!isset($data['action'])) {
echo'You havent selected an option.';
$h->endpage();
exit;
}
if(!isset($data['ID'])) {
echo'You havent selected anything to '.$data['action'].'.';
$h->endpage();
exit;
}
$ma = $db->fetch_row($db->query(sprintf("SELECT * FROM `marriage_requests` WHERE User_To=%d", $ir['userid'])));
if($m === FALSE)
{
echo'This doesnt exist.';
}
if($data['action'] == 'accept') {
if($ma['Crystals'] != 0 && $ir['crystals'] >= $ma['Crystals']) {
$db->query(sprintf("UPDATE users SET crystals=crystals+%u WHERE userid=%d", $ma['Crystals'], $ma['User_From']));
$db->query(sprintf("UPDATE users SET crystals=crystals-%u WHERE userid=%d", $ma['Crystals'], $ir['userid']));
echo'You have given '.$m['username'].' '.number_format($ma['Crystals']).' crystals.';
event_add($ma['User_From'], $ir['username'].' gave you the requested '.number_format($ma['Crystals']).' Crystals you wanted.');
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
if($ma['Money'] != 0 && $ir['money'] >= $ma['Money']) {
$db->query(sprintf("UPDATE users SET money=money+%u WHERE userid=%d", $ma['Money'], $ma['User_From']));
$db->query(sprintf("UPDATE users SET money=money-%u WHERE userid=%d", $ma['Money'], $ir['userid']));
echo'You have given '.$m['username'].' '.money_formatter($ma['Money']).'.';
event_add($ma['User_From'], $ir['username'].' gave you the requested '.money_formatter($ma['Money']).' you wanted.');
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
if($ma['Bank_Money'] != 0 && $ir['bankmoney'] >= $ma['Bank_Money']) {
$db->query(sprintf("UPDATE users SET bankmoney=bankmoney+%u WHERE userid=%d", $ma['Bank_Money'], $ma['User_From']));
$db->query(sprintf("UPDATE users SET bankmoney=bankmoney-%u WHERE userid=%d", $ma['Bank_Money'], $ir['userid']));
echo'You have given '.$m['username'].' '.money_formatter($ma['Bank_Money']).'.';
event_add($ma['User_From'], $ir['username'].' gave you the requested '.money_formatter($ma['Bank_Money']).' bank money you wanted.');
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
if($ma['Cyber_Money'] != 0 && $ir['cybermoney'] >= $ma['Cyber_Money']) {
$db->query(sprintf("UPDATE users SET cybermoney=cybermoney+%u WHERE userid=%d", $ma['Cyber_Money'], $ma['User_From']));
$db->query(sprintf("UPDATE users SET cybermoney=cybermoney-%u WHERE userid=%d", $ma['Cyber_Money'], $ir['userid']));
echo'You have given '.$m['username'].' '.money_formatter($ma['Cyber_Money']).'.';
event_add($ma['User_From'], $ir['username'].' gave you the requested '.money_formatter($ma['Cyber_Money']).' cyber money you wanted.');
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
}
elseif($data['action'] == 'decline') {
if($ma['Crystals'] != 0) {
event_add($ma['User_From'], $ir['username'].' refused to give you the '.number_format($ma['Crystals']).' crystals you requested.');
echo'You have declined the crystal request.';
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
if($ma['Money'] != 0) {
event_add($ma['User_From'], $ir['username'].' refused to give you the '.number_format($ma['Money']).' you requested.');
echo'You have declined the crystal request.';

$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
if($ma['Cyber_Money'] != 0) {
event_add($ma['User_From'], $ir['username'].' refused to give you the '.number_format($ma['Cyber_Money']).' cyber money you requested.');
echo'You have declined the crystal request.';
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
if($ma['Bank_Money'] != 0) {
event_add($ma['User_From'], $ir['username'].' refused to give you the '.number_format($ma['Bank_Money']).' bank money you requested.');
echo'You have declined the crystal request.';
$db->query(sprintf("DELETE FROM `marriage_requests` WHERE ID=%d", $data['ID']));
}
}
else
{echo'What'; }
$h->endpage();
}
function Give_Money()
{
global $data, $db, $m, $ir, $h;
if(isset($data['money'])) {
$data['money'] = Money_UnFormatter($data['money']);
$data['money'] = abs(@intval($data['money']));
if($ir['money'] >= $data['money']) {
$db->query(sprintf("UPDATE users SET money=money+%d WHERE userid=%u", $data['money'], $m['userid']));
$db->query(sprintf("UPDATE users SET money=money-%d WHERE userid=%u", $data['money'], $ir['userid']));
event_add($m['userid'], $ir['username'].' has sent you '.money_formatter($data['money']).'.');
echo'You have sent '.money_formatter($data['money']).' to '.$m['username'].'.';
exit;
}
echo'You don\'t have enough money to send that.';
exit;
}
?>
<span style="font-weight: bold;"><u>Sending money to <?php echo $m['username'] ?></u></span><br />
<form action="" method="post">
Amount to send: <input type="text" name="money" value="<?php echo money_formatter($ir['money']) ?>" /><br />
<input type="submit" value="Send" />
</form>
<?php
$h->endpage();
}
function Give_Bank()
{
global $data, $db, $m, $ir, $h;
if($m['bankmoney'] == -1) {
echo'Your partner doesnt have a bank account.';
$h->endpage();
exit;
}
if($ir['bankmoney'] == -1) {
echo'You don\'t have a bank account.';
$h->endpage();
exit;
}
if(isset($data['money'])) {
$data['money'] = Money_UnFormatter($data['money']);
$data['money'] = abs(@intval($data['money']));
if($ir['bankmoney'] >= $data['money']) {
$db->query(sprintf("UPDATE users SET bankmoney=bankmoney+%d WHERE userid=%u", $data['money'], $m['userid']));
$db->query(sprintf("UPDATE users SET bankmoney=bankmoney-%d WHERE userid=%u", $data['money'], $ir['userid']));
event_add($m['userid'], $ir['username'].' has sent you '.money_formatter($data['money']).' banked money.');
echo'You have sent '.money_formatter($data['money']).' bank money to '.$m['username'].'.';
exit;
}
echo'You don\'t have enough money to send that.';
exit;
}
?>
<span style="font-weight: bold;"><u>Sending bank money to <?php echo $m['username'] ?></u></span><br />
<form action="" method="post">
Amount to send: <input type="text" name="money" value="<?php echo money_formatter($ir['bankmoney']) ?>" /><br />
<input type="submit" value="Send" />
</form>
<?php
$h->endpage();
}
function Give_Crystal()
{
global $data, $db, $m, $ir, $h;
if(isset($data['crystals'])) {
$data['crystals'] = Money_UnFormatter($data['crystals']);
$data['money'] = abs(@intval($data['crystals']));
if($ir['crystals'] >= $data['crystals']) {
$db->query(sprintf("UPDATE users SET crystals=crystals+%d WHERE userid=%u", $data['money'], $m['userid']));
$db->query(sprintf("UPDATE users SET crystals=crystals-%d WHERE userid=%u", $data['money'], $ir['userid']));
event_add($m['userid'], $ir['username'].' has sent you '.number_format($data['crystals']).' crystals.');
echo'You have sent '.number_format($data['money']).' crystals to '.$m['username'].'.';
exit;
}
echo'You don\'t have enough money to send that.';
exit;
}
?>
<span style="font-weight: bold;"><u>Sending crystals to <?php echo $m['username'] ?></u></span><br />
<form action="" method="post">
Amount to send: <input type="text" name="crystals" value="<?php echo number_format($ir['crystals']) ?>" /><br />
<input type="submit" value="Send" />
</form>
<?php
$h->endpage();
}
function Give_Cyber()
{
global $data, $db, $m, $ir, $h;
if($m['cybermoney'] == -1) {
echo'Your partner doesnt have a cyber bank account.';
$h->endpage();
exit;
}
if($ir['cybermoney'] == -1) {
echo'You don\'t have a cyber bank account.';
$h->endpage();
exit;
}
if(isset($data['money'])) {
$data['money'] = Money_UnFormatter($data['money']);
$data['money'] = abs(@intval($data['money']));
if($ir['cybermoney'] >= $data['money']) {
$db->query(sprintf("UPDATE users SET cybermoney=cybermoney+%d WHERE userid=%u", $data['money'], $m['userid']));
$db->query(sprintf("UPDATE users SET cybermoney=cybermoney-%d WHERE userid=%u", $data['money'], $ir['userid']));
event_add($m['userid'], $ir['username'].' has sent you '.money_formatter($data['money']).' cyber money.');
echo'You have sent '.money_formatter($data['money']).' cyber money to '.$m['username'].'.';
exit;
}
echo'You don\'t have enough money to send that.';
exit;
}
?>
<span style="font-weight: bold;"><u>Sending bank money to <?php echo $m['username'] ?></u></span><br />
<form action="" method="post">
Amount to send: <input type="text" name="money" value="<?php echo money_formatter($ir['cybermoney']) ?>" /><br />
<input type="submit" value="Send" />
</form>
<?php
$h->endpage();
}
function Divorce()
{
global $data, $db, $ir, $m, $h;
if(isset($data['yesorno']) == 'yes') {
$theirwill = ($m['marriedwill'] !=0) ? ',maxwill=marriedwill,marriedwill=0' : '';
$mywill = ($ir['marriedwill'] != 0) ? ',maxwill=marriedwill,marriedwill=0' : '';
$db->query(sprintf("UPDATE users SET married=0%s WHERE userid=%d", $theirwill, $m['userid']));
$db->query(sprintf("UPDATE users SET married=0%s WHERE userid=%d", $mywill, $ir['userid']));
event_add($m['userid'], 'Your partner deivorced you.');
echo'You have divorced your partner.';
$h->endpage();
}
if(isset($data['clicked'])) {
echo'To make sure its a final decision, please type the word \'yes\' (case sensitive) in the box below.';
?>
<form action="" method="post">
<input type="text" name="yesorno" value="no" /><br />
<input type="submit" name="imsure" value="Do it!" />
<input type="reset" value="Don't do it!"/>
</form>
<?php
$h->endpage();
}
else {
?>
<span style="font-weight: bold;"><u>Divorcing <?php echo $m['username'] ?></u></span><br />
Are you sure?
<form action="" method="post">
<input type="submit" name="clicked" value="Yes!" />
</form>
<?php
$h->endpage();
}
}
function Move_In()
{
global $data, $db, $ir, $m, $h;
if(isset($data['moved'])) {
$db->query(sprintf("UPDATE users SET marriedwill=maxwill WHERE userid=%d", $ir['userid']));
$db->query(sprintf("UPDATE users SET maxwill=%u WHERE userid=%d", $m['maxwill'], $ir['userid']));
event_add($m['userid'], 'Your partner has moved in with you.');
echo'You have moved in with your partner.';
$h->endpage();
exit;
}
?>
<form action="" method="post">
<input type="submit" name="moved" value="Move in!" />
<input type="submit" value="Dont move in!" />
</form>
<?php
$h->endpage();
}
function Move_Out()
{
global $data, $db, $ir, $m, $h;
if(isset($data['moved'])) {
$marriedwill = $ir['marriedwill'];
$db->query(sprintf("UPDATE users SET maxwill=marriedwill WHERE userid=%u", $ir['userid']));
$db->query(sprintf("UPDATE users SET marriedwill=0 WHERE userid=%u", $ir['userid']));
event_add($m['userid'], 'Your partner has moved out.');
echo'You have moved out.';
$h->endpage();
exit;
}
?>
<form action="" method="post">
<input type="submit" name="moved" value="Move Out!" />
<input type="submit" value="Dont move out!" />
</form>
<?php
$h->endpage();
}