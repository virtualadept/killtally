<?php include "include.php" ?>
<html>
<head>
<title>Game Editor</title>
</head>
<body>
<?

$mode = mysql_real_escape_string($_GET['mode']);
$gameid = mysql_real_escape_string($_GET['gameid']);
$pcid = mysql_real_escape_string($_GET['pcid']);

// Top 10 CR creatures killed by Game
if ($mode == 'crbygameid' && $gameid) {
	$gamesql = mysql_query("SELECT name FROM game WHERE gameid=\"$gameid\"", $mysql);
	$gamename = mysql_fetch_array($gamesql);
	print "Top 10 Monsters Killed In $gamename[0]<br>";
	$crgameidsql = mysql_query("SELECT DISTINCT kt.date,m.name,m.cr from killtally kt JOIN monster m USING(foeid) WHERE kt.foeid = m.foeid AND gameid=\"$gameid\" ORDER BY m.cr DESC LIMIT 10", $mysql);
	print "<table border=1><tr><th>Date</th>";
	print "<th>Creature Name</th><th>CR</tr>";
	while ($row = mysql_fetch_row($crgameidsql)) {
		print "<tr>";
		foreach ($row as $field) {
			print "<td>$field</td>";
		}
		print "</tr>";
	}
	print "</table>";
}





/* Queries
mysql> select distinct kt.date,kt.pcid,pc.name,m.name,m.cr from killtally kt JOIN monster m USING(foeid) JOIN playercharacter pc USING(pcid) WHERE kt.pcid=pc.pcid AND kt.foeid = m.foeid AND gameid='2' AND stat='K' ORDER BY m.cr DESC;
+---------------------+------+----------+----------------------------+------+
| date                | pcid | name     | name                       | cr   |
+---------------------+------+----------+----------------------------+------+
| 2007-05-02 21:05:31 |    2 | Pal'Adin | Androsphinx                |    9 |
| 2007-05-02 21:06:20 |    2 | Pal'Adin | 1st-Level Astral Construct |    1 |
+---------------------+------+----------+----------------------------+------+
2 rows in set (0.00 sec)

---

*/



include "footer.php"
?>



</body>
</html>
