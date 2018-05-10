<?php
# =============================================================================
#
# ratingspecial.php
#
# Rating page for shipments that aren't LTL
#
# $Id: ratingspecial.php,v 1.11 2002/12/10 19:51:44 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: ratingspecial.php,v $
# Revision 1.11  2002/12/10 19:51:44  webdev
#   * Changed emails to be at the freight depot
#
# Revision 1.10  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.9  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.8  2002/09/19 20:47:57  youngd
#   * Changed harry's email to be the new one at aol
#
# Revision 1.7  2002/09/16 22:48:09  youngd
#   * Added Harry's email
#
# Revision 1.6  2002/09/15 07:26:06  webdev
#   * Added source header
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

require('zzgrabcookie.php');
if ($service) {


}
if ($action) {
	$mailmessage = "SERVICE = $service\n";
	$mailmessage .= "CUSTOMER = $userarray[1] - $userarray[3]\n";
	$mailmessage .= "EXPECTED SHIP DATE = $pickupdate\n";
	$mailmessage .= "EXPECTED DELIVER DATE = $deldate\n";
	$mailmessage .= "ORIGIN ADDRESS = $originaddress\n";
	$mailmessage .= "ORIGIN CITY = $origincity\n";
	$mailmessage .= "ORIGIN STATE = $originstate\n";
	$mailmessage .= "ORIGIN ZIP = $originzip\n";
	$mailmessage .= "ORIGIN COUNTRY = $origincountry\n";
	$mailmessage .= "ORIGIN PORT = $originport\n";
	$mailmessage .= "DESTINATION ADDRESS = $destaddress\n";
	$mailmessage .= "DESTINATION CITY = $destcity\n";
	$mailmessage .= "DESTINATION STATE = $deststate\n";
	$mailmessage .= "DESTINATION ZIP = $destzip\n";
	$mailmessage .= "DESTINATION COUNTRY = $destcountry\n";
	$mailmessage .= "DESTINATION PORT = $destport\n";
	$mailmessage .= "PIECES = $pieces\n";
	$mailmessage .= "COMMODITY = $description\n";
	$mailmessage .= "WEIGHT = $weight\n";
	$mailmessage .= "DIMENSIONS = $width x $height x $depth\n";
	$mailmessage .= "DIMENSIONS = $width2 x $height2 x $depth2\n";
	$mailmessage .= "DIMENSIONS = $width3 x $height3 x $depth3\n";
	$mailmessage .= "DIMENSIONS = $width4 x $height4 x $depth4\n";
	$mailmessage .= "DIMENSIONS = $width5 x $height5 x $depth5\n";
	$mailmessage .= "DECLARED VALUE = $declaredvalue\n";
	$mailmessage .= "ADDITIONAL CARGO PROTECTION = $security\n";
	$mailmessage .= "INSURANCE = $insurance\n";
	$mailmessage .= "BORDER CROSSING = $bordercity, $borderstate\n";
	$mailmessage .= "CUSTOMS BROKER NEEDED = $customsbroker\n";
	$mailmessage .= "HAZMAT = $hazmat\n";
	$mailmessage .= "HAZMAT CLASS = $hazclass\n";
	$mailmessage .= "COD AMOUNT = $codamount\n";
	$mailmessage .= "TRAILER NEEDED = $trailer\n";
	$mailmessage .= "LOAD TYPE = $load\n";
	$mailmessage .= "CONTAINER TYPE = $containertype\n";
	$mailmessage .= "CONTAINER SIZE = $container\n";
	$mailmessage .= "ROUTE TYPE = $route\n";
	$mailmessage .= "SCHEDULE B NUMBER = $scheduleb\n";
	$mailmessage .= "HTS NUMBER = $htsnumber\n";
	$mailmessage .= "SPECIAL INSTRUCTIONS = $instructions\n";
	$subject = "THIS IS A $service RATE REQUEST";
	mail("tjuedes@thefreightdepot.com,hpavlos@thefreightdepot.com", $subject, $mailmessage, "FROM: $userarray[2]");
	
	

}
?>

<html>
<head>
<title>The Freight Depot > Special Services</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>
<?php

require('zzheader.php');

