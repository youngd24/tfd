<?php
# =============================================================================
#
# lostpass.php
#
# Page that gathers the user email and sends their password
#
# $Id: lostpass.php,v 1.6 2002/10/30 18:18:04 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: lostpass.php,v $
# Revision 1.6  2002/10/30 18:18:04  youngd
#   * Changed text of mail message.
#
# Revision 1.5  2002/10/30 17:08:10  youngd
#   * Changed email to custemail so the send works.
#
# Revision 1.4  2002/10/30 17:01:37  youngd
#   * First test version.
#
# Revision 1.3  2002/10/30 12:33:05  youngd
#   * Added php code to form printing
#   * Still under development
#
# Revision 1.2  2002/10/30 12:14:16  youngd
#   * Converted to unix
#
# Revision 1.1  2002/10/29 19:08:59  youngd
#   * New file, initial version under development.
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

debug("lostpass.php: entering after initial requires");

?>


<link rel="stylesheet" type="text/css" href="css/main.css">
<meta name="Revision" content="$Revision: 1.6 $">
 
<script language="JavaScript" src="js/main.js"></script>

<!-- Function that checks if they plugged in their email address -->
<script language="JavaScript">

    function validate(frm) {
        
        var errors="";

        if (frm.custemail.value == "") {
            errors += "Please enter your email address...";
        }

        if (errors != "") {
            alert(errors);
            return(false);
        } else {
            return(true);
        }

    }

</script>

<?php

require("zzheader.php");


# If we didn't get an email address, paint the screen.
if ( $custemail == "" ) {

    debug("lostpass.php: Sending form to screen");

    print "<form name=\"lostpassword\" method=\"post\" action=\"$PHP_SELF\" onSubmit=\"return validate(this);\">";

    print "<br><br>";

    print "<table border=0 cellspacing=1 width=398>";
    print "  <tr>";
    print "    <td colspan=2 align=left>";
    print "      <font face=Tahoma size=3><blockquote><u><b>PASSWORD RETRIEVAL</b></u></blockquote></font>";
    print "    </td>";
    print "  </tr>";

    print "  <tr>";
    print "    <td style=\"border-style: none; border-width: medium\" width=190 align=right>";
    print "      <font face=Tahoma size=2>Your email address:</font>";
    print "    </td>";
    print "    <td style=\"border-style: none; border-width: medium\" width=208 valign=middle>";
    print "      <input type=text name=custemail size=30 style=\"font-family: Verdana; font-size: 8pt\">";
    print "    </td>";
    print "  </tr>";
    print "  <tr>";
    print "    <td style=\"border-style: none; border-width: medium\" width=190 align=right>";
	print "    </td>";
    print "    <td style=\"border-style: none; border-width: medium\" width=208 align=left>";
	print "      <input type=submit value=Submit name=submit style=\"font-family: Verdana; font-size: 8pt\">";
    print "      <input type=reset value=Reset name=reset style=\"font-family: Verdana; font-size: 8pt\">";
    print "    </td>";
    print "  </tr>";
    print "</table>";
    print "</form>";


} else {

	# If it's a valid email address, get the password stored with it and mail it out.
	if ( isValidEmail($custemail) ) {
		
		# If we get a password for the address, mail it out.
		if ( $theirpass = getCustomerPasswordByEmail($custemail) ) {
			#$mailmessage = "The Freight Depot password for $custemail is $theirpass\n";
			
			$custname = getCustomerNameByEmail($custemail);

			$mailmessage = "Hello $custname,\nOur records show you recently requested your password for login at thefreightdepot.com.\n\nYour password is: $theirpass\n\nTo access the Freight Depot system, just point your web browser to www.thefreightdepot.com and enter your email address and the password above into the login form.\n\nSincerely,\n\nThe FD Team";
			
			if ( mail("$custemail", "FREIGHT DEPOT PASSWORD REQUEST - DELETE WHEN FINISHED", $mailmessage, "FROM: csr@thefreightdepot.com") ) {
				print("<script language='javascript'>alert('Your password has been e-mailed to you at $custemail');</script>");
				print "<br><br>";
				print "&nbsp;&nbsp;<font face=verdana size=2>Please check your email for the password and delete the message when done.</font>";
			} else {
				print "<br><br>";
				print "&nbsp;&nbsp;<font face=verdana size=2>There was an error sending email to $custemail.<br>";
				print "&nbsp;&nbsp;We apologize for the inconvenience, please try again later.</font>";
			}
		} else {
			print "<br><br>";
			print "&nbsp;&nbsp;<font face=verdana size=2>There was an error while attempting to retrieve the password.<br>";
			print "&nbsp;&nbsp;We apologize for the inconvenience, please try again later.</font>";
		}
	} else {
	    print "<br><br>";
		print "&nbsp;&nbsp;<font face=verdana size=2>The email entered is not valid</font>";
	}

}

require("zzfooter.php");

?>
