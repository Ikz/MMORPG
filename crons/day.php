<?php
/*
 * Cron / Timestamp system
 * @Author: sniko / Harry Denley
 
 
CREATE TABLE `crons` (
  `file` varchar(30) collate latin1_general_ci NOT NULL,
  `nextUpdate` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO `crons` (`file`, `nextUpdate`) VALUES
('crons/minute.php', 0),
('crons/fivemins.php', 0),
('crons/day.php', 0),
('crons/hour.php', 0);

*/

$file = 'crons/day.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
if( $db->num_rows($ready_to_run) ) {
	
	$q=$db->query("SELECT * FROM fedjail WHERE fed_days=0");
	$ids=array();
	while($r=$db->fetch_row($q))
	{
	$ids[]=$r['fed_userid'];
	}
	if(count($ids) > 0)
	{
	$db->query("UPDATE users SET fedjail=0 WHERE userid IN(".implode(",", $ids).")");
	}
	$db->query("DELETE FROM fedjail WHERE fed_days=0");
	$db->query("UPDATE users SET daysingang=daysingang+1 WHERE gang > 0");
	$db->query("UPDATE users SET daysold=daysold+1, boxes_opened=0");
	$db->query("UPDATE users SET mailban=mailban-1 WHERE mailban > 0");
	$db->query("UPDATE users SET donatordays=donatordays-1 WHERE donatordays > 0");
	$db->query("UPDATE users SET cdays=cdays-1 WHERE course > 0");
	$db->query("UPDATE users SET bankmoney=bankmoney+(bankmoney/50) where bankmoney>0");
	$db->query("UPDATE users SET cybermoney=cybermoney+(cybermoney/100*7) where cybermoney>0");
	$q=$db->query("SELECT * FROM users WHERE cdays=0 AND course > 0");
	while($r=$db->fetch_row($q))
	{
	$cd=$db->query("SELECT * FROM courses WHERE crID={$r['course']}");
	$coud=$db->fetch_row($cd);
	$userid=$r['userid'];
	$db->query("INSERT INTO coursesdone VALUES({$r['userid']},{$r['course']})");
	$upd="";
	$ev="";
	if($coud['crSTR'] > 0)
	{
	$upd.=",us.strength=us.strength+{$coud['crSTR']}";
	$ev.=", {$coud['crSTR']} strength";
	}
	if($coud['crGUARD'] > 0)
	{
	$upd.=",us.guard=us.guard+{$coud['crGUARD']}";
	$ev.=", {$coud['crGUARD']} guard";
	}
	if($coud['crLABOUR'] > 0)
	{
	$upd.=",us.labour=us.labour+{$coud['crLABOUR']}";
	$ev.=", {$coud['crLABOUR']} labour";
	}
	if($coud['crAGIL'] > 0)
	{
	$upd.=",us.agility=us.agility+{$coud['crAGIL']}";
	$ev.=", {$coud['crAGIL']} agility";
	}
	if($coud['crIQ'] > 0)
	{
	$upd.=",us.IQ=us.IQ+{$coud['crIQ']}";
	$ev.=", {$coud['crIQ']} IQ";
	}
	$ev=substr($ev,1);
	if ($upd) {
	$db->query("UPDATE users u LEFT JOIN userstats us ON u.userid=us.userid SET us.userid=us.userid $upd WHERE u.userid=$userid");
	}
	$db->query("INSERT INTO events VALUES('',$userid,unix_timestamp(),0,'Congratulations, you completed the {$coud['crNAME']} and gained $ev!')");
	}
	$db->query("UPDATE users SET course=0 WHERE cdays=0");
	$db->query("TRUNCATE TABLE votes;");
	
$time = time()+86400;
$db->query("UPDATE `crons` SET `nextUpdate`={$time} WHERE `file`='{$file}'");
}

?>
