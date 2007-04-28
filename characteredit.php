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

if ($pcid) {
	// Pull all character data from database
	$pcsql = mysql_query("select * from playercharacter where pcid=\"$pcid\"");
	$pchash = mysql_fetch_assoc($pcsql);
	$pcname = $pchash['name'];
	$pcplayer = $pchash['player'];
	$pcactive = $pchash['active'];
	$pcdate = $pchash['date'];
	$enterer = $pchash['enterer'];

	print "Now editing <b>$pcname</b><br>";
	print "PCID: <b>$pcid</b><br>";
	print "Edited: $pcdate by $pcadmin<br><br>";
	print "Name: <input type=\"text\" name=\"pcname\" value=\"$pcname\"><br>";
	print "Played By: <input type=\"text\" name=\"pcplayer\" value=\"$pcplayer\"><br>";
//	print "Active? <input type=\"text\" name=\"pcactive\" value=\"$pcactive\"><br>";
	print "<input type=\"hidden\" name=\"save\" value=\"1\">";
	print "<input type=\"hidden\" name=\"pcid\" value=\"$pcid\">";
	print "<br><input type=\"submit\" value=\"Save\">";

}
	
if ($pcid && $save && $pcname && $pcplayer && $pcactive) {
//	$pcsql = mysql_query("UPDATE playercharacter SET name=\"$pcname\", player=\"$pcplayer\", active = \"$pcactive\" WHERE pcid=\"$pcid\"");
}
