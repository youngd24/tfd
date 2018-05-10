<?php
# =============================================================================
#
# nobill.php
#
# Page / form to tell / gather customer billing information
#
# $Id: nobill.php,v 1.2 2002/11/14 21:32:26 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: nobill.php,v $
# Revision 1.2  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.1.2.2  2002/11/08 22:02:29  webdev
#   * Added data gathering fields.
#
# Revision 1.1.2.1  2002/11/08 20:14:24  webdev
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

?>

<html>
<head>
	<title>The Freight Depot > Billing Information</title>
	
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script language="JavaScript" src="js/main.js"></script>

	<script language="JavaScript">
		function validate(frm) {
			var errors = "";
		
			if (frm.contact.value == "") {
				errors += "Please enter the contact name.\n"
			}
			if (frm.address1.value == "") {
				errors += "Please enter the address.\n"
			}
			if (frm.city.value == "") {
				errors += "Please enter the city.\n"
			}
			if (frm.state.value == "") {
				errors += "Please enter the state.\n"
			}
			if (frm.zip.value == "") {
				errors += "Please enter the zip.\n"
			}
			if (frm.email.value == "") {
				errors += "Please enter the email.\n"
			}
			if (frm.phone.value == "") {
				errors += "Please enter the phone.\n"
			}

			if (errors != "") {
				alert(errors);
				return false;
			} else {
				return true;
			}
		}

	</script>

</head>

<body>

<?php


if ( $submit ) {


} else {

	print "<font face=verdana size=2><div align=center>A new feature at The Freight Depot is the ability to have all your shipment bill to a single location. To take advantage of this, please fill in the following information:</font></div>";

	print "<br>";

	print "<form name=\"frmBilling\" method=\"POST\" action=\"nobill.php\" onSubmit=\"return validate(this);\">";

	print "  <table cellpadding=0 cellspacing=0 border=0>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>ADDRESS 1:&nbsp;</b></td>";
	print "      <td><input type=text size=20 name=address1 style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>ADDRESS 2:&nbsp;</b></td>";
	print "      <td><input type=text size=20 name=address2 style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>CITY:&nbsp;</b></td>";
	print "      <td><input type=text size=20 name=city style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>STATE/PROVINCE:&nbsp;</b></td>";
	print "      <td><input type=text size=5 name=state style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>POSTAL CODE:&nbsp;</b></td>";
	print "      <td><input type=text size=10 name=zip style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>COUNTRY:&nbsp;</b></td>";
	print "      <td>";
	print "        <select name='country' style='font-family:verdana; font-size:10pt'>";
                     $codequery = mysql_query("SELECT * FROM country_codes WHERE active=1 ORDER BY country");
                     while($codelist = mysql_fetch_row($codequery) ) {
                       print "<option value=$codelist[2]>$codelist[0]";
					 }
	print "        </select>";
	print "      </td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>CONTACT:&nbsp;</b></td>";
	print "      <td><input type=text size=10 name=contact style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right><font face=verdana size=2><b>PHONE:&nbsp;</b></td>";
	print "      <td><input type=text size=10 name=phone style='font-family:verdana; font-size:10pt'></td>";
	print "    </tr>";

	print "    <tr>";
	print "      <td align=right>&nbsp;</td>";
	print "      <td align=left>";
	print "        <input type=submit name=submit value=Submit style='font-family:verdana; font-size:9pt'>";
	print "        <input type=reset name=reset value=Reset style='font-family:verdana; font-size:9pt'>";
	print "      </td>";
	print "    </tr>";

	print "  </table>";

	print "</form>";

}


?>

</body>

</html>