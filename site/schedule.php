<?php
# =============================================================================
#
# schedule.php
#
# Shipment scheduling page
#
# $Id: schedule.php,v 1.30 2003/03/05 20:45:41 youngd Exp $
#
# Contents Copyright (c) 2002, transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: schedule.php,v $
# Revision 1.30  2003/03/05 20:45:41  youngd
#   Updates
#
# Revision 1.29  2003/02/14 21:03:56  youngd
#   * Still going...
#
# Revision 1.28  2003/02/14 20:02:33  youngd
#   * Still testing accessorials.
#
# Revision 1.27  2003/02/13 17:32:13  youngd
#   * Starting accessorial changes. Still in testing.
#
# Revision 1.26  2003/01/24 18:48:54  webdev
#   * Added ddcharges id.
#
# Revision 1.25  2003/01/16 22:32:44  webdev
#   * Added packaging quetions.
#
# Revision 1.24  2003/01/14 18:48:49  webdev
#   * Raised accessorial rates.
#
# Revision 1.23  2002/12/26 21:41:14  webdev
#   * Commented the section of code where the calendar bug was fixed. Forgot
#     to do that in the last release.
#
# Revision 1.22  2002/12/26 21:36:48  webdev
#   * Fixed bug in the calendar display that didn't print January when we're in December. It was
#     printing ahead to February.
#
# Revision 1.21  2002/11/15 21:56:29  webdev
#   * Removed bill ot my address for now.
#
# Revision 1.20  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.19.2.1  2002/11/05 15:15:12  webdev
#   * New registration in progress.
#
# Revision 1.19  2002/10/28 20:42:17  youngd
#   * Added fax link to index page
#   * Fixed hazmat link on scheudle.
#
# Revision 1.18  2002/10/25 22:26:01  youngd
#   * All works now.
#
# Revision 1.17  2002/10/25 20:28:23  youngd
#   * Schedule displays rate with fsc included now.
#
# Revision 1.16  2002/10/25 20:25:27  youngd
#   * Schedule page now correctly displays the total cost with surcharge
#   * Schedule page shows CALL if transit is 0
#   * Same changes to confirm.
#
# Revision 1.15  2002/10/25 20:15:53  youngd
#   * Schedule page now correctly displays the total cost
#
# Revision 1.14  2002/10/11 20:05:18  youngd
#   * Reworked accessorials which work now.
#
# Revision 1.13  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.12  2002/10/04 21:58:25  youngd
#   * Working on margin & pricing changes still.
#
# Revision 1.11  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.10  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.9  2002/09/19 02:55:17  youngd
#   * Schedule change updates
#
# Revision 1.8  2002/09/15 07:27:53  webdev
#   * Added source header
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

debug("schedule.php: entering after initial requires");

if ($quoteid) {
// get quote info
$quotestr = "SELECT origin, destination, weight, class, ar, transit, carrierid, fuel_surcharge from quotes where customerid = $digishipcookie2 and quoteid = $quoteid and booked != 1";
$quotedetail = mysql_query($quotestr) or die (mysql_error());
if ($quoteline = mysql_fetch_row($quotedetail)) {
	// get city, state, and zip info
	require('zzlocation.php');
	// get previous origin, destination, billing and accessorials
	require('zzgetprevious.php');
	$quotenolongeravl = 0;
}

else {
	// flag shipment isn't availble for booking option
	$quotenolongeravl = 1;
}
}
else {
	die();
}

// format in case 00001 on zips
		$disporigin = sprintf('%05d' , $quoteline[0]);
		$dispdestination = sprintf('%05d' , $quoteline[1]);
?>




<html>
<head>
<title>The Freight Depot > Rating & Scheduling</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>


