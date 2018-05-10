<?php
# ==============================================================================
#
# csrbol.php
#
# Customer Service Bill Of Lading
#
# $Id: csrbol.php,v 1.5 2002/12/06 23:03:23 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: csrbol.php,v $
# Revision 1.5  2002/12/06 23:03:23  webdev
#   * Changed the legal fonts to be 6 point.
#   * Changed the width of the table from 650 to 700.
#   * Started cleaning up the table, Applying tab stops and such.
#
# Revision 1.4  2002/10/15 20:31:15  youngd
#   * Modified the print bol and print invoice quick links to open the associated
#     document in a new window. Added JavaScript functions ot handle this.
#
# Revision 1.3  2002/10/15 17:08:34  youngd
#   * Added scrollbars to the bol.
#
# Revision 1.2  2002/10/14 19:08:42  youngd
#   * Company name prints correctly on the internal BOL.
#
# Revision 1.1  2002/10/14 14:42:58  youngd
#   * Initial copy from the customer BOL.
#
# ==============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// get cookie and user - Don't do this for internal people
//require('zzgrabcookie.php');

debug("bol.php: entering after initial requires");

if ( ! $shipmentid ) {
	debug("csrbol.php: param shipmentid not passed to me");
	htmlerror("param shipmentid not passed to me");
	exit(0);
}

// select bol information
$bolsql = "select carriers.name, shipment.customerid, shipment.pickupdate, shipment.pickupbefore, shipment.pickupafter, shipment.ponumber, shipment.productdescription, shipment.hazmat, shipment.hazmatphone, shipment.units, quotes.weight, quotes.class as classa, shipment.origin, shipment.destination, shipment.billing, shipment.specialinstructions from carriers, shipment, quotes where shipment.carrierid = carriers.carrierid and shipment.quoteid = quotes.quoteid and shipment.shipmentid = $shipmentid";
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

			// Customer information
			$custidquery = mysql_query("select customerid from shipment where shipmentid=$shipmentid");
			$custidline = mysql_fetch_row($custidquery);
			$customerid = $custidline[0];
			debug("csrbol.php: customerid is $customerid");

			$custnamequery = mysql_query("select company from customers where custid=$customerid");
			$custnameline = mysql_fetch_row($custnamequery);
			$custcompanyname = $custnameline[0];
			debug("csrbol.php: customer company name is $custcompanyname");

// get accessorials
$acceq = mysql_query("SELECT accessorials.name from accessorials, shipmentaccessorials where shipmentid = $shipmentid and shipmentaccessorials.assid = accessorials.assid");
			

// get digiship info
$digiselect = mysql_query("SELECT companyname, address1, address2, city, state, zip from digiship");
$digirow = mysql_fetch_row($digiselect);9/19/2002

?>

<html>
<head>
	<title>BILL OF LADING FOR SHIPMENT #<?php echo $shipmentid; ?></title>
</head>

