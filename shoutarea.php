<?php

include_once('globals.php');

print '


<div class="usercmtpart">
<div><img src="images/usercomment_left.jpg" alt="" /></div>
<div class="usercmt_txtpart"> <br>
 <center> <font size="2"> <b> Chat </font> </center> </b>
</div>
<div><img src="images/usercomment_right.jpg" alt="" /></div>  
';

$contentFile = "chat.html";
readfile( $contentFile );
?>