<script language="JavaScript">
function validate(frm) {
	
	var errors = "";
	if (!frm.originselected || frm.originselected.value == -1) {
		if (frm.origincompany.value == "") {
			errors += "Please enter the origin company's name.\n";
		}
		if (frm.originaddress1.value == "") {
			errors += "Please enter the origin company's address.\n";
		}
		if (frm.origincity.value == "") {
			errors += "Please enter the origin company's city.\n";
		}
		if (frm.originstate.value == "") {
			errors += "Please enter the origin company's state.\n";
		}
	}
	if (!frm.destselected || frm.destselected.value == -1) {
		if (frm.destinationcompany.value == "") {
			errors += "Please enter the destination company's name.\n";
		}
		if (frm.destinationaddress1.value == "") {
			errors += "Please enter the destination company's address.\n";
		}
		if (frm.destinationcity.value == "") {
			errors += "Please enter the destination company's city.\n";
		}
		if (frm.destinationstate.value == "") {
			errors += "Please enter the destination company's state.\n";
		}
	}
	if (frm.billingmethod[2].checked == true) {
		if (frm.billingcompany.value == "") {
			errors += "Please enter the billing company's name.\n";
		}
		if (frm.billingaddress1.value == "") {
			errors += "Please enter the billing company's address.\n";
		}
		if (frm.billingcity.value == "") {
			errors += "Please enter the billing company's city.\n";
		}
		if (frm.billingstate.value == "") {
			errors += "Please enter the billing company's state.\n";
		}
		if (frm.billingzip.value == "") {
			errors += "Please enter the billing company's zip code.\n";
		}
	}
	if (frm.productdesc.value == "") {
		errors += "Please enter the product's description.\n";
	}
	if (frm.pieces.value == "") {
		errors += "Please enter the number of units/pieces.\n";
	}
	if (frm.hidepickupdate.value == "0000-00-00") {
		errors += "Please click on a pickup date.\n";
	}
	if ((frm.hazmat[0].checked == true) && frm.hazmatphone.value == "") {
		errors += "Please enter your hazmat emergency contact phone number.\n";
	}

	if ((frm.palletized[0].checked == false) && frm.palletized[1].checked == false) {
		errors += "Make sure to answer if the shipment is on pallets or not.\n";
	}

	if ((frm.palletized[0].checked == true) && frm.palletqty.value == 0 ) {
		errors += "You stated that the shipment is on pallets, but did not specify the number of pallets.\n";
	}

	if (errors != "") {
		alert(errors);
		return false;
	}
	else {
		return true;
	}
	
}


function changebill(cntrl) {

	if (cntrl == 1) {
		document.forms.shipmentform.billingcompany.disabled = true;
		document.forms.shipmentform.billingaddress1.disabled = true;
		document.forms.shipmentform.billingaddress2.disabled = true;
		document.forms.shipmentform.billingcity.disabled = true;
		document.forms.shipmentform.billingstate.disabled = true;
		document.forms.shipmentform.billingzip.disabled = true;
		document.forms.shipmentform.billingcompany.value = "";
		document.forms.shipmentform.billingaddress1.value = "";
		document.forms.shipmentform.billingaddress2.value = "";
		document.forms.shipmentform.billingcity.value = "";
		document.forms.shipmentform.billingstate.value = "";
		document.forms.shipmentform.billingzip.value = "";
		
	}
	else {
		document.forms.shipmentform.billingcompany.disabled = false;
		document.forms.shipmentform.billingaddress1.disabled = false;
		document.forms.shipmentform.billingaddress2.disabled = false;
		document.forms.shipmentform.billingcity.disabled = false;
		document.forms.shipmentform.billingstate.disabled = false;
		document.forms.shipmentform.billingzip.disabled = false;
	}

}

var previousobject = "";
function switchbg(obje) {
	if(previousobject) {
		//previousobject.style.border='';
		previousobject.style.background='#ffffff';
	}
	//obje.style.border='1 #000000 solid';
	obje.style.background='#aaaaaa';
	previousobject = obje;
	
}


function overdate(d,m,ye) {
	document.shipmentform.hidepickupdate.value = ye + '-' + m + '-' + d;
}


function updateOrigin(contact, company, phone, address1, address2, city, state) {

    shipmentform.origincontact.value = "";
    shipmentform.origincontact.value = contact;

}

function doPallet(ans) {
	if ( ans == "YES" ) {
		document.forms.shipmentform.palletqty.disabled = false;
		document.forms.shipmentform.palletqty.focus;
	}
	if ( ans == "NO" ) {
		document.forms.shipmentform.palletqty.disabled = true;
	}
}



