<?php
# =============================================================================
#
# emailer.php
#
# Email Page ?
#
# $Id: emailer.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: emailer.php,v $
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
$host = "127.0.0.1";
// define db
$database = "market";
// open persistent connection to mysql
$db = mysql_pconnect($host, php, password) or die("Cannot Connect to $host<br><br>");
// select database
mysql_select_db($database, $db) or die ("Can't connect to $db<br><br>");



$theshwag = mysql_query("SELECT * from shwag where shwagid = $shwag") or die (mysql_error());
$shwagline = mysql_fetch_array($theshwag);
$timenow = getdate();
$date = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];
$i = 0;
$header = "From: $shwagline[replyaddress]\nContent-Type: text/html; charset=us-ascii";
if ($test and $testaddress) {
	echo "This is a test for $testaddress<br><br>";
	$thereturn = mail($testaddress, $shwagline[subject], $shwagline[html], $header);
	$i++;
	echo "MAIL SENT TO $testaddress: THE RETURN = $thereturn<br><br>";
	
}



else {
	if ($count and $test == 0 and $dbindex) {
		$intolog = mysql_query("INSERT INTO log values ($shwagline[shwagid], $count, '$date')");
		echo "<font color=red>This is not a test<br><br></font>";
		$getmails = mysql_query("SELECT emailid, email from $dbindex where shwagid = 0 order by emailid desc limit $count") or die (mysql_error());
		while($newmail = mysql_fetch_array($getmails)) {
			$mailaddress = $newmail[email];
			$thereturn = mail($mailaddress, $shwagline[subject], $shwagline[html], $header);
			echo "MAIL SENT TO $mailaddress: THE RETURN = $thereturn<br>";
			
			if ($thereturn) {
				$upit = mysql_query("UPDATE $dbindex set shwagid = '$shwagline[shwagid]', date = '$date' where emailid = $newmail[emailid]") or die (mysql_error());
				$i++;
			}
		}
	}
	mail('jstrope@enteract.com,tjjuedes@aol.com,dfl1@msn.com', $shwagline[subject], $shwagline[html], $header);
}

echo "<br><b>$i emails semt</b><br><br>";




?>

