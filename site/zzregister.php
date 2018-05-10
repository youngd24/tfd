<?php
# =============================================================================
#
# zzregister.php
#
# Registration
#
# $Id: zzregister.php,v 1.13 2002/12/10 19:51:44 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzregister.php,v $
# Revision 1.13  2002/12/10 19:51:44  webdev
#   * Changed emails to be at the freight depot
#
# Revision 1.12  2002/11/15 19:23:48  webdev
#   * Added extended registration forms
#
# Revision 1.11  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.10.2.3  2002/11/08 23:15:42  webdev
#   * Added extended registration check.
#
# Revision 1.10.2.2  2002/11/07 22:22:20  webdev
#   * Now adds default customer billing information
#   * Still working on new registration.
#
# Revision 1.10.2.1  2002/11/05 15:15:12  webdev
#   * New registration in progress.
#
# Revision 1.10  2002/11/04 19:40:49  youngd
#   * Changed to real (CSR) email for sending.
#
# Revision 1.9  2002/11/03 10:07:15  youngd
#   * Added csr email on new registration.
#
# Revision 1.8  2002/11/03 09:53:35  youngd
#   * Added use of getDefaultCustomerMargin instead of the sql.
#   * Changed the new user registration email. Removed Mike Smith's email and changed it to csr.
#
# Revision 1.7  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.4  2002/09/08 19:36:08  webdev
#   * Changed source header to be in php
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// check to see if user is already registered
	$selectcheckquery = mysql_query("select * from customers where email = '$email'");

	// if email address already found, crap out, else begin insert
	if (mysql_fetch_row($selectcheckquery)) {
		debug("zzregister.php: email $email was already registered");
		$alreadyregistered = 1;
	}

    /* 
      Need to add some additional checks here for duplicate customers
    */
	if ( getConfigValue("extendedRegistrationChecks") == 1 ) {
	}

	// All the previous registration tests have passsed, add them to the system
	if ($alreadyregistered != 1) {

		// retrieve current time and assign to $timenow
		$timenow = getdate();
		$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];

		// seed random number generator with microseconds since last "whole" second
		srand ((double) microtime() * 1000000);

		// grab a random number
		$randval = rand(1000000000,2000000000);

		// create an array of all letter
		$letterarray = Array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);

		// loop 10 times, select random number, select letter from array
		$letterstring = "";
		for ($i = 0; $i < 10; $i++) {
			$pntr = rand(0,25);
			$letterstring = $letterstring . $letterarray[$pntr];
		}

		// combine letter string and random number
		$loginstring = $randval . $letterstring;

		// grab the defaul customer margin from the digiship table
		$defaultmargin = getDefaultCustomerMargin();
		
		// build sql string
		$sqlstr = "INSERT into customers (name, company, email, password, phone, regdate, lastloginip, lastlogindate, loginstring, margin) VALUES ('$name', '$company', '$email', '$pass1', '$phone', '$thetime', '$REMOTE_ADDR', '$thetime', '$loginstring', $defaultmargin)";

		// insert into db
		if ( ! $insertquery = mysql_query($sqlstr) ) {
			debug("zzregister.php: unable to add new customer for $sqlstr");
			htmlerror("Unable to add new customer record, contact customer service");
			die();
		}

		// Get the customer id from that last insert
		$newcustid = mysql_insert_id();
		debug("zzregister.php: added new customer $newcustid");


        // Add their default address to the address table. This becomes their default billing address.
		$defaultaddrqry = mysql_query("INSERT INTO address (company, address1, address2, city, state, zip, country, custid, contact, phone, email) VALUES ('$company', '$address1', '$address2', '$city', '$state', '$zip', '$country', '$newcustid', '$name', '$phone', '$email')") or die (mysql_error());

        // Get the addressid of that last insert and store it away
		$defaultbillingid = mysql_insert_id();

		// Update the customer table with the default billing id
		if ( ! $qry = mysql_query("UPDATE customers SET default_billing=$defaultbillingid WHERE custid=$newcustid") ) {
			debug("zzregister.php: unable to set default billing for $newcustid");
			htmlerror("Unable to set default billing address, contact customer service");
			die();
		} else {
			debug("zzregister.php: added default billing of $defaultbillingid for custid $newcustid");
		}


		
		// send a hello email
		$header = "From: csr@thefreightdepot.com\nContent-Type: text/html; charset=us-ascii";
		$allmessage = "<html><head></head><body bgcolor=ffffff><Font face=arial size=2>Your account with <b>The Freight Depot</b> is now active! You can rate and book shipments with us on-line. <br><br>At <a href='http://www.thefreightdepot.com'>www.thefreightdepot.com</a> all you need to provide us with is your shipment's origin zip-code, destination zip-code, weight and freight class <b>and instantly your shipment is rated and the days in transit are applied.</b> <br><br>After you book your shipment with <b>The Freight Depot</b> we will dispatch one of our <b>Quality Carrier Partners</b> to <u>pick-up your order at the time you request</u> and <u>deliver your shipment on-time and damage free</u>, just like 98.7% of all the shipments we handle.<br><br> After your shipment is picked-up you and your customer can trace 24 hours a day at <a href='http://www.thefreightdepot.com'>www.thefreightdepot.com</a> using <b>Your Purchase Order Number</b> or your <b>Bill of Lading Number</b>.<br><Br>If you have any questions about shipping with <b>The Freight Depot</b> feel free to send us an email @ <a href='mailto:csr@thefreightdepot.com'>csr@thefreightdepot.com</a> or give us a call <b>toll-free</b> <u>1-866-445-1212</u>. <br><br>Try The Freight Depot, <b><i>we make shipping simple...</b></i><br><br>FD Customer Service<br><br>The Freight Depot </body></html>";
		$dothemail = mail($email, "Your account with The Freight Depot is now active.", $allmessage, $header);
		$password = $pass1;

        // Send an email to customer service with the new registration email
        $csrheader = "From: sysadmin@thefreightdepot.com\nContent-Type: text/html; charset=us-ascii";
        $csrsubject = "New User Registration";
        $csremail = "hpavlos@thefreightdepot.com";
        $csrmessage = "<html>
                       <head>
                            <title>New Registration</title>
                       </head>
                       <body>
                       <h2>Registration Information</h2>
                       <font fac=Verdana size=2>
                       <b>TIME:</b>$thetime<br>
                       <b>MARGIN:</b>$defaultmargin<br>
                       <b>NAME:</b>$name<br>
                       <b>COMPANY:</b>$company<br>
                       <b>EMAIL:</b>$email<br>
                       <b>PHONE:</b>$phone<br>
                       </font>
                       </body>
                       </html>";

        $csrmail = mail($csremail, $csrsubject, $csrmessage, $csrheader);
	}
	else {
		require("zznewlogin.php");
		$password = $pass1;
	}
?>
