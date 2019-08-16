<?php
 include "globals.php";
$id=abs((int) $_GET['id']);



if(!$id) { die("Invalid Usage"); }
$q=mysql_query("SELECT ch.*, cp.*, ct.*, u1.username as challenger, u2.username as challenged FROM challenges ch LEFT JOIN cars_playercars cp ON ch.chCHRCAR=cp.cpcID LEFT JOIN cars_types ct ON ct.carID=cp.cpcCAR LEFT JOIN users u1 ON ch.chCHR=u1.userid LEFT JOIN users u2 ON ch.chCHD=u2.userid WHERE ch.chID={$id} AND ch.chCHR=$userid AND ch.chSTATUS='open'", $c) or die(mysql_error());
if(mysql_num_rows($q) == 0) { die("Invalid Challenge"); }
$r=mysql_fetch_array($q);
mysql_query("UPDATE users SET money=money+{$r['chBET']} WHERE userid={$r['chCHR']}", $c);
event_add($r['chCHR'],"You cancelled the challenge to {$r['challenged']}", $c);
event_add($r['chCHD'],"{$ir['username']} cancelled their challenge to you.", $c);
mysql_query("UPDATE challenges SET chSTATUS='cancelled' WHERE chID={$id}", $c);
print "Challenge cancelled.";
$h->endpage();
?>