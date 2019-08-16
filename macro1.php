<?php

include "globals.php";
if(!$set['validate_on'] || $ir['verified'])
{
die("WTF are you doing on this page? Go home dickhead.");
}
$ref=$_GET['refer'];
unset($_SESSION['captcha']);
$chars="0123456789";
  $len=strlen($chars);
  $_SESSION['captcha']="";
  for($i=0;$i<1;$i++)
  $_SESSION['captcha'].=$chars[rand(0, $len - 1)];
print "

<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'> Validation</h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>

<br/><br/><b>Enter the number you see in the image into the box below.</b><br/> <br/> <form action='macro2.php' method='post'>";
if($_GET['code'])
{
print "<font color='red'><b>Invalid code or blank</b></font><br/><br />";
}
print "


<img src='captcha_verify.php'  width='150' height='150' alt='CAPTCHA - refresh if invisible' /><br />
Number: <input type='text' STYLE='color: black;  background-color: white;' name='captcha' /><br />
<input type='hidden' name='refer' value='{$_GET['refer']}' />
<input type='submit' STYLE='color: black;  background-color: white;' value='Verify' /></form></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
$h->endpage();
?>

