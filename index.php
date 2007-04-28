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

print "<form action=\"enter_entry.php\"><br>";

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

?>
<br>
<input type="submit" value="Kerblewie">
</form>
</body>
</html>
