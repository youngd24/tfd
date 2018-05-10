<?php
# =============================================================================
#
# bolfax.php
#
# Page to generate a fax cover sheet for a BOL
#
# $Id: bolfax.php,v 1.8 2002/10/29 20:44:42 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: bolfax.php,v $
# Revision 1.8  2002/10/29 20:44:42  youngd
#   * Adjusted breaks to leave 2 pages for faxing.
#
# Revision 1.7  2002/10/29 19:43:31  youngd
#   * Added include of the bol
#
# Revision 1.6  2002/10/29 19:08:48  youngd
#   * Changed title
#
# Revision 1.5  2002/10/28 22:51:52  youngd
#   * Working version of fax page
#
# Revision 1.4  2002/10/14 12:09:13  youngd
#   * Changed phone and fax numbers.
#
# Revision 1.3  2002/10/14 12:03:19  youngd
#  * Started adding dynamic elements.
#
# Revision 1.2  2002/10/09 23:06:10  youngd
#   * Basic template done.
#
# Revision 1.1  2002/10/09 21:34:30  youngd
#   * Moved to the internal folder.
#
# Revision 1.1  2002/10/09 21:31:43  youngd
#   * Initial version
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");


# Make sure we got a shipmentid as a param
if ( ! $shipmentid ) {
    
    logerr("SYNTAX", "bolfax.php: param shipmentid not passed to me");
    htmlerror("Param shipmentid not passed to me!");
    exit(0);

} else {

    # Get the carrierid for the requested shipment
    $carrierid = getShipmentCarrier($shipmentid);

    # Get the name of that carrier
    $carriername = getCarrierNameById($carrierid);

	# Get the origin addressid for the shipment
	$originzip = getShipmentOriginZip($shipmentid);

	# Get the fax number for the terminal
	$originfax = getTerminalFaxNumber($carrierid, $originzip);

	# Get the phone number for the terminal
	$originphone = getTerminalPhoneNumber($carrierid, $originzip);

	# Get the shipment pickup date
	$pickupdate = getShipmentPickupDate($shipmentid);

}



?>

<html>

	<head>
		<title>Freight Depot Bill Of Lading Fax Transmittal</title>
        <link rel="stylesheet" type="text/css" href="qa.css">
	</head>

	<body>

		<p align="center"><font face="Verdana" size="7">THE FREIGHT DEPOT</font>
        <img src="/images/xlogo-2.gif">
        </p>
		<hr color="#000000" width="90%">
		<p align="center"><b><font face="Verdana">FAX TRANSMITTAL<br>
		CONFIDENTIAL UNLESS SPECIFIED</font></b></p>
		<hr color="#000000" width="90%">
		<div align="center">
		  <center>
		  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="90%" id="AutoNumber1">
			<tr>
			  <td width="48%" colspan="4" height="40" style="border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">TO:&nbsp;&nbsp;</b>Dispatch</font></td>
			  <td width="52%" colspan="4" height="40" style="border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">FROM:&nbsp;&nbsp;</b>Customer Service</font></td>
			</tr>
			<tr>
			  <td width="48%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">COMPANY:&nbsp;&nbsp;</b><?php echo $carriername ?></font></td>
			  <td width="52%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">DATE:&nbsp;&nbsp;</b><?php echo date('m-d-Y H:i:s'); ?></font></td>
			</tr>
			<tr>
			  <td width="48%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">FAX NUMBER:&nbsp;&nbsp</b><?php echo $originfax ?></td>
			  <td width="52%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">TOTAL PAGES:&nbsp;&nbsp;</b>2</font></td>
			</tr>
			<tr>
			  <td width="48%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">PHONE NUMBER:&nbsp;&nbsp;</b><?php echo $originphone ?></font></td>
			  <td width="52%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">SENDER REFERENCE #:&nbsp;&nbsp;</b><?php echo $shipmentid ?></font></td>
			</tr>
			<tr>
			  <td width="48%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">RE:&nbsp;&nbsp;</b>BILL OF LADING <?php echo "$shipmentid"; ?></font></td>
			  <td width="52%" colspan="4" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">RECEIVER REFERENCE #:</font></b></td>
			</tr>
			<tr>
			  <td width="9%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">URGENT</font></b></td>
			  <td width="11%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <font face="Verdana" size="2">&nbsp;[&nbsp; ]</font></td>
			  <td width="14%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">FOR REVIEW</font></b></td>
			  <td width="14%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <font face="Verdana" size="2">&nbsp;[&nbsp; ]</font></td>
			  <td width="20%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">PLEASE COMMENT</font></b></td>
			  <td width="7%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <font face="Verdana" size="2">&nbsp;[&nbsp; ]</font></td>
			  <td width="16%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <b><font face="Verdana" size="2">PLEASE REPLY</font></b></td>
			  <td width="9%" height="40" style="border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">
			  <font face="Verdana">&nbsp;[&nbsp; ]</font></td>
			</tr>
			<tr>
			  <td width="100%" colspan="8" height="150" align="left" valign="top">
			  <b><font face="Verdana" size="2">COMMENTS:&nbsp;</font></b>
			  <br><br>
			  <font face="Verdana" size="1">
			  FOR PICKUP <?php echo "$pickupdate" ?>
			  <br>
			  <br>
			  PLEASE ADVISE PRO NUMBER
			  </font>
			  </td>
			</tr>
		  </table>
		  <p>&nbsp;</p>
		  <p>&nbsp;</p>
		  <p>&nbsp;</p>
		  <p>&nbsp;</p>
		  </center>
		</div>
		<hr color="#000000" width="90%">
		<p>&nbsp;</p>
		<div align="center">
		  <center>
		  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="75%" id="AutoNumber2">
			<tr>
			  <td width="100%" colspan="2" align="center">
			  <p align="center"><b><font face="Verdana" size="2">THE FREIGHT DEPOT 191 
			  E. DEERPATH SUITE 302<br>LAKE FOREST, IL 60045</font></b></td>
			</tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
			<tr>
			  <td align="center"><b><font face="Verdana" size="2">PHONE: 
			  847.283.0115</font></b></td>
			  <td align="center"><b><font face="Verdana" size="2">FAX 
			  847.283.0137</font></b></td>
			</tr>
		  </table>
		  </center>
		</div>
				
	</body>

	<br>

	<!-- Bring in the bill of lading -->
	<center>
		<?php include("csrbol.php"); ?>
	</center>

</html>
