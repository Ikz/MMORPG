<?php 
  
include "globals.php";
$sql       = 'SELECT COUNT(refID) FROM referals WHERE refREFER = ' . $userid;
$rs        = db->query($sql);
$referals = array_shift(mysql_fetch_row($rs));
mysql_free_result($rs);
  
    $sql = "
       
           SELECT userid, username
             FROM users
        LEFT JOIN referals ON refREFED = userid
            WHERE refREFER = $userid
         ORDER BY level DESC, username
       
   ";
       
    echo '
       
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Level</th>
            </tr>
       
    ';
       
    $rs = db->query($sql);
       
    WHILE ($row = mysql_fetch_row($rs))
    {
        list($id, $name, $level) = $row;
       
        $name  = htmlentities($name, ENT_QUOTES, 'utf-8');
        $level = number_format($level);
  
        echo "
       
            <tr>
                <td>".$id."</td>
                <td>".$name."</td>
                <td>".$level."</td>
            </tr>
       
    ";
    }
       
    mysql_free_result($rs);
       
    echo '<br style="clear:both;" /></table>';
  
$h->endpage();
?>