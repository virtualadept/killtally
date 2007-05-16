<?php include "include.php"; ?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

$gameid = mysql_real_escape_string($_GET['gameid']);
$sourceid = mysql_real_escape_string($_GET['sourceid']);
$spellid = mysql_real_escape_string($_GET['spellid']);
$itemid = mysql_real_escape_string($_GET['itemid']);
$destid = $_GET['destid'];
$hpadj = mysql_real_escape_string($_GET['hpadj']);
$sthrow = mysql_real_escape_string($_GET['sthrow']);

if (!$gameid) {
        print "Welcome $username!<br><br>";
        print "First off, please select which game you wish to administer: ";
        print "<form action=\"round.php\">";           // method=\"post\"><br>";
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
	print "<form action=\"round.php\">"; // method=\"post\"><br>";
	
	// SOURCE ($sourceid)
	print "<li>Source: ";
		// Because we'll have to generate this same data about 100 times, save the list in a variable
		// First, get a list of the characters
	$pcmonlist = "<option value=\"\"> ---Characters---</option>";
	$pcidsql = mysql_query("SELECT pc.pcid,pc.name,pc.player FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\" ORDER BY pc.name ASC",$mysql);
	while (list($pcid, $pcname, $pcplayer) = mysql_fetch_row($pcidsql)) {
		$pcmonlist .= "<option value=\"p-$pcid\">$pcname ($pcplayer)</option>";
	}
	print "<option value=\"\"> </option>";
	
		// Now lets get the monsters
	$pcmonlist .= "<option value=\"\"> ---Monsters---</option>";
	$pcmonlist .= "<option value=\"\"> </option>";
	$monidsql = mysql_query("SELECT id,name FROM monster ORDER BY name ASC", $mysql);
	while (list ($monid,$monname) = mysql_fetch_row($monidsql)) {
		$pcmonlist .= "<option value=\"m-$monid\">$monname</option>";
	}
	
	print "<select name=\"sourceid\">";
	print "$pcmonlist";
	print "</select><br><hr><br>";

	// ------------- //

	// ACTION ($actionid)
	print "<li>Method:<br> ";
		// Attack
	print "<input type=\"radio\" name=\"actionid\" value=\"A\"> Attack<br>";
		// Spell
	print "<input type=\"radio\" name=\"actionid\" value=\"S\"> Spell  ";
	print "<select name=\"spellid\">";
	print "<option value=\"\">---SELECT SPELL---</option>";
	$spellsql = mysql_query("SELECT id,name FROM spell ORDER BY name ASC",$mysql);
	while (list ($spellid,$spellname) = mysql_fetch_row($spellsql)) {
		print "<option value=\"$spellid\">$spellname</option>";
	}
	print "</select><br>";
		// Item.. Fuck this is a big list.. :(
	print "<input type=\"radio\" name=\"actionid\" value=\"I\"> Item   ";
	print "<select name=\"itemid\">";
	print "<option value=\"\">---SELECT ITEM---</option>";
	$itemlist = array("Ring", "Rod", "Staff", "Wonderous", "Artifact", "Potion", "Oil", "Potion, Oil", "Scroll", "Wand", "Cursed");
	$itemquery = "SELECT id,category,name FROM item WHERE ";
	foreach ($itemlist as $item) {
		$itemquery .= "category = '$item' OR ";
	}
	$itemquery .= "category = 'Universal Items' ORDER BY name ASC";
	$itemsql = mysql_query($itemquery, $mysql);
	while (list ($itemid,$itemcat,$itemname) = mysql_fetch_row($itemsql)) {
		print "<option value=\"$itemid\">$itemname ($itemcat)</option>";
	}
	print "</select><br><hr><br>";

	// -------------- //

	// DESTINATION ($destid)
	print "<li>Destination:<br> ";
	for ($i=0; $i<5; $i++) {
		print "<select name=\"destid[]\"> $pcmonlist </select><br>";
	}
	print "<br><hr><br>";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";

	// ------------- //
	
	// DAMAGE/HEALED ($hpadj)
	print "<li>Hit Point Adjustment (Damage/Healed): ";
	print "<input type=\"text\" name=\"hpadj\">";
	print "<br><hr><br>";

	// ------------ //

	// SAVING THROW ($sthrow)
	print "<li>Saving Throw: ";
	print "Succeeded <input type=\"radio\" name=\"sthrow\" value=\"Y\">";
	print "Failed <input type=\"radio\" name=\"sthrow\" value=\"N\">";
	


	print "<br><input type=\"submit\" value=\"Enter\">";

}
?>
