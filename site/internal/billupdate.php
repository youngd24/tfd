<?php
# ==============================================================================
#
# billupdate.php
#
# Internal BOL update page
#
# $Id: billupdate.php,v 1.7 2002/10/25 22:26:01 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: billupdate.php,v $
# Revision 1.7  2002/10/25 22:26:01  youngd
#   * All works now.
#
# Revision 1.6  2002/10/25 20:57:41  youngd
#   * Added final ap and ar displays to the page.
#
# Revision 1.5  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.4  2002/10/16 06:47:44  youngd
#   * Added standard source header and normalized includes.
#
# Revision 1.3  2002/10/11 15:02:18  youngd
#   * Rewored includes to be consistent and actually work. Removed the
#     variable includes.
#
# Revision 1.2  2002/10/07 17:25:53  youngd
#   * Added source header.
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

if ($apply) {
	// first get accessorials to determine finalar, finalap
	$shipmentaccq = mysql_query("select accessorials.name, accessorials.charge, accessorials.ap from accessorials, shipmentaccessorials where accessorials.assid = shipmentaccessorials.assid and shipmentaccessorials.shipmentid = $shipmentid");
	
	$quotedet = mysql_query("SELECT quotes.quoteid, quotes.carrierid, quotes.origin, quotes.destination, quotes.class, quotes.weight, quotes.ap, quotes.ar, quotes.customerid, quotes.fuel_surcharge from quotes where quotes.quoteid = $quoteid") or die (mysql_error());
	
	$quote = mysql_fetch_array($quotedet);

	$shipmentqry = mysql_query("SELECT finalar, finalap from shipment where shipmentid = $shipmentid");
	$shipment = mysql_fetch_array($shipmentqry);
	debug("billupdate.php: shipment ap and ar here is set to $shipment[0] and $shipment[1]");

	$artot = $quote[ar];
	$aptot = $quote[ap];

	while ($assline = mysql_fetch_array($shipmentaccq)) {
		debug("billupdate.php: adding $assline[name]");

		debug("billupdate.php: adding $assline[charge] to artot ($artot)");
		$artot += $assline[charge];

		debug("billupdate.php: adding $assline[ap] to aptot ($aptot)");
		$aptot += $assline[ap];
	
	}

	// Add the FSC before we shove the new shipment record in
	$artot += $quote[fuel_surcharge];
	$aptot += $quote[fuel_surcharge];
	
	// update shipmenttable (quoteid, finalar, finalap)
	$updateq = mysql_query("update shipment set quoteid = $quoteid, finalar = $artot, finalap = $aptot where shipmentid = $shipmentid") or die (mysql_error());


	# -------------------------------------------------------------------------
	# UPDATE ACCOUNTING
	# -------------------------------------------------------------------------
	
	//check to see if an invoice has been created.
	require('../zzaccounting.php');
	$conn = postgresconnect() || print "Unable to connect to postgres";
	
	// get the transids
	$sotransq = pg_exec("select id from oe where ordnumber = '$shipmentid' and customer_id != 0") or die(pg_errormessage());
	$potransq = pg_exec("select id from oe where ordnumber = '$shipmentid' and vendor_id != 0") or die(pg_errormessage());
	$soid = pg_fetch_array($sotransq);
	$poid = pg_fetch_array($potransq);
	
	if ($poid) {
		$pocheck = "update oe set amount = $aptot, netamount = $aptot where id=$poid[0]";
		$soq = pg_exec($pocheck) or die(pg_errormessage());
		$soq = pg_exec("update orderitems set sellprice = $aptot where trans_id = $poid[0]");
	}
	else {
		echo "<font color=red>WARNING: COULD NOT UPDATE AP VALUE.</FONT><br>";
		//mail("jstrope@enteract.com", "BAD UPDATE", "could not update ap value on shipmentid $shipmentid");
	}
	
	if ($soid) {
		$salesordercheck = "update oe set amount = $artot, netamount = $artot where id=$soid[0]";
		$soq = pg_exec($salesordercheck) or die(pg_errormessage());
		$soq = pg_exec("update orderitems set sellprice = $artot where trans_id = $soid[0]");
	}
	else {
		echo "<font color=red>WARNING: COULD NOT UPDATE AR VALUE.</FONT><br>";
		//mail("jstrope@enteract.com", "BAD UPDATE", "could not update ar value on shipmentid $shipmentid");
	}
	 
	postgresclose($conn);

} // Done if apply