function doService (method,service) {

	var dd_rate         = ddrate.innerHTML;
	var dd_charges      = ddcharges.innerHTML;
	var dd_totalcharges = ddtotalcharges.innerHTML;

	current_rate         = parseFloat(dd_rate.substring(1,dd_rate.length));
	current_charges      = parseFloat(dd_charges.substring(1,dd_charges.length));
	current_totalcharges = parseFloat(dd_totalcharges.substring(1,dd_totalcharges.length));

	alert("CURRENT_RATE: " + current_rate);
	alert("CURRENT_CHARGES: " + current_charges);
	alert("CURRENT_TOTALCHARGES: " + current_totalcharges);

	if ( method == "ADD" ) {
		new_charges = current_charges + service.value;
		new_totalcharges = current_totalcharges + new_charges;
		
		alert("Adding additional services charge of " + service.value + "\nto current additional services charges of " + current_charges + "\nTotal additional charges are now " + new_charges + "\nTotal is now " + new_totalcharges);
	}

	if ( method == "REMOVE" ) {
		a_new_charges = current_charges - parseFloat(service.value);
		a_new_totalcharges = current_totalcharges - current_charges;

		new_charges = roundit(a_new_charges,2);
		new_totalcharges = roundit(a_new_totalcharges,2);

		alert("Removing additional services charge of " + parseFloat(service.value) + "\nfrom current additional services charges of " + current_charges + "\nTotal additional charges are now " + new_charges + "\nTotal is now " + new_totalcharges);
	}

	ddcharges.innerHTML = "$" + new_charges;
	ddtotalcharges.innerHTML = "$" + new_totalcharges;


	return;

}


function changeService (service) {

	// User is turning on the button
	if ( service.checked == true ) {
		service.checked = true;
		doService("ADD",service);
		return;
	}

	// User is turning off the button
	if ( service.checked == false ) {
		service.checked = false;
		doService("REMOVE",service);
		return;
	}
}

</script>

</head>
<?php

require('zzheader.php');

?>

<center>
<table cellpadding=0 cellspacing=0>
<tr><td><img src="images/general/shipprocessleft.gif" width=27 height=22></td>
<td bgcolor="7390C0" valign=middle><font face='"trebuchet MS",arial' size=1>SHIPMENT SCHEDULING PROCESS: <img align=middle src="images/general/shipprocess1.gif" width=15 height=15> <font color=CDD2D9>GET A QUOTE</font>  <img align=middle src="images/general/shipprocess2.gif" width=15 height=15> <font size=2><U>COMPLETE BOL</U></font>  <img align=middle src="images/general/shipprocess3.gif" width=15 height=15> <font color=CDD2D9>CONFIRM  <img align=middle src="images/general/shipprocess4.gif" width=15 height=15> PRINT BOL</font></font></b></td>
<td><img src="images/general/shipprocessright.gif" width=27 height=22></td>
</tr></table>
<br>





