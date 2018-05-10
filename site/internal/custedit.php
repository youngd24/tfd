<?php
# ==============================================================================
#
# custedit.php
#
# Customer record edit page
#
# $Id: custedit.php,v 1.3 2002/11/21 23:34:29 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
#
# $Log: custedit.php,v $
# Revision 1.3  2002/11/21 23:34:29  webdev
#   * Under development still.
#
# Revision 1.2  2002/11/21 14:36:16  webdev
#   * Still under development.
#
# Revision 1.1  2002/11/21 00:35:39  webdev
#   * Initial version.
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

?>

<html>
<head>

    <!-- Pull in the internal stylesheet -->
    <link rel="stylesheet" type="text/css" href="/internal/qa.css">

    <!-- Set standard meta tags -->
    <meta name="Author" content="$Author: webdev $">
    <meta name="Revision" content="$Revision: 1.3 $">
    
    <!-- Set the page title (include the page revision) -->
    <title>Customer Editor ($Revision: 1.3 $)</title>
    
    <!-- Pull in standard site and internal JavaScript functions -->
    <script language="JavaScript" src="/internal/common.js"></script>
    <script language="JavaScript" src="/js/main/js"></script>

</head>

<body>


<?php

	// If we didn't get a customer id as a param, die off.
	if ( ! $custid ) {

		htmlerror("Customer ID not supplied");
		die();

	// Got a custid, move on
	} else {

		// See how many there are, has to be exactly 1.
		$isthereqry = mysql_query("SELECT COUNT(custid) FROM customers WHERE custid=$custid");
		$numrows = mysql_fetch_row($isthereqry);

		// No record found, die off.
		if ( $numrows[0] == 0 ) {

			echo "NO RECORD FOUND FOR CUSTOMER $custid";
			exit;

		// Got 1 record, we're good to go...
		} elseif ( $numrows[0] == 1 ) {

			/* VIEW MODE */
			if ( $mode == "view" ) {

				$custqry = mysql_query("SELECT * FROM customers WHERE custid=$custid");
				$custrecord = mysql_fetch_array($custqry);

				$defaddrqry = mysql_query("SELECT * FROM address WHERE addressid=$custrecord[default_billing]");
				$defaddress = mysql_fetch_array($defaddrqry);

				$form = <<< END_OF_FORM

				<center>
					<font face="verdana" size="4"><b><u>MAINTAIN CUSTOMER INFORMATION</u></b></font>
				</center>
				<br>

				<table cellpadding="0" cellspacing="0" border="1" align="center">
					<tr>
						<td colspan=2 bgcolor="7390C0" align="center" width="500">
							<font face="verdana" size="3"><b>:-) --> GENERAL INFORMATION <-- (-:</b></font>
						</td>
					</tr>
					<tr>
						<td width="100" align="right"><font face="verdana" size="2"><b>ID:&nbsp;</b></font></td>
						<td width="400" align="left"><font face="verdana" size="2">$custrecord[custid]</font></td>
					</tr>

					<tr>
						<td width="100" align="right"><font face="verdana" size="2"><b>NAME:&nbsp;</b></font></td>
						<td width="400" align="left"><font face="verdana" size="2">$custrecord[name]</font></td>
					</tr>
					<tr>
						<td width="100" align="right"><font face="verdana" size="2"><b>EMAIL:&nbsp;</b></font></td>
						<td width="400" align="left"><font face="verdana" size="2">$custrecord[email]</font></td>
					</tr>
					<tr>
						<td width="100" align="right"><font face="verdana" size="2"><b>COMPANY:&nbsp;</b></font></td>
						<td width="400" align="left"><font face="verdana" size="2">$custrecord[company]</font></td>
					</tr>


				</table>

END_OF_FORM;

				echo $form;
			
			} 
			
			/* EDIT MODE */
			elseif ( $mode == "edit" ) {

				echo "IN EDIT MODE";
			
			} 
			
			/* COMMIT MODE */
			elseif ( $mode == "commit" ) {

				echo "IN COMMIT MODE";

			}
		
		// More than 1 or less than 0, something bad is going on with the database
		} else {

			echo "Found more than 1 record for customer id $custid, really bad error..";
			die();
		
		}
	
	}

?>

</body>
</html>