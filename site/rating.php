<?php
# =============================================================================
#
# rating.php
#
# Rating and scheduling page
#
# $Id: rating.php,v 1.22 2003/02/14 18:35:33 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: rating.php,v $
# Revision 1.22  2003/02/14 18:35:33  youngd
#   * Moved away from hard coded accessorial names to new more
#     dynamic method.
#
# Revision 1.21  2003/02/10 20:34:36  youngd
#   * Added new accessorial codes.
#
# Revision 1.20  2003/02/04 20:50:13  webdev
#   * Removed accessorials.
#
# Revision 1.19  2003/01/29 20:27:13  webdev
#   * Changed Roadway discounts.
#
# Revision 1.18  2003/01/24 18:46:10  webdev
#   * Added the garbonzo element that contains the total shipping charges.
#     The client side test suite uses this to determine if the rating
#     process is working properly.
#
# Revision 1.17  2003/01/16 22:52:11  webdev
#   * Changed all calls to schedule.php to be HTTP GET's instead of POST's.
#     This was necessary so the user isn't prompted by IE if the page
#     is refreshed. To correctly recalculate the accessorial charges if the
#     user changes them on the page, the page has to be reloaded via JavaScript
#     using the check box onClick event.
#
# Revision 1.16  2003/01/14 18:48:49  webdev
#   * Raised accessorial rates.
#
# Revision 1.15  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.14  2002/10/08 18:55:15  youngd
#   * Removed display of baserate and fuel surcharge.
#
# Revision 1.13  2002/10/04 21:58:25  youngd
#   * Working on margin & pricing changes still.
#
# Revision 1.12  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.11  2002/10/03 22:09:32  youngd
#   * Added isset for all variable tests. Reduces error messages in logfiles.
#
# Revision 1.10  2002/09/19 05:44:58  youngd
#       * Changed to UNIX format again. I think CVS is somehow doing a change
#
# Revision 1.9  2002/09/19 02:55:17  youngd
#   * Schedule change updates
#
# Revision 1.8  2002/09/18 05:19:16  youngd
#   * Added new special services sections
#
# Revision 1.7  2002/09/16 23:32:55  youngd
#   * Added headers and other changes
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// get cookie and user
require('zzgrabcookie.php');

debug("rating.php: entering after initial requires");

?>

<?php
// if rate request
if ($origin and $destination and $weight and $shipclass) {
	require('zzgetprice.php');
	debug("rating.php: Carrier after getting price is $carrierid");
}

?>

<html>
<head>
<title>The Freight Depot > Rating & Scheduling</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>

<script language="javascript">
function validate(frm) {
	
	var errors = "";
	
	if (frm.origin.value.length != "5") {
		errors += "Please enter a valid origin zip code\n";
	}
	if (frm.destination.value.length != "5") {
		errors += "Please enter a valid destination zip code\n";
	}
	if (frm.weight.value == "") {
		errors += "Please enter a weight\n";
	}
	if (frm.weight.value.indexOf(",") > -1) {
		errors += "Please enter a valid weight (no commas)\n";
	}
	if (errors != "") {
		alert(errors);
		return false;
	}
	
	else {
		return true;
	}
}
</script>

</head>
<?php

require('zzheader.php');

?>

<!-- TOP SHIPPING PROCESS INSTRUCTIONS -->
<center>
<table cellpadding=0 cellspacing=0>
<tr><td><img src="images/general/shipprocessleft.gif" width=27 height=22></td>
<td bgcolor="7390C0" valign=middle>
<font face='"Trebuchet MS",arial' size=1>SHIPMENT SCHEDULING PROCESS: <img align=middle src="images/general/shipprocess1.gif" width=15 height=15> <font size=2><U>GET A QUOTE</U></font>  <font color=CDD2D9><img align=middle src="images/general/shipprocess2.gif" width=15 height=15> COMPLETE BOL  <img align=middle src="images/general/shipprocess3.gif" width=15 height=15> CONFIRM  <img align=middle src="images/general/shipprocess4.gif" width=15 height=15> PRINT BOL</font></font></b></td>
<td><img src="images/general/shipprocessright.gif" width=27 height=22></td>
</tr>
</table>

