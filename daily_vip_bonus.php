<?php


include 'globals.php';if($ir['donatordays'] == 0)

{

print "This feature is for donators only.";

$h->endpage();

exit;



}









$userid = $ir['userid'];

if ($ir['dailyvip'] != 0) {

    echo "You have already claimed your Daily Donator bonus, come back tomorrow.<br><br><a href='index.php'><b>Go Home</b></a>";

    exit($h->endpage());

} else {

    $query = "UPDATE `users` SET `dailyvip`='1' WHERE `userid`=$userid";

    $db->query($query);

    $query2 = "UPDATE `users` SET `crystals`=`crystals`+20 WHERE `userid`=$userid";///You can change the Values of this to whatever you want

    $db->query($query2);

    $query3 = "UPDATE `users` SET `money`=`money`+4000 WHERE `userid`=$userid";///You can change the Values of this to whatever you want

    $db->query($query3);

    echo "Thanks for playing!<font color=gold><br><b>You took your Daily Donator Bonus.<br> You received 20 Crystals & $4000</b></font>";///You can change the Values of this to whatever you want

    event_add($ir['userid'], "<b>You took your Daily Donator Bonus.<br> You gained 20 Crystals & $4000 <br>Come back tomorrow for more Money & Crystals!</b>");///You can change the Values of this to whatever you want

    exit($h->endpage());

}

?>
