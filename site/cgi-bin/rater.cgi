<?php
# =============================================================================
#
# rater.cgi
#
# CGI Rater
#
# $Id: rater.cgi,v 1.6 2003/02/10 20:35:02 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: rater.cgi,v $
# Revision 1.6  2003/02/10 20:35:02  youngd
#   * Added more debug statements.
#
# Revision 1.5  2003/02/06 21:05:23  youngd
#   * Added PI comment to tell the user about the quote being good for 30 days.
#
# Revision 1.4  2003/02/06 20:54:48  youngd
#   * Added services parsing and addition to XML output.
#
# Revision 1.3  2002/10/30 23:55:20  youngd
#   * Added FSC addition.
#
# Revision 1.2  2002/10/30 23:15:51  youngd
#   Test Version
#
# =============================================================================

# -----------------------------------------------------------------------------
#                             R E Q U I R E S 
# -----------------------------------------------------------------------------
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");

header("Content-Type: text/xml");

debug("rater.cgi: entering after initial requires");


# -----------------------------------------------------------------------------
#                           V A R I A B L E S
# -----------------------------------------------------------------------------
# Declare and set all global variables here
# -----------------------------------------------------------------------------
global		$errors;
global		$debug;

$errors		= 0;
$debug		= 0;


# -----------------------------------------------------------------------------
#                         P A R A M   C H E C K 
# -----------------------------------------------------------------------------
# Test to see if the required arguments were given to us. If it's not set
# send a HTTP 500 error page to the client.
# -----------------------------------------------------------------------------

// Create an array that holds all the valid script arguments
$params = array("username", "password", "srczip", "dstzip", "weight", "class");

// Iterate through each one and see if it's set
foreach ($params as $param) {

	debug("rater.cgi: testing $param");
	
	if ( $$param ) {
		debug("rater.cgi: $param is set");
	} else {
		debug("rater.cgi: $param is NOT set, telling the client to try again");

//		header("Content-Type: text/html");
//		header("Expires: now");
//		header("Status: 500 $$param required");
		
		print "<HTML>";
		print "<HEAD>";
		print "<TITLE>500 $param required</TITLE>";
		print "</HEAD>";
		print "<BODY>";
		print "<H2>ERROR: $param not supplied</H2>";
		print "</BODY>";
		print "</HTML>";

		$errors = 1;
		exit(1);

	}

}


# -----------------------------------------------------------------------------
#                     U S E R   V A L I D A T I O N
# -----------------------------------------------------------------------------

// Get the customer id, if we don't get one, puke.
debug("rater.cgi: getting the customer id for the email $username");
if ( $custid = getCustomerIdByEmail($username) ) {

	// Set this so zzgetprice.php will work
	$userarray[0] = $custid;
	$userarray[9] = getCustomerMargin($custid);

	// Now that we have an id, get the password associated with it
	if ( $realpass = getCustomerPassword($custid) ) {
	
		debug("rater.cgi: got password of $realpass");

		# Now make sure the passwords match
		if ( $password != $realpass ) {
			print "<HTML>";
			print "<HEAD>";
			print "<TITLE>500 Authentication Failure</TITLE>";
			print "</HEAD>";
			print "<BODY>";
			print "<H2>ERROR: Authentication Failure</H2>";
			print "</BODY>";
			print "</HTML>";

			debug("rater.cgi: bad password supplied");
			exit(1);
		}

	} else {
		print "<HTML>";
		print "<HEAD>";
		print "<TITLE>500 Authentication Failure</TITLE>";
		print "</HEAD>";
		print "<BODY>";
		print "<H2>ERROR: Authentication Failure</H2>";
		print "</BODY>";
		print "</HTML>";
	
		debug("rater.cgi: did NOT get password for custid $custid");
		exit(1);
	}

} else {
	print "<HTML>";
	print "<HEAD>";
	print "<TITLE>500 Authentication Failure</TITLE>";
	print "</HEAD>";
	print "<BODY>";
	print "<H2>ERROR: Authentication Failure</H2>";
	print "</BODY>";
	print "</HTML>";

	debug("rater.cgi: failed to get the customer id for the email $username");
	exit(1);
}


// Accessorial charges
$accessorials = array( "LFTORG" => 92.10,
                       "LFTDST" => 92.10,
					   "RSDPCK" => 62.00,
					   "RSDDEL" => 62.00,
					   "INSPCK" => 47.30,
					   "INSDEL" => 47.30,
					   "HAZMAT" => 22.80,
					   "CLLPCK" => 26.60,
					   "CLLDEL" => 26.60
					  );

// If we received additional services in the query, add them up
if ( $services ) {

	$gotservices = explode(",", $services);

	// Account for &services=	
	if ( count($gotservices) == 0 ) {
	
		$additionalcharges = sprintf("%01.2f", "0.00");	

	} else {

		for ( $i = 0; $i<count($gotservices); $i++ ) {
			$add = $add + $accessorials["$gotservices[$i]"];
		}

		$additionalcharges = sprintf("%01.2f", $add);
	
	}

} else {
	$additionalcharges = sprintf("%01.2f", "0.00");
}

// Map the inbound args to the getprice ones
$origin = $srczip;
$destination = $dstzip;
$shipclass = $class;

// Go get the price
require("zzgetprice.php");

// ardisplay comes from zzgetprice
$finalar = $ardisplay + $surcharge;
debug("rater.php: final ar before spewing out the XML is $finalar");

// Add the additional charges to the finalar to get the total price
$totalcharges = $finalar + $additionalcharges;
debug("rater.php: total charges before spewing out the XML is $totalcharges");

# Display the transit time
if ($transit < 1) {
	$transit = "CALL";
} elseif ($transit == 1) {
	$transit = "1 DAY";
} else {
	$transit = "$transit DAYS";
}


// Generate the final XML document and send it to the screen
print "<?xml version=\"1.0\"?>";
print '<!-- REVISION: $Revision: 1.6 $ -->';
print '<!-- ALL FREIGHT DEPOT QUOTES ARE VALID FOR 30 DAYS FROM THE DATE AND TIME OF ISSUE -->';
print "<quote customerid=\"$custid\" quoteid=\"$quoteid\">";
print "<timestamp>$thetime</timestamp>";
print "<srczip>$srczip</srczip>";
print "<dstzip>$dstzip</dstzip>";
print "<weight>$weight</weight>";
print "<class>$class</class>";
print "<transitdays>$transit</transitdays>";
print "<price>$finalar</price>";
print "<servicecharges>$additionalcharges</servicecharges>";
print "<totalcharges>$totalcharges</totalcharges>";
if ( $price == "-1.00" ) {
	print "<status>ERROR</status>";
} else {
	print "<status>OK</status>";
}
print "</quote>";



?>