# -----------------------------------------------------------------------------
# RE-RATE THE SHIPMENT
# -----------------------------------------------------------------------------
if ($origin and $destination and $weight and $shipclass and !($apply)) {
	$userarrayq = mysql_query("select * from customers where custid = $customerid") or die(mysql_error());
	$userarray = mysql_fetch_array($userarrayq);
	require('../zzgetprice.php');
	$quotedet = mysql_query("SELECT quotes.quoteid, quotes.carrierid, quotes.origin, quotes.destination, quotes.class, quotes.weight, quotes.ap, quotes.ar, quotes.customerid from quotes where quotes.quoteid = $quoteid") or die (mysql_error());
	$quote = mysql_fetch_array($quotedet);

	$shipmentqry = mysql_query("SELECT finalar, finalap from shipment where shipmentid = $shipmentid");
	$shipment = mysql_fetch_array($shipmentqry);
	debug("billupdate.php: shipment ap and ar here is set to $shipment[0] and $shipment[1]");
}

# -----------------------------------------------------------------------------
# JUST DISPLAY THE INFORMATION
# -----------------------------------------------------------------------------
else {

	// get quotedetails
	$quotedet = mysql_query("SELECT quotes.quoteid, quotes.carrierid, quotes.origin, quotes.destination, quotes.class, quotes.weight, quotes.ap, quotes.ar, shipment.customerid from shipment, quotes where shipment.quoteid = quotes.quoteid and shipment.shipmentid = $shipmentid") or die (mysql_error());
	$quote = mysql_fetch_array($quotedet);

	$shipmentqry = mysql_query("SELECT finalar, finalap from shipment where shipmentid = $shipmentid");
	$shipment = mysql_fetch_array($shipmentqry);
	debug("billupdate.php: shipment ap and ar here is set to $shipment[0] and $shipment[1]");
}

//we will have quoteid by now

//get carrierpro, pickupdate, estdel
if ($dateup) {
	$dateupq = mysql_query("update shipment set carrierpro = '$carrierpro', pickupdate = '$pickupdate', deliveryest = '$deliveryest' where shipmentid = $shipmentid");
}

$datedetailq = mysql_query("select carrierpro, pickupdate, deliveryest from shipment where shipmentid = $shipmentid") or die(mysql_error());
$datedetail = mysql_fetch_array($datedetailq);

//get accessorials where carrierid = carrierid, get current accessorials for this shipment



?>

<html>
<head><title>UPDATE SHIPMENT</title></head>
<body bgcolor=ffffff>
<b>MODIFYING BOL #<?PHP echo $shipmentid; ?></b><br><Br>
<b>MODIFY WEIGHT/CLASS (RERATE)</B><br>
<form method=post action=billupdate.php>
<blockquote>
<table style="font-family: arial; font-size: 11px;">
	<tr>
		<td><b>QUOTEID</b<</td>
		<td><b>ORIGIN</B></TD>
		<TD><B>DESTIN</B></TD>
		<TD><B>WEIGHT</B></TD>
		<TD><B>CLASS</B></TD>
		<TD><B>QUOTE AR</B></TD>
		<TD><B>FINAL AR</B></TD>
		<TD><B>QUOTE AP</B></TD>
		<TD><B>FINALAP</B></TD>
	</TR>
<?php
echo "<tr><td>$quote[quoteid]</td><td><input type=hidden name=origin value=$quote[origin]>$quote[origin]</td><td><input type=hidden name=destination value=$quote[destination]>$quote[destination]</td><td><input size=5 name=weight value=$quote[weight]></td><td><input size=4 name=shipclass value=$quote[class]></td><td>$$quote[ar]</td><td>$$shipment[finalar]</td><td>$$quote[ap]</td><td>$$shipment[finalap]</td></tr>";
echo "<input name=precarrierid type=hidden value=$quote[carrierid]><input name=customerid type=hidden value=$quote[customerid]><input name=shipmentid type=hidden value=$shipmentid>";
?>

<tr><Td colspan=5><input type=submit value="RATE"> 
<?php
if ($quoteid) {

	ECHO " <input type=hidden name=quoteid value=$quoteid><input type=submit name=apply value='APPLY NEW RATE'>";
}
?>
</td></tR>
</table>
</form>
</blockquote>
<b>MODIFY CARRIER PRO, ESTIMATED DELIVERY, PICKUP DATE</b><br><Br>
<blockquote><form method=post action=billupdate.php>
<?php

echo "CARRIERPRO: <input size=20 name=carrierpro value=$datedetail[carrierpro]><br>";
echo "PICKUPDATE (YYYY-MM-DD): <input size=15 name=pickupdate value=$datedetail[pickupdate]><br>";
echo "EST DELIVERY (YYYY-MM-DD): <input size=15 name=deliveryest value=$datedetail[deliveryest]><br>";
echo "<input type=hidden name=shipmentid value=$shipmentid>";
?>
<input type=submit name=dateup>
</blockquote>
</form><b>ADD ACCESSORIALS</b><br><Br>



</form> 
<a href="ships.php">BACK TO SHIPMENT LIST</a>
</body>
</html>
