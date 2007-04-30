<?php include "include.php" ?>
<html>
<head>
<title>KillTally Editor</title>
</head>
<body>
<?

$foename = mysql_real_escape_string($_GET['foename']);
$foeid = mysql_real_escape_string($_GET['foeid']);
$foecr = mysql_real_escape_string($_GET['foecr']);
$mode = mysql_real_escape_string($_GET['mode']);

if (!$foeid && !$mode) {
	print "Select Foe to Edit: ";
	print "<form action=\"killtally.php\">";
	print "<select name=\"foeid\">";
	$foesql = mysql_query("SELECT foeid,name FROM monster ORDER BY name ASC", $mysql);
	while (list($foeid,$foe) = mysql_fetch_array($foesql)) {
		print "<option value=\"$foeid\">$foe</option>";
	}
	print "<option value=\"addnewfoe\">Add Monster</option>";
	print "</select>";
	print "<input type=\"submit\" value=\"Edit Foe\">";
}

if ($foeid && !$mode && ($foeid != 'addnewfoe')) {
	print "<b>!!!WARNING!!!! THIS WILL CHANGE THE NAME OF ALL THE MONSTERS THAT USE THIS NAME !!!WARNING!!!</b><br>";
	print "Edit Foe";
	print "<form action=\"killtally.php\">";
	$foesql = mysql_query("SELECT name,cr FROM monster WHERE foeid=\"$foeid\"", $mysql);
	list($foename,$foecr) = mysql_fetch_array($foesql);
	print "Name: ";
	print "<input type=\"text\" name=\"foename\" value=\"$foename\">";
	print "<br>CR: ";
	print "<input type=\"text\" name=\"foecr\" value=\"$foecr\">";
	print "<input type=\"hidden\" name=\"foeid\" value=\"$foeid\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"update\">";
	print "<br><input type=\"submit\" value=\"Save Foe\">";
}

if ($foeid == 'addnewfoe') {
	print "Add A New Monster<br>";
	print "<form action=\"killtally.php\">";
	print "Name: <input type=\"text\" name=\"foename\"><br>";
	print "CR: <input type=\"text\" name=\"foecr\"><br>";
	print "<input type=\"hidden\" name=\"mode\" value=\"insert\">";
	print "<br><input type=\"submit\" value=\"Add\">";
}

if (($mode == 'insert') && $foename && $foecr) {
	$foeaddsql = mysql_query("INSERT INTO monster (name,cr) VALUES (\"$foename\",\"$foecr\")", $mysql);
	if ($foeaddsql) {
		print "Insert Successful";
	} else {
		print "Insert Failed";
	}
}

if ($foename && ($mode = 'update') && $foeid && $foecr) {
	$foeupdate = mysql_query("UPDATE monster SET name=\"$foename\" , cr=\"$foecr\" WHERE foeid=\"$foeid\"", $mysql);
	if ($foeupdate) {
		print "Success";
	} else {
		print "Failed";
	}
}

include "footer.php";
?>
</body>
</html>
