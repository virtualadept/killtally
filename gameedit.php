<?php include "include.php" ?>
<html>
<head>
<title>Game Editor</title>
</head>
<body>
<?

$gameid = mysql_real_escape_string($_GET['gameid']);

if (!$gameid) {
	print "Select game to edit: ";
	print "<form action=\"gameedit.php\">";
	print "<select name=\"gameid\">";
	$gamesql = mysql_query("SELECT gameid,name FROM game ORDER BY name ASC", $mysql);
	while (list($gameid,$gamename) = mysql_fetch_array($gamesql)) {
		print "<option value=\"$gameid\">$gamename</option>";
	}
	print "<option value=\"addnewgame\">Add new game</option>";
	print "</select>";
	print "<input type=\"submit\" value=\"Edit Game\">";
}

if ($gameid && !mode && ($gameid != 'addnewgame')) {
	print "Edit Game: ";
        print "<form action=\"gameedit.php\">";
        $gamesql = mysql_query("SELECT name,active,date FROM game WHERE gameid=\"$gameid\"", $mysql);
       	list($gamename,$active,$date) = mysql_fetch_array($gamesql);
        print "<li>Last Edited On: $date";
	print "<li>Name: ";
        print "<input type=\"text\" name=\"gamename\" value=\"$gamename\">";
        print "<li>Active: ";
        print "Yes <input type=\"radio\" name=\"active\" value=\"1\" checked>";
        print "No <input type=\"radio\" name=\"active\" value=\"0\">";
        print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
        print "<input type=\"hidden\" name=\"mode\" value=\"update\">";
        print "<br><input type=\"submit\" value=\"Save Changes\">";
}



include "footer.php";
?>
</body>
</html>
