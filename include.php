<?php
// Since this area is 100% auth'd already, no point in cookie setting
$username = "{$_SERVER['PHP_AUTH_USER']}";

// Connect to MySQL

$mysql = mysql_connect("localhost", "dndkilltally", "ihopethisworks");
if (!$mysql) {
	printf("Cannot connect to database: %s\n", mysql_connect_error());
	exit;
}
mysql_select_db("dndkilltally", $mysql);
?> 
