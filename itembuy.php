<?php

include "globals.php";
$_GET['ID']= abs((int) $_GET['ID']);
$_POST['qty']= abs((int) $_POST['qty']);
if(!$_GET['ID'] || !$_POST['qty'])
{
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

Invalid use of file    <br><br>

<a href='shops.php'><font color='white'>Back To Shops</font></a>

</div></div> 


";
}
else if($_POST['qty'] <= 0)
{
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

You have been added to the delete list for trying to cheat the game.  <br><br>

<a href='shops.php'><font color='white'>Back To Shops</font></a>

</div></div> 


";
}
else
{
$q=$db->query("SELECT * FROM items WHERE itmid={$_GET['ID']}");
if(mysql_num_rows($q) == 0)
{
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

Invalid item ID

<br><br>

<a href='shops.php'><font color='white'>Back To Shops</font></a>

</div></div> 


";
}
else
{
$itemd=$db->fetch_row($q);
if($ir['money'] < $itemd['itmbuyprice']*$_POST['qty'])
{
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>


You don't have enough money to buy this item!


<br><br>

<a href='shops.php'><font color='white'>Back To Shops</font></a>

</div></div> 

";
$h->endpage();
exit;
}
if($itemd['itmbuyable'] == 0)
{
print "

<div id='mainOutput' style='text-align: center; color: red;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

This item can't be bought!

<br><br>

<a href='shops.php'><font color='white'>Back To Shops</font></a>

</div></div> 

";
$h->endpage();
exit;
}
$price=($itemd['itmbuyprice']*$_POST['qty']);
item_add($userid, $_GET['ID'], $_POST['qty']);
$db->query("UPDATE users SET money=money-$price WHERE userid=$userid");
$db->query("INSERT INTO itembuylogs VALUES ('', $userid, {$_GET['ID']}, $price, {$_POST['qty']}, unix_timestamp(), '{$ir['username']} bought {$_POST['qty']} {$itemd['itmname']}(s) for {$price}')");
print "

<div id='mainOutput' style='text-align: center; color: green;  width: 600px; border: 1px solid #222222; height: 70px;
margin: 0 auto 10px; clear: both; position: relative; left: -20px; padding: 8px'>

You bought {$_POST['qty']} {$itemd['itmname']}(s) for \$$price


<br><br>

<a href='shops.php'><font color='white'>Back To Shops</font></a>

</div></div> 

";
}
}
$h->endpage();
?>
