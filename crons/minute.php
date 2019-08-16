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
$file = 'crons/minute.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
if( $db->num_rows($ready_to_run) ) {

	$db->query("UPDATE users set hospital=hospital-1 WHERE hospital>0");
	$db->query("UPDATE `users` SET jail=jail-1 WHERE `jail` > 0");
	$hc=$db->num_rows($db->query("SELECT * FROM users WHERE hospital > 0"));
	$jc=$db->num_rows($db->query("SELECT * FROM users WHERE jail > 0"));
	$db->query("UPDATE settings SET conf_value='$hc' WHERE conf_name='hospital_count'");
	$db->query("UPDATE settings SET conf_value='$jc' WHERE conf_name='jail_count'");
	
$time = time()+60;
$db->query("UPDATE `crons` SET `nextUpdate`={$time} WHERE `file`='{$file}'");
}

?>
