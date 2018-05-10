<?php
# =============================================================================
#
# update.php
#
# Internal BOL Update Page
#
# $Id: update.php,v 1.5 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: update.php,v $
# Revision 1.5  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.4  2002/10/16 06:47:44  youngd
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



// select bol information
$bolsql = "select carriers.name, shipment.customerid, shipment.pickupdate, shipment.pickupbefore, shipment.pickupafter, shipment.ponumber, shipment.productdescription, shipment.hazmat, shipment.hazmatphone, shipment.units, quotes.weight, quotes.class as classa, shipment.origin, shipment.destination, shipment.billing, shipment.carrierpro, shipment.specialinstructions from carriers, shipment, quotes where shipment.carrierid = carriers.carrierid and shipment.quoteid = quotes.quoteid and shipment.shipmentid = $shipmentid";
$bolselect = mysql_query($bolsql) or die (mysql_error());
if (!($bolline = mysql_fetch_array($bolselect))) {
		
	die ();
}

// let's get the addresses too
			//origin
			$originquery = mysql_query("select company, address1, address2, city, state, zip, contact, phone from address where addressid = $bolline[origin]") or die(mysql_error());
			$originline = mysql_fetch_row($originquery);
			$origincompany = $originline[0];
			$originaddress1 = $originline[1];
			$originaddress2 = $originline[2];
			$origincity = $originline[3];
			$originstate = $originline[4];
			$originzip = $originline[5];
			$origincontact = $originline[6];
			$originphone = $originline[7];

			//destination
			$destquery = mysql_query("select company, address1, address2, city, state, zip, contact, phone from address where addressid = $bolline[destination]") or die(mysql_error());
			$destline = mysql_fetch_row($destquery);
			$destcompany = $destline[0];
			$destaddress1 = $destline[1];
			$destaddress2 = $destline[2];
			$destcity = $destline[3];
			$deststate = $destline[4];
			$destzip = $destline[5];
			$destcontact = $destline[6];
			$destphone = $destline[7];

			//billing
			$billquery = mysql_query("select company, address1, address2, city, state, zip, contact, phone from address where addressid = $bolline[billing]") or die(mysql_error());
			$billline = mysql_fetch_row($billquery);
			$billingcompany = $billline[0];
			$billingaddress1 = $billline[1];
			$billingaddress2 = $billline[2];
			$billingcity = $billline[3];
			$billingstate = $billline[4];
			$billingzip = $billline[5];
			$billingcontact = $billline[6];
			$billingphone = $billline[7];

// get accessorials
$acceq = mysql_query("SELECT accessorials.name from accessorials, shipmentaccessorials where shipmentid = $shipmentid and shipmentaccessorials.assid = accessorials.assid");
			
			
// get digiship info
$digiselect = mysql_query("SELECT companyname, address1, address2, city, state, zip from digiship");
$digirow = mysql_fetch_row($digiselect);

?>

<?php
require('../zzmysql.php');
if ($action == 1) {
	if ($delivered == 0) {
		$code = 4;
	}
	else {
		$code = 5;
	}
  $timenow = getdate();
   $thetime2 = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];

	$updateq = mysql_query("INSERT INTO shipmentstatus (shipmentid, statusdetails, statuscode, statustime) values ($shipmentid, '$details', $code, '$thetime2')") or die (mysql_error()); 
	if ($code == 5) {
		$delupdate = mysql_query ("Update shipment set delivered = 1, deliverdate = '$deliverdate' where shipmentid = $shipmentid") or die (mysql_error());
	
	}
}

//get shipmentstatuses
$statusq = mysql_query("SELECT statusdetails, statustime from shipmentstatus where shipmentstatus.shipmentid = $shipmentid order by statustime desc") or die (mysql_error());


?>

<html>
<head>
<title>Update Shipment</title>

