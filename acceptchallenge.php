<?php
 
include_once "globals.php";
 
$id=abs((int) $_POST['id']);
 
$car=abs((int) $_POST['car']);
 
if(!$id || !$car) { print "Invalid Car"; $h->endpage(); exit(); }
$q=$db->query("SELECT ch.*, cp.*, ct.*, u1.username as challenger, u2.username as challenged FROM challenges ch LEFT JOIN cars_playercars cp ON ch.chCHRCAR=cp.cpcID LEFT JOIN cars_types ct ON ct.carID=cp.cpcCAR LEFT JOIN users u1 ON ch.chCHR=u1.userid LEFT JOIN users u2 ON ch.chCHD=u2.userid WHERE ch.chID={$id} AND (ch.chCHR=$userid OR ch.chCHD=$userid)") or die(mysql_error());
if($db->num_rows($q) == 0) { print "Invalid Challenge"; $h->endpage(); exit(); }
$r=$db->fetch_row($q);
if($r['chSTATUS']=="open") {
$bet=$r['chBET'];
if($bet > $ir['money']) { print "The bet is too large."; $h->endpage(); exit(); }
$q=$db->query("SELECT cp.*,ct.* FROM cars_playercars cp LEFT JOIN cars_types ct ON ct.carID=cp.cpcCAR WHERE cp.cpcID={$car} AND cp.cpcPLAYER=$userid");
if($db->num_rows($q) == 0) { print "Invalid Car"; $h->endpage(); exit(); }
$m=$db->fetch_row($q);
if($m['cpcID'] == $r['cpcID']) { print "???"; $h->endpage(); exit(); }
//kk, time to race =D
print "OK, you will receive the results of this race in an event.";
$db->query("UPDATE users SET money=money-$bet WHERE userid={$userid}");
 
$q=$db->query("SELECT * FROM cars_tracks ORDER BY rand() LIMIT 1");
$t=mysql_fetch_array($q);
$stats_y=0;
$stats_y+=$m['cpcACCLV']*$m['carACC']*$t['ctrkACC'];
$stats_y+=$m['cpcHANLV']*$m['carHAN']*$t['ctrkHAN'];
$stats_y+=$m['cpcSPDLV']*$m['carSPD']*$t['ctrkSPD'];
$stats_y+=$m['cpcSHDLV']*$m['carSHD']*$t['ctrkSHD'];
 
$stats_o=0;
$stats_o+=$r['cpcACCLV']*$r['carACC']*$t['ctrkACC'];
$stats_o+=$r['cpcHANLV']*$r['carHAN']*$t['ctrkHAN'];
$stats_o+=$r['cpcSPDLV']*$r['carSPD']*$t['ctrkSPD'];
$stats_o+=$r['cpcSHDLV']*$r['carSHD']*$t['ctrkSHD'];
$stats_y*=rand(800,1200);
$stats_o*=rand(800,1200);
$notes="No-one won anything";
$db->query("UPDATE users SET cars_challs_accpt=cars_challs_accpt+1 WHERE userid=$userid");
if($stats_y > $stats_o)
{
$winner=$ir['username'];
$winnings=$bet*2;
$db->query("UPDATE users SET money=money+$winnings, cars_races_income=cars_races_income+$bet,cars_races_won=cars_races_won+1 WHERE userid={$r['chCHD']}");
$db->query("UPDATE users SET cars_races_income=cars_races_income-$bet,cars_races_lost=cars_races_lost+1 WHERE userid={$r['chCHR']}");
if($bet > 0)
{
$notes="{$r['challenged']} won \$$winnings"; 
}
if($r['chTYPE'] == "High-Stakes")
{
$db->query("UPDATE cars_playercars SET cpcPLAYER=$userid WHERE cpcID={$r['cpcID']}");
$notes="{$r['challenged']} won {$r['challenger']}\'s {$r['carNAME']}";
$db->query("UPDATE users SET cars_lost=cars_lost+1 WHERE userid={$r['chCHR']}");
$db->query("UPDATE users SET cars_won=cars_won+1,cars_owned=cars_owned+1 WHERE userid={$r['chCHD']}");
}
else if($r['chTYPE'] == "Betted")
{
$db->query("UPDATE users SET cars_races_betted=cars_races_betted+1 WHERE userid IN ({$r['chCHR']}, {$r['chCHD']})");
}
else
{
$db->query("UPDATE users SET cars_races_friendly=cars_races_friendly+1 WHERE userid IN ({$r['chCHR']}, {$r['chCHD']})");
}
}
else
{
$winner=$r['challenger'];
$winnings=$bet*2;
if($bet > 0)
{
$notes="{$r['challenger']} won \$$winnings"; 
}
$db->query("UPDATE users SET money=money+$winnings, cars_races_income=cars_races_income+$bet,cars_races_won=cars_races_won+1 WHERE userid={$r['chCHR']}");
$db->query("UPDATE users SET cars_races_income=cars_races_income-$bet,cars_races_lost=cars_races_lost+1 WHERE userid={$r['chCHD']}");
if($r['chTYPE'] == "High-Stakes")
{
$db->query("UPDATE cars_playercars SET cpcPLAYER={$r['chCHR']} WHERE cpcID={$m['cpcID']}");
$notes="{$r['challenger']} won {$r['challenged']}\'s {$m['carNAME']}";
$db->query("UPDATE users SET cars_lost=cars_lost+1 WHERE userid={$r['chCHD']}");
$db->query("UPDATE users SET cars_won=cars_won+1,cars_owned=cars_owned+1 WHERE userid={$r['chCHR']}");
}
else if($r['chTYPE'] == "Betted")
{
$db->query("UPDATE users SET cars_races_betted=cars_races_betted+1 WHERE userid IN ({$r['chCHR']}, {$r['chCHD']})");
}
else
{
$db->query("UPDATE users SET cars_races_friendly=cars_races_friendly+1 WHERE userid IN ({$r['chCHR']}, {$r['chCHD']})");
}
}
$challengercar=$r['carNAME'];
$challengedcar=$m['carNAME'];
$db->query("INSERT INTO race_results VALUES('', '{$r['chTYPE']}', '{$r['chBET']}', '{$r['challenger']}', '{$r['challenged']}', '$challengercar', '$challengedcar','$winner', '$notes')");
$i=$db->insert_id();
event_add($r['chCHR'], "Your race with {$r['challenged']} is finished. Click <a href='viewrace.php?race=$i'><font color='green'>here</font></a> to view the results.</a>");
event_add($r['chCHD'], "Your race with {$r['challenger']} is finished. Click <a href='viewrace.php?race=$i'><font color='green'>here</font></a> to view the results.</a>");
$db->query("UPDATE challenges SET chSTATUS='accepted' WHERE chID={$id}");
}
$h->endpage();
?>