<body bgcolor=ffffff topmargin=2>
	<table width=700 border=1 align=center>

		<tr>
			<td colspan=6 align=center bgcolor=cacaca>
				<font face="verdana" size=2><b>THIS BILL OF LADING MUST BE PRESENTED TO THE CARRIER AT TIME OF SHIPMENT</b></font>
			</td>
		</tr>
		
		<tr>
			<td align=center colspan=2 rowspan=3>
				<img src="/images/logos/mainfd-3.gif" height=50 width=195>
			</td>

			<td>
				<font face="verdana" size=2><b>BOL #</b>
			</td>
			<td>
				<font face="verdana" size=2><?php echo $shipmentid ?>
			</td>
			<td colspan=2 rowspan=3 valign=middle align=center>
				<font face="verdana" size=2>(PLACE PRO LABEL HERE)
			</td>
		</tr>

		<tr>
			<td>
				<font face="verdana" size=2><b>PO #</b>
			</td>
			<td>
				<font face="verdana" size=2><?php echo $bolline[ponumber] ?>
			</td>
		</tr>

		<tr valign=top>
			<td>
				<font face="verdana" size=2><b>CARRIER</b>
			</td>
			<td>
				<font face="verdana" size=2><?php echo $bolline[name] ?>
			</td>
		</tr>
		
		<tr>
			<td colspan=3 align=center width=300 BGCOLOR=cacaca>
				<font face="verdana" size=2><b>SHIPMENT ORIGIN</b>
			</td>

			<td colspan=3 align=center BGCOLOR=cacaca>
				<font face="verdana" size=2><b>SHIPMENT DESTINATION</b>
			</td>
		</tr>

		<tr>
			<td><font face="verdana" size=1><b>CONTACT:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $origincontact ?></td>
			<td><font face="verdana" SIZE=1><b>CONTACT:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $destcontact ?></td>
		</tr>
		<tr>
			<td><font face="verdana" size=1><b>COMPANY:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $origincompany ?></td>
			<td><font face="verdana" SIZE=1><b>COMPANY:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $destcompany ?></td>
		</tr>
		<tr>
			<td><font face="verdana" SIZE=1><b>ADDRESS 1:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $originaddress1 ?></td>
			<td><font face="verdana" SIZE=1><b>ADDRESS 1:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $destaddress1 ?></td>
		</tr>
		<tr>
			<td><font face="verdana" SIZE=1><b>ADDRESS 2:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $originaddress2 ?></td>
			<td><font face="verdana" SIZE=1><b>ADDRESS 2:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $destaddress2 ?></td>
		</tr>
		<tr>
			<td><font face="verdana" SIZE=1><b>CITY:</td>
			<td colspan=2><font face="verdana" size=1><?php echo $origincity ?></td>
<td><font face="verdana" SIZE=1><b>CITY:</td><td colspan=2><font face="verdana" size=1><?php echo $destcity ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>STATE:</td><td colspan=2><font face="verdana" size=1><?php echo $originstate ?></td>
<td><font face="verdana" SIZE=1><b>STATE:</td><td colspan=2><font face="verdana" size=1><?php echo $deststate ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>ZIP:</td><td colspan=2><font face="verdana" size=1><?php echo $originzip ?></td>
<td><font face="verdana" SIZE=1><b>ZIP:</td><td colspan=2><font face="verdana" size=1><?php echo $destzip ?></td></tr>
<tr><td><font face="verdana" SIZE=1><b>PHONE:</td><td colspan=2><font face="verdana" size=1><?php echo $originphone ?></td>
<td><font face="verdana" SIZE=1><b>PHONE:</td><td colspan=2><font face="verdana" size=1><?php echo $destphone ?></td></tr>

<tr><td colspan=6 bgcolor=cacaca align=center><font face="Verdana" size=2><b>SHIPMENT DETAILS</b></font></td></tr>

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
	echo "$assl[name] <br>";
}
?>&nbsp; </td>
</tr>
	<tr>
		<td colspan=2 align=right>
			<b><font face=verdana size=1>SPECIAL INSTRUCTIONS:</b></font>
		</td>
		<td colspan=4>
			<font face=verdana size=1>
			<?php
				echo "$bolline[specialinstructions]";
			?>
		</td>
	</tr>

	<tr>
		<td colspan=6 align=center>
			<div style="font-family:verdana;font-size:6pt;">
			<i>When transporting hazardous materials include the technical or chemical name for n.o.s. (not otherwise specified) or generic description of material with appropriate UN or NA number as defined in US DOT Emergency Communication Standard (HM-126C). Provide emergency response phone number in case of incident or accident in box above.
			</i>
			</div>
		</td>
	</tr>

