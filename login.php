<?php
function ae_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

include"facebook/facebook.php";
$facebook = new Facebook(array(
  'appId'  => '1421630661396856',
  'secret' => 'cb7a03cd3b6b482476e74a63594d17bf',
  'cookie' => true
));
print"<div id='fb-root'></div>
<script src='http://connect.facebook.net/en_US/all.js'></script>
<script>
FB.init({
appId  : '1421630661396856',
status : true, // check login status
cookie : true, // enable cookies to allow the server to access the session
xfbml  : true  // parse XFBML
});
</script>";
require "core.php";
if(file_exists("install.php"))
{
die("<p><strong><font color='#ff0000'>Warning ! </font></strong></p>
<p>For security reasons you have to delete install.php before accessing this page !</p>
<p>Please delete or rename install.php file and try again !</p>");
} 
print <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>{$set['game_name']} - $metal </title>
<meta name="keywords" content="RPG, Online Games, Online Mafia Game" />
<meta name="description" content=" {$set['game_name']} - Online Mafia Game " />
<meta name="author" content="Diamond Designs" />
<meta name="copyright" content="Copyright {$_SERVER['HTTP_HOST']} " />
<link rel="SHORTCUT ICON" href="favicon.ico" />
<link rel="stylesheet" href="css/styledd.css" type="text/css" />
<link rel='stylesheet' href='css/lightbox.css' type='text/css' media='screen' />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>

</head>
<body>
<div id="pagecontainer">
<img src="images/banner.png" alt="GTA Mobster">

<!-- Center Part Starts -->
<div class="centerpart">
<div class="column1">                    
<div class="col1_top"><img src="images/col1_top.gif" alt="" /></div>

<div class="welpart">
<div class="column1_left">
<h1>{$set['game_name']}</h1><br><h2>Massive Multiplayer Online Role Playing Game (MMORPG)</h2>
<p> {$set['game_description']}</p>

</div>
<div class="column1_right">
                        
</div>
</div>                    

<div class="col1_btm"><img src="images/col1_btm.jpg" alt="Bottom" /></div>                                
</div>

<div class="column2">
<form method="post" action="authenticate.php" name="loginform" id="loginform">

<p>Username :<span></span>Password :</p>
<div class="formpart">
<div class="uname_box"><input type="text" name="username" id="uname" /></div>
<div class="uname_box"><input type="password" name="password" id="upass" style="margin-left:7px;"/></div>
<div class="loginbtn"><input type="submit" value="Login" title="Login" /></div>            
</div>


<div class="userchoice">
<div class="server">
</div>
<div class="forgot_txt"><input type="checkbox" name="remember" value="1" > Remember &nbsp; <a href="forgot.php" title="Forgot password ?">Forgot password ?</a></div>

</div>
</form>


<div class="redbg">
<div class="red_txt1">
<style type='text/css'>
.style1 {
text-align: center;
}
</style>Total Mobsters:&nbsp;&nbsp;<span> $membs</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Online Now: <span> $online</span>&nbsp;</div> 
<table width='180' border='0' cellspacing='0' cellpadding='0'>
<tr>&nbsp;&nbsp;

<div class='style1'>
<h3><u>$gameinfo</h3></u><br>
$players $membs <br>
$mal $male <br>
$fems $fem</div> <br /></td></tr>
</table> <br/> <?php
if(!ae_detect_ie()){
?>
<center><fb:login-button
registration-url="fbregister.php" onlogin='window.location="authenticate.php?fb=1"' /></fb:login-button></center>
<?php
}      ?>
<div align="center"><a href="signup.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('signup','','images/createaccount_over.gif',1)"><img src="images/createaccount.gif" name="signup" width="254" height="90" border="0" id="signup" /></a><br />
</div> </div> </div> </div></div>        
</div>
<div class="clear">
</div>


<!--  Do Not Remove Designed & Powered By Diamond Designs without permission. 

However, if you would like to use the script without the powered by links you may do so by purchasing a Copyright removal license for a very low fee.   -->


EOF;
$IP = $IP = $_SERVER['REMOTE_ADDR'];

if(file_exists('ipbans/'.$IP))
{
die("<b><font color=red size=+1>$ipban</font></b></body></html>");
}
$year=date('Y');
include "lfooter.php";
exit;





?>


