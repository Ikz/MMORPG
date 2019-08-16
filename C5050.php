<?php
include_once "globals.php";
$minbet=1;
$maxg=5;
echo "<h1>50 / 50 Crystals Game</h1>";
function add_game()
{
  global $ir,$c,$userid, $db, $minbet, $maxg;
  if(!isset($_POST['amt']))
  {
    echo "<br><h3>Adding a game</h3><br>
      <form action='C5050.php?add=1' method='post'>
      Amount of Crystals: <input type='text' STYLE='color: black;  background-color: white;' name='amt' size=10 maxlength=10>
      <input type='submit' STYLE='color: black;  background-color: white;' value='Add!'></form>($maxg games max per user)<br><a href='C5050.php'>Back</a>";
  }
  else
  {
    $_POST['amt'] = abs((int) $_POST['amt']);
    if($_POST['amt'] < $minbet){echo "The minimum bet is $minbet<a href='C5050.php?add=1'>Back</a>";exit;}
    $theckcount=mysql_query("SELECT logID FROM tchance WHERE userID={$ir['userid']} and active=1");
    if(mysql_num_rows($theckcount) > ($maxg-1)){echo "There is a maximum of $maxg games per user.<a href='C5050.php'>Back</a>";exit;}
    if($ir['crystals'] < $_POST['amt']){echo "You cannot afford that amount.<a href='C5050.php'>Back</a>";exit;}
    mysql_query("UPDATE users SET crystals = crystals - {$_POST['amt']} WHERE userid = {$ir['userid']}");
    $ir['crystals']=$ir['crystals'] - $_POST['amt'];
    mysql_query("INSERT INTO tchance VALUES ('', {$ir['userid']}, {$_POST['amt']}, 1)");
    echo "Your game has been set. Good Luck.<br><a href='C5050.php'>Back</a>";
  }
}
function view_games()
{
  global $ir,$c,$userid, $db, $maxg;
  $q=mysql_query("SELECT t.*, u.username FROM tchance t left join users u on u.userid = t.userID WHERE t.active = 1 ORDER BY t.logID ASC");
  echo "<br><a href='C5050.php?add=1'>Add Game</a><br>Table of users awaiting a challenge<table class=table width='75%' border='1'><tr background='header.jpg'><th>User</th><th>Cost</th><th>Prize</th><th>Challenge</th><th>Cancel</th></tr>";
  if(mysql_num_rows($q) < 1){echo "<tr background='images/background.gif'><td colspan=5>There are currently no challenges</td></tr>";}
  while($r=mysql_fetch_array($q))
  {
    echo "<tr background='images/background.gif'><td align='center'><a href='viewuser.php?u={$r['userID']}'>{$r['username']}</a> [{$r['userID']}]</td><td align='center'>{$r['amount']} Crystals</td><td align='Center'>".number_format(($r['amount'])*2)." Crystals</td><td align='center'><a href='C5050.php?chal={$r['logID']}'>Challenge</a></td><td align='center'>";
    if($ir['userid']==$r['userID']){echo "<a href='C5050.php?cancel={$r['logID']}'>Cancel</a>";}
    echo "</td></tr>";
  }
  echo "</table>";
}
function dogame()
{
  global $ir,$c,$userid, $db;
 
  $_GET['chal'] = abs((int) $_GET['chal']);
  $q=mysql_query("SELECT t.*, u.username from tchance t LEFT JOIN users u ON t.userID = u.userid Where t.logID={$_GET['chal']} AND t.active = 1 LIMIT 1");
  if(mysql_num_rows($q) > 0)
  {
    $r=mysql_fetch_array($q);
 
    if($ir['crystals'] < $r['amount']){echo "You cannot afford the challenge amount.<br><a href='C5050.php'>Back</a>";exit;}
    if($ir['userid'] == $r['userID']){echo "You cannot accept your own challenge.<br><a href='C5050.php'>Back</a>";exit;}
 
    if(rand(1,2) == 1)
    {
      $winner=$r['userID']; $loser=$ir['userid'];
      $winnername=$r['username'];
      $losername=$ir['username'];
      $tstring="Sorry, you Lost. Better luck next time.<a href='C5050.php'>Back</a>";
      mysql_query("UPDATE users SET crystals = crystals - {$r['amount']} WHERE userid={$ir['userid']}");
      mysql_query("UPDATE users SET crystals = crystals + ({$r['amount']} * 2) WHERE userid={$r['userID']}");
    }
    else
    {
      $winner=$ir['userid']; $loser=$r['userID'];
      $winnername=$ir['username'];
      $losername=$r['username'];
      $tstring="You Won! Congratulations! You Won ".number_format(($r['amount']) * 2)." Crystals. <a href='C5050.php'>Back</a>";
      mysql_query("UPDATE users SET crystals = crystals + {$r['amount']} WHERE userid={$ir['userid']}");
    }
    event_add($winner, "You Won the 50/50 Crystals against <a href='viewuser.php?u={$loser}'>{$losername}</a> and collected ".number_format(($r['amount']) * 2)." Crystals.", $c);
    event_add($loser, "You Lost the 50/50 Crystals against <a href='viewuser.php?u={$winner}'>{$winnername}</a>.", $c);
 
    mysql_query("UPDATE tchance SET active = 0 WHERE logID={$_GET['chal']}");
 
    echo $tstring;
  }
  else
  {
    echo "This game has either been cancelled or someone played before you got the 50/50.<a href='C5050.php'>Back</a>"; exit;
  }
}
function cancel()
{
  global $ir,$c,$userid, $db;
  $_GET['cancel'] = abs((int) $_GET['cancel']);
  $q=mysql_query("SELECT * from tchance where logID={$_GET['cancel']} AND userID=$userid AND active = 1");
  if(mysql_num_rows($q) > 0)
  {
    $r=mysql_fetch_array($q);
    mysql_query("UPDATE users SET crystals = crystals + {$r['amount']} WHERE userid = {$ir['userid']}");
    $ir['crystals']=$ir['crystals'] + $r['amount'];
    mysql_query("UPDATE tchance SET active = -1 WHERE logID = {$_GET['cancel']}");
 
    echo "The game has been cancelled, and your Crystals have been returned.<a href='C5050.php'>Back</a>";
  }
  else
  {
    echo "This game has already been canceled, does not exist, or someone already played.<a href='C5050.php'>Back</a>";
  }
 
}
if(isset($_GET['cancel'])){cancel();}
elseif(isset($_GET['chal'])){dogame();}
elseif(isset($_GET['add'])){add_game();}
else{view_games();}
$h->endpage();
?>