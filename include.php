<?php
// Include the database handle
include "db.php";

// Since this area is 100% auth'd already, no point in cookie setting
$username = "{$_SERVER['PHP_AUTH_USER']}";
?> 
