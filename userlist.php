<?php

include "globals.php";
$_GET['st'] = ( isset($_GET['st']) AND ctype_digit($_GET['st']) ) ? $_GET['st'] : '' ;
$st = ( isset($_GET['st']) AND ctype_digit($_GET['st']) ) ? $_GET['st'] : 0;
$by = ( isset($_GET['by']) AND in_array($_GET['by'], array('userid','username','level','money')) ) ? $_GET['by'] : 'userid';
$ord = ( isset($_GET['ord']) AND in_array($_GET['ord'], array('asc','desc')) ) ? $_GET['ord'] : 'ASC';
print "

<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'> Users List</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>

";
$cnt=mysql_query("SELECT userid FROM users",$c);
$membs=mysql_num_rows($cnt);
$pages=(int) ($membs/100)+1;
if($membs % 100 == 0)
{
$pages--;
}
print "Pages: ";
for($i=1;$i <= $pages;$i++)
{
$stl=($i-1)*100;
print "<a href='userlist.php?st=$stl&by=$by&ord=$ord'>$i</a>&nbsp;";
}
print "<br />
Order By: <a href='userlist.php?st=$st&by=userid&ord=$ord'>User ID</a>&nbsp;| <a href='userlist.php?st=$st&by=username&ord=$ord'>Username</a>&nbsp;| <a href='userlist.php?st=$st&by=level&ord=$ord'>Level</a>&nbsp;| <a href='userlist.php?st=$st&by=money&ord=$ord'>Money</a><br />
<a href='userlist.php?st=$st&by=$by&ord=asc'>Ascending</a>&nbsp;| <a href='userlist.php?st=$st&by=$by&ord=desc'>Descending</a><br /><br />";
$q=$db->query("SELECT u.*,g.* FROM users u LEFT JOIN gangs g ON u.gang=g.gangID ORDER BY $by $ord LIMIT $st,100");
$no1=$st+1;
$no2=$st+100;
print "Showing users $no1 to $no2 by order of $by $ord.
<table width=75% cellspacing=1 class='table'><tr style='background:gray'><th>ID</th><th>Name</th><th>Money</th><th>Level</th><th>Gender</th><th>Online</th></tr>";
while($r=$db->fetch_row($q))
{
$d="";
if($r['donatordays']) { $r['username'] = "{$r['username']}";$d=""; }
print "<tr><td>{$r['userid']}</td><td><a href='viewuser.php?u={$r['userid']}'>";
if($r['gangPREF']) { print" [{$r['gangPREF']}]"; } { print " {$r['username']} $d</a></td><td>\${$r['money']}</td><td>{$r['level']}</td><td>{$r['gender']}</td><td>"; }
if($r['laston'] >= time() -30*60)
{
print "<font color=green><b>Online</b></font>";
}
else
{
print "<font color=red><b>Offline</b></font>";
}
print "</td></tr>";
}
print "</table><br>";
$cnt=mysql_query("SELECT userid FROM users",$c);
$membs=mysql_num_rows($cnt);
$pages=(int) ($membs/100)+1;
if($membs % 100 == 0)
{
$pages--;
}
print "Pages: ";
for($i=1;$i <= $pages;$i++)
{
$stl=($i-1)*100;
print "<a href='userlist.php?st=$stl&by=$by&ord=$ord'>$i</a>&nbsp;";
}
print "</div></div></div></div></div></div></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div>";
$h->endpage();
?>
