<?php
include "sglobals.php";
global $db,$ir,$c,$r,$userid,$h;
$_GET['st'] = ( isset($_GET['st']) AND ctype_digit($_GET['st']) ) ? $_GET['st'] : '' ;
$st = ( isset($_GET['st']) AND ctype_digit($_GET['st']) ) ? $_GET['st'] : 0;
$by = ( isset($_GET['by']) AND in_array($_GET['by'], array('stock_a','stock_b','stock_c','credits')) ) ? $_GET['by'] : 'userid';
$ord = ( isset($_GET['ord']) AND in_array($_GET['ord'], array('asc','desc')) ) ? $_GET['ord'] : 'ASC';
$a="SELECT * FROM users WHERE userid=$userid";
$aapl=$db->query($a);
if(mysql_num_rows($aapl))
	{
		$row = mysql_fetch_array($aapl);
		$aapl = $row['aapl'];
	}
	else
	{
		$aapl = '0';
	}
$b="SELECT * FROM users WHERE userid=$userid";
$bbry=$db->query($b);
if(mysql_num_rows($bbry))
	{
		$row = mysql_fetch_array($bbry);
		$bbry = $row['bbry'];
	}
	else
	{
		$bbry = '0';
	}

$c="SELECT * FROM users WHERE userid=$userid";
$csco=$db->query($c);
if(mysql_num_rows($csco))
	{
		$row = mysql_fetch_array($csco);
		$csco = $row['csco'];
	}
	else
	{
		$csco = '0';
	}
	
$d="SELECT * FROM users WHERE userid=$userid";
$dell=$db->query($d);
if(mysql_num_rows($dell))
	{
		$row = mysql_fetch_array($dell);
		$dell = $row['dell'];
	}
	else
	{
		$dell = '0';
	}
	
$f="SELECT * FROM users WHERE userid=$userid";
$fb=$db->query($f);
if(mysql_num_rows($fb))
	{
		$row = mysql_fetch_array($fb);
		$fb = $row['fb'];
	}
	else
	{
		$fb = '0';
	}

$g="SELECT * FROM users WHERE userid=$userid";
$goog=$db->query($g);
if(mysql_num_rows($goog))
	{
		$row = mysql_fetch_array($goog);
		$goog = $row['goog'];
	}
	else
	{
		$goog = '0';
	}

$h="SELECT * FROM users WHERE userid=$userid";
$himx=$db->query($h);
if(mysql_num_rows($himx))
	{
		$row = mysql_fetch_array($himx);
		$himx = $row['himx'];
	}
	else
	{
		$himx = '0';
	}

$i="SELECT * FROM users WHERE userid=$userid";
$intc=$db->query($i);
if(mysql_num_rows($intc))
	{
		$row = mysql_fetch_array($intc);
		$intc = $row['intc'];
	}
	else
	{
		$intc = '0';
	}

$l="SELECT * FROM users WHERE userid=$userid";
$lulu=$db->query($l);
if(mysql_num_rows($lulu))
	{
		$row = mysql_fetch_array($lulu);
		$lulu = $row['lulu'];
	}
	else
	{
		$lulu = '0';
	}

$m="SELECT * FROM users WHERE userid=$userid";
$msft=$db->query($m);
if(mysql_num_rows($msft))
	{
		$row = mysql_fetch_array($msft);
		$msft = $row['msft'];
	}
	else
	{
		$msft = '0';
	}

$q="SELECT * FROM users WHERE userid=$userid";
$qcom=$db->query($q);
if(mysql_num_rows($qcom))
	{
		$row = mysql_fetch_array($qcom);
		$qcom = $row['qcom'];
	}
	else
	{
		$qcom = '0';
	}

$s="SELECT * FROM users WHERE userid=$userid";
$siri=$db->query($s);
if(mysql_num_rows($siri))
	{
		$row = mysql_fetch_array($siri);
		$siri = $row['siri'];
	}
	else
	{
		$siri = '0';
	}

$v="SELECT * FROM users WHERE userid=$userid";
$vod=$db->query($v);
if(mysql_num_rows($vod))
	{
		$row = mysql_fetch_array($vod);
		$vod = $row['vod'];
	}
	else
	{
		$vod = '0';
	}

$w="SELECT * FROM users WHERE userid=$userid";
$wmt=$db->query($w);
if(mysql_num_rows($wmt))
	{
		$row = mysql_fetch_array($wmt);
		$wmt = $row['wmt'];
	}
	else
	{
		$wmt = '0';
	}

$y="SELECT * FROM users WHERE userid=$userid";
$yhoo=$db->query($y);
if(mysql_num_rows($yhoo))
	{
		$row = mysql_fetch_array($yhoo);
		$yhoo = $row['yhoo'];
	}
	else
	{
		$yhoo = '0';
	}

$z="SELECT * FROM users WHERE userid=$userid";
$znga=$db->query($z);
if(mysql_num_rows($znga))
	{
		$row = mysql_fetch_array($znga);
		$znga = $row['znga'];
	}
	else
	{
		$znga = '0';
	}
	
$id="SELECT * FROM users WHERE userid=$userid";
$user=$db->query($id);
if(mysql_num_rows($user))
	{
		$row = mysql_fetch_array($user);
		$user = $row['userid'];
	}
	else
	{
		$user = '0';
	}
$cred="SELECT credits FROM users WHERE userid=$userid";
$credits=$db->query($cred);
if(mysql_num_rows($credits))
	{
		$row = mysql_fetch_array($credits);
		$credits = $row['credits'];
	}
	else
	{
		$credits = '0';
	}
