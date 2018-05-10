<?php
# =============================================================================
#
# profile.php
#
# Customer Profile Page
#
# $Id: profile.php,v 1.2 2002/11/14 21:32:26 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: profile.php,v $
# Revision 1.2  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.1.2.2  2002/11/07 23:51:49  webdev
# *** empty log message ***
#
# Revision 1.1.2.1  2002/11/07 23:05:16  webdev
#   * Initial version.
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

require("zzgrabcookie.php");

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<meta name="Revision" content="$Revision: 1.2 $">
	<meta name="Author" content="$Author: youngd $">
    <meta name="Tag" content="$Tag$">
	<title>Customer Password Change</title>
	<script language="JavaScript" src="js/main.js"></script>
</head>

<body>

<?php

require("zzheader.php");


if ( $mode == "view" ) {

	if ( ! $cusqry = mysql_query("SELECT * FROM customers WHERE custid=$userarray[0]") ) {
		htmlerror("Error retrieving customer information, please call customer service");
		die();
	}

	$cusinfo = mysql_fetch_row($cusqry);
	$default_billing = $cusinfo[15];


	if ( ! $addrqry = mysql_query("SELECT * FROM address WHERE addressid=$default_billing") ) {
		htmlerror("Error retrieving default billing information, please call customer service");
		die();
	}

	$defbillinginfo = mysql_fetch_row($addrqry);



	print "<br>";
	print "<blockquote>";

	print "   <table cellpadding=0 cellspacing=0 border=0>";
	
	print "      <form method=POST action=$PHP_SELF?mode=editAccountInfo>";
	print "      <tr align=center>";
	print "         <td colspan=2 bgcolor=7390C0><font face=verdana size=2><b>ACCOUNT INFORMATION</b></font></td>";
	print "      </tr>";

	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>NAME:&nbsp;</b></font></td>";
	print "         <td align=left><font face=verdana size=2>$cusinfo[1]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>COMPANY:&nbsp;</b></font></td>";
	print "         <td align=left><font face=verdana size=2>$cusinfo[3]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>EMAIL:&nbsp;</b></font></td>";
	print "         <td align=left><font face=verdana size=2>$cusinfo[2]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>PHONE:&nbsp;</b></font></td>";
	print "         <td align=left><font face=verdana size=2>$cusinfo[10]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=center colspan=2><input type=submit name='editAccountInfo' value='EDIT ACCOUNT INFO' style='font-face:verdana; font-size: 9pt'></td>";
	print "      </tr>";
	print "      </form>";


	print "      <tr>";
	print "         <td align=right colspan=2><font face=verdana size=2>&nbsp;</font></td>";
	print "      </tr>";


	print "      <form method=POST action=$PHP_SELF?mode=editBillingInfo>";
	print "      <tr align=center>";
	print "         <td colspan=2 bgcolor=7390C0><font face=verdana size=2><b>BILLING INFORMATION</b></font></td>";
	print "      </tr>";

	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>CONTACT:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[8]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>COMPANY:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[1]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>ADDRESS 1:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[2]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>ADDRESS 2:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[3]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>CITY:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[4]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>STATE:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[5]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>POSTAL CODE:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[6]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>COUNTRY:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[11]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>PHONE:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[9]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=right><font face=verdana size=2><b>EMAIL:&nbsp;</b></font></td>";
	print "         <td><font face=verdana size=2>$defbillinginfo[10]</font></td>";
	print "      </tr>";
	print "      <tr>";
	print "         <td align=center colspan=2><input type=submit name='editBillingInfo' value='EDIT BILLING INFO' style='font-face:verdana; font-size: 9pt'></td>";
	print "      </tr>";
	print "      </form>";

	print "   </table>";

	print "</blockquote>";

} elseif ( $mode == "editAccountInfo" ) {
	print "<br>";


} elseif ( $mode == "editBillingInfo") {

}



require("zzfooter.php");

?>

</body>

</html>