<tr valign=top><td colspan=2 bgcolor=cacaca width=200 align=center><font face="verdana" size=2><b>THIRD PARTY BILLING</TD>
<td rowspan=7 align=center colspan=2 width=200><div style="font-family:verdana;font-size:6pt;">Subject to Section 7 of the conditions, if this shipment is to be delivered to the consignee without recourse on the consignor, the consignor shall sign the following statement: The carrier shall not make delivery of this shipment without payment of freight and all other lawful charges.<br><br>
_____________________<br>
Signature of Consignor</td>
<td rowspan=7 align=center width=200 colspan=2><div style="font-family:verdana;font-size:6pt;">
This is to certify that the above named materials are properly classified, described, packaged, marked, and labeled, and are in proper condition for transportation according to the applicable regulation of the Department of Transportation<br><br><br><br>
_____________________<br>
Signature
</td></tr>
<tr><td width=100><font face="verdana" size=1><b>COMPANY:</b></td><td width=100><font face="verdana" size=1><?php echo $digirow[0]; ?></td></tr>
<tr><td><font face="verdana" size=1><b>ADDRESS 1:</b></td><td><font face="verdana" size=1><?php echo $digirow[1]; ?></td></tr>
<tr><td><font face="verdana" size=1><b>ADDRESS 2:</b></td><td><font face="verdana" size=1><?php echo $digirow[2]; ?></td></tr>
<tr><td><font face="verdana" size=1><b>CITY:</b></td><td><font face="verdana" size=1><?php echo $digirow[3]; ?></td></tr>
<tr><td><font face="verdana" size=1><b>STATE:</b></td><td><font face="verdana" size=1><?php echo $digirow[4]; ?></td></tr>
<tr><td><font face="verdana" size=1><b>ZIP:</b></td><td><font face="verdana" size=1><?php echo $digirow[5]; ?></td></tr>

<tr><td colspan=6><div style="font-family:verdana;font-size:6pt;">Received at the point of origin on the date specified, from the consignor mentioned herein, the property herein described, in apparent good order, except as noted (contents and condition of contents of packages unknown), marked, consigned, and destined, as indicated above, which the carrier agrees to carry and to deliver to the consignee at the said destination, if on its route or otherwise to deliver to another carrier on the route to said destination. It is mutually agreed as to each carrier of all or any of the goods over all or any portion of the route to destination, and as to each party of any time interested in all or any of the goods, that every service to be performed here under shall be subject to all the conditions not prohibited by law, whether printed or written, are hereby agreed by the consignor and accepted for himself and his assigns.</div></td></tr>
<tr><td colspan=6><div style="font-family:verdana;font-size:6pt;"><b>NOTICE:</b> Freight moving under this Bill of Lading is subject to tariffs on file with the Interstate Commerce Commission. This notice supersedes and negates any claimed oral or written contract, promise, representation, or understanding between parties, except to the extent of any written contract signed by both parties to the contract.</div></td></tr>
<tr><td colspan=6><div style="font-family:verdana;font-size:6pt;">I hereby declare that the contents of this consignment are fully accurately described above by proper shipping name and are classified, packed, marked and labeled, and are in all respects in proper condition for transport by rail, water according to applicable international and national government regulations.</td></tr>
<tr><td colspan=6><font face="verdana" size=1><b>*Haz Mat - Mark with an X to designate hazardous materials as referenced in 49CFR S 172.202</b></div></td></tr>
<tr><td align=center><font face=verdana size=1><b>SHIPPER</b></td><td colspan=2><font face=verdana size=1><?php echo $custcompanyname; ?></td><td align=center><font face=verdana size=1><b>CARRIER:</b></td><td colspan=2><font face="verdana" size=1>The Freight Depot</font></td></tr>
<tr><td align=center><font face=verdana size=1><b>PER</b></td><td colspan=2></td><td align=center><font face=verdana size=1><b>PER:</b></td><td colspan=2><font face="verdana" size=1></font></td></tr>
<tr><td colspan=6><div style="font-family:verdana;font-size:6pt;"><b>NOTE</b> - Where the rate is dependent on value, shippers are required to state specifically in writing the agreed or declared value of the property. The agreed or declared value of the property is hereby specifically stated by the shipper to be not exceeding $0.25 per pound.</div></td></tr>
</table>

</body>

</html>

<?php
    debug("bol.php: leaving");
?>