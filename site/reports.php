<?php
# =============================================================================
#
# reports.php
#
# Shipment reports page
#
# $Id: reports.php,v 1.6 2002/10/02 20:01:42 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: reports.php,v $
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/15 07:27:25  webdev
#   * Added source header
#
# =============================================================================

// get cookie and user
require('zzgrabcookie.php');
?>

<html>
<head>
<title>The Freight Depot > Reports</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>

</head>

<?php

require('zzheader.php');

?>
<br><br>

<?php

if ($report) {
	
	echo "<center><table class=reporttext cellpadding=4 cellspacing=5>";	
	require('zzreports.php');
	echo "</table><br><br><a href=reports.php class=links_general>BACK</a></center><BR><BR>";
	
}

else {
	echo "<center><table><tr><td align=center>";
	echo "<font face='Trebuchet MS' size=2><b>Available Reports</b></font><br><br>";
	echo "<a href=reports.php?report=outbound class=links_general>Historical Outbound Activity Report</a><br><br>";
	echo "<a href=reports.php?report=transit class=links_general>Historical Total Transit Report</a><br><br>";
	echo "<a href=reports.php?report=revenue class=links_general>Monthly Revenue Report</a><br><br>";
	echo "</td></tr></table></center>";

}

?>


<?php

require('zzfooter.php');

?>
