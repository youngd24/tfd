<?php
# =============================================================================
#
# shwaglog.php
#
# Old Marketing Page
#
# $Id: shwaglog.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: shwaglog.php,v $
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

//get a list of the emails
$shwagq = mysql_query("select shwagid, name from shwag");

//get all send dates - time
$logqsql = "SELECT sum(totalemails), date from log";
if ($message) {
	$logqsql .= " where shwagid=$message";

}
$logqsql .= " group by date";
$logdates = mysql_query($logqsql);
$i = 0;
while ($logline = mysql_fetch_array($logdates)) {

	$datelogs[$i][0] = substr($logline[1], 0, 10);
	$datelogs[$i][1] = $logline[0];
	$i++;
}


// define host for db
$host = "127.0.0.1";
// define db
$database = "digiship";
// open persistent connection to mysql
$db = mysql_pconnect($host, php, password) or die("Cannot Connect to $host<br><br>");
// select database
mysql_select_db($database, $db) or die ("Can't connect to $db<br><br>");
echo "<table WIDTH=400><tr><td colspan=3>SHOW LOG FOR:";
while ($shwags = mysql_fetch_array($shwagq)) {
	echo " <a href=shwaglog.php?message=$shwags[0]>$shwags[1]</a>";
}
echo "</tr><tr><td><b>DATE</B></TD><TD ALIGN=RIGHT><b>EMAILS SENT</b></TD><TD ALIGN=RIGHT><b>REGISTRATIONS</b></TD></TR>";
for ($m = 0; $m < $i; $m++) {
	$sql = "select count(custid) from customers where regdate like '" . $datelogs[$m][0] . "%'";
	$custquery = mysql_query($sql) or die(mysql_error());
	$custline = mysql_fetch_array($custquery);
	echo "<tr><td>" . $datelogs[$m][0] . "</td><td ALIGN=RIGHT>" . $datelogs[$m][1] . "</td><td ALIGN=RIGHT>$custline[0]</td></tr>";

}
