<?php include "include.php" ?>
<html>
<head>
<title>KillTally Editor</title>
</head>
<body>
<?

$foe = mysql_real_escape_string($_GET['foe']);
$mode = mysql_real_escape_string($_GET['mode']);
$oldfoe = mysql_real_escape_string($_GET['oldfoe']);

if (!$foe && !$mode) {
	print "Select Foe to Edit: ";
	print "<form action=\"killtally.php\">";
	print "<select name=\"foe\">";
	$foesql = mysql_query("SELECT DISTINCT foe FROM killtally ORDER BY foe ASC", $mysql);
	while (list($foe) = mysql_fetch_array($foesql)) {
		print "<option value=\"$foe\">$foe</option>";
	}
	print "</select>";
	print "<input type=\"submit\" value=\"Edit Foe\">";
}

if ($foe && !$mode) {
	print "<b>!!!WARNING!!!! THIS WILL CHANGE THE NAME OF ALL THE MONSTERS THAT USE THIS NAME !!!WARNING!!!</b><br>";
	print "Edit Foe: ";
	print "<form action=\"killtally.php\">";
	print "<input type=\"text\" name=\"foe\" value=\"$foe\">";
	print "<input type=\"hidden\" name=\"oldfoe\" value=\"$foe\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"update\">";
	print "<br><input type=\"submit\" value=\"Save Foe\">";
}

if ($foe && ($mode = 'update') && $oldfoe) {
	$foesql = mysql_query("UPDATE killtally SET foe=\"$foe\" WHERE foe=\"$oldfoe\"", $mysql);
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
