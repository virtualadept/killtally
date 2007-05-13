<?php include "include.php" ?>
<html>
<head>
<title>Game Editor</title>
</head>
<body>
<?

$gameid = mysql_real_escape_string($_POST['gameid']);
$gamename = mysql_real_escape_string($_POST['gamename']);
$mode = mysql_real_escape_string($_POST['mode']);
$active = mysql_real_escape_string($_POST['active']);
$whowhere = $_POST['whowhere'];

if (!$gameid && !$mode) {
	print "Select game to edit: ";
	print "<form action=\"gameedit.php\" method=\"post\">";
	print "<select name=\"gameid\">";
	$gamesql = mysql_query("SELECT gameid,name FROM game ORDER BY name ASC", $mysql);
	while (list($gameid,$gamename) = mysql_fetch_array($gamesql)) {
		print "<option value=\"$gameid\">$gamename</option>";
	}
	print "<option value=\"addnewgame\">Add new game</option>";
	print "</select>";
	print "<input type=\"submit\" value=\"Edit Game\">";
}

if ($gameid && !$mode && ($gameid != 'addnewgame')) {
	print "Edit Game: ";
        print "<form action=\"gameedit.php\" method=\"post\">";
        $gamesql = mysql_query("SELECT name,active,date,enterer FROM game WHERE gameid=\"$gameid\"", $mysql);
       	list($gamename,$active,$date,$enterer) = mysql_fetch_array($gamesql);
        print "<li>Last Edited On: $date by $enterer";
	print "<li>Name: ";
        print "<input type=\"text\" name=\"gamename\" value=\"$gamename\">";
        print "<li>Active: ";
        print "Yes <input type=\"radio\" name=\"active\" value=\"1\" checked>";
        print "No <input type=\"radio\" name=\"active\" value=\"0\">";
	print "<li>Characters Involved<br>";
	
	// Pull data from whowhere table to see who's in what game
	$wwsql = mysql_query("SELECT pcid FROM whowhere WHERE gameid=\"$gameid\"",$mysql);
	while (list($wwsqlout) = mysql_fetch_array($wwsql)) {
		$wwpcid["$wwsqlout"] = "1";
	}

	// Get all characters and flag the ones that are in the game already
	$charnamesql = mysql_query("SELECT pcid,name,player FROM playercharacter WHERE active=\"1\"",$mysql);
	while (list($pcid,$pcname,$player) = mysql_fetch_array($charnamesql)) {
		$charselect =  "<input type=\"checkbox\" name=\"whowhere[]\" value=\"$pcid\"";
		if (isset($wwpcid["$pcid"])) {
			$charselect .= " checked>$pcname ($player) <a href=\"characteredit.php?pcid=$pcid\">Edit</a><br>";
		} else {
			$charselect .= ">$pcname ($player) <a href=\"characteredit.php?pcid=$pcid\">Edit</a><br>";
		}
		print $charselect;
	}
	print "<input type=\"hidden\" name=\"gameid\" value=\"$gameid\">";
        print "<input type=\"hidden\" name=\"mode\" value=\"update\">";
        print "<br><input type=\"submit\" value=\"Save Changes\">";
}

if ($gameid == 'addnewgame' && !$mode) {
	print "Enter new Game<br>";
	print "<form action=\"gameedit.php\" method=\"post\">";
	print "<li>Name: <input type=\"text\" name=\"gamename\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"addgame\">";
	print "<br><input type=\"submit\" value=\"Save Changes\">";
}

if ($mode == 'addgame' && $gamename) {
	$gameaddsql = mysql_query("INSERT INTO game (name,active,date,enterer) VALUES (\"$gamename\",\"1\",NOW(),\"$username\")",$mysql);
	if ($gameaddsql) {
		print "Game added, <a href=\"gameedit.php\">Go Add Players</a>";
	} else {
		print "Something went wrong";
	}
}

if ($mode == 'update' && $gameid && $gamename && $active) {
	$gameupdatesql = mysql_query("UPDATE game SET name=\"$gamename\", active=\"$active\", date=NOW(), enterer=\"$username\" WHERE gameid=\"$gameid\"",$mysql);
	
	// Here we delete the entire game, if this isnt set, no new players
	$whowheredel = mysql_query("DELETE FROM whowhere WHERE gameid=\"$gameid\"",$mysql);
	
	// If whowhere is set, then there are players to be added
	if ($whowhere) {
		foreach ($whowhere as $pcid) {
			$whowhereadd = mysql_query("INSERT INTO whowhere (gameid,pcid) VALUES (\"$gameid\",\"$pcid\")",$mysql);
		}
	
	}
	print "Completed, <a href=\"gameedit.php\">Go Lookie!</a>";
}

include "footer.php";
?>
</body>
</html>
