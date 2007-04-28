<?php
session_start();
include "include.php";
?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

print "Welcome $username!<br> Please make your selection<br>";

print "<form action=\"index.php\"><br>";

// Set the correct Game
$gameid = $_GET['gameid'];
if (!$gameid) { $gameid = "1"; }
$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql);
$gamename = mysql_fetch_row($gamesql);
print "You are currently running <b>$gamename[0]</b> (<a href=\"session.php\">change</a>)<br><br>";
print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";

// Get character dropdown box
print "Character: ";
print "<select name=\"pcid\">";
$character = mysql_query("SELECT pc.pcid,pc.name FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\"",$mysql);
while (list($pcid, $pcname) = mysql_fetch_row($character)) {
	print "<option value=\"$pcid\">$pcname</option>";
}
print "</select><br><br>";

// What are we killing, and how much is it worth?
print "Enemy Killed: ";
print "<input type=\"text\" name=\"foe\" maxlength=\"50\">";
print "  Challenge Rating: ";
print "<input type=\"text\" name=\"cr\" maxlength=\"5\">";

// We are submitting this form to itself
print "<input type=\"hidden\" name=\"mode\" value=\"submit\">";

print "<br><input type=\"submit\" value=\"Kerblewie\">";


if ($_GET['mode'] == "submit") {
	// Hate!
	$gameid = mysql_real_escape_string($_GET['gameid']);
	$pcid = mysql_real_escape_string($_GET['pcid']);
	$foe = mysql_real_escape_string($_GET['foe']);
	$cr = mysql_real_escape_string($_GET['cr']);

	if (!$gameid || !$pcid || !$foe || !$cr) {
		print "<br><b>You forgot to fill in something</b>;
		exit;
	}

	print "<br>Your entry of: <br>";

	$enterkill = mysql_query("INSERT INTO killtally (gameid,pcid,foe,challengerating,enterer,date) VALUES (\"$gameid\",\"$pcid\",\"$foe\",\"$cr\",\"$username\",NOW())");

	if ($enterkill != FALSE) {
		// Print out the results!
		$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql); 
		$gamename = mysql_fetch_row($gamesql);

		$pcsql = mysql_query("SELECT name FROM playercharacter WHERE pcid=\"$pcid\"", $mysql); 
		$pcname = mysql_fetch_row($pcsql);

		print "<br><b>$cr</b> CR points awarded to <b>$pcname[0]</b> for the slaying of a <b>$foe</b> in the game <b>$gamename[0]</b> by <b>$username</b>";
	} else {
		print "Didnt get entered, something screwed up";
	}
}

?>
<br>
</form>
</body>
</html>
