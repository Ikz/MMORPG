<?php


include "globals.php";
require "bbcodestyle.css";
require "bbcode.php";

if($ir['mailban'])
{
die("<font color=red><h3>ERROR!</h3>
You have been mail banned for {$ir['mailban']} days.<br />
<br />
<b>Reason: {$ir['mb_reason']}</font></b>");
}
$_GET['ID'] = abs((int) $_GET['ID']);

print "

<div class='generalinfo_txt'>
<div><img src='images/info_left.jpg' alt='' /></div>
<div class='info_mid'><h2 style='padding-top:10px;'> Mail </h2></div>
<div><img src='images/info_right.jpg' alt='' /></div> </div>
<div class='generalinfo_simple'><br> <br><br>

<table width=85% class='table' cellspacing='1'><tr> <td align=center><a href='mailbox.php?action=inbox'><img src='images/indox.gif' title='Inbox'></a></td>  <td align=center><a href='mailbox.php?action=outbox'><img src='images/sent.gif' title='Sent Messages'></a></td> <td align=center><a href='mailbox.php?action=compose'><img src='images/compose.gif' title='Compose'></a></td> <td align=center><a href='mailbox.php?action=delall'><img src='images/deleteall.gif' title='Delete All Messages'></a></td> <td align=center><a href='mailbox.php?action=archive'><img src='images/archiveall.gif' title='Archive All Messages'></a></td> <td align=center><a href='contactlist.php'><img src='images/contacts.gif' title='Contacts List'></a></td>  </tr> </table><br />



";
switch($_GET['action'])
{
case 'inbox':
mail_inbox();
break;

case 'outbox':
mail_outbox();
break;

case 'compose':
mail_compose();
break;

case 'delb': 
delb(); 
break;

case 'send':
mail_send();
break;

case 'delall':
mail_delall();
break;

case 'delall2':
mail_delall2();
break;

case 'archive':
mail_archive();
break;

default:
mail_inbox();
break;
}
function mail_inbox()
{
global $db,$ir,$c,$userid,$h;
print <<<OUT
Only the last 25 messages sent to you are visible.<br><br />
<table width=75% class='table' border='1' bordercolor='#000000' cellspacing=1>
<tr>
<td class="h" width="30%">From</td>
<td class="h" width="70%">Subject/Message</td>
</tr>
OUT;
$q=$db->query("SELECT m.*,u.* FROM mail m LEFT JOIN users u ON m.mail_from=u.userid WHERE m.mail_to=$userid ORDER BY mail_time DESC LIMIT 25");
while($r=$db->fetch_row($q))
{
$sent=date('F j, Y, g:i:s A',$r['mail_time']);
print "<tr><td>";
if($r['userid'])
{
print "<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]";
}
else
{
print "SYSTEM";
}
$fm=urlencode($r['mail_text']); 
print <<<EOF
</td>
<td>{$r['mail_subject']}</td>
</tr>
<tr>
<td>Sent at: {$sent}<br /><a href='mailbox.php?action=compose&ID={$r['userid']}'>Reply</a>
<br />
<a href='?action=delb&ID={$r['mail_id']}'>Delete</a>
<br />
</td>
<td>{$r['mail_text']}</td>
</tr>
EOF;
}
if($ir['new_mail'] > 0)
{
$db->query("UPDATE mail SET mail_read=1 WHERE mail_to=$userid");
$db->query("UPDATE users SET new_mail=0 WHERE userid=$userid");
}
echo "</table></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
}
function mail_outbox()
{
global $db,$ir,$c,$userid,$h; 
print "Only the last 25 messages you have sent are visible.<br><br /><table width=75% class='table' border='1' bordercolor='#000000' cellspacing=1><tr style='background:gray'>
<td class='h' width='30%'>To</td>
<td class='h' width='70%'>Subject/Message</td>
</tr>";
$q=$db->query("SELECT m.*,u.* FROM mail m LEFT JOIN users u ON m.mail_to=u.userid WHERE m.mail_from=$userid ORDER BY mail_time DESC LIMIT 25");
while($r=$db->fetch_row($q))
{
$sent=date('F j, Y, g:i:s A',$r['mail_time']);
print "<tr><td><a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]</td><td>{$r['mail_subject']}</td></tr><tr><td>Sent at: $sent<br /></td><td>{$r['mail_text']}</td></tr>";
}

echo "</table></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";

} 


function mail_compose()
{
global $ir,$c,$userid,$h;
echo <<< EOF
<script type="text/javascript">
function insert(el,ins) {
if (el.setSelectionRange){
el.value = el.value.substring(0,el.selectionStart) + ins + el.value.substring(el.selectionStart,el.selectionEnd) +

el.value.substring(el.selectionEnd,el.value.length);
}
else if (document.selection && document.selection.createRange) {
el.focus();
var range = document.selection.createRange();
range.text = ins + range.text;
}
}
</script>
<center><form action='mailbox.php?action=send' method='post'>
<table width=75% border=0> <tr>
<td><center>ID to send to:</center></td><td><center><input type='text' STYLE='color: black;  background-color: white;' name='userid' value='{$_GET['ID']}'/></center></td></tr><tr>
<td><center>Subject:</center></td> <td><center><input type='text' STYLE='color: black;  background-color: white;' name='subject'/></center></td></tr><tr>
<td>Message:</td>
<td><center><input type='submit' STYLE='color: black;  background-color: white;' value='Bold Text' onclick="insert(this.form.message,'[b][/b]'); return false;"> <input type='submit' STYLE='color: black;  background-color: white;' value='Italic Text' onclick="insert(this.form.message,'[i][/i]'); return false;"> <input type='submit' STYLE='color: black;  background-color: white;' value='Underline Text' onclick="insert(this.form.message,'[u][/u]'); return false;"> <input type='submit' STYLE='color: black;  background-color: white;' value='Left Align' onclick="insert(this.form.message,'[align=left][/align]'); return false;"> <input type='submit' STYLE='color: black;  background-color: white;' value='Right Align' onclick="insert(this.form.message,'[align=right][/align]'); return false;"></center><center><textarea name='message' rows='10' cols='80' style='color: black; background-color: white'></textarea></center><br /><center><input type="image" src="smilies/smile.png" alt="Smile" title="Smile" onclick="insert(this.form.message,':)'); return false;" />
<input type="image" src="smilies/wink.png" alt="Wink" title="Wink" onclick="insert(this.form.message,';)'); return false;" />
<input type="image" src="smilies/eek.png" alt="Surprised" title="Surprised" onclick="insert(this.form.message,':o'); return false;" />
<input type="image" src="smilies/biggrin.png" alt="Cheesy Grin" title="Cheesy Grin" onclick="insert(this.form.message,':D'); return false;" />
<input type="image" src="smilies/confused.png" alt="Confused" title="Confused" onclick="insert(this.form.message,':s'); return false;" />
<input type="image" src="smilies/frown.png" alt="Sad" title="Sad" onclick="insert(this.form.message,':('); return false;" />
<input type="image" src="smilies/mad.png" alt="Angry" title="Angry" onclick="insert(this.form.message,':red'); return false;" />
<input type="image" src="smilies/party.png" alt="Party" title="Party" onclick="insert(this.form.message,'=-P'); return false;" />
<input type="image" src="smilies/redface.png" alt="Embarrassed" title="Embarrassed" onclick="insert(this.form.message,':$'); return false;" />
<input type="image" src="smilies/lips-sealed.png" alt="Lips Sealed" title="Lips Sealed" onclick="insert(this.form.message,':x'); return false;" />
<input type="image" src="smilies/sick.png" alt="Sick" title="Sick" onclick="insert(this.form.message,':green'); return false;" />
<input type="image" src="smilies/straight-face.png" alt="Bored" title="Bored" onclick="insert(this.form.message,':|'); return false;" />
<input type="image" src="smilies/crying.png" alt="Crying" title="Crying" onclick="insert(this.form.message,':crying'); return false;" />
<input type="image" src="smilies/eyelashes.png" alt="Eyelashes" title="Eyelashes" onclick="insert(this.form.message,':eyelashes'); return false;" />
<input type="image" src="smilies/devil.png" alt="Devil" title="Devil" onclick="insert(this.form.message,':devil'); return false;" />
<input type="image" src="smilies/cool.png" alt="Cool" title="Cool" onclick="insert(this.form.message,'B-)'); return false;" />
<input type="image" src="smilies/angel.png" alt="Angel" title="Angel" onclick="insert(this.form.message,':angel'); return false;" />
<input type="image" src="smilies/big-hug.png" alt="Big Hug" title="Big Hug" onclick="insert(this.form.message,'({})'); return false;" />
<input type="image" src="smilies/cant-watch.png" alt="Can't Watch" title="Can't Watch" onclick="insert(this.form.message,'X_X'); return false;" />
<input type="image" src="smilies/dancing.png" alt="Dancing" title="Dancing" onclick="insert(this.form.message,':dancing'); return false;" />
<input type="image" src="smilies/huh.png" alt="Huh" title="Huh" onclick="insert(this.form.message,':/'); return false;" />
<input type="image" src="smilies/kiss.png" alt="Kiss" title="Kiss" onclick="insert(this.form.message,':kiss'); return false;" />
<input type="image" src="smilies/laughing.png" alt="Laughing" title="Laughing" onclick="insert(this.form.message,'=D'); return false;" />
<input type="image" src="smilies/not-interested.png" alt="Not Interested" title="Not Interested" onclick="insert(this.form.message,'3-|'); return false;" />
<input type="image" src="smilies/rotfl.png" alt="ROTFLl" title="ROTFL" onclick="insert(this.form.message,'=))'); return false;" />
<input type="image" src="smilies/tongue.png" alt="Tongue" title="Tongue" onclick="insert(this.form.message,':P'); return false;" />
<input type="image" src="smilies/yes.png" alt="Yes" title="Yes" onclick="insert(this.form.message,'(Y)'); return false;" />
<input type="image" src="smilies/no.png" alt="No" title="No" onclick="insert(this.form.message,'(N)'); return false;" /></center>
</td></tr><tr>
</td></tr><td colspan=2><center><input type='submit' STYLE='color: black;  background-color: white;' value='Send' class='btn'></center></td></tr></table></form></center></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>
EOF;

if($_GET['ID'])
{
print "<br /><table width=75% border=2><tr><td colspan=2><b>Your last 5 mails to/from this person:</b></td></tr>";
$q=mysql_query("SELECT m.*,u1.username as sender from mail m left join users u1 on m.mail_from=u1.userid WHERE (m.mail_from=$userid AND m.mail_to= {$_GET['ID']} ) OR (m.mail_to=$userid AND m.mail_from={$_GET['ID']}) ORDER BY m.mail_time

DESC LIMIT 5",$c);
while($r=mysql_fetch_array($q))
{
$sent=date('F j, Y, g:i:s a',$r['mail_time']);
print "<tr><td>$sent</td> <td><b>{$r['sender']} wrote:</b> {$r['mail_text']}</td></tr>";
}
}

echo " </table> ";

}

function mail_send()
{
global $ir,$c,$userid,$h;


$sql=mysql_query("select max(userid) from users");
$result=mysql_result($sql,$users);

if($_POST['userid'] > $result OR $_POST['userid'] ==0 )
{
print "Oh no, you're trying to mail a ghost.<br /><br />
<a href='mailbox.php'>Back</a>";

$h->endpage();
exit;


}

if($userid==$_POST['userid'] )
{
print "What's the point in mailing yourself?<br /><br />
<a href='mailbox.php'>Back</a>";

$h->endpage();
exit;

}




$subj=str_replace(array("\n"),array("<br />"),strip_tags($_POST['subject']));

$msg=bb2html($_POST['message']);

$codes = array(":)", ";)", ":o", ":D", ":s", ":(", ":red", "=-P", ":$", ":x", ":green", ":|", ":crying", ":eyelashes", ":devil", "B-)", ":angel", "({})", "X_X", ":dancing", ":/", ":kiss", "=D", "3-|", "=))", ":P", "(Y)", "(N)");
$images  = array("<img src=smallsmilies/smile.png>", "<img src=smallsmilies/wink.png>", "<img src=smallsmilies/eek.png>", "<img 

src=smallsmilies/biggrin.png>", "<img src=smallsmilies/confused.png>", "<img src=smallsmilies/frown.png>", "<img 

src=smallsmilies/mad.png>", "<img src=smallsmilies/party.png>", "<img src=smallsmilies/redface.png>", "<img 

src=smallsmilies/lips-sealed.png>", "<img src=smallsmilies/sick.png>", "<img src=smallsmilies/straight-face.png>", "<img 

src=smallsmilies/crying.png>", "<img src=smallsmilies/eyelashes.png>", "<img src=smallsmilies/devil.png>", "<img src=smallsmilies/cool.png>", "<img src=smallsmilies/angel.png>", "<img src=smallsmilies/big-hug.png>", "<img src=smallsmilies/cant-watch.png>", "<img src=smallsmilies/dancing.png>", "<img src=smallsmilies/huh.png>", "<img src=smallsmilies/kiss.png>", "<img src=smallsmilies/laughing.png>", "<img src=smallsmilies/not-interested.png>", "<img src=smallsmilies/rotfl.png>", "<img src=smallsmilies/tongue.png>", "<img src=smallsmilies/yes.png>", "<img src=smallsmilies/no.png>");
$newmsg = str_replace($codes, $images, $msg);
$to= (int) $_POST['userid'];
mysql_query("INSERT INTO mail VALUES ('',0,$userid,$to,unix_timestamp(),'$subj','$newmsg')",$c) or die(mysql_error());
mysql_query("UPDATE users SET new_mail=new_mail+1 WHERE userid={$to}")  or die(mysql_error()); 
print "Message sent.<br /><br />
<a href='mailbox.php'>Back</a>";
}

function delb()
 {
global $db,$ir,$c,$userid,$h;
$db->query("DELETE FROM mail WHERE mail_id={$_GET['ID']} AND mail_to=$userid");
print "Message deleted!<br /><br />
<a href='mailbox.php'>Back</a>";
}

function mail_delall()
{
global $db,$ir,$c,$userid,$h;
print " 
       

This will delete all the messages in your mailbox.
There is <b>NO</b> undo, so be sure.<br />    <br />
<a href='mailbox.php?action=delall2'> Yes, delete all messages</a><br /> <br />
<a href='mailbox.php'>No, go back</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
}
function mail_delall2()
{
global $db,$ir,$c,$userid,$h;
$db->query("DELETE FROM mail WHERE mail_to=$userid");
print "All ".$db->affected_rows()." mails in your inbox were deleted.<br />
<a href='mailbox.php'>Back</div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div></a>";
}
function mail_archive()
{
global $ir,$c,$userid,$h;
print "This tool will download an archive of all your messages.<br />
<a href='dlarchive.php?a=inbox'><br />Download Inbox</a><br /><br /
<a href='dlarchive.php?a=outbox'>Download Outbox</a></div><div><img src='images/generalinfo_btm.jpg' alt='' /></div><br></div></div></div></div></div>";
}
$h->endpage();
?>
