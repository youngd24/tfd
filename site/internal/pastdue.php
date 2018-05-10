<?php
# =============================================================================
#
# pastdue.php
#
# Past Due Invoices Page
#
# $Id: pastdue.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: pastdue.php,v $
# Revision 1.3  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.2  2002/10/16 06:47:44  youngd
#   * Added standard source header and normalized includes.
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
require_once("zzaccounting.php");

$conn = postgresconnect();
if ($ordnumber) {
	
	require('invoicepd.php');
	die();
}
else {
	$timenow = getdate();
	$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'];
	$somesql = "select ordnumber, netamount, paid, duedate from ar where duedate < '$thetime' and netamount != paid order by duedate"; 
	$checkquery = pg_exec($somesql) or die(pg_errormessage());
	postgresclose($conn);
}

?>

<html>
<head>
<title>GENERATE PAST DUE BILL</title>
</head>
<body bgcolor=ffffff>
<?php

if ($invoicenum) {


}
else {
	echo "<table style='font-family: arial; font-size: 11px;' width=400>";
	echo "<tr><td><b>BOL #</b></td><td><b>ORIGINAL AMOUNT</b></td><td><b>AMOUNT PAID</b></td><td><b>DUE DATE</b></td></tr>";
	while ($line = pg_fetch_array($checkquery)) {
		echo "<tr><td><a href=invoicepd?ordnumber=$line[ordnumber]>$line[ordnumber]</a></td>";
		echo "<td>$$line[netamount]</td><td>$$line[paid]</td><td>$line[duedate]</td></tr>";
	}
	echo "</table>";

}
?>

</body></html>
