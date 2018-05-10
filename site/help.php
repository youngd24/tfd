<?php
# =============================================================================
#
# qanda.php
#
# Question and answer page
#
# $Id: help.php,v 1.2 2003/01/16 22:31:33 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: help.php,v $
# Revision 1.2  2003/01/16 22:31:33  webdev
#   * Added content.
#
# Revision 1.1  2003/01/14 18:48:49  webdev
#   * Raised accessorial rates.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/15 07:15:07  webdev
#   * Added source header.
#
# =============================================================================

$unsecure = 1;
require("zzgrabcookie.php");

?>
<html>
<head>
<title>The Freight Depot > Help</title>

<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>

<?php

require("zzheader.php");

?>
<br>
<table><tr><td>

<font face="verdana" size=3>
<b>Need help?</b>
<br><br><br>
<font face=verdana size=2>Contents</font>
<ol>
	<li style="font-family:verdana;font-size:9pt;"><a href="#shipment_information" style="color:blue">SHIPMENT INFORMATION</a></li>
	<ul>
		<li style="font-family:verdana;font-size:9pt;"><a href="#description" style="color:blue">Product Description</a></li>
		<li style="font-family:verdana;font-size:9pt;"><a href="#unit" style="color:blue">Units/Pieces</a></li>
		<li style="font-family:verdana;font-size:9pt;"><a href="#packagetype" style="color:blue">Packaging Type</a></li>
		<li style="font-family:verdana;font-size:9pt;"><a href="#palletized" style="color:blue">Palletized?</a></li>
		<li style="font-family:verdana;font-size:9pt;"><a href="#packageqty" style="color:blue">Pallet Quantity?</a></li>
	</ul>
</ol>
<br><br><br>



<div id="shipment_information"><font face="verdana" size=2><b><u>SHIPMENT INFORMATION:</u></b></font></div>
<br><br>

<!-- PRODUCT DESCRIPTION -->
<div id=description style="font-family:verdana;font-size:9pt;font-weight:bold">
Product Description <a href="javascript:window.history.back">back</a>
</div>
<p style="font-family:verdana;font-size:9pt;">Enter a free form text description of the product you are shipping up to 56 characters.</p>
<br>

<!-- UNITS/PIECES -->
<div id=unit style="font-family:verdana;font-size:9pt;font-weight:bold">
Units/Pieces
</div>
<p style="font-family:verdana;font-size:9pt;">This is the total number of packaged and non-packaged pieces in the shipment. As an example, if you were shipping 2 pallets each with 10 units each and 10 loose units, the total would be 30.</p>
<br>

<!-- PACKAGING TYPE -->
<div id=packagetype style="font-family:verdana;font-size:9pt;font-weight:bold">
Packaging Type
</div>
<p style="font-family:verdana;font-size:9pt;">The packaging type that your shipment uses. Examples are Pallet, Skid and Box. Select one of the available options in the drop-down list. If your packaging type isn't available, choose the closest available option. If the packaging type you are using doesn't match any there, please call us and we will add it in. </p>
<br>

<!-- PALLETIZED -->
<div id=palletized style="font-family:verdana;font-size:9pt;font-weight:bold">
Palletized
</div>
<p style="font-family:verdana;font-size:9pt;">Indicate here whether or not the shipment has been loaded onto pallets. If you answer yes to this question you will need to fill in the pallet quantity field.</p>
<br>

<!-- PALLET QUANTITY -->
<div id=palletqty style="font-family:verdana;font-size:9pt;font-weight:bold">
Pallet Quantity
</div>
<p style="font-family:verdana;font-size:9pt;">This is the total number of pallets that the goods have been loaded onto (if the shipment has been palletized).</p>
<br>


<br><br>

</font>
</td></tr></table>
<?php

require('zzfooter.php');

?>