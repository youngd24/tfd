<?php
# ==============================================================================
#
# cancelled_shipments.php
#
# Page to display the cancelled shipments
#
# $Id: cancelled_shipments.php,v 1.3 2002/10/14 15:28:57 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: cancelled_shipments.php,v $
# Revision 1.3  2002/10/14 15:28:57  youngd
#   * Added the ability to sort the information by column headers.
#
# Revision 1.2  2002/10/14 15:12:10  youngd
#   * Initial version for testing.
#
# Revision 1.1  2002/10/14 14:58:24  youngd
#   * Initial version.
#
# ==============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");

?>

<center>
<h2><font face=verdana>Cancelled Shipments</font></h2>
<font face=verdana size=2><i>(Click on the column headers to sort)</i></font>
</center>
<br>

<?php

if ( $sort == "date" ) {
	# Sort by the cancel date
	$query = "select cancellations.date,cancellations.shipmentid,cancel_reasons.reason from cancellations,cancel_reasons where cancellations.reason = cancel_reasons.id ORDER BY cancellations.date";
} elseif ( $sort == "shipmentid" ) {
	# Sort by the shipmentid (bol number)
	$query = "select cancellations.date,cancellations.shipmentid,cancel_reasons.reason from cancellations,cancel_reasons where cancellations.reason = cancel_reasons.id ORDER BY cancellations.shipmentid";
} elseif ( $sort == "reason" ) {
	# Sort by the cancellation reason
	$query = "select cancellations.date,cancellations.shipmentid,cancel_reasons.reason from cancellations,cancel_reasons where cancellations.reason = cancel_reasons.id ORDER BY cancellations.reason";
} else {
	# Don't sort by anything
	$query = "select cancellations.date,cancellations.shipmentid,cancel_reasons.reason from cancellations,cancel_reasons where cancellations.reason = cancel_reasons.id";
}

echo "<table cellpadding=2 cellspacing=2 align=center>";
echo "<tr>";
echo "<th style='border-style: ridge; border-width: 1' bgcolor=#C0C0C0><font face=verdana size=2><b><a href=$PHP_SELF?sort=date>DATE</a></b></th>";
echo "<th style='border-style: ridge; border-width: 1' bgcolor=#C0C0C0><font face=verdana size=2><b><a href=$PHP_SELF?sort=shipmentid>SHIPMENT</b></td>";
echo "<th style='border-style: ridge; border-width: 1' bgcolor=#C0C0C0><font face=verdana size=2><b><a href=$PHP_SELF?sort=reason>REASON</b></td>";
echo "</tr>";

$result = mysql_query($query) or die (mysql_error());

while($row = mysql_fetch_row($result)) {
	if ( $color == "silver" ) {
		$color = "white";
		echo "<tr bgcolor=white>";
	} else {
		$color = "silver";
		echo "<tr bgcolor=silver>";
	}
	echo "<td><font face=verdana size=2>$row[0]</td>";
	echo "<td><font face=verdana size=2>$row[1]</td>";
	echo "<td><font face=verdana size=2>$row[2]</td>";
	echo "</tr>";
}
echo "</table>";
echo "<br>";

?>


<center>
<font face=verdana size=1>
Cancelled shipments with a reason of 'Entry Error' were cancelled from the internal bill of lading page.
<br>
All others were explicity set when the cancellation was performed.
</font>
<br>
<br>
<font face=verdana size=1>[ </font><a href=/internal/><font face=verdana size=1>Intranet</a> ] </font>
</center>