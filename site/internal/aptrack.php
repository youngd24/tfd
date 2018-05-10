<?php
# =============================================================================
#
# aptrack.php
#
# Payables tracking page
#
# $Id: aptrack.php,v 1.4 2002/12/26 17:43:39 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: aptrack.php,v $
# Revision 1.4  2002/12/26 17:43:39  webdev
#   * Added the check number paid with to the output. It's the source field in acc_trans.
#
# Revision 1.3  2002/12/23 23:32:53  webdev
#   * Added search by pro feature.
#
# Revision 1.2  2002/12/23 22:53:42  webdev
#   * Added search code.
#
# Revision 1.1  2002/12/23 21:48:39  webdev
#   * First sort of working version, still under development.
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


function table_header () {
	echo "<table cellpadding=1 cellspacing=2 border=1>";
	echo "<tr bgcolor=silver>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=bol>BOL</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=pro>PRO</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=shipdate>SHIPDATE</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=carrier>CARRIER</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=estap>EDTIMATED AP</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=invap>INVOICED AP</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=paidap>PAID AP</a></b></font></td>";
	echo "<td><font face=verdana size=2><b><a href=$PHP_SELF?sort=source>SOURCE</a></b></font></td>";
	return(true);
}


function display_row ($bol,$pro,$shipdate,$carriername,$estimated_amount,$invoiced_amount,$paid_amount,$source) {
		if ( $invoiced_amount == "" ) {
			$invoiced_amount = "0.00";
		}
		if ( $paid_amount == "" ) {
			$paid_amount = "0.00";
		}
		echo "<tr>";
		echo "<td><font face=verdana size=2>$bol</font></td>";
		echo "<td><font face=verdana size=2>$pro</font></td>";
		echo "<td><font face=verdana size=2>$shipdate</font></td>";
		echo "<td><font face=verdana size=2>$carriername</font></td>";
		echo "<td><font face=verdana size=2>$estimated_amount</font></td>";
		echo "<td><font face=verdana size=2>$invoiced_amount</font></td>";
		echo "<td><font face=verdana size=2>$paid_amount</font></td>";
		echo "<td><font face=verdana size=2>$source</font></td>";
		echo "</tr>";
		return(true);
}


function table_footer () {
	echo "</table>";
	return(true);
}