?>
<form method=post action=ratingspecial.php>
<br><blockquote><table class="reporttext" width=550>
<tr><Td>
<?php
if ($action) {

	echo "Thank you for your request! Someone will be contacting you with a rate as soon as possible.<br><br><a href=mydigiship.php>Click here</a> to continue.";
}
else {
if ($service) {
	echo "<input type=hidden name=service value=$service>";
	echo "<input type=hidden name=action value=1>";
	if ($service == "airfreight") {
	echo "<input type=hidden name=service value=$service>";
	echo "<tr><td colspan=2><b>AIRFREIGHT/EXPEDITED QUOTES</b></td></tr>";
	echo "<tr><td colspan=2>To obtain an airfreight/expedited quote, please provide us with the information in the form below, and we'll contact you as soon as possible with your rate.<br><br></td></tr>";
	echo "<tr><td>PICKUP DATE</b></td><td><input size=20 name=pickupdate></td></tr>";
	echo "<tr><td>DELIVER DATE</b></td><td><input size=20 name=deldate></td></tr>";
	echo "<tr><td><b>ORIGIN</b></td><td></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><input size=20 name=origincountry></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=origincity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=originstate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=originzip></td></tr>";
	echo "<tr><td><b>DESTINATION</b></td><td></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><input size=20 name=destcountry></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=destcity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=deststate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=destzip></td></tr>";
	echo "<tr><td><b>SHIPMENT INFORMATION</b></td><td></td></tr>";
	echo "<tr><td>PIECES</b></td><td><input size=20 name=pieces></td></tr>";
	echo "<tr><td>COMMODITY DESCRIPTION</b></td><td><input size=20 name=description></td></tr>";
	echo '<tr><td>SIZE</b></td><td><input size=5 name=width>"W x <input size=5 name=height>"H x <input size=5 name=depth>"D</td></tr>';
	echo "<tr><td>WEIGHT</b></td><td><input size=20 name=weight></td></tr>";
	echo "<tr><td>COD AMOUNT</b></td><td><input size=20 name=codamount></td></tr>";
	echo "<tr><td><input type=submit value=SUBMIT></td></tr>";
	echo "<input type=hidden name=service value=$service>";

	}
if ($service == "mexico") {
	echo "<input type=hidden name=service value=$service>";
	echo "<tr><td colspan=2><b>LTL MEXICO QUOTES</b></td></tr>";
	echo "<tr><td colspan=2>To obtain a quote for LTL service to and from Mexico, please provide us with the information in the form below, and we'll contact you as soon as possible with your rate.<br><br></td></tr>";
	echo "<tr><td><b>ORIGIN</b></td><td></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=origincity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=originstate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=originzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><select name=origincountry><option value=usa>USA<option value=mexico>MEXICO</select></td></tr>";
	echo "<tr><td><b>DESTINATION</b></td><td></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=destcity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=deststate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=destzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><select name=destcountry><option value=usa>USA<option value=mexico>MEXICO</select></td></tr>";
	echo "<tr><td><b>SHIPMENT INFORMATION</b></td><td></td></tr>";
	echo "<tr><td>PIECES</b></td><td><input size=20 name=pieces></td></tr>";
	echo "<tr><td>COMMODITY DESCRIPTION</b></td><td><input size=20 name=description></td></tr>";
	echo "<tr><td>WEIGHT</b></td><td><input size=20 name=weight></td></tr>";
	echo '<tr><td>DIMENSIONS</b></td><td><input size=5 name=width>"W x <input size=5 name=height>"H x <input size=5 name=depth>"D</td></tr>';
	echo "<tr><td>DECLARED VALUE OF GOODS</b></td><td><input size=20 name=declaredvalue></td></tr>";
	echo "<tr><td>ADDITIONAL CARGO PROTECTION?</b></td><td><input size=20 name=security></td></tr>";
	echo "<tr><td>BORDER CROSSING LOCATION (CITY/STATE)</b></td><td><input size=16 name=bordercity> <input size=4 name=borderstate></td></tr>";
	echo "<tr><td>MEXICAN CUSTOMS BROKER NEEDED?</b></td><td><input type=radio name=customsbroker value=yes> Yes <input type=radio name=customsbroker value=no> No</td></tr>";
	echo "<tr><td>HAZARDOUS?</b></td><td><input type=radio name=hazmat value=yes> Yes <input type=radio name=hazmat value=no> No</td></tr>";
	echo "<tr><td>EXPECTED SHIP DATE</b></td><td><input size=20 name=pickupdate></td></tr>";
	
	echo "<tr><td><input type=submit value=SUBMIT></td></tr>";

	}
if ($service == "canada") {
	echo "<input type=hidden name=service value=$service>";
	echo "<tr><td colspan=2><b>LTL CANADA QUOTES</b></td></tr>";
	echo "<tr><td colspan=2>To obtain a quote for LTL service to and from Canada, please provide us with the information in the form below, and we'll contact you as soon as possible with your rate.<br><br></td></tr>";
	echo "<tr><td><b>ORIGIN</b></td><td></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=origincity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=originstate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=originzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><select name=origincountry><option value=usa>USA<option value=canada>CANADA</select></td></tr>";
	echo "<tr><td><b>DESTINATION</b></td><td></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=destcity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=deststate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=destzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><select name=destcountry><option value=usa>USA<option value=canada>CANADA</select></td></tr>";
	echo "<tr><td><b>SHIPMENT INFORMATION</b></td><td></td></tr>";
	echo "<tr><td>PIECES</b></td><td><input size=20 name=pieces></td></tr>";
	echo "<tr><td>COMMODITY DESCRIPTION</b></td><td><input size=20 name=description></td></tr>";
	echo "<tr><td>WEIGHT</b></td><td><input size=20 name=weight></td></tr>";
	echo "<tr><td>CLASS</b></td><td><input size=20 name=class></td></tr>";
	echo "<tr><td>HAZARDOUS?</b></td><td><input type=radio name=hazmat value=yes> Yes <input type=radio name=hazmat value=no> No</td></tr>";
	echo "<tr><td>EXPECTED SHIP DATE</b></td><td><input size=20 name=pickupdate></td></tr>";
	echo "<tr><td><input type=submit value=SUBMIT></td></tr>";

	}
if ($service == "international") {
	echo "<input type=hidden name=service value=$service>";
	echo "<tr><td colspan=2><b>INTERNATIONAL QUOTES</b></td></tr>";
	echo "<tr><td colspan=2>To obtain a quote for service internationally, please provide us with the information in the form below, and we'll contact you as soon as possible with your rate.<br><br></td></tr>";
	echo "<tr><td>LESS THAN CONTAINER OR FULL CONTAINER?</td><TD><input type=radio name=containertype value=less> LESS <input type=radio name=containertype value=full> FULL</td></tr>";
	echo "<tr><td>CONTAINER SIZE?</b></td><td><input type=radio name=container value=20> 20' <input type=radio name=container value=40> 40' <input type=radio name=container value=45> 45' <input type=radio name=container value=40cube> 40' High Cube <input type=radio name=container value=45cube> 45' High Cube</td></tr>";
	echo "<tr><td>LOAD TYPE?</td><td><input type=radio name=load value=live> Live Load (2hrs free) <input type=radio name=load value=drop> Drop and Pull</td></tr>";
	echo "<tr><td>ROUTE TYPE?</b><td><input type=radio name=route value=dtp> Door To Port <input type=radio name=route value=dtd> Door To Door <input type=radio name=route value=ptd> Port To Door</td></tr>";
	echo "<tr><td><b>ORIGIN</b></td><td></td></tr>";
	echo "<tr><td>ORIGIN PORT</b></td><td><input size=20 name=originport></td></tr>";
	echo "<tr><td>ORIGIN STREET ADDRESS</b></td><td><input size=20 name=originaddress></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=origincity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=originstate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=originzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><input size=20 name=origincountry></td></tr>";
	echo "<tr><td><b>DESTINATION</b></td><td></td></tr>";
	echo "<tr><td>DESTINATION PORT</b></td><td><input size=20 name=destport></td></tr>";
	echo "<tr><td>DESTINATION STATE ADDRESS</b></td><td><input size=20 name=destaddress></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=destcity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=deststate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=destzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><input size=20 name=destcountry></td></tr>";
	echo "<tr><td><b>SHIPMENT INFORMATION</b></td><td></td></tr>";
	echo "<tr><td>PIECES</b></td><td><input size=20 name=pieces></td></tr>";
	echo "<tr><td>COMMODITY</b></td><td><input size=20 name=description></td></tr>";
	echo "<tr><td>WEIGHT</b></td><td><input size=20 name=weight></td></tr>";
	echo '<tr><td>SIZE PIECE 1</b></td><td><input size=5 name=width>"W x <input size=5 name=height>"H x <input size=5 name=depth>"D</td></tr>';
	echo '<tr><td>SIZE PIECE 2</b></td><td><input size=5 name=width2>"W x <input size=5 name=height2>"H x <input size=5 name=depth2>"D</td></tr>';
	echo '<tr><td>SIZE PIECE 3</b></td><td><input size=5 name=width3>"W x <input size=5 name=height3>"H x <input size=5 name=depth3>"D</td></tr>';
	echo '<tr><td>SIZE PIECE 4</b></td><td><input size=5 name=width4>"W x <input size=5 name=height4>"H x <input size=5 name=depth4>"D</td></tr>';
	echo '<tr><td>SIZE PIECE 5</b></td><td><input size=5 name=width5>"W x <input size=5 name=height5>"H x <input size=5 name=depth5>"D</td></tr>';
	echo "<tr><td>SCHEDULE B NUMBER (EXPORT)</TD><td><input size=20 name=scheduleb></td></tr>";
	echo "<tr><td>HTS NUMBER (IMPORT)</td><td><input size=20 name=htsnumber></td></tr>";
	echo "<tr><td>HAZARDOUS CLASS?</b></td><td><input size=20 name=hazclass></td></tr>";
	echo "<tr><td>INSURANCE (STANDARD IS 110% OF COST + FREIGHT)</b></td><td><input size=20 name=insurance></td></tr>";
	echo "<tr><td>LIKELY SHIP DATE</b></td><td><input size=20 name=pickupdate></td></tr>";
	echo "<tr><td>SPECIAL INSTRUCTIONS</b></td><td><textarea cols=20 rows=10 name=instructions></textarea></td></tr>";
	echo "<tr><td><input type=submit value=SUBMIT></td></tr>";

	}
if ($service == "truckload") {
	echo "<tr><td colspan=2><b>TRUCKLOAD / FLATBED QUOTES</b></td></tr>";
	echo "<tr><td colspan=2>To obtain a quote for truckload or flatbed service, please provide us with the information in the form below, and we'll contact you as soon as possible with your rate.<br><br></td></tr>";

	echo "<tr><td>TRAILER NEEDED?</td><td><select name=trailer><option value=48>48' VAN<option value=53>53' VAN<option value=AIR>AIR RIDE VAN<option value=flatbed>FLATBED<option value=flatbedtarps>FLATBED WITH TARPS<option value=flatbedsides>FLATBED SIDES<option value=reefer>REFRIGERATED REEFER</select></td></tr>";
	echo "<tr><td>LOAD TYPE?</td><td><input type=radio name=load value=live> Live Load (2hrs free) <input type=radio name=load value=drop> Drop and Pull</td></tr>";
	echo "<tr><td><b>ORIGIN</b></td><td></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=origincity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=originstate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=originzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><select name=origincountry><option value=usa>USA<option value=canada>CANADA<option value=mexico>MEXICO</select></td></tr>";
	echo "<tr><td><b>DESTINATION</b></td><td></td></tr>";
	echo "<tr><td>CITY</b></td><td><input size=20 name=destcity></td></tr>";
	echo "<tr><td>STATE</b></td><td><input size=20 name=deststate></td></tr>";
	echo "<tr><td>ZIP</b></td><td><input size=20 name=destzip></td></tr>";
	echo "<tr><td>COUNTRY</b></td><td><select name=destcountry><option value=usa>USA<option value=canada>CANADA<option value=mexico>MEXICO</select></td></tr>";
	echo "<tr><td><b>SHIPMENT INFORMATION</b></td><td></td></tr>";
	echo "<tr><td>PIECES</b></td><td><input size=20 name=pieces></td></tr>";
	echo "<tr><td>COMMODITY</b></td><td><input size=20 name=description></td></tr>";
	echo "<tr><td>WEIGHT</b></td><td><input size=20 name=weight></td></tr>";

	echo "<tr><td>HAZARDOUS</td><td><input type=radio name=hazmat value=yes> Yes <input type=radio name=hazmat value=no> No</td></tr>";
	echo "<tr><td>SHIP DATE</td><td><input size=20 name=pickupdate></td></tr>";
	echo "<tr><td>SPECIAL INSTRUCTIONS</b></td><td><textarea cols=20 rows=10 name=instructions></textarea></td></tr>";
	echo "<tr><td><input type=submit value=SUBMIT></td></tr>";

	}
}

}

?>
</td></tr></table></blockquote>
</form>

<?php

require('zzfooter.php');

?>
