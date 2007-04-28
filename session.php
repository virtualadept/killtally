<?php
session_start();
include "include.php";
print "Welcome $username!<br><br>";

print "Please select which game you wish to administer: ";
print "<form action=\"index.php?gameid=$gameid\"><br>";
print "<select name=\"gameid\">";
$game = mysql_query("SELECT * FROM game where active=\"1\"", $mysql);
while (list($gameid, $gamename) = mysql_fetch_row($game)) {
        print "<option value=\"$gameid\">$gamename</option>";
}
?>
</select><br><br>
<input type="submit" value="Save">


