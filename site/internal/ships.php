<?php
# =============================================================================
#
# ships.php
#
# Shipment Listing Page
#
# $Id: ships.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: ships.php,v $
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


if ($action == 1) {

   //update carrierbookeddate, carrierbooked flag
   $timenow = getdate();
   $thetime2 = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];
	
   $thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'];
   $updatequery = mysql_query("UPDATE shipment set carrierbooked = 1, carrierbookeddate = '$thetime' where shipmentid=$shipmentid and carrierbooked = 0") or die (mysql_error()); 
   //update shipmentstatus
   // check if it's already there
   $statcheck = mysql_query("Select statusid from shipmentstatus where shipmentid = $shipmentid and statusdetails = 'AWAITING PICKUP'") or die (mysql_error());
	if (!(mysql_fetch_row($statcheck))) {
	   $statup = mysql_query("INSERT INTO shipmentstatus (shipmentid, statusdetails, statuscode, statustime) values ($shipmentid, 'AWAITING PICKUP', 2, '$thetime2')") or die (mysql_error());
	}
}


//get ships
$shipquery = mysql_query ("Select shipmentid, customerid, customers.name, customers.company, finalar from shipment, customers where customers.custid = shipment.customerid and carrierbooked = 0") or die (mysql_error());

//get old shipments
$shipqueryold = mysql_query ("Select shipmentid, shipment.customerid, customers.name, customers.company, finalar, carrierpro, deliveryest, pickupdate, quotes.weight, quotes.class, shipment.carrierid from shipment, customers, quotes where customers.custid = shipment.customerid and shipment.quoteid = quotes.quoteid and carrierbooked = 1 and delivered = 0 order by pickupdate") or die (mysql_error());

// get delivered
$shipquerydel = mysql_query ("Select shipmentid, customerid, customers.name, customers.company, finalar, carrierpro, deliveryest, deliverdate from shipment, customers where customers.custid = shipment.customerid and delivered = 1 order by deliverdate") or die (mysql_error());
?>
<html>
<head>
<title>Outstanding BOLs</title>
</head>
<body bgcolor=ffffff>
<font size="+1"><b>Hello.</b></font><br><br>


<table cellpadding=3>
<tr><td><font size='-1'><b>New Shipments</b></td><td><b><font size='-1'>CUSTOMER</b><td><b><font size='-1'>AR</b></td></tr>
<tr><td colspan=3><font size="-1"><i>TOM AND MIKE - DO NOT JUST CLICK THE "MARK AS BOOKED" LINK BELOW -- IT WILL INTERUPT THE EDI PROCESS ON THE SHIPMENT</i></font></td></tr>
<?php

while($shipline = mysql_fetch_row($shipquery)) {

	echo "<tr><td><a href=update.php?shipmentid=$shipline[0]>BOL #$shipline[0]</a> | <a href=ships.php?shipmentid=$shipline[0]&action=1><font size='-1'>Mark as booked</a></td><td><font size='-1'>$shipline[2], $shipline[3]</td><td><font size='-1'>$$shipline[4]</font></td></tr>";

}
?>
</table>
<br>
<table cellpadding=3>
<tr><td><font size='-1'><b>Undelivered Shipments</b></td><td><b><font size='-1'>CUSTOMER</b><td><b><font size='-1'>AR</b></td><td><b><font size='-1'>CARRIER PRO</b></td><td><b><font size='-1'>WGT</b></td><td><b><font size='-1'>CLS</b></td><td><b><font size='-1'>PICKUP</b></td><td><b><font size='-1'>EST DEL</b></td></tr>
<?php

while($shipline = mysql_fetch_row($shipqueryold)) {

	echo "<tr><td><a href=update.php?shipmentid=$shipline[0]>BOL #$shipline[0]</a></td><td><font size='-1'>$shipline[2], $shipline[3]</td><td><font size='-1'>$$shipline[4]</td><td><font size='-1'>$shipline[5]</font></td><td><font size='-1'>$shipline[8]</font></td><td><font size='-1'>$shipline[9]</font></td><td><font size='-1'>$shipline[7]</font></td><td><font size='-1'>$shipline[6]</font></td><td><font size='-1'>$shipline[10]</td></tr>";

}
?>
</table>
<br>
<table cellpadding=3>
<tr><td><font size='-1'><b>Delivered Shipments</b></td><td><b><font size='-1'>CUSTOMER</b><td><b><font size='-1'>AR</b></td><td><b><font size='-1'>CARRIER PRO</b></td><td><b><font size='-1'>EST DEL</b></td><td><b><font size='-1'>ACT DEL</b></td></tr>
<?php

while($shipline = mysql_fetch_row($shipquerydel)) {

	echo "<tr><td><a href=update.php?shipmentid=$shipline[0]>BOL #$shipline[0]</a></td><td><font size='-1'>$shipline[2], $shipline[3]</td><td><font size='-1'>$$shipline[4]</td><td><font size='-1'>$shipline[5]</font></td><td><font size='-1'>$shipline[6]</font></td><td><font size='-1'>$shipline[7]</font></td><td><a href=invoice.php?ordnumber=$shipline[0]><font size='-1'>VIEW INVOICE</A></FONT></tr>";

}
?>
</body>
</html>