<script language="JavaScript">

	function changewrite(flg) {
		if (flg == 1) {
			document.forms.updateform.deliverdate.disabled = true;
		}
		else {
			document.forms.updateform.deliverdate.disabled = false;
		}
	}

	function validate() {

		if (document.forms.updateform.details.value == "") {
			alert("Status Details cannot be blank");
			return false;
		}

		if (document.forms.updateform.delivered[0].checked == true && document.forms.updateform.deliverdate.value == "") {
			alert("If the shipment has been delivered, you must enter the deliverydate in YYYY-MM-DD format");
			return false;
		}
		return true;
	}

    // Function to pop up the cancel shipment window
    function cancelShipment(shipmentid) {
		url = "/internal/cancelshipment.php?bolnumber=" + shipmentid + "&reason=1";
        var cancelShipmentWindow =
        window.open(url,
                    "cancelShipmentWindow",
                    "resizable=no,height=200,width=325");
		return true;
    }

</script>
<head>
<body bgcolor=ffffff onLoad='changewrite(1);'>
<table width=650 border=1>
<tr><td colspan=6 align=center bgcolor=cacaca><font face="verdana" size=1><b>THIS BILL OF LADING MUST BE PRESENTED TO THE CARRIER AT TIME OF SHIPMENT</b></font></td></tr>
<tr><td align=center colspan=2 rowspan=5><form method=post action="billupdate.php"><input type=hidden name=shipmentid value=<?php echo $shipmentid; ?>><input type=submit value="CHANGE BILL"></form></td><td><font face="verdana" size=2><b>SHIPPER #</b></td><td><font face="verdana" size=2><?php echo $bolline[customerid] ?></td><td colspan=2 rowspan=5 valign=middle align=center><font face="verdana" size=2>(PLACE PRO LABEL HERE)</TD></TR>
<tr><td><font face="verdana" size=2><b>PO #</b></td><td><font face="verdana" size=2><?php echo $bolline[ponumber] ?></td></tr>
<tr><td><font face="verdana" size=2><b>BOL #</b></td><td><font face="verdana" size=2><?php echo $shipmentid ?></td></tr>
<tr valign=top><td><font face="verdana" size=2><b>CARRIER</b></td><td><font face="verdana" size=2><?php echo $bolline[name] ?></td></tr>
<tr valign=top><td><font face="verdana" size=2><b>CARRIER PRO</b></td><td><font face="verdana" size=2><?php echo $bolline[carrierpro] ?></td></tr>
<tr>
<td colspan=3 align=center width=300 BGCOLOR=cacaca><font face="verdana" size=2><b>SHIPMENT ORIGIN</TD>
<td colspan=3 align=center BGCOLOR=cacaca><font face="verdana" size=2><b>SHIPMENT DESTINATION</TD>
</tr>
<tr><td><font face="verdana" size=1><b>CONTACT:</td><td colspan=2><font face="verdana" size=1><?php echo $origincontact ?></td>
<td><font face="verdana" SIZE=1><b>CONTACT:</td><td colspan=2><font face="verdana" size=1><?php echo $destcontact ?></td></tr>
<tr><td><font face="verdana" size=1><b>COMPANY:</td><td colspan=2><font face="verdana" size=1><?php echo $origincompany ?></td>
<td><font face="verdana" SIZE=1><b>COMPANY:</td><td colspan=2><font face="verdana" size=1><?php echo $destcompany ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>ADDRESS 1:</td><td colspan=2><font face="verdana" size=1><?php echo $originaddress1 ?></td>
<td><font face="verdana" SIZE=1><b>ADDRESS 1:</td><td colspan=2><font face="verdana" size=1><?php echo $destaddress1 ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>ADDRESS 2:</td><td colspan=2><font face="verdana" size=1><?php echo $originaddress2 ?></td>
<td><font face="verdana" SIZE=1><b>ADDRESS 2:</td><td colspan=2><font face="verdana" size=1><?php echo $destaddress2 ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>CITY:</td><td colspan=2><font face="verdana" size=1><?php echo $origincity ?></td>
<td><font face="verdana" SIZE=1><b>CITY:</td><td colspan=2><font face="verdana" size=1><?php echo $destcity ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>STATE:</td><td colspan=2><font face="verdana" size=1><?php echo $originstate ?></td>
<td><font face="verdana" SIZE=1><b>STATE:</td><td colspan=2><font face="verdana" size=1><?php echo $deststate ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>ZIP:</td><td colspan=2><font face="verdana" size=1><?php echo $originzip ?></td>
<td><font face="verdana" SIZE=1><b>ZIP:</td><td colspan=2><font face="verdana" size=1><?php echo $destzip ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>PHONE:</td><td colspan=2><font face="verdana" size=1><?php echo $originphone ?></td>
<td><font face="verdana" SIZE=1><b>PHONE:</td><td colspan=2><font face="verdana" size=1><?php echo $destphone ?></td></tr>