$q=$db->query("SELECT aapl FROM users");
while($r=$db->fetch_row($q))
{
$total+=$r['aapl'];
}
$q=$db->query("SELECT bbry FROM users");
while($r=$db->fetch_row($q))
{
$totalb+=$r['bbry'];
}
$q=$db->query("SELECT csco FROM users");
while($r=$db->fetch_row($q))
{
$totalc+=$r['csco'];
}
$q=$db->query("SELECT dell FROM users");
while($r=$db->fetch_row($q))
{
$totald+=$r['dell'];
}
$q=$db->query("SELECT fb FROM users");
while($r=$db->fetch_row($q))
{
$totalf+=$r['fb'];
}
$q=$db->query("SELECT goog FROM users");
while($r=$db->fetch_row($q))
{
$totalg+=$r['goog'];
}
$q=$db->query("SELECT himx FROM users");
while($r=$db->fetch_row($q))
{
$totalh+=$r['himx'];
}
$q=$db->query("SELECT intc FROM users");
while($r=$db->fetch_row($q))
{
$totali+=$r['intc'];
}
$q=$db->query("SELECT lulu FROM users");
while($r=$db->fetch_row($q))
{
$totall+=$r['lulu'];
}
$q=$db->query("SELECT msft FROM users");
while($r=$db->fetch_row($q))
{
$totalm+=$r['msft'];
}
$q=$db->query("SELECT qcom FROM users");
while($r=$db->fetch_row($q))
{
$totalq+=$r['qcom'];
}
$q=$db->query("SELECT siri FROM users");
while($r=$db->fetch_row($q))
{
$totals+=$r['siri'];
}
$q=$db->query("SELECT vod FROM users");
while($r=$db->fetch_row($q))
{
$totalv+=$r['vod'];
}
$q=$db->query("SELECT wmt FROM users");
while($r=$db->fetch_row($q))
{
$totalw+=$r['wmt'];
}
$q=$db->query("SELECT yhoo FROM users");
while($r=$db->fetch_row($q))
{
$totaly+=$r['yhoo'];
}
$q=$db->query("SELECT znga FROM users");
while($r=$db->fetch_row($q))
{
$totalz+=$r['znga'];
}
$q=$db->query("SELECT credits FROM users");
while($r=$db->fetch_row($q))
{
$totalcr+=$r['credits'];
}
print "
<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'>Tracker</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>";
$q=$db->query("SELECT u.*,g.* FROM users u LEFT JOIN gangs g ON u.gang=g.gangID ORDER BY $by $ord LIMIT $st,100");

print "Track the levels of stocks and credits in the game.<br>Hover over each stock letter to get the full stock name.<br><br>
<table width=100% cellspacing=1 class='table' border=1><tr><th><acronym title='Username'>User</acronym></th><th><acronym title='Apple (AAPL)'>A</acronym></th><th><acronym title='BlackBerry (BBRY)'>B</acronym></th><th><acronym title='Cisco (CSCO)'>C</acronym></th><th><acronym title='Dell (DELL)'>D</acronym></th><th><acronym title='Facebook (FB)'>F</acronym></th><th><acronym title='Google (GOOG)'>G</acronym></th><th><acronym title='Himax (HIMX)'>H</acronym></th><th><acronym title='Intel (INTC)'>I</acronym></th><th><acronym title='Lululemon (LULU)'>L</acronym></th><th><acronym title='Microsoft (MSFT)'>M</acronym></th><th><acronym title='QUALCOMM (QCOM)'>Q</acronym></th><th><acronym title='Sirius (SIRI)'>S</acronym></th><th><acronym title='Vodafone (VOD)'>V</acronym></th><th><acronym title='Wal-Mart (WMT)'>W</acronym></th><th><acronym title='Yahoo (YHOO)'>Y</acronym></th><th><acronym title='Zynga (ZNGA)'>Z</acronym></th><th><acronym title='Credits'>Creds</acronym></th></tr>";
while($r=$db->fetch_row($q))
{
$d="";
if($r['donatordays']) { $r['username'] = "{$r['username']}";$d=""; }
print "<tr><td><a href='viewuser.php?u={$r['userid']}'> {$r['username']} [{$r['userid']}]</a></td> <td>{$r['aapl']}</td> <td>{$r['bbry']}</td> <td>{$r['csco']}</td><td>{$r['dell']}</td><td>{$r['fb']}</td><td>{$r['goog']}</td><td>{$r['himx']}</td><td>{$r['intc']}</td><td>{$r['lulu']}</td><td>{$r['msft']}</td><td>{$r['qcom']}</td><td>{$r['siri']}</td><td>{$r['vod']}</td><td>{$r['wmt']}</td><td>{$r['yhoo']}</td><td>{$r['znga']}</td><td>{$r['credits']}"; print "</td></tr>";
}
print"
 
<tr><td>Total </td><td>$total</td><td>$totalb</td><td>$totalc</td><td>$totald</td><td>$totalf</td><td>$totalg</td><td>$totalh</td><td>$totali</td><td>$totall</td><td>$totalm</td><td>$totalq</td><td>$totals</td><td>$totalv</td><td>$totalw</td><td>$totaly</td><td>$totalz</td><td>$totalcr</td></tr>
<tr><th colspan=18></th></tr>
</table> </div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
$cnt=mysql_query("SELECT userid FROM users",$c);
$membs=mysql_num_rows($cnt);
exit;
$h->endpage();
?>