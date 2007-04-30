<?php include "include.php" ?>
<html>
<head>
<title>KillTally Editor</title>
</head>
<body>
<?

$foename = mysql_real_escape_string($_GET['foename']);
$foeid = mysql_real_escape_string($_GET['foeid']);
$mode = mysql_real_escape_string($_GET['mode']);

if (!$foeid && !$mode) {
	print "Select Foe to Edit: ";
	print "<form action=\"killtally.php\">";
	print "<select name=\"foeid\">";
	$foesql = mysql_query("SELECT foeid,name FROM monster ORDER BY name ASC", $mysql);
	while (list($foeid,$foe) = mysql_fetch_array($foesql)) {
		print "<option value=\"$foeid\">$foe</option>";
	}
	print "</select>";
	print "<input type=\"submit\" value=\"Edit Foe\">";
}

if ($foeid && !$mode) {
	print "<b>!!!WARNING!!!! THIS WILL CHANGE THE NAME OF ALL THE MONSTERS THAT USE THIS NAME !!!WARNING!!!</b><br>";
	print "Edit Foe: ";
	print "<form action=\"killtally.php\">";
	$foesql = mysql_query("SELECT name FROM monster WHERE foeid=\"$foeid\"", $mysql);
	$foename = mysql_fetch_array($foesql);
	print "<input type=\"text\" name=\"foename\" value=\"$foename[0]\">";
	print "<input type=\"hidden\" name=\"foeid\" value=\"$foeid\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"update\">";
	print "<br><input type=\"submit\" value=\"Save Foe\">";
}

if ($foename && ($mode = 'update') && $foeid) {
	$foesql = mysql_query("UPDATE monster SET name=\"$foename\" WHERE foeid=\"$foeid\"", $mysql);
	if ($foesql) {
		print "Success";
	} else {
		print "Failed";
	}
}

include "footer.php";
?>
</body>
</html>
