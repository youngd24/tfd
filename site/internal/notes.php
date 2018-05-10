<?php
# =============================================================================
#
# notes.php
#
# Add notes to a customer record
#
# $Id: notes.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: notes.php,v $
# Revision 1.3  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.2  2002/10/16 06:47:44  youngd
#   * Added standard source header and normalized includes.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");

// define host for db
$host = "localhost";
// define db
$database = "custnotes";
// open persistent connection to mysql
$db = mysql_pconnect($host, php, password) or die("Cannot Connect to $host<br><br>");
// select database
mysql_select_db($database, $db) or die ("Can't connect to $db<br><br>");


if ($action) {
	// retrieve current time and assign to $timenow
	$timenow = getdate();
	$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];
	$noteins = mysql_query("insert into note values ($custid, '$note', '$thetime')") or die (mysql_error());
	if ($email and $note) {
		mail("$email", "NOTE ALERT", $note, "FROM: csr@thefreightdepot.com");
	
	}
}


$notesquery = mysql_query("SELECT * from note where custid = $custid") or die(mysql_error());
?>

<html>
<head>
<title>CUSTOMER NOTES</title>
</head>

<body bgcolor=ffffff>
<font size=2 face=tahoma>
<b>ADD A NEW NOTE</b><br>
<form method=post action=notes.php>
<?php
echo "<input type=hidden name=custid value=$custid>";
?>
<input type=hidden name=action value=1>
<textarea name=note cols=50 rows=5></textarea><br>
EMAIL THIS NOTE TO (SEPERATE MULTIPLE ADDRESSES WITH A COMMA): <input size=20 name=email><br>
<input type=submit></form>
</font><pre>
<?php

while($note = mysql_fetch_array($notesquery)) {
	echo "<b>$note[date]</b>\n";
	echo "$note[note]";
	echo "\n\n";

}
?></pre>
<font size=2 face=tahoma><a href="custs.php">BACK TO CUSTOMER LIST</a></font>
</body>

</html>