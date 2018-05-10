<?php
# =============================================================================
#
# changepass.php
#
# Page for customers to change their password with
#
# $Id: changepass.php,v 1.5 2002/11/14 21:30:10 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: changepass.php,v $
# Revision 1.5  2002/11/14 21:30:10  youngd
#   * Conflicts resolved after merge
#
# Revision 1.4.2.3  2002/11/05 15:15:12  webdev
#   * New registration in progress.
#
# Revision 1.4.2.2  2002/11/05 08:28:53  webdev
#   * Added Tag meta
#
# Revision 1.4.2.1  2002/11/05 08:27:59  webdev
#   * First working development version.
#
# Revision 1.4  2002/11/03 10:13:42  youngd
#   * Still under development.
#
# Revision 1.3  2002/11/03 09:13:15  youngd
#   * Still under development
#
# Revision 1.2  2002/10/30 20:36:20  youngd
#   * Added basic table
#
# Revision 1.1  2002/10/30 17:37:22  youngd
#   * Initial version from template.
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

debug("changepass.php: entering after initial requires");

require("zzgrabcookie.php");

header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<meta name="Revision" content="$Revision: 1.5 $">
	<meta name="Author" content="$Author: youngd $">
    <meta name="Tag" content="$Tag$">
	<title>Customer Password Change</title>
	<script language="JavaScript" src="js/main.js"></script>
    <script language="JavaScript">

        function validate(frm) {

            var errors = "";

            if (frm.currpass.value == "" ) {
                errors += "Enter your current password\n";
            }
            if (frm.newpass1.value == "" ) {
                errors += "Enter your new password\n";
            }
            if (frm.newpass2.value == "" ) {
                errors += "Confirm your new password\n";
            }

            if ( errors != "" ) {
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

require("zzheader.php");

# If we received all the necessary params, do the work
if ( $currpass and $newpass1 and $newpass2 ) {

    $custid = $userarray[0];
    debug("changepass.php: changing password for $custid");

    // Make sure the current password is correct
    if ( $currpass == getCustomerPassword($custid) ) {

        // Make sure both new passwords are the same
        if ( $newpass1 == $newpass2 ) {
            
            // Change the password
            if ( changePassword($custid, $newpass1)) {
            print "<br><font face=verdana size=2>&nbsp;&nbsp;Password changed</font>";
            } else {
                htmlerror("Error changing password, please contact customer service");
                die();
            }
        } else {
            print "<br>";
            htmlerror("The passwords you entered aren't the same.");
            die();
        }
    } else {
        print "<br>";
        htmlerror("Current password is not correct");
        die();
    }



} else {

    print "<form name=\"frmChangePassword\" method=\"POST\" action=\"$PHP_SELF\" onSubmit=\"return validate(this);\">";

    print "<br><br>";

    print "<table border=0 cellspacing=1 width=398>";
    
    print "  <tr>";
    print "    <td colspan=2 align=left>";
    print "      <font face=Tahoma size=3><blockquote><u><b>PASSWORD CHANGE</b></u></blockquote></font>";
    print "    </td>";
    print "  </tr>";

    print "  <tr>";
    print "    <td style=\"border-style: none; border-width: medium\" width=190 align=right>";
    print "      <font face=Tahoma size=2>Current Password:</font>";
    print "    </td>";
    print "    <td style=\"border-style: none; border-width: medium\" width=208 valign=middle>";
    print "      <input type=password name=currpass size=16 style=\"font-family: Verdana; font-size: 8pt\">";
    print "    </td>";
    print "  </tr>";

    print "  <tr>";
    print "    <td style=\"border-style: none; border-width: medium\" width=190 align=right>";
    print "      <font face=Tahoma size=2>New Password:</font>";
    print "    </td>";
    print "    <td style=\"border-style: none; border-width: medium\" width=208 valign=middle>";
    print "      <input type=password name=newpass1 size=16 style=\"font-family: Verdana; font-size: 8pt\">";
    print "    </td>";
    print "  </tr>";

    print "  <tr>";
    print "    <td style=\"border-style: none; border-width: medium\" width=190 align=right>";
    print "      <font face=Tahoma size=2>New Password Again:</font>";
    print "    </td>";
    print "    <td style=\"border-style: none; border-width: medium\" width=208 valign=middle>";
    print "      <input type=password name=newpass2 size=16 style=\"font-family: Verdana; font-size: 8pt\">";
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

	print "</table>";

}


?>


<?php require("zzfooter.php"); ?>

</body>

</html>

