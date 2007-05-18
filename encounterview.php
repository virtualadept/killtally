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
	print "Please Select EncounterID to view: ";
	print "<form action=\"encounterview.php\">";
	print "<select name=\"eventid\">";
	$eventidsql = mysql_query("SELECT DISTINCT st.eventid,g.name,st.date FROM stattally st JOIN game g USING(gameid) WHERE st.gameid = g.gameid AND st.gameid = \"$gameid\" ORDER BY st.eventid ASC",$mysql);
	while (list($eventid,$gamename,$date) = mysql_fetch_row($eventidsql)) {
		print "<option value=\"$eventid\">$gamename @ $date</option>";
	}
	print "</select>";
	print "<br>";
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
	print "<input type=\"submit\" value=\"Show Me!\">";
	print "</form>";

}

/*
        id: 105
   eventid: 1179336497
  sourceid: 2
  actionid: S
   spellid: 95
    itemid: 0
    destid: 648
     hpadj: 0
    sthrow: F
  destkill: Y
      date: 2007-05-16 10:28:17
   enterer: brandon
sourcetype: P
  desttype: M
    gameid: 2
 hpadjtype: NULL
1 row in set (0.00 sec)
*/


if ($gameid && $eventid) {
	print "Details for $eventid:<br>";
	$eventsql = mysql_query("SELECT st.sourcetype,st.sourceid,st.desttype,st.destid,st.actionid,sp.name AS spell,it.name AS item,st.hpadj,st.sthrow,st.destkill,st.date,st.enterer,st.hpadjtype FROM stattally st LEFT JOIN spell sp ON st.spellid = sp.id  LEFT JOIN item it ON st.itemid = it.id  WHERE st.eventid=\"$eventid\"",$mysql);
	// And it starts.. :\
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
			$sthrow = "Made";
		} elseif ($eventhash['sthrow'] == 'F') {
			$sthrow = "Failed";
		}

		// Killed?
		if ($eventhash['sthrow'] == 'Y') {
			$destkill = "Killed";
		}

		// Date
		$date = $eventhash['date'];

		// Enterer
		$enterer = $eventhash['enterer'];


		print "On $date by $enterer<br>";
		print "Source: $sourcename<br>";
		print "Dest: $destname<br>";
	 	print "Action: $actionid<br>";
		print "Spell: $spellname<br>";
		print "Item: $itemname<br>";
		print "HP Adj: $hpadj<br>";
		print "Save: $sthrow<br>";
		print "$hpadjtype: $hpadj<br>";
		print "killed: $destkill<br><br>";
		
		print_r($eventhash);
	}
}









include "footer.php"
?>



</body>
</html>