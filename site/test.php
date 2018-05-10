<!--
==============================================================================

test.php

Page to test various parts of the site

$Id: test.php,v 1.7 2003/02/14 18:35:01 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren@younghome.com]

==============================================================================

ChangeLog:

$Log: test.php,v $
Revision 1.7  2003/02/14 18:35:01  youngd
  * Added accessorial testing code.

Revision 1.6  2002/10/15 20:31:15  youngd
  * Modified the print bol and print invoice quick links to open the associated
    document in a new window. Added JavaScript functions ot handle this.

Revision 1.5  2002/10/10 22:44:00  youngd
  * Added code to test getCustomerMarginForCarrier();

Revision 1.4  2002/09/16 08:28:08  webdev
  * Added testing of objects

Revision 1.3  2002/09/16 07:58:39  webdev
  * Many updates

Revision 1.2  2002/09/08 08:33:01  webdev
* Still working on error logging

Revision 1.1  2002/09/08 04:38:50  webdev
  * Initial version

==============================================================================
-->

<html>
	<head>
		<title>Test Page</title>
		<script language="JavaScript" src="/internal/common.js">
		</script>
	</head>

	<body>

<?php

	// Bring in our standard includes
	require_once("config.php");
	require_once("debug.php");
	require_once("error.php");
	require_once("event.php");
	require_once("functions.php");
	require_once("logging.php");
	require_once("zzmysql.php");

    $custid = getCustomerIdByName("Darren Young");
    $password = getCustomerPassword($custid);

    $carrierid="10003";
    $margin = getCustomerMargin($custid);
    $discount = getCarrierDiscount($carrierid);
    $minimum = getCarrierMinimum($carrierid);

	$cust_carrier_margin = getCustomerMarginForCarrier($custid, $carrierid);


    echo "CUSTOMER ID: $custid";
    echo "<br>";
    echo "MARGIN: $margin";
    echo "<br>";
    echo "CARRIER DISCOUNT: $discount";
    echo "<br>";
    echo "CARRIER MINIMUM: $minimum";
    echo "<br>";
    echo "PASSWORD: $password";
    echo "<br>";
	echo "CUSTOMER CARRIER MARGIN: $cust_carrier_margin";

	echo "<br><br>";
	echo "ACCESSORIAL TESTS:";
	echo "<br>";

	$refs = getAccessorialListing();

	$refcount = count($refs);
	echo "TOTAL ACCESSORIALS ON FILE: $refcount<br>";

	for ($i = 0; $i<$refcount; $i++) {
		print "$refs[$i]&nbsp;";
	}


?>

<br><br>
<input type="text" size="10" name="shipmentid" style="font-family: verdana; font-size:11px">
<font face=verdana size=2>[ <a href="<?php echo $PHP_SELF; ?>" onClick="JavaScript:displayBillOfLading(shipmentid)">print</a> ] </font>


</body>

</html>