<tr><td colspan=6 bgcolor=cacaca align=center><font face="Verdana" size=2><b>SHIPMENT DETAILS</b></font></td></tr>
<tr><td><font face=verdana size=1><b>BILL TO:</td><td colspan=5><font face="verdana" size=1><?php echo "$billingcontact - $billingcompany - $billingaddress1 $billingaddress2 $billingcity $billingstate $billingzip"; ?></font></td></tr>
<TR><TD  align=center><font face="verdana" size=1><b>UNITS</td>
<TD  align=center><font face="verdana" size=1><b>HM*</td>
<TD colspan=2 align=center><font face="verdana" size=1><b>DESCRIPTION</td>
<TD  align=center><font face="verdana" size=1><b>WEIGHT</td>
<TD  align=center><font face="verdana" size=1><b>CLASS</td></tr>

<TR><TD  align=center><font face="verdana" size=1><?php echo $bolline[units] ?></td>
<TD  align=center><font face="verdana" size=1><?php echo $bolline[hazmat] ?></td>
<TD colspan=2 align=center><font face="verdana" size=1><?php echo $bolline[productdescription] ?></td>
<TD  align=center><font face="verdana" size=1><?php echo $bolline[weight] ?></td>
<TD  align=center><font face="verdana" size=1><?php echo $bolline[classa] ?></td></tr>
<tr><td colspan=2 align=right><font face="verdana" size=1><b>HAZMAT EMERGENCY PHONE:</b></TD>
<td colspan=4><font face="verdana" size=1><?php echo $bolline[hazmatphone] ?>&nbsp;</td></tr>
<tr><td colspan=2 align=right><B><font face=verdana size=1>PICKUP DATE:</b></font></td>
<td colspan=4>
<?php
$thisdate = substr($bolline[pickupdate],5,5);
$thisyear = substr($bolline[pickupdate],0,4);
?>
<font face=verdana size=1><?php echo "$thisdate-$thisyear between $bolline[pickupafter] and $bolline[pickupbefore]"; 
?></td>
</tr>
<tr><td colspan=2 align=right><B><font face=verdana size=1>ADDITIONAL SERVICES:</b></font></td>
<td colspan=4><font face=verdana size=1>
<?php
while ($assl = mysql_fetch_array($acceq)) {
	echo "$assl[name] ";
}
?>&nbsp; </td>
</tr>
<tr><td colspan=2 align=right><b><font face=verdana size=1>SPECIAL INSTRUCTIONS:</b></font></td>
<td colspan=4><font face=verdana size=1>
<?php
	echo "$bolline[specialinstructions]";
?>
</td></tr>
</table>

<br>
<b><font size="+1" face=verdana><u>Order Control</u></font></b>
<br><br>
<?php
	print "<a href='' onClick='cancelShipment($shipmentid);'><font face=verdana size=2>Cancel It</font></a>";
?>
<br><br>


<br>
<b><font size="+1" face=verdana><u>Status Control</u></font></b><br><br>
<b><font face=verdana size=2>Current Statuses:</font></b><br><br>
<table>
<?php
while ($statline = mysql_fetch_array($statusq)) {
	echo "<tr><td><font face=verdana size=1>$statline[0]</font></td><td><font face=verdana size=1>$statline[1]</font></td></tr>";
}

?>  
</table>

<br>
<b>Update Status</b><br><br>
<form method=post action=update.php name=updateform onSubmit='return validate();'>
<input type=hidden name=action value=1>
<?php
echo "<input type=hidden name=shipmentid value=$shipmentid>";

?>
Status Details:	<input size=40 name=details><br>
Has The Shipment Been Delivered to Destination? <input type=radio name=delivered value=1 onClick='changewrite(0);'> Yes <input type=radio name=delivered value=0 checked onClick='changewrite(1);'> No<br>
Delivery Date (YYYY-MM-DD): <input size=20 name=deliverdate><br>
<input type=submit value=UPDATE!>
</form>

<a href="index.php">Back to shipment list</a>
</body></html>