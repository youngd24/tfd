<?php
# ==============================================================================
#
# adminremote.php
#
# Site Administrator Remote Control Panel
#
# $Id: adminremote.php,v 1.4 2002/10/15 21:06:18 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
#
# $Log: adminremote.php,v $
# Revision 1.4  2002/10/15 21:06:18  youngd
#   * Changed size
#
# Revision 1.3  2002/10/15 21:01:33  youngd
#   * QuickRater in process.
#
# Revision 1.2  2002/10/14 19:08:42  youngd
#   * Company name prints correctly on the internal BOL.
#
# Revision 1.1  2002/10/14 18:54:39  youngd
#   * Added new admin remote for testing.
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

<head>
	<title>ADMIN REMOTE</title>
	<script language=JavaScript src="/internal/common.js">
	</script>
</head>

<hr width="100%">

<font face=verdana size=3><b><u>BOL & Invoice Control:</u></b></font>
<br><br>

<table cellpadding=1 cellspacing=1>

	<tr>
		<td valign=middle align=right><font face=verdana size=2><b>BOL:&nbsp;</b></font></td>
		<td>
			<input type=text name=shipmentid size=10 style="font-family: Verdana; font-size: 8pt">
		</td>
		<td><font face=verdana size=1>[<a href="JavaScript:displayBillOfLading(shipmentid)" style="text-decoration:none; color:blue">open</a>]</font></td>
	</tr>

	<tr>
		<td valign=middle align=right><font face=verdana size=2><b>Invoice:&nbsp;</b></font></td>
		<td>
			<input type=text name=ordnumber size=10 style="font-family: Verdana; font-size: 8pt">
		</td>
		<td><font face=verdana size=1>[<a href="JavaScript:displayInvoice(ordnumber)" style="text-decoration:none; color:blue">open</a>]</font></td>
	</tr>

</table>

<br>
<hr width="100%">

<br>


<font face=verdana size=3><u><b>Quick Rater:</b></u></font>
<br><br>

<table cellpadding=1 cellspacing=1 border=0>

	<tr>
		<td valign=top align=right><font face=verdana size=2><b>Customer:&nbsp;</b></font></td>
		<td valign=bottom align=left><input type=text name=custid size=5 style="font-family:verdana;font-size:11px"></td>
		<td valign=middle align=left><font face=verdana size=1>&nbsp;<a href="JavaScript:customerLookup()" style="text-decoration:none; color:blue">Lookup</a></font></td>
	</tr>

	<tr>
		<td valign=top align=right><font face=verdana size=2><b>Origin Zip:&nbsp;</b></font></td>
		<td valign=bottom align=left><input type=text name=srczip size=5 style="font-family:verdana;font-size:11px"></td>
	</tr>

	<tr>
		<td valign=top align=right><font face=verdana size=2><b>Dest Zip:&nbsp;</b></font></td>
		<td valign=bottom align=left><input type=text name=dstzip size=5 style="font-family:verdana;font-size:11px"></td>
	</tr>

	<tr>
		<td valign=top align=right><font face=verdana size=2><b>Weight:&nbsp;</b></font></td>
		<td valign=bottom align=left><input type=text name=weight size=5 style="font-family:verdana;font-size:11px"></td>
	</tr>

	<tr>
		<td valign=top align=right><font face=verdana size=2><b>Class:&nbsp;</b></font></td>
		<td valign=bottom align=left><input type=text name=class size=5 style="font-family:verdana;font-size:11px"></td>
	</tr>

	<tr>
		<td></td>
		<td valign=top align=left><font face=verdana size=1><a href="JavaScript:quickRater()" style="text-decoration:none; color:blue">Rate It</a></font></td>
	</tr>


</table>

<hr width="100%">

<br>
<center>
	<font face=verdana size=1>[<a href="JavaScript:window.close()" style="text-decoration:none; color:blue">close</a>]</font>
</center>