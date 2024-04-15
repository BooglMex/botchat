<?php
$db_host		= 'localhost';
$db_user		= 'arsenmg9_bot';
$db_pass		= 'B299792458t';
$db_database	= 'arsenmg9_bot';
$link = mysql_connect($db_host, $db_user, $db_pass);

mysql_select_db($db_database,$link) or die("Нет соединения с БД ".mysql_error());
mysql_query("SET names UTF8");
?>