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

$file = 'crons/hour.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
if( $db->num_rows($ready_to_run) ) {
	
	$settq=$db->query("SELECT * FROM settings");
	while($r=$db->fetch_row($settq))
	{
	$set[$r['conf_name']]=$r['conf_value'];
	}
	$db->query("UPDATE gangs SET gangCHOURS=gangCHOURS-1 WHERE gangCRIME>0");
	$q=$db->query("SELECT g.*,oc.* FROM gangs g LEFT JOIN orgcrimes oc ON g.gangCRIME=oc.ocID WHERE g.gangCRIME > 0 AND g.gangCHOURS = 0");
	while($r=$db->fetch_row($q))
	{
	$suc=rand(0,1);
	if($suc) {
	$log=$r['ocSTARTTEXT'].$r['ocSUCCTEXT'];
	$muny=(int) (rand($r['ocMINMONEY'],$r['ocMAXMONEY']));
	$log=str_replace(array("{muny}","'"),array($muny,"''"),$log);
	$db->query("UPDATE gangs SET gangMONEY=gangMONEY+$muny,gangCRIME=0 WHERE gangID={$r['gangID']}");
	$db->query("INSERT INTO oclogs VALUES ('',{$r['ocID']},{$r['gangID']}, '$log', 'success', $muny, '{$r['ocNAME']}', unix_timestamp())");
	$i=$db->insert_id();
	$qm=$db->query("SELECT * FROM users WHERE gang={$r['gangID']}");
	while($rm=$db->fetch_row($qm))
	{
	event_add($rm['userid'],"Your Gang's Organised Crime Succeeded. Go <a href='oclog.php?ID=$i'>here</a> to view the details.",$c);
	}
	}
	else
	{
	$log=$r['ocSTARTTEXT'].$r['ocFAILTEXT'];
	$muny=0;
	$log=str_replace(array("{muny}","'"),array($muny,"''"),$log);
	$db->query("UPDATE gangs SET gangCRIME=0 WHERE gangID={$r['gangID']}");
	$db->query("INSERT INTO oclogs VALUES ('',{$r['ocID']},{$r['gangID']}, '$log', 'failure', $muny, '{$r['ocNAME']}', unix_timestamp())");
	$i=$db->insert_id();
	$qm=$db->query("SELECT * FROM users WHERE gang={$r['gangID']}");
	while($rm=$db->fetch_row($qm))
	{
	event_add($rm['userid'],"Your Gang's Organised Crime Failed. Go <a href='oclog.php?ID=$i'>here</a> to view the details.",$c);
	}
	}
	}
	if(date('G')==17)
	{
	$db->query("UPDATE users u LEFT JOIN userstats us ON u.userid=us.userid LEFT JOIN jobs j ON j.jID=u.job LEFT JOIN jobranks jr ON u.jobrank=jr.jrID SET u.money=u.money+jr.jrPAY, u.exp=u.exp+(jr.jrPAY/20) 
	WHERE u.job > 0 AND u.jobrank > 0");
	$db->query("UPDATE userstats us LEFT JOIN users u ON u.userid=us.userid LEFT JOIN jobs j ON j.jID=u.job LEFT JOIN jobranks jr ON u.jobrank=jr.jrID SET us.strength=(us.strength+1)+jr.jrSTRG-1,us.labour=(us.labour+1)+jr.jrLABOURG-1,us.IQ=(us.IQ+1)+jr.jrIQG-1 WHERE u.job > 0 AND u.jobrank > 0");
	}
	if($set['validate_period'] == 60 && $set['validate_on'])
	{
	$db->query("UPDATE users SET verified=0");
	}

	
$time = time()+3600;
$db->query("UPDATE `crons` SET `nextUpdate`={$time} WHERE `file`='{$file}'");
}

?>
