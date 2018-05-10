<?php
# =============================================================================
#
# contact.php
#
# Contact us page
#
# $Id: contact.php,v 1.8 2003/01/16 22:31:22 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: contact.php,v $
# Revision 1.8  2003/01/16 22:31:22  webdev
#   * Added use of the getOfficeInfo() function. it's all built dynamically now.
#
# Revision 1.7  2002/10/03 15:59:58  youngd
#   * Changed address.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/13 00:14:28  webdev
#   * Converted to UNIX format
#
# Revision 1.4  2002/09/13 00:14:12  webdev
#   * Added php source header
#
# =============================================================================

$unsecure = 1;

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// Get cookie and user
require("zzgrabcookie.php");



?>
<html>
<head>
<title>The Freight Depot > Contact Us</title>

<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>

<?php

require("zzheader.php");

?>

<?php

	$companyInfo = getCompanyInfo();

?>

<br><br>

<center>
<table>
<tr><td align=center>
<font face="Trebuchet MS" size=2><b>CONTACT US</b></font><br><bR>

<table width=500>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Toll Free:</font>
		</td>
		<td>
			<font face=verdana size=2><b><?php echo $companyInfo[tollfree] ?></b>
		</td>
	</tr>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Office:</font>
		</td>
		<td>
			<font face=verdana size=2><b><?php echo $companyInfo[address1] ?><br><?php echo $companyInfo[address2] ?><br><?php echo $companyInfo[city] ?>, <?php echo $companyInfo[state] ?> <?php echo $companyInfo[zip] ?><br><?php echo $companyInfo[mainphone] ?></b>
		</td>
	</tr>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Fax:</font>
		</td>
		<td>
			<font face=verdana size=2><b><?php echo $companyInfo[faxphone] ?></b>
		</td>
	</tr>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Carrier Relations E-mail:</font>
		</td>
		<td>
			<font face=verdana size=2><b><a href="mailto:<?php echo $companyInfo[carrieremail] ?>"><?php echo $companyInfo[carrieremail] ?></a></b>
		</td>
	</tr>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Customer Service E-mail:</font>
		</td>
		<td>
			<font face=verdana size=2><b><a href="mailto:<?php echo $companyInfo[csremail] ?>"><?php echo $companyInfo[csremail] ?></a></b>
		</td>
	</tr>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Billing E-mail:</font>
		</td>
		<td>
			<font face=verdana size=2><b><a href="mailto:<?php echo $companyInfo[billingemail] ?>"><?php echo $companyInfo[billingemail] ?></a></b>
		</td>
	</tr>
	<tr valign=top>
		<td align=right>
			<font face=verdana size=2>Webmaster:</font>
		</td>
		<td>
			<font face=verdana size=2><b><a href="mailto:<?php echo $companyInfo[webmasteremail] ?>"><?php echo $companyInfo[webmasteremail] ?></a></b>
		</td>
	</tr>
</table>
</td></tr></table></center>

<?php

require('zzfooter.php');

?>