<table width=700 border=0>
<tr valign=top><form name="shipmentform" method="post" action="confirm.php" onSubmit="return validate(this);">
<td width=450 align=center>
	<table border=0>
	<tr><td colspan=3>
		<?php
			if($quotenolongeravl == 1) {
				echo "<br><br><font face=verdana color=cb0000 size=2><b>SORRY!</b><br><br><i>This shipment is no longer available for scheduling.</i><br><br><font color=000000>Please <a href=rating.php>go back</a> and begin rating another shipment.</td></tr></table></td><td width=220>&nbsp; &nbsp; &nbsp; </td></tr></table></body></html>";
				die();
			}
		?>
		
	<br>
	</td></tr>
	
	
	<tr><td colspan=3>
	<font face="verdana" size=2><b>SHIPMENT ORIGIN</b></font></td></tr>
	<tr><td colspan=3>
			<table>
			<?php
			// display previous
			if ($originrow = mysql_fetch_row($getorigins)) {
				echo "<tr><td align=center>";
				echo "<select name=originselected onChange=\"updateOrigin('$originrow[8]');\">";
				echo "<option value=-1>-- PREVIOUS ORIGIN ADDRESSES IN $quoteline[0] --";
				echo "<option value=$originrow[0]>$originrow[1] - $originrow[4], $originrow[5]";
				while ($originrow = mysql_fetch_row($getorigins)) {
					echo "<option value=$originrow[0]>$originrow[1] - $originrow[4], $originrow[5]";
				}
				echo "</select></font><br></td></tr>";

			}
			?>
			</table>
	</td></tr>

	<tr>
		<td colspan=3><img src="images/pixels/blackpixel.gif" width=400 height=1></td>
	</tr>
	
	
	<tr>
		<td width=5></td>
		<td><font face="verdana" size=2>Contact Name</font></td>
		<td><input size=20 name="origincontact"></td>
	</tr>
	<tr>
		<td width=5></td>
		<td width=225><font face="verdana" size=2>Company Name</font></td>
		<td><input size=20 name="origincompany"></td>
	</tr>
	<tr>
		<td width=5></td>
		<td><font face="verdana" size=2>Phone</font></td>
		<td><input size=20 name="originphone"></td>
	</tr>
	<tr>
		<td width=5></td>
		<td><font face="verdana" size=2>Address 1</font></td>
		<td><input size=20 name="originaddress1"></td>
	</tr>
	<tr>
		<td width=5></td>
		<td><font face="verdana" size=2>Address 2</font></td>
		<td><input size=20 name="originaddress2"></td>
	</tr>
	<tr>
		<td width=5></td>
		<td><font face="verdana" size=2>City</font></td>
		<td><input size=20 name="origincity" value="<?php echo "$theorigincity"; ?>"></td>
	</tr>
	<tr>
		<td width=5></td>
		<td><font face="verdana" size=2>State</font></td>
		<td><input size=3 name="originstate" maxlength=2 value="<?php echo "$theoriginstate"; ?>"></td>
	</tr>
	<tr valign=top>
		<td width=5></td>
		<td><font face="verdana" size=2>Zip</font></td>
		<td><font face="verdana" size=2><?php echo "$quoteline[0]"; ?></font><br><br></td>
	</tr>
	
	<input type=hidden name="originzip" maxlength=5 value="<?php echo "$quoteline[0]"; ?>">
	
	<tr>
		<td colspan=3><font face="verdana" size=2><b>SHIPMENT DESTINATION</b></font></td>
	</tr>
	<tr>
		<td colspan=3>
			<table>
			<?php
				// display previous
				if ($destrow = mysql_fetch_row($getdests)) {
					echo "<tr><td align=center>";
					echo "<select name=destselected>";
					echo "<option value=-1>-- PREVIOUS DESTINATION ADDRESSES IN $quoteline[1] --";
					echo "<option value=$destrow[0]>$destrow[1] - $destrow[4], $destrow[5]";
					while ($destrow = mysql_fetch_row($getdests)) {
						echo "<option value=$destrow[0]>$destrow[1] - $destrow[4], $destrow[5]";
					}
					echo "</select></font><br></td></tr>";
				}	
			?>
			</table>
		</td>
	</tr>
	
	<tr>
		<td colspan=3><img src="images/pixels/blackpixel.gif" width=400 height=1></td>
	</tr>
	
	
	<tr><td width=5></td><td><font face="verdana" size=2>Contact Name</font></td>
	<td><input size=20 name="destinationcontact"></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Company Name</font></td>
	<td><input size=20 name="destinationcompany"></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Phone</font></td>
	<td><input size=20 name="destinationphone"></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Address 1</font></td>
	<td><input size=20 name="destinationaddress1"></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Address 2</font></td>
	<td><input size=20 name="destinationaddress2"></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>City</font></td>
	<td><input size=20 name="destinationcity" value="<?php echo "$thedestcity"; ?>"></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>State</font></td>
	<td><input size=3 name="destinationstate" maxlength=2 value="<?php echo "$thedeststate"; ?>"></td></tr>
	<tr valign=top><td width=5></td><td><font face="verdana" size=2>Zip</font></td>
	<td><font face="verdana" size=2><?php echo "$quoteline[1]"; ?></font><br><bR></td></tr>
	<input type=hidden name="destinationzip" maxlength=5 value="<?php echo "$quoteline[1]"; ?>">
	
	<tr><td colspan=3>
	<a name="billing">
	<font face="verdana" size=2><b>BILLING ADDRESS</b></font></td></tr>
	<tr><td colspan=3><img src="images/pixels/blackpixel.gif" width=400 height=1></td></tr>
	<tr valign=top>
        <td width=5></td>
        <td colspan=3>
            <font face="verdana" size=2>
	        <input type=radio name="billingmethod" value=1 onClick='changebill(1);'> Shipper 
	        <input type=radio name="billingmethod" value=2 onClick='changebill(1);'> Consignee 
	        <input type=radio name="billingmethod" value=3 onClick='changebill(2);'> Third Party <i>(FILL IN BELOW)</i>
            </font>
        </td>
    </tr>
	<!--
    <tr valign=top>
        <td width=5></td>
        <td colspan=1>
            <font face="verdana" size=2>
            <input type=radio name="billingmethod" value=4 checked onClick='changebill(3);'> My Default Address
            <br><br>
            </font>
        </td>
    </tr>
	-->
	<tr><td width=5></td><td><font face="verdana" size=2>Company Name</font></td>
	<td><input name="billingcompany" size=20></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Address 1</font></td>
	<td><input name="billingaddress1" size=20></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Address 2</font></td>
	<td><input name="billingaddress2" size=20></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>City</font></td>
	<td><input name="billingcity" size=20></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>State</font></td>
	<td><input name="billingstate" size=3 maxlength=2></td></tr>
	<tr><td width=5></td><td><font face="verdana" size=2>Zip</font></td>
	<td><input name="billingzip" size=6 maxlength=5></td></tr>
	
	<tr>
		<td colspan=3>
			<font face="verdana" size=2><b>SHIPMENT INFORMATION</b></font>
		</td>
	</tr>
	<tr>
		<td colspan=3>
			<img src="images/pixels/blackpixel.gif" width=400 height=1>
		</td>
	</tr>
	<tr>
		<td width=5></td>
		<td width=225>
			<font face="verdana" size=2>Product Description</font>
		</td>
		<td>
			<input size=20 name="productdesc">
		</td>
	</tr>
	<tr>
		<td width=5></td>
		<td width=225>
			<font face="verdana" size=2>Units/Pieces</font>
		</td>
		<td>
			<input size=3 name="pieces">
		</td>
	</tr>
	<tr>
		<td width=5></td>
		<td width=225>
			<font face="verdana" size=2>Packaging Type</font>
		</td>
		<td>
			<select name=packagingtype>
				<?php
					$packagingtypesql = mysql_query("SELECT * FROM package_types ORDER BY description");

					while ($p = mysql_fetch_row($packagingtypesql)) {
						echo "<option value=$p[1]>$p[2]";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td width=5></td>
		<td>
			<font face="verdana" size=2>Is the shipment on pallets?</font>
		</td>
		<td>
			<font face="verdana" size=2>
			<input type=radio name="palletized" value="Y" onClick='doPallet("YES");'> Yes 
			<input type=radio name="palletized" value="N" onClick='doPallet("NO");'> No
		</td>
	</tr>
	<tr>
		<td width=5></td>
		<td width=225>
			<font face="verdana" size=2>Number of pallets</font>
		</td>
		<td>
			<input size=3 name="palletqty" value=0>
		</td>
	</tr>	
	<tr>
		<td width=5></td>
		<td>
			<font face="verdana" size=2>Haz Mat?</font>
		</td>
		<td>
			<font face="verdana" size=2>
			<input type=radio name="hazmat" value="Y"> Yes 
			<input type=radio name="hazmat" value="N" checked> No
		</td>
	</tr>
	<tr>
		<td width=5></td>
		<td width=225>
			<font face="verdana" size=2>Haz Mat Emrgcy Phone</font>
		</td>
		<td>
			<input size=20 name="hazmatphone">
		</td>
	</tr>
	<tr VALIGN=TOP>
		<td width=5></td>
		<td width=225>
			<font face="verdana" size=2>PO Number</font><BR><BR>
		</td>
		<td>
			<input size=20 name="ponumber">
		</td>
	</tr>

	
	<tr><td colspan=3>
	<font face="verdana" size=2><b>ADDITIONAL SERVICES</b></font></td></tr>
	<tr><td colspan=3><img src="images/pixels/blackpixel.gif" width=400 height=1></td></tr>
	<tr valign=top><td width=5></td>
	<td>
        <font face="verdana" size=2>
            Select any additional<br>
            services that will<br>
            apply to this shipment.<br>
        </font><br>
    </td>
	<td>

	<!-- ACCESSORIALS -->
	<?php

	// These are all the additional services the user selected previously
	// Blow up the hidden field and shove it into an array
	$selected_service_names = explode(",",$shipmentaccessorials);

	// These are the service ids selected that are carrier specific
	$selected_service_ids = explode(",", $shipmentaccessorialids);

	// These are all the currently available services (for all carriers)
	$all_services = getAccessorialListing();
	$asscharges = 0;

	// Display the selected and unselected accessorials
	for ( $i=0; $i<count($all_services); $i++ ) {
		debug("schedule.php: checking to see if all_services entry $all_services[$i] came inbound");
		for ( $j=0; $j<count($selected_service_names); $j++ ) {
			$hit = 0;
			if ( $all_services[$i] == $selected_service_names[$j] ) {
				$hit = 1;
				break;
			}
		}

		if ( $hit ) {
			debug("schedule.php: $all_services[$i] IS SET");
			$sname = getAccessorialNameByRefCode($all_services[$i]);
			$thischarge = getAccessorialCharge($quoteline[6], $all_services[$i]);
			$asscharges = $asscharges + $thischarge;
			echo "<font face=verdana size=1>\n";
			echo "<input type=checkbox name=$all_services[$i] value=$thischarge checked=true onClick='changeService($all_services[$i]);'>$sname<br>\n";
			echo "</font>\n";
		} else {
			debug("schedule.php: $all_services[$i] IS *NOT* SET");
			$sname = getAccessorialNameByRefCode($all_services[$i]);
			$thischarge = getAccessorialCharge($quoteline[6], $all_services[$i]);
			echo "<font face=verdana size=1>\n";
			echo "<input type=checkbox name=$all_services[$i] value=$thischarge onClick='changeService($all_services[$i]);'>$sname<br>\n";
			echo "</font>\n";
		}
	}


	unset($i);
	unset($j);
	unset($sname);
	unset($hit);

	?>

	</td></tr>
	<tr><td colspan=3>
	<br><font face="verdana" size=2><b>SPECIAL INSTRUCTIONS</b></font></td></tr>
	<tr><td colspan=3><img src="images/pixels/blackpixel.gif" width=400 height=1></td></tr>
	<tr><td colspan=3><font face="verdana" size=2>Please enter any special instructions you will need for this shipment.</td></tr>
	<tr><td colspan=3><input size=60 name=speci></td></tr>
	
	<tr><td colspan=3>
	<a name="billing">
	<font face="verdana" size=2><b>PICKUP TIME</b></font></td></tr>
	<tr><td colspan=3><img src="images/pixels/blackpixel.gif" width=400 height=1></td></tr>
	
	<tr valign=top><td width=5></td><td colspan=2><font face="verdana" size=2>What date and time will your shipment be ready?</font></td></tr>
	<tr><td></td><td colspan=2>
		<table width=350>
		<tr valign=top><td>
		<input type=hidden name=hidepickupdate value="0000-00-00">
		<?php

		// Print this month's calendar.
		debug("schedule.php: loading zzcalendar.php");
		include ('zzcalendar.php');
		$year = date('Y');
		$month = date('m');
		$todate = date('d');
		$future = 0;

		debug("schedule.php: year=$year, month=$month, todate=$todate");
		$now  = new Calendar($month,$year);
		echo $now->gethtml($future);
		?>
		</td>
		<td>
		
		<?php
		debug("schedule.php: loading next month's calendar");
		// Next month's calendar. This has to be done BEFORE the $month==12 thing down below.
		// Otherwise it'll print February when you're in December. Bug fixed. /DAY
		if ($month < 12) {
			$month += 1;
		}

		if ($month == 12) {
			$month = 1;
			$year += 1;
			debug("schedule.php: month is 12, adding 1 to year (now $year) and month (now $month)");
		}
		$future = 1;
		$now  = new Calendar($month,$year);
		echo $now->gethtml($future);
		?>
		</td>
		</tr>
		<tr>
		<td colspan=2 align=center><font face="verdana" size=2>READY TIME:<br>
		<select name="after"><option value="1">1<option value="2">2<option value="3">3<option value="4">4<option value="5" SELECTED>5
		<option value="6">6<option value="7">7<option value="8">8<option value="9" selected>9<option value="10">10<option value="11">11<option value="12">12</select><select name="afterampm"><option value="am" selected>AM<option value="pm">PM</select>
		<br><br>
		CLOSING TIME:<br>
		<select name="close"><option value="1">1<option value="2">2<option value="3">3<option value="4">4<option value="5" SELECTED>5
	<option value="6">6<option value="7">7<option value="8">8<option value="9">9<option value="10">10<option value="11">11<option value="12">12</select><select name="ampm"><option value="am">AM<option value="pm" SELECTED>PM</select>
		<br><Br>
		<?php
		//make hidden variables
		echo "<input type=hidden name=quoteid value=$quoteid>"
		?>
		<input type=submit value="SUBMIT">
		</td></tr>
		</table>
	</td></tr>

	</table>
</font>
</td>


<td width=210><br><br>
	<table cellpadding=0 cellspacing=1 border=0 bgcolor=000000>
	<tr><td align=center>
		<table width=196 cellspacing=0 cellpadding=0 border=0 bgcolor=FAFAFA>
		<tr><td colspan=2 align=center bgcolor=A20E07><font face=verdana size=1 color=ffffff><b>
		YOU ARE CURRENTLY SCHEDULING THE FOLLOWING SHIPMENT:</td></tr>
		</td></tr>
		</table>
	<tr>
	<td bgcolor=FAFAFA>
		<table WIDTH=196>
		<?php

		$transitdays = $quoteline[5];

		if ( $transitdays == 0 ) {
			$transitdays = "CALL";
		}
		debug("schedule.php: transit days set to $transitdays");
		
		
		echo "<tr><td><font face=verdana size=1>ORIGIN:</font></td><td><font face=verdana size=1>$quoteline[0]</font></td></tr>";
		echo "<tr><td><font face=verdana size=1>DESTINATION:</font></td><td><font face=verdana size=1>$quoteline[1]</font></td></tr>";
		echo "<tr><td><font face=verdana size=1>WEIGHT:</font></td><td><font face=verdana size=1>$quoteline[2]</font></td></tr>";
		echo "<tr><td><font face=verdana size=1>CLASS:</font></td><td><font face=verdana size=1>$quoteline[3]</font></td></tr>";
		$rate = $quoteline[4] + $quoteline[7];
		echo "<tr><td><font face=verdana size=1>RATE:</font></td><td><font face=verdana size=1 id=ddrate>$" . sprintf('%01.2f',$rate) . "</font></td></tr>";
		echo "<tr><td><font face=verdana size=1>SERVICES:</font></td><td><font face=verdana size=1 id=ddcharges>$" . sprintf('%01.2f',$asscharges) . "</font></td></tr>";

        $totalcharges = $quoteline[4] + $asscharges;
		$totalcharges += $quoteline[7];
        $additionalcharges = $asscharges;

        echo "<input type=hidden name='additionalcharges' value='" . sprintf('%01.2f',$additionalcharges) . "'>";
        echo "<input type=hidden name='totalcharges' value='" . sprintf('%01.2f',$totalcharges) . "'>";
		echo "<tr><td><font face=verdana size=1>TOTAL:</font></td><td><font face=verdana size=1 id=ddtotalcharges>$" . sprintf('%01.2f',$totalcharges) . "</font></td></tr>";
        echo "<tr><td><font face=verdana size=1>TRANSIT DAYS:</font></td><td><font face=verdana size=1>$transitdays</font></td></tr>";
		echo "<tr><td colspan=2 align=center><font face=verdana size=1><br><b>[ <a href=rating.php>CANCEL SCHEDULING</a> ]</b></td></tr>";
		?>
		</table>
	</td>
	</tr>
	</table>
	</td></tr>
	</table>

</td></tr>
</table>

<br><br>
<?php

require ('zzfooter.php');

debug("schedule.php: leaving");

?>
