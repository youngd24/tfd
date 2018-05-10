<?php
# ==============================================================================
#
# class.php
#
# Class page
#
# $Id: class.php,v 1.12 2002/12/10 19:51:44 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: class.php,v $
# Revision 1.12  2002/12/10 19:51:44  webdev
#   * Changed emails to be at the freight depot
#
# Revision 1.11  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.10  2002/09/19 20:47:57  youngd
#   * Changed harry's email to be the new one at aol
#
# Revision 1.9  2002/09/16 22:48:09  youngd
#   * Added Harry's email
#
# Revision 1.8  2002/09/16 20:07:03  youngd
#   * Changed email addresses to include Harry
#
# Revision 1.7  2002/09/16 20:03:03  youngd
#   * Changes per Tom
#
# Revision 1.6  2002/09/13 00:10:44  webdev
#   * Corrected opening and closing php tags
#
# Revision 1.5  2002/09/13 00:10:20  webdev
#   * Added php source header
#
# ==============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// get cookie and user
require('zzgrabcookie.php');

if ($action) {
	$mailmessage="CLASS REQUEST FOR PRODUCT\n\nDESCRIPTION: $description\nAPPLICATION: $application\nPACKAGING: $packaging\nDIMENSIONS: $height\" x $width\" x $depth\"\nVALUE: $value\n\n";
	$cond = mail("tjuedes@thefreightdepot.com,hpavlos@thefreightdepot.com", "CLASS REQUEST", $mailmessage, "FROM: $userarray[2]");

}
?>

<html>
<head>
<title>GET MY CLASS</title>
</head>
<body background="images/backgrounds/bg.gif" bgcolor=ffffff><form method=post action=class.php>
<font face=tahoma size=1><b>GET MY CLASS!</b><br>


<?php
if ($action) {
	echo "<br><font color=red><b>YOUR REQUEST HAS BEEN RECEIVED. SOMEONE WILL BE CONTACTING YOU SHORTLY!<br><a href='#' onClick='window.close()'>CLOSE WINDOW</a>";

}
else {
	echo "Please provide the following information about your product:<br><br>";
	echo "DESCRIPTION OF ITEM<br>";
	echo "<input size=20 name=description><br>";
	echo "HOW THE ITEM IS USED<br>";
	echo "<input size=20 name=application><br>";
	echo "WEIGHT PER PIECE OR UNIT<br>";
	echo "<input size=20 name=weightperpiece><br>";
	echo "HOW THE ITEM IS PACKAGED<br>";
	echo "<input size=20 name=packaging><br>";
	echo "DIMENSIONS OF PIECE OR UNIT (in inches)<br>";
	echo '<input size=4 name=height>H x <input size=4 name=width>W x <input size=4 name=depth>D<br>';
	echo "VALUE OF THE ITEM<br>";
	echo "<input size=10 name=value> <input type=hidden name=action value=1>";
	echo "<input type=submit value=GO!>";
}
?>

</form>
</b></font>
</body>

</html>
