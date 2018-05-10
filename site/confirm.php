<?php
# ==============================================================================
#
# confirm.php
#
# Shipment confirmation page
#
# $Id: confirm.php,v 1.13 2003/01/24 18:45:15 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: confirm.php,v $
# Revision 1.13  2003/01/24 18:45:15  webdev
#   * Added display of new quantity and pallet info.
#
# Revision 1.12  2002/10/25 20:30:21  youngd
#   * Confirm displays rate with fsc included now
#
# Revision 1.11  2002/10/25 20:25:27  youngd
#   * Schedule page now correctly displays the total cost with surcharge
#   * Schedule page shows CALL if transit is 0
#   * Same changes to confirm.
#
# Revision 1.10  2002/10/25 19:59:44  youngd
#   * Fuel surcharges are now inserted into the shipment record.
#
# Revision 1.9  2002/10/25 19:41:22  youngd
#   * Accessorial amounts are correctly commited to the shipment record.
#
# Revision 1.8  2002/10/11 20:05:18  youngd
#   * Reworked accessorials which work now.
#
# Revision 1.7  2002/09/19 02:55:17  youngd
#   * Schedule change updates
#
# Revision 1.6  2002/09/13 00:13:58  webdev
#   * Adjusted headers
#
# Revision 1.5  2002/09/13 00:12:44  webdev
#   * Added some comments
#
# Revision 1.4  2002/09/13 00:11:44  webdev
#   * Added php source header
# 
# ==============================================================================


// Set this page so it never gets cached. This must be done before the cookies
// are set since they have to happen in the header before any data is sent to
// the browser.
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

// get cookie and user
require('zzgrabcookie.php');

