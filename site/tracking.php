<?php
# =============================================================================
#
# tracking.php
#
# Shipment tracking page
#
# $Id: tracking.php,v 1.11 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren_young@yahoo.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: tracking.php,v $
# Revision 1.11  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.10  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.9  2002/09/19 20:55:34  youngd
#   * Added standard requires.
#   * Cleaned up some of the if/the/else blocks
#
# Revision 1.8  2002/09/19 14:02:07  youngd
#   * Added source header and changed my email address
#
# Revision 1.7  2002/09/13 08:34:54  webdev
#   * Many updates.
#   * Added services section in the rating page
#
# Revision 1.6  2002/09/13 01:38:05  webdev
#   * Added source header
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("tracking.php: ENTERING");
debug("tracking.php: done loading requires");

// there are three possibilities here:
// 1. a customer comes here looking for all there shipments
// 2. a customer queries using the form by entering a po or bol number
// 3. a non-customer comes here by quering the form
// 4. a non-customer comes here for the form

// lets split the possibilties into twos
// first, is there a query or not
// 1. someone comes to the page with a number & numbertype
if ($number) {
	$query = 1;
}
// 2. someone just comes here
else {
	$query = 0;
}

// second, is this a customer or not?
// get cookie and user if user exists

if ($digishipcookie1 and $digishipcookie2) {
	$customer = 1;
	require('zzgrabcookie.php');
}
else {
	$customer = 0;
}

if ($customer == 1) {
	// this means this person is our customer and they haven't queried for a shipment yet
	// time frame for delivered shipments = 5
	
	require('zztrackinglist.php');
}
if ($query == 1) {
	require('zztracking.php');
}


?>

<html>
<head>
	<title>The Freight Depot > Tracking</title>
	
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>
<?php

require('zzheader.php');

?>

<br><br>

