<?php include "include.php"; ?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

$gameid = mysql_real_escape_string($_POST['gameid']);
$sourceid = mysql_real_escape_string($_POST['sourceid']);

if (!$gameid) {
        print "Welcome $username!<br><br>";
        print "First off, please select which game you wish to administer: ";
        print "<form action=\"round.php\" method=\"post\"><br>";
        print "<select name=\"gameid\">";
        $game = mysql_query("SELECT * FROM game where active=\"1\" ORDER BY name ASC", $mysql);
        while (list($gameid, $gamename) = mysql_fetch_row($game)) {
                print "<option value=\"$gameid\">$gamename</option>";
        }
        print "</select><br><br>";
        print "<input type=\"submit\" value=\"Go!\">";
        print "</form>";
}

if ($gameid) {
	print "Enter player action!<br><br>";
        $gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql);
        $gamename = mysql_fetch_row($gamesql);
        print "You are currently running <b>$gamename[0]</b> (<a href=\"round.php\">change</a>)<br><br>";
	print "<form action=\"round.php\" method=\"post\"><br>";
	
	// SOURCE ($sourceid)
	print "Source: ";
	print "<select name=\"sourceid\">";

		// First, get a list of the characters
	print "<option value=\"\"> ---Characters---</option>";
	$pcidsql = mysql_query("SELECT pc.pcid,pc.name,pc.player FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\" ORDER BY pc.name ASC",$mysql);
	while (list($pcid, $pcname, $pcplayer) = mysql_fetch_row($pcidsql)) {
		print "<option value=\"p-$pcid\">$pcname ($pcplayer)</option>";
	}
	print "<option value=\"\"> </option>";
	
		// Now lets get the monsters
	print "<option value=\"\"> ---Monsters---</option>";
	print "<option value=\"\"> </option>";
	$monidsql = mysql_query("SELECT id,name FROM monster ORDER BY name ASC", $mysql);
	while (list ($monid,$monname) = mysql_fetch_row($monidsql)) {
		print "<option value=\"m-$monid\">$monname</option>";
	}
	print "</select><br><hr><br>";

	// ------------- //

	// ACTION ($actionid)
	print "Method: ";
		// Attack
	print "<li><input type=\"radio\" name=\"actionid\" value=\"A\"> Attack<br>";
		// Spell
	print "<li><input type=\"radio\" name=\"actionid\" value=\"S\"> Spell  ";
	print "<select name=\"spellid\">";
	print "<option value=\"\">---SELECT SPELL---</option>";
	$spellsql = mysql_query("SELECT id,name FROM spell ORDER BY name ASC",$mysql);
	while (list ($spellid,$spellname) = mysql_fetch_row($spellsql)) {
		print "<option value=\"s-$spellid\">$spellname</option>";
	}
	print "</select><br>";
		// Item.. Fuck this is a big list.. :(
	print "<li><input type=\"radio\" name=\"actionid\" value=\"I\"> Item   ";
	print "<select name=\"itemid\">";
	print "<option value=\"\">---SELECT ITEM---</option>";
	$itemsql = mysql_query("SELECT id,category,name FROM item WHERE category != 'Armor' OR category != 'Shield' OR category != 'Armor, Shield' OR category != 'Weapon' ORDER BY name ASC", $mysql);
	while (list ($itemid,$itemcat,$itemname) = mysql_fetch_row($itemsql)) {
		print "<option value=\"i-$itemid\">$itemname ($itemcat)</option>";
	}



	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";

}
?>
