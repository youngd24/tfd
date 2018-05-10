<?php
# ==============================================================================
#
# about.php
#
# About page
#
# $Id: about.php,v 1.12 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: about.php,v $
# Revision 1.12  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.11  2002/09/19 20:54:11  youngd
#   * Added require of functions page
#
# Revision 1.10  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.9  2002/09/13 08:33:06  webdev
#   * Added requires.
#
# Revision 1.8  2002/09/12 22:46:12  webdev
#   * Converted to UNIX format
#
# Revision 1.7  2002/09/12 22:42:12  webdev
#   * Added php source header
#
# ==============================================================================

    $unsecure = 1;

	require_once("config.php");
	require_once("debug.php");
	require_once("error.php");
	require_once("event.php");
	require_once("functions.php");
	require_once("logging.php");

    debug("about.php: ENTERING");
    debug("about.php: done loading requires");

    debug("about.php: loading zzgrabcookie");
    require("zzgrabcookie.php");
    debug("about.php: done loading zzgrabcookie");

?>

<html>
<head>
<title>The Freight Depot > About Us</title>

<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>

<?php

    debug("about.php: loading site header");
    require("zzheader.php");

?>

<br>
<table>
<tr><td>

<font face="verdana" size=2>

<b>About Us</b>
<br><br>

The Freight Depot provides freight shipment services through an e-commerce application and strategic network of high performance freight carriers. The Freight Depot provides LTL services locally, regionally and nationally.
<br><br>
The Freight Depot's business-to-business e-commerce application creates a seamless, real-time communication network in a highly fragmented and manual process-driven industry. From the initial rate quote through the delivery of a shipment, critical information along with logistic and management data is readily available to or from any location at any time via <a href="http://www.thefreightdepot.com">www.thefreightdepot.com</a>.
<br><br>
By utilizing the The Freight Depot's operating system, small and medium sized companies can take advantage of price and service levels usually reserved for their largest competitors. The Freight Depot provides its shipping customers with a single transportation resource that will generate cost savings, improved service standards while reducing time spent on coordinating shipments. 
<br><br>
Using the The Freight Depot's application, shippers are presented with a simple, intuitive set of screens that will allow them to quickly obtain critical shipping information.
<ul>
<li>	Electronic claims management
<li>	Real-time customized reports
<li>	Multi-mode carrier shipping capability
<li>	Vendor management control systems
<li>	Electronic paperwork management
<li>	Immediate customer services
<li>	Automated pricing of LTL and airfreight shipments
<li>	Data warehousing and distribution
<li>	Single-source freight payment and auditing capability
<li>	Automated LTL and air freight scheduling
<li>	Single-source real-time shipment tracing and tracking
<li>	Local, regional, national and international coverage
</ul>


</font>
</td></tr></table>

<?php

    debug("about.php: loading site footer");
    debug("about.php: DONE");
    require('zzfooter.php');

?>
