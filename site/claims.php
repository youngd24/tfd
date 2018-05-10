<?php
# ==============================================================================
#
# claims.php
#
# Claims page
#
# $Id: claims.php,v 1.10 2002/12/06 23:04:55 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: claims.php,v $
# Revision 1.10  2002/12/06 23:04:55  webdev
#   * Get the claim email address from the config file now.
#
# Revision 1.9  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.8  2002/09/19 20:47:57  youngd
#   * Changed harry's email to be the new one at aol
#
# Revision 1.7  2002/09/16 22:48:09  youngd
#   * Added Harry's email
#
# Revision 1.6  2002/09/13 08:47:11  webdev
#   * Removed Jeff's email
#
# Revision 1.5  2002/09/13 00:10:07  webdev
#   * Cleaned up log
#
# Revision 1.4  2002/09/13 00:09:12  webdev
#   * Added php source header
#
# ==============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// get cookie and user
require('zzgrabcookie.php');

if ($action) {


	// mail to the claim request address

	$claimaddress = getConfigValue("claimRequestEmailAddress");

	if ( $claimaddress == "" ) {
		debug("claims.php: failed to get claim email address");
	} else {
		$claimaddress = "sysadmin@thefreightdepot.com";
	}
 
	$mailmessage = "THIS IS A CLAIM SUBMISSION FROM $userarray[1] AT $userarray[3]\n\nBOL #: $bol\nCLAIMANT: $name\nCOMPANY: $company\nPHONE: $phone\nADDRESS: $address, $city $state $zip\n\nCLAIM AMOUNT: $amount\nCLAIM FOR: $reason $reasonother\n\nSHIPPER: $shipper\nCONSIGNEE: $consignee\nPICKUP DATE: $pickupdate\n\nCLAIM DESCRIPTION: $description\n\nCAN BE REPAIRED? $repairedfor for $repairamt\nCAN BE USED AS IS? $usedasis for $allowance\nAVAILABLE FOR PICKUP? $available";
	mail($claimaddress, "CLAIM SUBMISSION", $mailmessage, "FROM: $userarray[2]");
}


?>

<html>
<head>
<title>The Freight Depot > Submit a Claim</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>
<?php

require('zzheader.php');

?>
<BR>
<?php
if ($action) {
	echo "<center><br><FONT FACE=TAHOMA SIZE=2><b>YOUR CLAIM HAS BEEN SUBMITTED AND WILL BE REVIEWED BY OUR CLAIMS PROCESSING TEAM. IF YOU HAVE ANY QUESTIONS, PLEASE FEEL FREE TO CONTACT US AT 866.445.1212.</center><Br><br></FONT>";	
}

	echo "<center><table width=500><form method=post action=claims.php>";
	echo "<tr><td><font face=tahoma size=2><b>Shipment BOL Number</td><td><input size=20 name=bol value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Claimants Name</td><td><input size=20 name=name value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Company Name</td><td><input size=20 name=company value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Telephone Number</td><td><input size=20 name=phone value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Address</td><td><input size=20 name=address value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>City</td><td><input size=20 name=city value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>State</td><td><input size=4 name=state value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Zip</td><td><input size=20 name=zip value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Claim Amount</td><td><input size=20 name=amount value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Claim For</td><td><font face=tahoma size=2><input type=radio name=reason value=Shortage> Shortage <input type=radio name=reason value=Damage> Damage<br><input type=radio name=reason value=Other> OTHER: <input size=20 name=reasonother></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Shipper</td><td><input size=20 name=shipper value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Consignee</td><td><input size=20 name=consignee value=></td></tr>";
	echo "<tr><td><font face=tahoma size=2><b>Pickup Date</td><td><input size=20 name=pickupdate value=></td></tr>";
	echo "<tr><td colspan=2><font face=tahoma size=2><b>Please describe your claim and how the claim amount was calculated</td></tr><tr><td colspan=2><textarea cols=60 rows=10 name=description></textarea></td></tr>";
	echo "<tr><td colspan=2><font face=tahoma size=2><b>If the claim involves damaged goods, please check one or more of the following</td></tr><tr><td colspan=2><font face=tahoma size=2><input type=checkbox name=repairedfor value=yes> Damaged goods can be reparied for approximately $<input size=7 name=repairamt><br><input type=checkbox name=usedasis value=yes> Damaged goods can be used as is for an allowance of $<input size=7 name=allowance><br><input type=radio name=available value=yes> Damaged goods are available for carrier pickup<br><input type=radio name=available value=no> Damaged goods are unavailable for pickup</td></tr>";

	echo "<tr><td><input type=hidden name=action value=1><input type=submit value=SUBMIT></form></td></tr>";
	echo "</table>";

?>

<?php

require('zzfooter.php');

?>
