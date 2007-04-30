<?php include "include.php" ?>
<html>
<head>
<title>Character Editor</title>
</head>
<body>
<?php

$pcid = mysql_real_escape_string($_GET['pcid']);
$mode = mysql_real_escape_string($_GET['mode']);
$pcname = mysql_real_escape_string($_GET['pcname']);
$pcplayer = mysql_real_escape_string($_GET['pcplayer']);
$pcactive = mysql_real_escape_string($_GET['pcactive']);

if (!$pcid && !$mode) {
	print "Select character to edit: (<a href=\"characteredit.php?mode=add\">Add new character</a>)";
	print "<form action=\"characteredit.php\">";
	print "<select name=\"pcid\">";
	$pcsql = mysql_query("SELECT pcid,name FROM playercharacter", $mysql);
	while (list($pcid, $pcname) = mysql_fetch_row($pcsql)) {
		print "<option value=\"$pcid\">$pcname</option>";
	}
	print "</select><br>";
	print "<br><input type=\"submit\" value=\"Edit\">";
	}

if ($pcid && !$mode) {
	// Pull all character data from database
	$pcsql = mysql_query("SELECT * FROM playercharacter WHERE pcid=\"$pcid\"", $mysql);
	$pchash = mysql_fetch_assoc($pcsql);
	$pcname = $pchash['name'];
	$pcplayer = $pchash['player'];
	$pcactive = $pchash['active'];
	$pcdate = $pchash['date'];
	$pcenterer = $pchash['enterer'];

	print "<form action=\"characteredit.php\">";
	print "Now editing <b>$pcname</b> (<a href=\"characteredit.php\">change?</a>)<br>";
	print "PC-ID: $pcid<br>";
	print "Edited: $pcdate by $pcenterer<br><br>";
	print "Name: <input type=\"text\" name=\"pcname\" value=\"$pcname\"><br>";
	print "Played By: <input type=\"text\" name=\"pcplayer\" value=\"$pcplayer\"><br>";
	print "Active? Yes <input type=\"radio\" name=\"pcactive\" value=\"1\" checked>";
	print " No <input type=\"radio\" name=\"pcactive\" value=\"0\"><br>";
	print "<input type=\"hidden\" name=\"mode\" value=\"update\">";
	print "<input type=\"hidden\" name=\"pcid\" value=\"$pcid\">";
	print "<br><input type=\"submit\" value=\"Save\">";

}

if ($pcid && ($mode == 'update') && $pcname && $pcplayer && $pcactive) {
	$pcsql = mysql_query("UPDATE playercharacter SET name=\"$pcname\", player=\"$pcplayer\", active = \"$pcactive\", date = NOW(), enterer = \"$username\" WHERE pcid=\"$pcid\"", $mysql);
	if (!$pcsql) {
		print "<br><br><b>Somethings not right! Did not update!</b>";
	} else {
		print "Saved! <a href=\"characteredit.php\">Do Another?</a>";
	}
}

if (!$pcid && $mode == 'add') {
	print "Add a new character:<br>";
	print "<form action=\"characteredit.php\">";
	print "Name: <input type=\"text\" name=\"pcname\"><br>";
	print "Played By: <input type=\"text\" name=\"pcplayer\"><br>";
	print "Active? Yes <input type=\"radio\" name=\"pcactive\" value=\"1\" checked>";
	print " No <input type=\"radio\" name=\"pcactive\" value=\"0\"><br>";
	print "<input type=\"hidden\" name=\"mode\" value=\"insert\">";
	print "<br><input type=\"submit\" value=\"Save\">";
}

if (!$pcid && ($mode == 'insert') && $pcname && $pcplayer && $pcactive) {
	$pcsql = mysql_query("INSERT INTO playercharacter (name, player, active, date, enterer) VALUES (\"$pcname\",\"$pcplayer\",\"$pcactive\", NOW(), \"$username\")", $mysql);
	if (!$pcsql) {
		print "<br><br><b>Somethings not right! Did not update!</b>";
	} else {
		print "Saved! <a href=\"characteredit.php\">Do Another?</a>";
	}	
}
?>
</body>
</html>
