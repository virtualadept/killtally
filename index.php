<?php include "include.php"; ?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

$gameid = mysql_real_escape_string($_GET['gameid']);
$mode = mysql_real_escape_string($_GET['mode']);
$gameid = mysql_real_escape_string($_GET['gameid']);
$pcid = mysql_real_escape_string($_GET['pcid']);
$foename = mysql_real_escape_string($_GET['foename']);
$foeid = mysql_real_escape_string($_GET['foeid']);

if (!$gameid) {
	print "Welcome $username!<br><br>";
	print "First off, please select which game you wish to administer: ";
	print "<form action=\"index.php?gameid=$gameid\"><br>";
	print "<select name=\"gameid\">";
	$game = mysql_query("SELECT * FROM game where active=\"1\"", $mysql);
	while (list($gameid, $gamename) = mysql_fetch_row($game)) {
        	print "<option value=\"$gameid\">$gamename</option>";
	}
	print "</select><br><br>";
	print "<input type=\"submit\" value=\"Save\">";
}

if ($gameid && !$mode) {
	print "Welcome $username!<br> Please make your selection<br>";
	print "<form action=\"index.php\"><br>";
	$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql);
	$gamename = mysql_fetch_row($gamesql);
	print "You are currently running <b>$gamename[0]</b> (<a href=\"index.php\">change</a>)<br><br>";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "Character: ";
	print "<select name=\"pcid\">";
	$character = mysql_query("SELECT pc.pcid,pc.name FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\"",$mysql);
	while (list($pcid, $pcname) = mysql_fetch_row($character)) {
		print "<option value=\"$pcid\">$pcname</option>";
	}
	print "</select><br><br>";
	print "Enemy Killed: ";
	print "<select name=\"foeid\">";
	$foesql = mysql_query("SELECT foeid,name FROM monster ORDER BY name ASC", $mysql);
	while (list($foeid,$foename) = mysql_fetch_array($foesql)) {
		print "<option value=\"$foeid\">$foename</option>";
	}
	print "</select>";
	print "<input type=\"hidden\" name=\"mode\" value=\"insert\">";
	print "<br><input type=\"submit\" value=\"Kerblewie\">";
}

if (($mode == 'insert') && $gameid && $pcid && ($foeid != 'addnew')) {
	$enterkill = mysql_query("INSERT INTO killtally (gameid,pcid,foeid,enterer,date) VALUES (\"$gameid\",\"$pcid\",\"$foeid\",\"$username\",NOW())");
	if ($enterkill) {
		print "Entered!<br>";
		print "<a href=\"index.php?gameid=$gameid\">Another One!</a>";
	} else {
		print "Didnt get entered, something screwed up";
	}
}

include "footer.php"

?>
<br>
</form>
</body>
</html>
