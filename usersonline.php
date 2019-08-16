<?php
require "globals.php";
global $db,$ir,$c,$r,$userid,$h;
 

if ($_GET['time'])
{
  $time=$_GET['time'];
}
else
{
  $time=30;
}
$cn=0;
$lk=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-1440*60");
$aa=mysql_num_rows($lk);
$ll=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-60*60");
$ab=mysql_num_rows($ll);
$lm=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-45*60");
$ac=mysql_num_rows($lm);
$ln=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-30*60");
$ad=mysql_num_rows($ln);
$lo=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-15*60");
$ae=mysql_num_rows($lo);
$he=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-1*60");
$hu=mysql_num_rows($he);
$q=mysql_query("SELECT * FROM users WHERE laston>unix_timestamp()-{$time}*60 ORDER BY laston DESC");
  
?>
 
  
 
<table border="0" class=table width="100%" cellpading="0" cellspacing="0">
  <tr>    
     <td class=table>
      <center><h3>Statistics</h3></center>
      <center>Users online in the last minute: <b><?php print"{$hu}"; ?></b><br>
      Users online in the last 15 minutes: <b><?php print"{$ae}"; ?></b><br>
      Users online in the last 30 minutes: <b><?php print"{$ad}"; ?></b><br>
      Users online in the last 45 minutes: <b><?php print"{$ac}"; ?></b><br>
      Users online in the last hour: <b><?php print"{$ab}"; ?></b><br>
      Users online in the last day: <b><?php print"{$aa}"; ?></b></center>
    </td>
    <td class=table>
      <center><h3>Time Selecter</h3></center>
      <center><a href='usersonline.php?time=1'>(1 Min)</a>
      <a href='usersonline.php?time=15'>(15 Mins)</a>
      <a href='usersonline.php?time=30'>(30 Mins)</a>
      <a href='usersonline.php?time=45'>(45 Mins)</a>
      <a href='usersonline.php?time=60'>(60 Mins)</a>
      <a href='usersonline.php?time=1440'>(24 Hours)</a> </center>
    </td>
  
    <td class=table>
      <center><h3>Legend</h3></center>
<center>Admins = <font color =lime>Lime</font><br>Secretaries = <font color =blue>Blue</font><br>
Donators = <img src='donator.gif' alt='Donator' /></center>
 
    </td>
  
  <tr>
</table>
  
<table border="0" class=table width="100%" cellpading="0" cellspacing="0">
    <tr>
        
        <td class=table>
      <center><u><b>Level</b></u></center>
        </td>
        <td class=table>
          <center><u> <b>User</b></u></center>
        </td>
        <td class=table>
        <center><u><b> Money</b></u></center>
        </td>
  
        <td class=table>
          <center><u>  <b>Last Action</b></u></center>
        </td>
  
        <td class=table>
        <center><u>  <b> Time Online</b></u></center>

        <td class=table>
<center><u><b>User Actions</b></u></center>
        </td>
    </tr><hr />
<?php
            while($r=mysql_fetch_assoc($q))
            {
              $la=time()-$r['laston'];
              $unit="secs";
              if($la >= 60)
              {
                $la=(int) ($la/60);
                $unit="mins";
              }
              if($la >= 60)
              {
                $la=(int) ($la/60);
                $unit="hours";
                if($la >= 24)
                {
                  $la=(int) ($la/24);
                  $unit="days";
                }
              }
$money=money_formatter($r['money']);
              $cn++;
  
  
  
 
   
$name=$r['username'];
 
 
 
 
$id=$r['userid'];
  
$gangtag=$r['yourgangPREF'];
  
if($r['donatordays'] > 0)
{
$donator="<img src='donator.gif' alt='Donator: {$r['donatordays']} Days Left' title='Donator: {$r['donatordays']} Days Left' />";
}
if($r['donatordays'] == 0)
{
$donator="";
}

if($r['donatordays'] > 0)
{
$donator="<img src='donator.gif' alt='Donator: {$r['donatordays']} Days Left' title='Donator: {$r['donatordays']} Days Left' />";
}
if($r['donatordays'] == 0)
{
$donator="";
}
 
if($r['user_level']== 2)
{
$name="<font color=lime>$name</font>";
}
if($r['user_level']== 3)
{
$name="<font color=blue>$name</font>";
}
if($r['user_level']== 4)
{
$name="<font color=purple>$name</font>";
}
if($r['user_level']== 5)
{
$name="<font color=cyan>$name</font>";
}
if($r['user_level']== 1)
{
$name="<font color=white>$name</font>";
}
 
 
 
$r['username']="<a href='viewuser.php?u={$r['userid']}'>$name [$id]</a> $donator";
 
              print "<tr>
                       <td class=table>
                     <center>{$r['level']}</center>
                       </td>
                       <td class=table>
  <center>{$r['username']}</center>
 
 
                       </td>
                       <td class=table bgcolor='#000000'>
 <center>$money</center>
                       </td>
";
 
print "
                       <td class=table>
                       <center>($la $unit)</center>
                       </td>
                       <td class=table>";
              $lb=time()-$r['last_login'];
              $units="secs";
              if($lb >= 60)
              {
                $lb=(int) ($lb/60);
                $units="mins";
              }
              if($lb >= 60)
              {
                $lb=(int) ($lb/60);
                $units="hours";
                if($lb >= 24)
                {
                  $lb=(int) ($lb/24);
                  $units="days";
                }
              }
              if($r['laston'] <= time()-60*60)
              {
                $lb="Offline";
                $units=" ";
              }
               print"<center>{$lb} {$units}</center></td>
  
 
  
";
 
 
 
              if($r['hospital'] > 0) {
              print"<td><center> 
 
[<a href='mailbox.php?action=compose&ID={$r['userid']}'><font color='{$r['colour']}'>Mail</font></a>]
               (In Hospital)</center> </td>";
              }
               if($r['jail'] > 0) {
              print"<td><center>
 
[<a href='mailbox.php?action=compose&ID={$r['userid']}'><font color='{$r['colour']}'>Mail</font></a>]
                (In Jail) </center></td>";
              }
               if($r['hospital'] == 0 AND $r['jail'] == 0) {
              print"<td><center>
               
 
[<a href='attack.php?ID={$r['userid']}'><font color='{$r['colour']}'>Attack</font></a>]
[<a href='mailbox.php?action=compose&ID={$r['userid']}'><font color='{$r['colour']}'>Mail</font></a>]
 
  
</center></td>";
 
 
}
         print"</tr>";
       }
   
?>
    </td>
  </tr>
</table>
<?php
$h->endpage();
?>