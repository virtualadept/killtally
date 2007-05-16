<?php include "include.php"; ?>
<html>
<head>
<title> D&D Kill Tallier <title>
<body>
<?php

$gameid = mysql_real_escape_string($_GET['gameid']);
$eventid = mysql_real_escape_string($_GET['eventid']);
$mode = mysql_real_escape_string($_GET['mode']);
$sourceid = mysql_real_escape_string($_GET['sourceid']);
$actionid = mysql_real_escape_string($_GET['actionid']);
$spellid = mysql_real_escape_string($_GET['spellid']);
$itemid = mysql_real_escape_string($_GET['itemid']);
$destid = $_GET['destid'];
$hpadj = $_GET['hpadj'];
$sthrow = $_GET['sthrow'];
$kill = $_GET['kill'];

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
	
	if (!$eventid) {
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
			list ($desttype,$did) = split("-",$dest);
			if ($did > 0) {  // Holy shit this is a kludge
				$enterstat = mysql_query("INSERT INTO stattally (eventid, sourcetype, sourceid, actionid, spellid, itemid, desttype, destid, hpadj, sthrow, destkill, date, enterer) values (\"$eventid\",\"$sourcetype\",\"$sid\",\"$actionid\",\"$spellid\",\"$itemid\",\"$desttype\",\"$did\",\"$hp\",\"$st\",\"$ded\",NOW(),\"$username\")",$mysql);
			}
		}
		if ($enterstat) {
			print "Success! Do Another!<br>";
		} else {
			print "Something went wrong<br>";
	
		}
	}
	
	print "Enter hot player-on-player action $username!<br>";
	print "The eventid is <b>$eventid</b><br>";
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
		$pcmonlist .= "<option value=\"P-$pcid\">$pcname ($pcplayer)</option>";
	}
	print "<option value=\"\"> </option>";
	
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

	// DESTINATION ($destid), HPADJUSTMENT ($hpadj), SAVES ($sthrow)
	print "<li>Destination:<br> ";
	for ($i=0; $i<5; $i++) {
		print "<select name=\"destid[]\"> $pcmonlist </select>";
	        print "Save Throw: ";
        	print "<input type=\"radio\" name=\"sthrow[$i]\" value=\"Y\">Made";
        	print " <input type=\"radio\" name=\"sthrow[$i]\" value=\"N\">Fail";
		print " | Killed? <input type=\"checkbox\" name=\"kill[$i]\" value=\"Y\"><br>";

	}
	print "<hr>";

	// ------------- //
	
	// DAMAGE/HEALED ($hpadj)
	print "<li>Hit Point Adjustment (Damage/Healed): ";
	print "<input type=\"text\" name=\"hpadj\">";
	print "<hr>";

	// ------------ //

	// SAVING THROW ($sthrow)
	print "<li>Saving Throw:<br> ";
	print "<input type=\"radio\" name=\"sthrow[0]\" value=\"Y\">Succeeded<br>";
	print "<input type=\"radio\" name=\"sthrow[0]\" value=\"N\">Failed<br>";
	
	print "<input type=\"radio\" name=\"sthrow[1]\" value=\"Y\">Succeeded<br>";
	print "<input type=\"radio\" name=\"sthrow[1]\" value=\"N\">Failed<br>";

	print "<input type=\"radio\" name=\"sthrow[2]\" value=\"Y\">Succeeded<br>";
	print "<input type=\"radio\" name=\"sthrow[2]\" value=\"N\">Failed<br>";


	print "<input type=\"radio\" name=\"sthrow[3]\" value=\"Y\">Succeeded<br>";
	print "<input type=\"radio\" name=\"sthrow[3]\" value=\"N\">Failed<br>";


	print "<input type=\"radio\" name=\"sthrow[4]\" value=\"Y\">Succeeded<br>";
	print "<input type=\"radio\" name=\"sthrow[4]\" value=\"N\">Failed<br>";

	
	print "<input type=\"radio\" name=\"sthrow[5]\" value=\"Y\">Succeeded<br>";
	print "<input type=\"radio\" name=\"sthrow[5]\" value=\"N\">Failed<br>";
	
	// ------------ //

	// Enemy Killed? ($kill)
	print "<li> Enemy Killed? ";
	print "<input type=\"checkbox\" name=\"kill\" value=\"Y\">";
	print "<br><br>";


	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "<input type=\"hidden\" name=\"eventid\" value=\"$eventid\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"save\">";
	print "<br><input type=\"submit\" value=\"Enter\">";

}
?>
