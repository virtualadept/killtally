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
$foe = mysql_real_escape_string($_GET['foe']);
$cr = mysql_real_escape_string($_GET['cr']);

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
	print "<select name=\"foe\">";
	print "<option value=\"addnew\">Add A New Foe</option>";
	$foesql = mysql_query("SELECT DISTINCT foe FROM killtally ORDER BY foe ASC", $mysql);
	while (list($foe) = mysql_fetch_array($foesql)) {
		print "<option value=\"$foe\">$foe</option>";
	}
	print "</select>";
	print "<br>Challenge Rating: ";
	print "<input type=\"text\" name=\"cr\" maxlength=\"5\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"insert\">";
	print "<br><input type=\"submit\" value=\"Kerblewie\">";
}

if (($mode == 'insert') && $gameid && $pcid && ($foe == 'addnew') && $cr) {
	print "<form action=\"index.php\">";
	print "Name of Foe: ";
	print "<input type=\"text\" name=\"foe\" maxlength=\"50\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"insert\">";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "<input type=\"hidden\" name=\"pcid\" value=\"$pcid\">";
	print "<input type=\"hidden\" name=\"cr\" value=\"$cr\">";
	print "<br><input type=\"submit\" value=\"Kerblewie\">";
}

if (($mode == 'insert') && $gameid && $pcid && ($foe != 'addnew') && $cr) {
	print "<br>Your entry of: <br>";
	$enterkill = mysql_query("INSERT INTO killtally (gameid,pcid,foe,challengerating,enterer,date) VALUES (\"$gameid\",\"$pcid\",\"$foe\",\"$cr\",\"$username\",NOW())");
	if ($enterkill) {
		// Print out the results!
		$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql); 
		$gamename = mysql_fetch_row($gamesql);
		$pcsql = mysql_query("SELECT name FROM playercharacter WHERE pcid=\"$pcid\"", $mysql); 
		$pcname = mysql_fetch_row($pcsql);
		print "<br><b>$cr</b> CR points awarded to <b>$pcname[0]</b> for the slaying of a <b>$foe</b> in the game <b>$gamename[0]</b> by <b>$username</b><br>";
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
