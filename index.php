<?php include "include.php"; ?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

$gameid = mysql_real_escape_string($_POST['gameid']);
$mode = mysql_real_escape_string($_POST['mode']);
$gameid = mysql_real_escape_string($_POST['gameid']);
$killid = mysql_real_escape_string($_POST['killid']);
$assistid = $_POST['assistid'];
$killdamage = $_POST['killdamage'];
$assistdamage = $_POST['assistdamage'];
$foename = mysql_real_escape_string($_POST['foename']);
$foeid = mysql_real_escape_string($_POST['foeid']);
$encounternotes = mysql_real_escape_string($_POST['encounternotes']);

if (!$gameid) {
	print "Welcome $username!<br><br>";
	print "First off, please select which game you wish to administer: ";
	print "<form action=\"index.php\" method=\"post\"><br>";
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
	print "<form action=\"index.php\" method=\"post\"><br>";
	$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql);
	$gamename = mysql_fetch_row($gamesql);
	print "You are currently running <b>$gamename[0]</b> (<a href=\"index.php\">change</a>)<br><br>";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";

	// Character who scored kill
	print "Character who scored kill:<br>";
	$charsql = mysql_query("SELECT pc.pcid,pc.name FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\"",$mysql);
	while (list($pcid, $pcname) = mysql_fetch_row($charsql)) {
		print "<li>$pcname <input type=radio name=\"killid\" value=\"$pcid\">";
		print " @ <input type=text size=\"3\" maxlength=\"3\" name=\"killdamage[$pcid]\"> hp<br>";
	}

	// Characters who assisted
	print "<hr>";
	print "Character(s) who assisted:<br>";
	$assistsql = mysql_query("SELECT pc.pcid,pc.name FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\"",$mysql);
	while (list($pcid,$pcname) = mysql_fetch_row($assistsql)) {
		print "<li>$pcname <input type=\"checkbox\" name=\"assistid[]\" value=\"$pcid\">";
		print " @ <input type=text size=\"3\" maxlength=\"3\" name=\"assistdamage[$pcid]\">hp<br>";
	}
	
	// Who did we all kill?
	print "<hr>";
	print "Enemy Killed: ";
	print "<select name=\"foeid\">";
	$foesql = mysql_query("SELECT foeid,name FROM monster ORDER BY name ASC", $mysql);
	while (list($foeid,$foename) = mysql_fetch_array($foesql)) {
		print "<option value=\"$foeid\">$foename</option>";
	}
	print "</select>";
	
	// Notes about the Encounter?
	print "<hr>";
	print "Encounter Notes:<br>";
	print "<textarea name=\"encounternotes\" cols=\"40\" rows=\"6\"></textarea><br>";
	
	// Submitola!
	print "<input type=\"hidden\" name=\"mode\" value=\"insert\">";
	print "<br><input type=\"submit\" value=\"Enter\">";
}


// Ah shit, here we go with the database magic!
if (($mode == 'insert') && $gameid && $killid && ($foeid != 'addnew')) {
	// Sanity checks to make sure killid != assistid
	if ($assistid) {
		foreach ($assistid as $foo) {
			if ($killid == $foo) {
				print "Cannot Kill and Assist!<br>";
				exit;
			}
		}
	}

	// Get a nice random unique number to identify all people in one encounter
	$eventid = time();
	
	// Process the kill first
	$enterkill = mysql_query("INSERT INTO killtally (gameid,pcid,foeid,enterer,date,stat,eventid,damage) VALUES (\"$gameid\",\"$killid\",\"$foeid\",\"$username\",NOW(),\"K\",\"$eventid\",\"$killdamage[$killid]\")");
	if ($enterkill) {
		print "Entered!<br>";
		print "<form action=\"index.php\" method=\"post\">";
		print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
		print "<br><input type=\"submit\" value=\"Do Another?\">";
	} else {
		print "Didnt get entered, something screwed up";
	}
	
	// Process the assists after
	if (count($assistid) >= 1) {
		for ($x = 0; $x < count($assistid); $x++) {
			$pcid = $assistid[$x];
			$enterassist = mysql_query("INSERT INTO killtally (gameid,pcid,foeid,enterer,date,stat,eventid,damage) VALUES (\"$gameid\",\"$pcid\",\"$foeid\",\"$username\",NOW(),\"A\",\"$eventid\",\"$assistdamage[$pcid]\")");
		}
	}
	
	// Process the encounter notes
	if ($encounternotes) {
		$encountersql = mysql_query("INSERT INTO encounternotes (eventid,notes) VALUES (\"$eventid\",\"$encounternotes\")", $mysql);
	}

}

include "footer.php";

?>
<br>
</form>
</body>
</html>
