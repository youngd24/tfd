<?php
# =============================================================================
#
# tomreport.php
#
# Tom's Reports
#
# $Id: tomreport.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: tomreport.php,v $
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


if ($action) {
	$min = $year . "-" . $month . "-" . $day;
	$max = $year2 . "-" . $month2 . "-" . $day2;
	$results = mysql_query("SELECT sum(finalar), count(shipmentid) from shipment where pickupdate >= '$min' and pickupdate <= '$max'") or die(mysql_error());
	$min .= " 00:00:00";
	$max .= " 11:59:59";
	$results2 = mysql_query("SELECT count(custid) from customers where regdate >= '$min' and regdate <= '$max'") or die(mysql_error());
	$results3 = mysql_query("SELECT count(quoteid), sum(ar) from quotes where date >= '$min' and date <= '$max'") or die(mysql_error());
	$line1 = mysql_fetch_row($results);
	$line2 = mysql_fetch_row($results2);
	$line3 = mysql_fetch_row($results3); 
}

?>

<html>
<head>
<title>Tom's Reports</title>
</head>
<body bgcolor=ffffff>
<?php
if ($action) {
   echo "<table style='font-family: arial; font-size: 11px;'>";
   echo "<tr><td colspan=2><b>RESULTS FOR RANGE OF $min - $max</td></tr>";
	echo "<tr><td><b>TOTAL SHIPMENTS</b></td><td>$line1[1]</td></tr><tr><td><b>TOTAL REVENUE</b></td><td>$line1[0]</td></tr><tr><td><b>TOTAL REGISTRATIONS</b></td><td>$line2[0]</td></tr><tr><td><b>TOTAL QUOTES</b></td><td>$line3[0]</td></tr><tr><td><b>TOTAL QUOTE VALUE</b></td><td>$line3[1]</td></tr>";
	echo "</table>";
}

?>
<br><font face=arial size =2>

<form method=get action=tomreport.php>
ENTER DATE RANGE mm/dd/yyyy - mm/dd/yyyy:<br>
<input size=4 name=month> / <input size=4 name=day> / <input size=4 name=year> to <input size=4 name=month2> / <input size=4 name=day2> / <input size=4 name=year2><br>
<input type=submit name=action>

</form>

</body>
</html>