if ( $shipmentid ) {

	// Open up the connection to the SQL-Ledger database
	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password") or die(pg_errormessage());

	// Get the PO information
	$poquery = pg_exec("select * from oe where customer_id=0 and ordnumber='$shipmentid'") or die(pg_errormessage());

	// Print the table header
	table_header();

	// For every PO, do some stuff
	while ($row = pg_fetch_array($poquery) ) {

		// Get the carrier pro from the shipping database
		$proquery = mysql_fetch_array(mysql_query("select carrierpro from shipment where shipmentid=$row[ordnumber]"));

		// Get the vendor name from the vendor table in the sql-ledger database
		$vendor_query = pg_fetch_array(pg_exec("select name from vendor where id=$row[vendor_id]"));

		// Get the invoiced and paid amounts from the ap table in the sql-ledger database
		$invoice_query = pg_fetch_array(pg_exec("select * from ap where ordnumber='$row[ordnumber]'"));

		// Get the check number used to pay from the acc_trans table
		$acc_query = pg_fetch_array(pg_exec("select source from acc_trans where trans_id=$invoice_query[id] and source is not null"));

		$bol = $row[ordnumber];
		$pro = $proquery[carrierpro];
		$shipdate = $row[transdate];
		$vendor_name = $vendor_query[name];
		$estimated_amount = $row[amount];
		$invoiced_amount = $invoice_query[amount];
		$paid_amount = $invoice_query[paid];
		$source = $acc_query[source];

		display_row($bol,$pro,$shipdate,$vendor_name,$estimated_amount,$invoiced_amount,$paid_amount,$source);
	}

	table_footer();

	pg_close($conn);


} elseif ( $carrierpro ) {

	$pro = $carrierpro;

	// Get the shipmentif from the shipping database
	$shipit = mysql_fetch_array(mysql_query("select shipmentid from shipment where carrierpro=$carrierpro"));
	$bol = $shipit[shipmentid];

	// Open up the connection to the SQL-Ledger database
	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password") or die(pg_errormessage());

	// Get the PO information
	$poquery = pg_exec("select * from oe where customer_id=0 and ordnumber='$bol'") or die(pg_errormessage());
	$row = pg_fetch_array($poquery);

	// Get the vendor name from the vendor table in the sql-ledger database
	$vendor_query = pg_fetch_array(pg_exec("select name from vendor where id=$row[vendor_id]"));

	// Get the invoiced and paid amounts from the ap table in the sql-ledger database
	$invoice_query = pg_fetch_array(pg_exec("select * from ap where ordnumber='$row[ordnumber]'"));

	// Get the check number used to pay from the acc_trans table
	$acc_query = pg_fetch_array(pg_exec("select source from acc_trans where trans_id=$invoice_query[id] and source is not null"));

	$shipdate = $row[transdate];
	$vendor_name = $vendor_query[name];
	$estimated_amount = $row[amount];
	$invoiced_amount = $invoice_query[amount];
	$paid_amount = $invoice_query[paid];
	$source = $row[source];

	table_header();

	display_row($bol,$pro,$shipdate,$vendor_name,$estimated_amount,$invoiced_amount,$paid_amount,$source);

	table_footer();

	pg_close();


} elseif ( $carrierid ) {

	// Open up the connection to the SQL-Ledger database

	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password") or die(pg_errormessage());

	if ( $carrierid == "all" ) {
		$poquery = pg_exec("select * from oe where customer_id=0") or die(pg_errormessage());
	} else {
		$poquery = pg_exec("select * from oe where customer_id=0 and vendor_id='$carrierid'") or die(pg_errormessage());
	}

	table_header();

	while ($row = pg_fetch_array($poquery) ) {

		// Get the carrier pro from the shipping database
		$proquery = mysql_fetch_array(mysql_query("select carrierpro from shipment where shipmentid=$row[ordnumber]"));
		$pro = $proquery[carrierpro];

		// Get the vendor name from the vendor table in the sql-ledger database
		$vendor_query = pg_fetch_array(pg_exec("select name from vendor where id=$row[vendor_id]"));

		// Get the invoiced and paid amounts from the ap table in the sql-ledger database
		$invoice_query = pg_fetch_array(pg_exec("select * from ap where ordnumber='$row[ordnumber]'"));

		// Get the check number used to pay from the acc_trans table
		$acc_query = pg_fetch_array(pg_exec("select source from acc_trans where trans_id=$invoice_query[id] and source is not null"));

		$bol = $row[ordnumber];
		$pro = $proquery[carrierpro];
		$shipdate = $row[transdate];
		$vendor_name = $vendor_query[name];
		$estimated_amount = $row[amount];
		$invoiced_amount = $invoice_query[amount];
		$paid_amount = $invoice_query[paid];
		$source = $acc_query[source];

		display_row($bol,$pro,$shipdate,$vendor_name,$estimated_amount,$invoiced_amount,$paid_amount,$source);

	}

	table_footer();

	pg_close($conn);

} else {

	echo "<font face=verdana size=3><b>Select search criteria</b></font>";
	echo "<br><br>";

	echo "<form method=POST action=$PHP_SELF>";
	echo "<table>";

	echo "<tr>";
	echo "<td align=right><font face=verdana size=2>BOL NUMBER:</font></td>";
	echo "<td><input type=text name=shipmentid style='font-family:verdana;font-size:9pt'></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td></td>";
	echo "<td><font face=verdana size=2>-OR-</font></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=right><font face=verdana size=2>PRO NUMBER:</font></td>";
	echo "<td><input type=text name=carrierpro style='font-family:verdana;font-size:9pt'></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td></td>";
	echo "<td><font face=verdana size=2>-OR-</font></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=right><font face=verdana size=2>BY CARRIER:</font></td>";
	echo "<td><select name=carrierid style='font-family:verdana;font-size9pt'>";
		echo "<option value=all>- ALL CARRIERS -";
		$qry = mysql_query("select * from carriers order by name");
		while ( $r = mysql_fetch_array($qry)) {
			echo "<option value=$r[carrierid]>$r[name]";
		}
	echo "</select></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td>";
	echo "<td>";
	echo "<input type=submit value=submit name=submit style='font-family:verdana;font-size:8pt'>&nbsp;";
	echo "<input type=reset value=reset name=reset style='font-family:verdana;font-size:8pt'>";
	echo "</td>";
	echo "</tr>";
	echo "</form>";
	echo "</table>";

}


?>