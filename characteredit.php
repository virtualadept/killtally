<?php include "include.php" ?>
<html>
<head>
<title>Character Editor</title>
</head>
<body>
<?php

$pcid = mysql_real_escape_string($_GET['pcid']);
$save = mysql_real_escape_string($_GET['save']);
$pcname = mysql_real_escape_string($_GET['pcname']);
$pcplayer = mysql_real_escape_string($_GET['pcplayer']);
$pcactive = mysql_real_escape_string($_GET['pcactive']);

if (!$pcid) {
	print "Select character to edit: ";
	print "<form action=\"characteredit.php\">";
	print "<select name=\"pcid\">";
	$pcsql = mysql_query("SELECT pcid,name FROM playercharacter", $mysql);
	while (list($pcid, $pcname) = mysql_fetch_row($pcsql)) {
		print "<option value=\"$pcid\">$pcname</option>";
	}
	print "</select><br>";
	print "<br><input type=\"submit\" value=\"Edit\">";
	}

if ($pcid && !$save) {
	// Pull all character data from database
	$pcsql = mysql_query("SELECT * FROM playercharacter WHERE pcid=\"$pcid\"");
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
	print "<input type=\"hidden\" name=\"save\" value=\"1\">";
	print "<input type=\"hidden\" name=\"pcid\" value=\"$pcid\">";
	print "<br><input type=\"submit\" value=\"Save\">";

}

if ($pcid && $save && $pcname && $pcplayer && $pcactive) {
	$pcsql = mysql_query("UPDATE playercharacter SET name=\"$pcname\", player=\"$pcplayer\", active = \"$pcactive\", date = NOW() WHERE pcid=\"$pcid\"");
	if (!$pcsql) {
		print "<br><br><b>Somethings not right! Did not save!</b>";
	} else {
		print "Saved! <a href=\"characteredit.php\">Do Another?</a>";
	}
}

?>
</body>
</html>