<br>
<br>

<table cellpadding=0 cellspacing=1 border=0 bgcolor=#000000>
	<tr valign=top>
		<td width=200 bgcolor=FAFAFA>
			<table>
				<tr>
					<td>
						<font face="Verdana" size=2>
						<?php
		
							if ($origin and $destination and $weight and $shipclass and ($cantservice == 0) and $ar > 0) {
								echo 'To schedule this shipment, click on the "Schedule It" button. You will then proceed to Step 2 of the Scheduling process where you will be asked to provide BOL information. You may also rate another shipment if you would like.';
							} else {
								echo 'To obtain an LTL rate quote, fill out the information in the form on the right and click the "Rate It" button. This will return our rate for your shipment.<br><Br><a href="#" onClick=\'javascript:window.open("class.php","class","height=350,width=250")\';>HELP! What is my class?</a>';
							}
						?>
						</font>
					</td>
				</tr>
			</table>
		</td>
		<td bgcolor=FAFAFA>
			<table>
				<tr>
					<form method="post" action="rating.php" onSubmit="return validate(this);">
					<td><font face="verdana" size=2 color=000000>Origin Zip Code:</font></td>
					<td align=right><input size=6 maxlength=5 name="origin"></td>
				</tr>
				<tr>
					<td><font face="verdana" size=2 color=000000>Destination Zip:</font></td>
					<td align=right><input size=6 maxlength=5 name="destination"></td>
				</tr>
				<tr>
					<td><font face="verdana" size=2 color=000000>Weight (lbs):</font></td>
					<td align=right><input size=6 maxlength=5 name="weight"></td>
				</tr>
				<tr>
					<td><font face="verdana" size=2 color=000000>Class<font size=1>:</font></td>
					<td align=right>
						<select name="shipclass">
							<option value="50">50
							<option value="55">55
							<option value="60">60
							<option value="65">65
							<option value="70">70
							<option value="77">77
							<option value="85">85
							<option value="92">92
							<option value="100">100
							<option value="110">110
							<option value="125">125
							<option value="150">150
							<option value="175">175
							<option value="200">200
							<option value="250">250
							<option value="300">300
							<option value="400">400
							<option value="500">500
						</select>
					</td>
				</tr>
				<!--
				    SECTION FOR ADDITIONAL SERVICES
					-- TO BE ADDED --
				<tr align=right valign=middle>
					<td colspan=2><div style="font-family:verdana;font-size:7pt;">Liftgate Origin<input type="checkbox" style=""></div></td>
				</tr>
				<tr align=right valign=middle>
					<td colspan=2><div style="font-family:verdana;font-size:7pt;">Liftgate Destination<input type="checkbox" style=""></div></td>
				</tr> -->
				<tr>
					<td colspan=2 align=center>
						<input type="image" src="images/buttons/rateit.gif" border=0 width=99 height=21></td>
						</form>
				</tr>
			</table>
		</td>


	<?php
	#
	# Display the final rate information.
	#
	if ($origin and $destination and $weight and $shipclass and ($cantservice == 0) and $ar > 0) {
		echo "<td bgcolor=FAFAFA>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><form method=get action=schedule.php>";
		echo "<tr><td>";
			echo "<table>";
			echo "<table border=0 align=center>";
			echo "<tr><td><font face=verdana size=1>ORIGIN:</font></td><td><b><font face=verdana size=1>$origin</font></b></td></tr>";
			echo "<tr><td><font face=verdana size=1>DESTINATION:</font></td><td><b><font face=verdana size=1>$destination</font></b></td></tr>";
			echo "<tr><td><font face=verdana size=1>WEIGHT:</font></td><td><b><font face=verdana size=1>$weight</font></b></td></tr>";
			echo "<tr><td><font face=verdana size=1>CLASS:</font></td><td><b><font face=verdana size=1>$shipclass</font></b></td></tr>";

			$zzgetprice_ar = $ar;
			debug("rating.php: set zzgetprice_ar to $zzgetprice_ar");


			# -----------------------------------------------------------------
			# Display the fuel surcharge information
			# -----------------------------------------------------------------
			if ( isset($surcharge) ) {

				echo "<input type=hidden value=$surcharge name=surcharge>";
				$totalar = $ar +  $surcharge;
				debug("rating.php: adding surcharge of $surcharge to ar to get totalar $totalar");

			} else {

				echo "<input type=hidden value=0.00 name=surcharge>";
				$totalar = $ar;
				debug("rating.php: NOT adding surcharge to ar to get totalar $totalar");

			}


			# -----------------------------------------------------------------
			# Calculate and display the accessorials for this shipment
			# -----------------------------------------------------------------
			
			$asscharges = 0;
			$accessorials = "";
			$assids = "";

			// The old bad way... hard coded
			// $standard_services = array( 0 => 'LFTORG',
			// 	                        1 => 'LFTDST',
			//	                        2 => 'RSDPCK',
			//	                        3 => 'RSDDEL',
			//	                        4 => 'INSPCK',
			//	                        5 => 'INSDEL',
			//	                        6 => 'HAZMAT',
			//	                        7 => 'CLLPCK',
			//	                        8 => 'CLLDEL');

			// Create an array with all of the current accessorials we have on file (new way, dynamic)
			$standard_services = getAccessorialListing();
			
			for ( $i = 0; $i <= count($standard_services); $i++ ) {
				if ( isset($$standard_services[$i]) ) {
					debug("rating.php: standard service $standard_services[$i] is set from our caller");
					$this_charge = getAccessorialCharge($carrierid, $standard_services[$i]);
					$this_id = getAccessorialId($carrierid, $standard_services[$i]);
					if ( $this_charge > 0 ) {
						debug("rating.php: the charge for $standard_services[$i] with carrier $carrierid is $this_charge");
					} else {
						debug("rating.php: the charge for $standard_services[$i] with carrier $carrierid is 0, check the db");
					}

					// Create a comma separated list of accessorials to shove into the cookie.
					// If it's the first one, don't add the damn comma.
					if ( $accessorials == "" ) {
						$accessorials = $standard_services[$i];
						$assids = $this_id;
						$asscharges = $this_charge;
					} else {
						$accessorials = $accessorials . "," . $standard_services[$i];
						$assids = $assids . "," . $this_id;
						$asscharges = $asscharges + $this_charge;
					}
				}
			}

			debug("rating.php: accessorials string before hiding in the page: $accessorials");
			debug("rating.php: accessorialids string before hiding in the page: $assids");

			// Be good and clean up after yourself.
			unset($i);

			// Set the hidden field
			debug("rating.php: setting shipmentaccessorial hidden field");
			echo "<input type=hidden name=shipmentaccessorials value='$accessorials'>";

			debug("rating.php: setting shipmentaccessorialids hidden field");
			echo "<input type=hidden name=shipmentaccessorialids value='$assids'>";
			

			// Display the total additional accessorial charges.
			if ( isset($asscharges)) {
	            echo "<tr><td><font face=verdana size=1><b>SERVICES:</b></font></td><td><font face=verdana size=1 color=cb0000><b>$" . sprintf('%01.2f', $asscharges) . "</b></font></td></tr>";
				echo "<input type=hidden value=$asscharges name=asscharges>";
				$totalar = $totalar + $asscharges;
				debug("rating.php: adding asscharges of $asscharges to current totalar to get totalar of $totalar");
			} else {
	            echo "<tr><td><font face=verdana size=1><b>SERVICES:</b></font></td><td><font face=verdana size=1 color=cb0000><b>$0.00</b></font></td></tr>";
				echo "<input type=hidden value=0.00 name=asscharges>";
				$totalar = $totalar;
				debug("rating.php: NOT adding asscharges to current totalar to get totalar of $totalar");
			}
            

			# Display the final total
			echo "<tr><td><font face=verdana size=2><b>TOTAL:</b></font></td><td><b><font face=verdana size=2 color=cb0000>$" . sprintf('%01.2f', $totalar) . "</font></b></td></tr>";

			$ardisplay = sprintf('%01.2f', $totalar);

			# Display the transit time
            if ($transit < 1) {
				$transit = "CALL";
			}
			elseif ($transit == 1) {
				$transit = "1 DAY";
			}
			else {
				$transit = "$transit DAYS";
			}
			
			echo "<tr><td><font face=verdana size=1><b>TRANSIT TIME:</b></font></td><td><b><font face=verdana size=1 color=cb0000>$transit</font></b></td></tr>";
			echo "<tr valign=middle height=40><td colspan=2 align=center><font face=verdana size=1><b><input type=hidden name=quoteid value=$quoteid><input type=hidden name=garbonzo value=$totalar><input type=image src=images/buttons/schedule.gif width=100 height=21 border=0 alt='SCHEDULE SHIPMENT'></b></td></tr>";
			echo "</table>";
		echo "</td></form>";
		echo "</table>";
	}

	// if we can't find a carrier
	elseif ($origin and $destination and $weight and $shipclass and ($cantservice == 1)) {
		echo "<td bgcolor=FAFAFA width=200>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><form method=get action=schedule.php>";
		echo "<tr valign=top><td>";
			echo "<table>";
			echo "<table border=0 align=center>";
			echo "<tr valign=top><td align=center>";
			echo "<font face=verdana size=2><b>SORRY!</b><br><BR>";
			echo "We are not currently servicing that lane.<br><br>Please check<br>back soon!";
			echo "</td></tr>";
			echo "</table>";
		echo "</td></form>";
		echo "</table>";
	}
	
	// means we got an error in the rate client
	elseif ($ap == -2) {
		echo "<td bgcolor=FAFAFA width=200>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><form method=get action=schedule.php>";
		echo "<tr valign=top><td>";
			echo "<table width=170>";
			echo "<table border=0 align=center>";
			echo "<tr valign=top><td align=center>";
			echo "<font face=verdana size=2><b>SORRY!</b><br><BR>";
			echo "An unexpected error has occurred.<br><br>Please re-submit this rate information.";
			echo "</tD></tr>";
			echo "</table>";
		echo "</td></form>";
		echo "</table>";
	}
	elseif ($truckload) {
		echo "<td bgcolor=FAFAFA width=200>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><form method=get action=schedule.php>";
		echo "<tr valign=top><td>";
			echo "<table width=170>";
			echo "<table border=0 align=center>";
			echo "<tr valign=top><td align=center>";
			echo "<font face=verdana size=2><b>TRUCKLOAD REQUEST</b><br><BR>";
			echo "This request has been forwarded to the Volume Services Deptment, you will recieve an e-mail rate within the hour.";
			echo "</tD></tr>";
			echo "</table>";
		echo "</td></form>";
		echo "</table>";
	
	}
	?>
	</td>
	</tr>
	</table><br>
	<table>
	<tr><td align=center>
	<font face=verdana size=2><b>OTHER SERVICES: NON-LTL SHIPMENTS</B></FONT><BR><BR>
	<a href="ratingspecial.php?service=airfreight" class="links_general">Airfreight</a> | <a href="ratingspecial.php?service=airfreight" class="links_general">Expedited</a> | <a href="ratingspecial.php?service=canada" class="links_general">Canada</a> | <a href="ratingspecial.php?service=mexico" class="links_general">Mexico</a> | <a href="ratingspecial.php?service=truckload" class="links_general">Flatbed</a> | <a href="ratingspecial.php?service=truckload" class="links_general">Truckload</a> | <a href="ratingspecial.php?service=international" class="links_general">International</a>   
	
	</td></tr>
</table>
<br><br><br>
<?php

require('zzfooter.php');

debug("rating.php: leaving");

?>
