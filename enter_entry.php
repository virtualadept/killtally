<?php
include "include.php";
?>
<html>
<head>
<title> Saved! <title>
</head>
<body>
Your entry of:<br>
<?php
// Hate!
$gameid = mysql_real_escape_string($_GET['gameid']);
$pcid = mysql_real_escape_string($_GET['pcid']);
$foe = mysql_real_escape_string($_GET['foe']);
$cr = mysql_real_escape_string($_GET['cr']);

$enterkill = mysql_query("INSERT INTO killtally (gameid,pcid,foe,challengerating,enterer,date) VALUES (\"$gameid\",\"$pcid\",\"$foe\",\"$cr\",\"$username\",NOW())");

if ($enterkill != FALSE) {
	// Print out the results!
	$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql); 
	$gamename = mysql_fetch_row($gamesql);

	$pcsql = mysql_query("SELECT name FROM playercharacter WHERE pcid=\"$pcid\"", $mysql); 
	$pcname = mysql_fetch_row($pcsql);

	print "<b>$cr</b> CR points awarded to <b>$pcname[0]</b> for the slaying of a <b>$foe</b> in the game <b>$gamename[0]</b> by <b>$username</b>";
} else {
	print "Didnt get entered, something screwed up";
}
?>
