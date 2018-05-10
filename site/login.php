<?php
# =============================================================================
#
# login.php
#
# User login page
#
# $Id: login.php,v 1.12 2002/10/30 18:16:57 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: login.php,v $
# Revision 1.12  2002/10/30 18:16:57  youngd
#   * Removed bold.
#   * Reduced font size.
#
# Revision 1.11  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.10  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.9  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.8  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.7  2002/09/13 08:33:58  webdev
#  * Added source header.
#  * Added requires
#
#
# =============================================================================

// Standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("login.php: entering after initial requires");

?>

<html>
<head>
	<title>The Freight Depot > Login</title>
	
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>


<?php

debug("login.php: Loading site header");
require("zzheader.php");

?>

<br><br>
<blockquote>
		<form method="POST" action="mydigiship.php">
		<font face="'Trebuchet MS', tahoma" size=2>Please enter your e-mail address and password to login.<br><br>
		<blockquote>
			E-MAIL ADDRESS:<br>
			<input size=20 name="email"><br><br>
			PASSWORD:<br>
			<input size=20 name="password" TYPE="PASSWORD"><br><br>
			<input type=image border=0 src="images/buttons/loginnow.gif" width=99 height=21><br><br>		
			</form>
		</blockquote>
</blockquote>

	<blockquote>
	<blockquote>
	<font face=verdana size=1><a href="lostpass.php" style="font=family:tahoma;font-size:7pt;color:blue">Lost your password?</a></font>
	</blockquote>
	</blockquote>

<?php

debug("login.php: Loading site footer");
require("zzfooter.php");

debug("login.php: leaving");

?>