if ($quoteid) {
	// get all the quote information

	$quoteinfo = mysql_query("SELECT * from quotes where quoteid = '$quoteid' and customerid = '$userarray[0]'") or die(mysql_error());
	$quoteline = mysql_fetch_row($quoteinfo);

	// if new address is passed, insert addresses
	require('zzaddress.php');

	// update charge for accessorials
	debug("confirm.php: accessorials I received are $accessorials");
	// require('zzaccessorials.php');

	// total charges equals quoted price plus accessorials from zzaccessorials
	$totalcharges = $quoteline[9] + $additionalcharges;
	$totalcost = $quoteline[8] + $additionalcharges;

	debug("confirm.php: totalcharges here is $totalcharges");
	debug("confirm.php: totalcost here is $totalcosr");

	debug("confirm.php: fuel surcharge here (quoteline[14]) is $quoteline[14]");

	// if prepaid, billing goes to shipper. if collect, consignee. if thirdparty, assign insert id from zzaddress
	if  ($billingmethod == 1) {
		$hidebilling = "$shiporiginid";
	}
	elseif ($billingmethod == 2) {
		$hidebilling = "$shipdestid";
	}
	else {
		$hidebilling = "$shipbillid";
	}

	// assign pickup date and then the estimated delivery date based on the transit time
	
	// first explode pickupdate
	$hidearray = explode("-", $hidepickupdate);
	$pickupyear = $hidearray[0];
	$pickupmonth = $hidearray[1];
	$pickupday = $hidearray[2];

	
	// m steps loop
	$m = 1;
	// n is total number of days to add on
	$n = $quoteline[12];
	
	// NOTE: xmas, new years, july 4, memorial day, thanksgiving
	while ($m <= $n) {
		$heyday = date('l', mktime(0,0,0,$pickupmonth,($pickupday + $m),$pickupyear));
		$heymd = date('md', mktime(0,0,0,$pickupmonth,($pickupday + $m),$pickupyear));
		if ($heyday == "Saturday" or $heyday == "Sunday" or $heymd == 1225 or $heymd == 0704 or $heymd == 0507 or $heymd == 1122) {
			$n++;
		}
		$m++;
	}
	$hidedeldate = date('Y-m-d', mktime(0,0,0,$pickupmonth,$pickupday + $n,$pickupyear));
	
	// test for holiday
	if ($quoteline[12] <= 0) {
		$hidedeldate = "0000-00-00";
	}
	
	
	// assign pickup time. pickupdate = $hidepickupdate from calendar on previous page	
	if ($ampm == "am" and $close == 12) {
		$hidepickuptime = 0;
	}
	elseif ($ampm == "pm" and $close == 12) {
		$hidepickuptime = 12;
	}
	elseif ($ampm == "am" and $close != 12) {
		$hidepickuptime = $close;
	}
	elseif ($ampm == "pm" and $close != 12) {
		$hidepickuptime = $close + 12;
	}
	else {
		$hidepickuptime = 0;
	}
	$hidepickuptime .= "0000";
	
	// assign pickup after
	if ($afterampm == "am" and $after == 12) {
		$hidepickupafter = 0;
	}
	elseif ($afterampm == "pm" and $after == 12) {
		$hidepickupafter = 12;
	}
	elseif ($afterampm == "am" and $after != 12) {
		$hidepickupafter = $after;
	}
	elseif ($afterampm == "pm" and $after != 12) {
		$hidepickupafter = $after + 12;
	}
	else {
		$hidepickupafter = 0;
	}
	$hidepickupafter .= "0000";

	// get origin info for display $shiporiginid comes from zzaddress
	$getorigin = mysql_query("SELECT * from address where addressid = $shiporiginid and custid = $userarray[0]") or die(mysql_error());
	$originline = mysql_fetch_row($getorigin);

	// get destination info for display $shipdestid comes from zzaddress
	$getdestination = mysql_query("SELECT * from address where addressid = $shipdestid and custid = $userarray[0]") or die(mysql_error());
	$destinationline = mysql_fetch_row($getdestination);

	// get billing info for display if billing is thirdparty from zzaddress
	if ($billingmethod == 3) {
		$getbilling = mysql_query("SELECT * from address where addressid = $shipbillid and custid = $userarray[0]") or die(mysql_error());
		$billingline = mysql_fetch_row($getbilling);
	}

	// set a cookie named digishiptransaction with all the info
	// the structure looks like this:
	// origin,destination,quoteid,carrierid,billing,finalar,finalap,pickupdate,pickupbefore,
	// ponumber,productdesc,pieces,hazmatyesno,hazmatemergencyphone,pickupafter,deliverydate,
	// speci,fuel_surcharge,packagingtype,palletized,palletqty

	setcookie("digishiptransaction", "$shiporiginid|$shipdestid|$quoteline[0]|$quoteline[2]|$hidebilling|$totalcharges|$totalcost|$hidepickupdate|$hidepickuptime|$ponumber|$productdesc|$pieces|$hazmat|$hazmatphone|$hidepickupafter|$hidedeldate|$speci|$quoteline[14]|$packagingtype|$palletized|$palletqty", 0, "/", "", 0);
}
else {
	die();
}

// format in case 00001 on zips
$disporigin = sprintf('%05d' , $originline[6]);
$dispdestination = sprintf('%05d' , $destinationline[6]);
$dispbilling = sprintf('%05d' , $billingline[6]);

// format total charges
$totalcharges = sprintf('%01.2f', $totalcharges);
?>

<html>
<head>
<title>The Freight Depot > Rating & Scheduling</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>


</head>
<?php

require('zzheader.php');

