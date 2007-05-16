<?php include "include.php"; ?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

$gameid = mysql_real_escape_string($_POST['gameid']);
$eventid = mysql_real_escape_string($_POST['eventid']);
$mode = mysql_real_escape_string($_POST['mode']);
$sourceid = mysql_real_escape_string($_POST['sourceid']);
$actionid = mysql_real_escape_string($_POST['actionid']);
$spellid = mysql_real_escape_string($_POST['spellid']);
$itemid = mysql_real_escape_string($_POST['itemid']);
$encounternotes = mysql_real_escape_string($_POST['encounternotes']);
$destid = $_POST['destid'];
$hpadj = $_POST['hpadj'];
$hpadjtype = $_POST['hpadjtype'];
$sthrow = $_POST['sthrow'];
$kill = $_POST['kill'];

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
	
	if (!$eventid || $eventidreset = '1') {
		$eventid = time();
	}
	
	if ($gameid && $eventid && $sourceid && $actionid && $destid && $mode == 'save') {
		list ($sourcetype,$sid) = split("-",$sourceid);
		for ($x=0; $x < count($destid); $x++) {
			$dest = $destid[$x];
			if ($sthrow) {
				$st = $sthrow[$x];
			}
			if ($kill) {
				$ded = $kill[$x];
			}
			if ($hpadj) {
				$hp = $hpadj[$x];
			}
			if ($hpadjtype) {
				$hptype = $hpadjtype[$x];
			}
			list ($desttype,$did) = split("-",$dest);
			if ($did > 0) {  // Holy shit this is a kludge
				$enterstat = mysql_query("INSERT INTO stattally (eventid, gameid, sourcetype, sourceid, actionid, spellid, itemid, desttype, destid, hpadj, hpadjtype, sthrow, destkill, date, enterer) values (\"$eventid\", \"$gameid\", \"$sourcetype\",\"$sid\",\"$actionid\",\"$spellid\",\"$itemid\",\"$desttype\",\"$did\",\"$hp\", \"$hptype\",\"$st\",\"$ded\",NOW(),\"$username\")",$mysql);
			}
		}
		if ($enterstat) {
			print "Success! Do Another!<br>";
			print "<hr>";
		} else {
			print "Something went wrong<br>";
	
		}
	}
	
	if ($encounternotes) {
		$encountersql = mysql_query("INSERT IGNORE INTO encounternotes (eventid,notes,date) values (\"$eventid\", \"$encounternotes\",NOW())",$mysql);
	}

	print "Enter hot player-on-player action $username!<br>";
	
	// EventID with Reset Button
	print "The EncounterID is <b>$eventid</b>";
	print "<form action=\"round.php\" method=\"post\">";
	print "<input type=\"hidden\" name=\"eventidreset\" value=\"1\">";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "<input type=\"submit\" value=\"Reset for new encounter!\"><br>";
	print "</form>";

	// What game are we playing?
        $gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql);
        $gamename = mysql_fetch_row($gamesql);
        print "You are currently running <b>$gamename[0]</b> (<a href=\"round.php\">change</a>)<br><br>";
	
	// Start the Form of D00m
	print "<form action=\"round.php\" method=\"post\">";
	
	// SOURCE ($sourceid)
	print "<li>Source: ";
		// Because we'll have to generate this same data about 100 times, save the list in a variable
		// First, get a list of the characters
	$pcmonlist = "<option value=\"\"> ---Characters---</option>";
	$pcidsql = mysql_query("SELECT pc.pcid,pc.name,pc.player FROM playercharacter pc JOIN whowhere ww USING(pcid) JOIN game g USING(gameid) WHERE g.gameid = \"$gameid\" ORDER BY pc.name ASC",$mysql);
	while (list($pcid, $pcname, $pcplayer) = mysql_fetch_row($pcidsql)) {
		$pcmonlist .= "<option value=\"P-$pcid\">$pcname ($pcplayer)</option>";
	}
	$pcmonlist .= "<option value=\"\"> </option>";
	
		// Now lets get the monsters
	$pcmonlist .= "<option value=\"\"> ---Monsters---</option>";
	$pcmonlist .= "<option value=\"\"> </option>";
	$monidsql = mysql_query("SELECT id,name FROM monster ORDER BY name ASC", $mysql);
	while (list ($monid,$monname) = mysql_fetch_row($monidsql)) {
		$pcmonlist .= "<option value=\"M-$monid\">$monname</option>";
	}
	
	print "<select name=\"sourceid\">";
	print "$pcmonlist";
	print "</select><hr>";

	// ------------- //

	// ACTION ($actionid)
	print "<li>Method:<br> ";
		// Attack
	print "<input type=\"radio\" name=\"actionid\" value=\"A\"> Attack<br>";
		// Hold Action
	print "<input type=\"radio\" name=\"actionid\" value=\"H\"> Hold Action<br>";
		// Spell
	print "<input type=\"radio\" name=\"actionid\" value=\"S\"> Spell  ";
	print "<select name=\"spellid\">";
	print "<option value=\"\">---SELECT SPELL---</option>";
	$spellsql = mysql_query("SELECT id,name FROM spell ORDER BY name ASC",$mysql);
	while (list ($spellid,$spellname) = mysql_fetch_row($spellsql)) {
		print "<option value=\"$spellid\">$spellname</option>";
	}
	print "</select><br>";
		// Item.. Fuck this is a big list.. :(  Anyway to cache it? Maybe Memcache?
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
	print "</select><hr>";

	// -------------- //

	// DESTINATION ($destid), HPADJUSTMENT ($hpadj), SAVES ($sthrow), HPTYPE ($hpadjtype)
	print "<li>Destination:<br> ";
	for ($i=0; $i<5; $i++) {
		print "<select name=\"destid[]\"> $pcmonlist </select>";
	        print "Save: ";
        	print "<input type=\"radio\" name=\"sthrow[$i]\" value=\"M\">Made";
        	print " <input type=\"radio\" name=\"sthrow[$i]\" value=\"F\">Fail";
		print " | Killed? <input type=\"checkbox\" name=\"kill[$i]\" value=\"Y\">";
		print " | HP: <input type=\"text\" size=4 name=\"hpadj[$i]\">";
        	print "<input type=\"radio\" name=\"hpadjtype[$i]\" value=\"D\" checked>Damage";
        	print " <input type=\"radio\" name=\"hpadjtype[$i]\" value=\"H\">Heal<br>";
	}
	print "<hr>";


	// -------------- //

	// Encounter Notes
        print "Encounter Notes:<br>";
        print "<textarea name=\"encounternotes\" value=\"$encounternotes\" cols=\"40\" rows=\"6\"></textarea><br>";



	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "<input type=\"hidden\" name=\"eventid\" value=\"$eventid\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"save\">";
	print "<br><input type=\"submit\" value=\"Enter\">";

	print "</form>";
}
?>
