<?php include "include.php" ?>
<html>
<head>
<title>Encounter Viewer</title>
</head>
<body>
<?


$gameid = mysql_real_escape_string($_GET['gameid']);
$eventid = mysql_real_escape_string($_GET['eventid']);


if (!$gameid) {
        print "First off, please select which game you wish to view stats for ";
        print "<form action=\"encounterview.php\"><br>";
        print "<select name=\"gameid\">";
        $game = mysql_query("SELECT * FROM game where active=\"1\" ORDER BY name ASC", $mysql);
        while (list($gameid, $gamename) = mysql_fetch_row($game)) {
                print "<option value=\"$gameid\">$gamename</option>";
        }
        print "</select><br>";
        print "<input type=\"submit\" value=\"Go!\">";
        print "</form>";
}

if ($gameid && !$eventid) {
	print "Please select encounter to view: ";
	print "<form action=\"encounterview.php\">";
	print "<select name=\"eventid\">";
	$eventidsql = mysql_query("SELECT DISTINCT st.eventid,g.name,st.date FROM stattally st JOIN game g USING(gameid) WHERE st.gameid = g.gameid AND st.gameid = \"$gameid\" GROUP BY st.eventid ASC",$mysql);
	while (list($eventid,$gamename,$date) = mysql_fetch_row($eventidsql)) {
		$roundnumbersql = mysql_query("SELECT COUNT(*) FROM stattally WHERE eventid=\"$eventid\"",$mysql);
		$roundnumber = mysql_fetch_row($roundnumbersql);
		print "<option value=\"$eventid\">$gamename ($roundnumber[0] rounds) @ $date</option>";
	}
	print "</select>";
	print "<br>";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "<input type=\"submit\" value=\"Show Me!\">";
	print "</form>";

}

if ($gameid && $eventid) {
	$eventsql = mysql_query("SELECT st.sourcetype,st.sourceid,st.desttype,st.destid,st.actionid,sp.name AS spell,it.name AS item,st.hpadj,st.sthrow,st.destkill,st.date,st.enterer,st.hpadjtype FROM stattally st LEFT JOIN spell sp ON st.spellid = sp.id  LEFT JOIN item it ON st.itemid = it.id  WHERE st.eventid=\"$eventid\"",$mysql);
	// And it starts.. :\
	$roundnumber = 1;
	$rounddate = 0;
	print "Results for $eventid";
	while ($eventhash = mysql_fetch_assoc($eventsql)) {
		if ($eventhash['sourcetype'] == "P") {
			$sourcetype = "playercharacter";
			$sid = "pcid";
		} elseif ($eventhash['sourcetype'] == "M") {
			$sourcetype = "monster";
			$sid = "id";
		}
		if ($eventhash['desttype'] == "P") {
			$desttype = "playercharacter";
			$did = "pcid";
		} elseif ($eventhash['desttype'] == "M") {
			$desttype = "monster";
			$did = "id";
		}
		
		// Source
		$sourceid = $eventhash['sourceid'];
		$sourcenamesql = mysql_query("SELECT name FROM $sourcetype WHERE $sid = \"$sourceid\"",$mysql);
		$sourcenameres = mysql_fetch_row($sourcenamesql);
		$sourcename = $sourcenameres[0];

		// Destination
		$destid = $eventhash['destid'];
		$destnamesql = mysql_query("SELECT name FROM $desttype WHERE $did = \"$destid\"",$mysql);
		$destnameres = mysql_fetch_row($destnamesql);
		$destname = $destnameres[0];
	
		// Action
		if ($eventhash['actionid'] == 'A') {
			$actionid = "Attack";
		} elseif ($eventhash['actionid'] == 'H') {
			$actionid = "Held Action";
		} elseif ($eventhash['actionid'] == 'S') {
			$actionid = "Spell";
		} elseif ($eventhash['actionid'] == 'I') {
			$actionid = "Item Used";
		}

		// Spell
		$spellname = $eventhash['spell'];

		// Item
		$itemname = $eventhash['item'];

		// HP Adjustment
		$hpadj = $eventhash['hpadj'];
		
		// HP Adjustment Type
		if ($eventhash['hpadjtype'] == 'D') {
			$hpadjtype = "Damage";
		} elseif ($eventhash['hpadjtype'] == 'H') {
			$hpadjtype = "Healed";
		}

		// Saving Throws
		if ($eventhash['sthrow'] == 'M') {
			$sthrow = "Made Save";
		} elseif ($eventhash['sthrow'] == 'F') {
			$sthrow = "Failed Save";
		}

		// Killed?
		if ($eventhash['destkill'] == 'Y') {
			$destkill = "->Killed<-";
		}

		// Date
		$date = $eventhash['date'];

		// Enterer
		$enterer = $eventhash['enterer'];

		// Round Number
		$break = " ";
		$roundannounce = "";
		if ($date != $rounddate) {
			$roundannounce = "Round $roundnumber @ $date<br>";
			$roundnumber++;
			$break = "<hr>";
		}
		$rounddate = $date;	

		// Print this shit out
		print "$break";
		print "$roundannounce";
		print "$sourcename does $actionid $spellname $itemname to $destname for $hpadj $hpadjtype <b>$sthrow $destkill</b><br>";
		
	}
}









include "footer.php"
?>



</body>
</html>
