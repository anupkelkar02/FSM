<?php
$conn = mysql_connect('db581384879.db.1and1.com','dbo581384879','Storm@123');
mysql_select_db('db581384879',$conn) or die('Unable to connect to DB');

$str = 'insert into grs_reply_status(number,code,title) values(6,"CB","Callback")';
mysql_query($str);
echo mysql_insert_id();
?>