<?php
if ($number) {
	if  ($noinfofound != 1 and $multiplepo != 1) {
			echo "<blockquote><table width=670 cellspacing=0>";
			echo "<tr><td colspan=2><font face=verdana size=2><b>TRACKING INFO FOR BILL OF LADING #$indtrackline[shipmentid]</b></td></tr>";
			echo "<tr><td colspan=2><img src=images/pixels/blackpixel.gif width=670 height=1></td></tr>";
			echo "<tr valign=top bgcolor=cfcfcf><td><font face=verdana size=1><b>CURRENT SHIPMENT STATUS:</b></td>";
			echo "<td><font face=verdana size=1>$statusline[statusdetails]</td></tr>";
			if ($digishipcookie1 and $digishipcookie2 and ($digishipcookie2 == $indtrackline[customerid])) {
				echo "<tr><td><font face=verdana size=1><b>ORIGIN:</td>";
				echo "<td><font face=verdana size=1>$originline[company]</td></tr>";
				echo "<tr><td><font face=verdana size=1><b>DESTINATION:</td>";
				echo "<td><font face=verdana size=1>$destline[company]</td></tr>";
				echo "<tr><td><font face=verdana size=1><b>WEIGHT:</td>";
				echo "<td><font face=verdana size=1>$indtrackline[weight]</td></tr>";
				echo "<tr><td><font face=verdana size=1><b>CLASS:</td>";
				echo "<td><font face=verdana size=1>$indtrackline[class]</td></tr>";
				echo "<tr><td><font face=verdana size=1><b>PIECES:</td>";
				echo "<td><font face=verdana size=1>$indtrackline[units]</td></tr>";
			}
			echo "<tr><td><font face=verdana size=1><b>ESTIMATED DELIVERY DATE:</td>";
			$thisdate = substr($indtrackline[deliveryest],5,5);
			echo "<td><font face=verdana size=1>$thisdate</td></tr>";
			echo "</table><br>";
			echo "<table width=400 border=0>";
			echo "<tr><td colspan=2><font face=verdana size=2><b>SHIPMENT HISTORY</b></td></tr>";
			echo "<tr><td colspan=2><img src=images/pixels/blackpixel.gif width=670 height=1></td></tr>";
			echo "<tr><td><font face=verdana size=1><b>$statusline[statusdetails]</td>";
			echo "<td><font face=verdana size=1>$statusline[statustime]</td></tr>";
			while ($statusline = mysql_fetch_array($statusit)) {
					echo "<tr><td><font face=verdana size=1><b>$statusline[statusdetails]</td>";
					echo "<td><font face=verdana size=1>$statusline[statustime]</td></tr>";
			}
				
			echo "</table></blockquote>";

		} else if ($noinfofound == 1) {
			echo "<center><font color=cb0000 face=verdana size=2><b>NO TRACKING INFORMATION FOUND ON THIS SHIPMENT.</b></font></center><br><br><center><table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000><table width=170 cellspacing=1 cellpadding=0><tr height=20><td bgcolor=A20E07 align=center><font face=verdana size=1 color=ffffff><b>SHIPMENT TRACKING</b></font></td></tr><tr><td bgcolor=FAFAFA align=center><form method=post action=tracking.php><table width=160><tr><td align=center><font face=tahoma size=1>ENTER BOL OR PO NUMBER:</font></td></tr><tr><td align=center><input size=15 name=number></td></tr><tr><td align=center><input type=image order=0 src='images/buttons/trackit.gif' width=99 height=21 border=0></td></tr></table></td></form></tr></table></td></tr></table></center><Br><br><br><br><br><br><br><br><br><br>";
		
		} else if ($multiplepo == 1) {
			echo "<center><font color=cb0000 face=verdana size=2><b>WE'RE SORRY, YOU MUST TRACK THIS SHIPMENT USING<br>IT'S BOL NUMBER RATHER THAN PO NUMBER.</b></font><Br><br><center><table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000><table width=170 cellspacing=1 cellpadding=0><tr height=20><td bgcolor=A20E07 align=center><font face=verdana size=1 color=ffffff><b>SHIPMENT TRACKING</b></font></td></tr><tr><td bgcolor=FAFAFA align=center><form method=post action=tracking.php><table width=160><tr><td align=center><font face=tahoma size=1>ENTER BOL OR PO NUMBER:</font></td></tr><tr><td align=center><input size=15 name=number></td></tr><tr><td align=center><input type=image order=0 src='images/buttons/trackit.gif' width=99 height=21 border=0></td></tr></table></td></form></tr></table></td></tr></table></center></center><Br><br><br><br><br><br><br><br><br><br>";
		
		}
	
} else if ($indx != 0 and !($number)) {
    echo "<table border=0 align=center width=700 cellspacing=0 cellpadding=2><tr height=18 valign=middle><form method=post action=tracking.php><Td bgcolor=000000 colspan=2><font face='verdana' size=1 color=ffffff><b>";
	
	echo "RECENT SHIPMENTS ($timeframedisp)</font></td>";
	echo "<td colspan=8 align=right bgcolor=000000><font face=verdana size=1 color=ffffff><b>SEARCH BY PO OR BOL #: <input size=9 name=number></b></font> <input type=submit value=GO!>&nbsp;</td></tr>";
	echo "<tr bgcolor=D9E1EE><td><font face=tahoma size=1><b>DESTINATION</b></font></td><td><font face=tahoma size=1><b>CITY / STATE</b></font></td><td><font face=tahoma size=1><b>ORIGIN</b></font></td><td><font face=tahoma size=1><b>CITY / STATE</b></font></td><td><font face=tahoma size=1><b>PO #</b></font></td><td><font face=tahoma size=1><b>BOL #</b></font></td><td><font face=tahoma size=1><b>UNITS</b></font></td><td><font face=tahoma size=1><b>WEIGHT</b></font></td><td><font face=tahoma size=1><b>CLASS</b></font></td><td><font face=tahoma size=1><b>DATE</b></font></td>";
	$newindx = 0;
	while ($newindx != $indx) {
		if ($currentbg != "fafafa") {
		   $currentbg = "fafafa";
		}
		else {
		   $currentbg = "D9E1EE";
		 }

		echo "<tr><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][12] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][13] . ", " . $allships[$newindx][14] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][9] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][10] . ", " . $allships[$newindx][11] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][1] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][0] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][2] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][4] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][5] . "</font></td><td bgcolor=$currentbg><font face=verdana size=1>" . $allships[$newindx][3] . "</font></td></tr>";
		echo "<tr bgcolor=$currentbg><td colspan=6><font face=verdana size=1><a href='tracking.php?number=" . $allships[$newindx][0] . "' class='links_inactive'>TRACK THIS SHIPMENT</a></td><td align=right colspan=6><a href='bol.php?shipmentid=" . $allships[$newindx][0] . "' class='links_inactive'><font face=verdana size=1>VIEW / PRINT BOL</a></td></tr>";
	
		$newindx++;
	}

	echo "</table>";
} else {

	echo "<br><br><center><blockquote><font face='Trebuchet MS, tahoma' size=2><b>You do not have any current shipments. If you would like to track by PO Number or BOL Number, use the form below.</b></center></blockquote><br>";
	echo "<center><table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000><table width=170 cellspacing=1 cellpadding=0><tr height=20><td bgcolor=A20E07 align=center><font face=verdana size=1 color=ffffff><b>SHIPMENT TRACKING</b></font></td></tr><tr><td bgcolor=FAFAFA align=center><form method=post action=tracking.php><table width=160><tr><td align=center><font face=tahoma size=1>ENTER BOL OR PO NUMBER:</font></td></tr><tr><td align=center><input size=15 name=number></td></tr><tr><td align=center><input type=image order=0 src='images/buttons/trackit.gif' width=99 height=21 border=0></td></tr></table></td></form></tr></table></td></tr></table></center>";
}

?>
<br><br>

<?php

require('zzfooter.php');

?>

