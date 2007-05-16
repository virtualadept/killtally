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
	$eventsql = mysql_query("SELECT st.sourceid,st.actionid,sp.name AS spell,it.name AS item,st.destid,st.hpadj,st.sthrow,st.destkill,st.date,st.enterer,st.sourcetype,st.desttype,st.hpadjtype FROM stattally st LEFT JOIN spell sp ON st.spellid = sp.id  LEFT JOIN item it ON st.itemid = it.id  WHERE eventid=\"$eventid\"",$mysql);
	// And it starts.. :\
	while ($eventhash = mysql_fetch_assoc($eventsql)) {
		if ($eventhash['sourcetype'] = "P") {
			$sourcetype = "playercharacter";
			$id = "pcid";
		} else {
			$sourcetype = "monster";
			$id = "id";
		}
		if ($eventhash['desttype'] = "P") {
			$desttype = "playercharacter";
			$id = "pcid";
		} else {
			$desttype = "monster";
			$id = "id";
		}

		$sourceid = $eventhash['sourceid'];
		$sourcenamesql = mysql_query("SELECT name FROM $sourcetype WHERE $id = \"$sourceid\"",$mysql);
		$sourcename = mysql_fetch_row($sourcenamesql);

		Print "Source: $sourcename[0]";
	}
}









include "footer.php"
?>



</body>
</html>