?>
<center>
<table cellpadding=0 cellspacing=0>
<tr><td><img src="images/general/shipprocessleft.gif" width=27 height=22></td>
<td bgcolor="7390C0" valign=middle><font face='"Trebuchet MS",arial' size=1>SHIPMENT SCHEDULING PROCESS: <img align=middle src="images/general/shipprocess1.gif" width=15 height=15> <font color=CDD2D9>GET A QUOTE  <img align=middle src="images/general/shipprocess2.gif" width=15 height=15> COMPLETE BOL</font>  <img align=middle src="images/general/shipprocess3.gif" width=15 height=15> <font size=2><U>CONFIRM</U></font> <img align=middle src="images/general/shipprocess4.gif" width=15 height=15> <font color=CDD2D9>PRINT BOL</font></font></b></td>
<td><img src="images/general/shipprocessright.gif" width=27 height=22></td>
</tr></table>
<br>
<br>
<table width=642 border=0 align=center>
	<tr><td colspan=2><font face="'Trebuchet MS', tahoma" size=2><b>PLEASE CONFIRM THE FOLLOWING SHIPMENT INFORMATION:<br></td></tr>
	<tr valign=top><td width=214><font face="verdana" size=1><b>SHIPMENT ORIGIN</b></font><br>
	<font size=1 face="verdana">
	<?php
	echo "$originline[1]<br>";
	echo "$originline[2]<br>";
	echo "$originline[3]<br>";
	echo "$originline[4], $originline[5] $originline[6]";
	?>
	</font>
	<br><br>
	<font face="verdana" size=1><b>SHIPMENT DESTINATION</b></font><br>
	<font size=1 face="verdana">
	<?php
	echo "$destinationline[1]<br>";
	echo "$destinationline[2]<br>";
	echo "$destinationline[3]<br>";
	echo "$destinationline[4], $destinationline[5] $destinationline[6]";
	?>
	</font><br><br>
	<font face="verdana" size=1><b>BILLING ADDRESS</b></font><br>
	<font size=1 face="verdana">
	<?php
	if  ($billingmethod == 1) {
		echo "$originline[1]<br>";
		echo "$originline[2]<br>";
		echo "$originline[3]<br>";
		echo "$originline[4], $originline[5] $originline[6]";
	}
	elseif ($billingmethod == 2) {
		echo "$destinationline[1]<br>";
		echo "$destinationline[2]<br>";
		echo "$destinationline[3]<br>";
		echo "$destinationline[4], $destinationline[5] $destinationline[6]";
	}
	elseif ($billingmethod == 3) {
		echo "$billingline[1]<br>";
		echo "$billingline[2]<br>";
		echo "$billingline[3]<br>";
		echo "$billingline[4], $billingline[5] $billingline[6]";
	}
	?>
	</font>
	</td>
	
	<form method="post" action="booked.php">
	<td><font face="verdana" size=1><b>SHIPMENT DETAILS</b></font><br>
	<font size=1 face="verdana">
	<?php

	if ( $quoteline[12] == 0 ) {
		$transitdays = "CALL";
	} else {
		$transitdays = $quoteline[12];
	}

	echo "PRODUCT: $productdesc<br>";
	echo "PIECES: $pieces<br>";
	echo "PO NUMBER: $ponumber<br>";
	echo "WEIGHT: $quoteline[5] lbs.<br>";
	echo "CLASS: $quoteline[6]<br>";
	echo "TRANSIT DAYS: $transitdays<br>";
	echo "PICKUP DATE: $hidepickupdate<br>";
	echo "PICKUP AFTER: $after $afterampm<br>";
	echo "PICKUP BEFORE: $close $ampm<br>";
	echo "ESTIMATED DELIVERY: $hidedeldate<br>";
	echo "HAZ MAT? $hazmat<br>";
	echo "PACKAGING TYPE: $packagingtype<br>";
	echo "PALLETIZED? $palletized<br>";
	echo "NUMBER OF PALLETS: $palletqty";
	?>
	</font>
	<br><br>

	<font face="verdana" size=1><b>ADDITIONAL SERVICES</b></font>
	<br>

	<?php
	#
	# Deal with the accessorials passed to us
	#
    if (isset($CALL_FOR_PICKUP)) {
		debug("confirm.php: CALL_FOR_PICKUP is set");
		echo "<font face=verdana size=1>Call For Pickup</font><br>";
		echo "<input type='hidden' name='CALL_FOR_PICKUP' value='CALL_FOR_PICKUP'>";
    }

    if ( isset($CALL_BEFORE_DELIVERY)) {
		debug("confirm.php: CALL_BEFORE_DELIVERY is set");
		echo "<font face=verdana size=1>Call Before Delivery</font><br>";
		echo "<input type='hidden' name='CALL_BEFORE_DELIVERY' value='CALL_BEFORE_DELIVERY'>";
	}

    if ( isset($LIFTGATE_ORIGIN)) {
		debug("confirm.php: LIFTGATE_ORIGIN is set");
		echo "<font face=verdana size=1>Origin Liftgate</font><br>";
		echo "<input type='hidden' name='LIFTGATE_ORIGIN' value='LIFTGATE_ORIGIN'>";
    }

    if ( isset($LIFTGATE_DESTINATION)) {
   		debug("confirm.php: LIFTGATE_DESTINATION is set");
		echo "<font face=verdana size=1>Destination Liftgate</font><br>";
		echo "<input type='hidden' name='LIFTGATE_DESTINATION' value='LIFTGATE_DESTINATION'>";
	}

    if ( isset($RESIDENTIAL_PICKUP)) {
		debug("confirm.php: RESIDENTIAL_DELIVERY is set");
		echo "<font face=verdana size=1>Residential Delivery</font><br>";
		echo "<input type='hidden' name='RESIDENTIAL_PICKUP' value='RESIDENTIAL_PICKUP'>";
	}

    if ( isset($INSIDE_PICKUP)) {
		debug("confirm.php: INSIDE_PICKUP is set");
		echo "<font face=verdana size=1>Inside Pickup</font><br>";
		echo "<input type='hidden' name='INSIDE_PICKUP' value='INSIDE_PICKUP'>";
	}

    if ( isset($INSIDE_DELIVERY)) {
		debug("confirm.php: INSIDE_DELIVERY is set");
		echo "<font face=verdana size=1>Inside Delivery</font><br>";
		echo "<input type='hidden' name='INSIDE_DELIVERY' value='INSIDE_DELIVERY'>";
    }

    if ( isset($RESIDENTIAL_DELIVERY)) {
		debug("confirm.php: RESIDENTIAL_PICKUP is set");
		echo "<font face=verdana size=1>Residential Delivery</font><br>";
		echo "<input type='hidden' name='RESIDENTIAL_DELIVERY' value='RESIDENTIAL_DELIVERY'>";
	}

    if ( isset($HAZMAT)) {
		debug("confirm.php: HAZMAT is set");
		echo "<font face=verdana size=1>Hazmat</font><br>";
		echo "<input type='hidden' name='HAZMAT' value='HAZMAT'>";
    }

	?>
	
	<br>
	<font face="verdana" size=1><b>SPECIAL SERVICES</b></font><br>
	<?php
	
		echo "$speci<br>";
	?>
	</font>
	</td>
	<td>
	<font face="verdana" size=1><b>TOTAL CHARGES</b></font><br>
	<font size=1 face="verdana">
	<?php
	$quoteamt = $quoteline[9] + $quoteline[14];

	echo "QUOTE: $$quoteamt<br>";

	if ($additionalcharges) {
		echo "ADDITIONAL SERVICES: $$additionalcharges<br>";
	}
	$totalcharges += $quoteline[14];
	echo "<font color=cb000000><b>TOTAL COST: $$totalcharges</b></font>";
	?>
	</font>
	<br><br>
	<font size=1>
	
	[ <a href="javascript:history.back();"><font color=000000>GO BACK & EDIT</font></a> ]<br><br>
	[ <a href="rating.php"><font color=000000>CANCEL SCHEDULING</font></a> ]<br><br>
	<input type="submit" value="BOOK NOW!"><br>
	</td>
	</form>
	</tr>
</table>
</font>

<?php

require('zzfooter.php');

?>
