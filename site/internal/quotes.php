<?php
# =============================================================================
#
# quotes.php
#
# List Quotes
#
# $Id: quotes.php,v 1.5 2002/11/14 21:32:26 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: quotes.php,v $
# Revision 1.5  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.4  2002/11/12 16:57:04  youngd
#  * Added FSC to display.
#
# Revision 1.3.2.1  2002/11/12 16:54:52  webdev
#   * Modified to include fsc on display.
#
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

$customerquery = mysql_query("select origin, destination, weight, class, ar, date, booked,fuel_surcharge from quotes where customerid=$customerid order by date desc") or die(mysql_query());
$shipmentquest = mysql_query("select shipmentid, finalar, finalap, pickupdate from shipment where customerid=$customerid order by pickupdate desc");
?>

<html>
<head><title>Quotes</title></head>
<body bgcolor=ffffff>

<table style="font-size: 11px; font-family: tahoma;" cellpadding = 3 cellspacing = 2 width=400>
<tr><td colspan=4><b>SHIPMENT HISTORY</B></TD></TR>
<tr><td><b>PICKUP DATE</b></td><td><b>BOL #</b></td><td><b>AR</b></td><td><b>AP</b></td></tr>
<?php
$totalshipments = 0;
$totalar = 0;
$totalap = 0;
while ($custline = mysql_fetch_array($shipmentquest)) {

	echo "<tr><td>$custline[3]</td><td>$custline[0]</a></td><td>$$custline[1]</td><td>$$custline[2]</td></tr>";
	$totalshipments += 1;
	$totalar += $custline[1];
	$totalap += $custline[2];
}
echo "<tr><td><b>TOTALS:</b></td><td><b>$totalshipments</b></td><td><b>$$totalar</b></td><td><b>$$totalap</b></td></tr>";
?>
</table>

<br>
<table style="font-size: 11px; font-family: tahoma;" cellpadding = 3 cellspacing = 2>
<tr><td colspan=4><b>QUOTE HISTORY</B></TD></TR>
<tr><td><b>ORIGIN</b></td><td><b>DESTINATION</b></td><td><b>WEIGHT</b></td><td><b>CLASS</b></td><td><b>PRICE</b></td><td><b>DATE</b></td></tr>
<?php

while ($custline = mysql_fetch_array($customerquery)) {

	$ar_with_fsc = $custline[ar] + $custline[fuel_surcharge];
	echo "<tr><td>$custline[origin]</a></td><td>$custline[destination]</td><td>$custline[weight]</td><td>$custline[class]</td><td>$ar_with_fsc</td><td>$custline[date] ($custline[booked])</td></tr>";

}
?>
</table>

</body>
