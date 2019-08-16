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
$file = 'crons/fivemins.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
if( $db->num_rows($ready_to_run) ) {
	
	//brave update
	$query="UPDATE users SET brave=brave+((maxbrave/10)+0.5) WHERE brave<maxbrave ";
	$query2="UPDATE users SET brave=maxbrave WHERE brave>maxbrave";
	$query3="UPDATE users SET hp=hp+(maxhp/3) WHERE hp<maxhp";
	$query4="UPDATE users SET hp=maxhp WHERE hp>maxhp";
	$db->query($query);
	$db->query($query2);
	$db->query($query3);
	$db->query($query4);
	//enerwill update
	$query="UPDATE users SET energy=energy+(maxenergy/(12.5)) WHERE energy<maxenergy AND donatordays=0";
	$query5="UPDATE users SET energy=energy+(maxenergy/(6)) WHERE energy<maxenergy AND donatordays>0";
	$query2="UPDATE users SET energy=maxenergy WHERE energy>maxenergy";
	$query3="UPDATE users SET will=will+10 WHERE will<maxwill";
	$query4="UPDATE users SET will=maxwill WHERE will>maxwill";
	$db->query($query);
	$db->query($query5);
	$db->query($query2);
	$db->query($query3);
	$db->query($query4);
	if($set['validate_period'] == 5 && $set['validate_on'])
	{
	$db->query("UPDATE users SET verified=0");
	}
	if($set['validate_period'] == 15 && $set['validate_on'] && in_array(date('i'),array("00", "15", "30", "45")))
	{
	$db->query("UPDATE users SET verified=0");
	}
	
$time = time()+300;
$db->query("UPDATE `crons` SET `nextUpdate`={$time} WHERE `file`='{$file}'");
}

?>
