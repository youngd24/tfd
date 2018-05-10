<?php
# ==============================================================================
#
# booked.php
#
# Display booked shipment(s)
#
# $Id: booked.php,v 1.9 2002/10/11 20:05:18 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: booked.php,v $
# Revision 1.9  2002/10/11 20:05:18  youngd
#   * Reworked accessorials which work now.
#
# Revision 1.8  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.7  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.6  2002/09/15 07:11:00  webdev
#   * Added source header
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

// get cookie and user
require('zzgrabcookie.php');

require('zzbooked.php');


debug("booked.php: entering after initial requiers");
?>

<html>
<head>
<title>The Freight Depot > Rating & Scheduling</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>



	
<?php
// get header -- MUST FOLLOW zzgrabcookie.php
require('zzheader.php');
?>
<center>
<table cellpadding=0 cellspacing=0>
<tr><td><img src="images/general/shipprocessleft.gif" width=27 height=22></td>
<td bgcolor="7390C0" valign=middle><font face='"Trebuchet MS",arial' size=1>SHIPMENT SCHEDULING PROCESS: <img align=middle src="images/general/shipprocess1.gif" width=15 height=15> <font color=CDD2D9>GET A QUOTE  <img align=middle src="images/general/shipprocess2.gif" width=15 height=15> COMPLETE BOL  <img align=middle src="images/general/shipprocess3.gif" width=15 height=15> CONFIRM</font> <img align=middle src="images/general/shipprocess4.gif" width=15 height=15> <font size=2><U>PRINT BOL</U></font></b></td>
<td><img src="images/general/shipprocessright.gif" width=27 height=22></td>
</tr></table>
<br><br>
<table align=center border=0><tr valign=top>
<td width=450>
<?php


	if ($shipmentid) {
		ECHO "<font face=verdana size=2><b>CONGRATULATIONS!</b><br><br>Your shipment has been booked. An e-mail has been sent to you confirming the shipment details.<br><Br>";
		echo "THE BILL OF LADING NUMBER FOR THIS SHIPMENT IS: <b>$shipmentid</b><br><br>";
		echo "THE CARRIER FOR THIS SHIPMENT IS: <b>$mailqueryline[9]<br><br>";
		echo "Please download and print your Bill of Lading by <a href=bol.php?shipmentid=$shipmentid target=newwindow>clicking here</a>. It is <i>very</i> important that you use this Bill of Lading when our carrier's driver arrives at the origin location. Not using our BOL could result in a lack of tracking information for your shipment, or incorrect charges and duplicate bills. If you must use your own BOL, please use ours as a model.<br><br>";
		echo "<a href=rating.php><i>CLICK HERE</i></a> to return to the shipment rating page.";

	}

	else {
		echo "<center><br><br><font face=verdana size=2 color=cb0000><b>YOU'VE ALREADY SCHEDULED THIS SHIPMENT</B><br><br>";
		echo "<font color=000000><a href=rating.php>Click here to continue</a><br><br><br><br></center>";
	}

    debug("booked.php: leaving");

?>
</font>
</td>
	
	
	
</tr>
</table><br>

<?php
// get footer
require('zzfooter.php');
?>
