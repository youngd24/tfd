<?php
# =============================================================================
#
# zzaccounting.php
#
# SQL-Ledger connect page
#
# $Id: zzaccounting.php,v 1.12 2002/10/17 19:21:07 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzaccounting.php,v $
# Revision 1.12  2002/10/17 19:21:07  youngd
#   * Fixed bug that prevented SO's from being deleted properly.
#
# Revision 1.11  2002/10/09 23:37:13  youngd
#   * Cancel shipment now works and deletes the necessary accounting
#     information.
#
# Revision 1.10  2002/10/09 23:20:06  youngd
#   * Added basic logic to SO and PO delte metnods.
#
# Revision 1.9  2002/10/09 23:06:36  youngd
#   * Added prototype for the PO and SO delete functions.
#
# Revision 1.8  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.7  2002/10/03 22:10:02  youngd
#   * Added require pages
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/15 07:27:53  webdev
#   * Added source header
#
# =============================================================================


// functions for the SQL-LEDGER (w/ pg-sql)
// must test to see if server is available

// Pull in standard requires
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");


// open postgres connection
function postgresconnect() {

	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password") or die(pg_errormessage()); 
	return $conn;
	
}


// close postgres connection
function postgresclose($conn) {

	$check = pg_close($conn);
	return $check;

}


function addcustomer($conn, $auserarray) {
	// check to see if customer is in there
	$somesql = "SELECT * from customer where name like '%$auserarray[company]%' and addr1 like '%$auserarray[address1]%' and addr2 like '%$auserarray[address2]%' and addr3 like '%$auserarray[city] $auserarray[state] $auserarray[zip]%'";
	$checkquery = pg_exec($somesql) or die(pg_errormessage());
	if (!($line = pg_fetch_array($checkquery))) {
		$insql = "INSERT INTO customer (name, contact, addr1, addr2, addr3, phone, email, discount, terms, taxincluded) VALUES ('$auserarray[company]', '$auserarray[name]', '$auserarray[address1]', '$auserarray[address2]', '$auserarray[city] $auserarray[state] $auserarray[zip]', '$auserarray[phone]', '$auserarray[email]', 0, 15, 't')";
		
		$queresult = pg_exec($conn, $insql);
		$checkquery = pg_exec($somesql) or die(pg_errormessage());
		$line = pg_fetch_array($checkquery);
	}
	$userid = $line[0];
	
	return $userid;
}


// adds sales order and purchase order (don't forget to put in accessorials)
function addtransaction($trans, $oitems, $conn) {
	
	//check to see if pro number has already been includes
	$checkup = pg_exec($conn, "SELECT * from oe where ordnumber = '$trans[ordernumber]'") or die(pg_errormessage());
	
	if (!($line = pg_fetch_array($checkup))) {
	   
		// insert order
		$somsql = "INSERT INTO oe (ordnumber, vendor_id, customer_id, amount, netamount, reqdate, taxincluded, curr) VALUES ('$trans[ordernumber]', 0, $trans[customerid], $trans[amount], $trans[netamount], '$trans[reqdate]', '$trans[taxincluded]', '$trans[curr]')";

		$inres = pg_exec($conn, $somsql) or die(pg_errormessage());
		// get transid
		$gettransid = pg_exec($conn, "SELECT id from oe where ordnumber = '$trans[ordernumber]' and customer_id = $trans[customerid]") or die(pg_errormessage());
		$line = pg_fetch_array($gettransid);
		$oitems[transid] = $line[0];
		// insert order items
		$inres = pg_exec($conn, "INSERT INTO orderitems (trans_id, parts_id, description, qty, sellprice, discount) VALUES ($oitems[transid], $oitems[partsid], '$oitems[description]', $oitems[qty], $oitems[sellprice], $oitems[discount])") or die(pg_errormessage());


		// insert purchase order
		$somsql = "INSERT INTO oe (ordnumber, vendor_id, customer_id, amount, netamount, reqdate, taxincluded, curr) VALUES ('$trans[ordernumber]', $trans[vendorid], 0, $trans[poamount], $trans[ponetamount], '$trans[reqdate]', '$trans[taxincluded]', '$trans[curr]')";
		$inres = pg_exec($conn, $somsql) or die(pg_errormessage());
		// get transid
		$gettransid = pg_exec($conn, "SELECT id from oe where ordnumber = '$trans[ordernumber]' and vendor_id = $trans[vendorid]") or die(pg_errormessage());
		$line = pg_fetch_array($gettransid);
		$oitems[transid] = $line[0];
		// insert order items
		$inres = pg_exec($conn, "INSERT INTO orderitems (trans_id, parts_id, description, qty, sellprice, discount) VALUES ($oitems[transid], $oitems[partsid], '$oitems[description]', $oitems[qty], $oitems[posellprice], $oitems[discount])") or die(pg_errormessage());		
	}
}


function deletePurchaseOrder ($ponumber, $conn) {

	if ( ! $ponumber ) {
		logmsg('SYNTAX', 'deletePurchaseOrder(): param ponumber not passed');
		return(0);
	}
	
	if ( ! $conn ) {
		logmsg('SYNTAX', 'deletePurchaseOrder(): param conn not passed');
		return(0);
	}

	$sql = "DELETE FROM oe WHERE ordnumber='$ponumber' and vendor_id != ''";
	$inres = pg_exec($conn, $sql) or die(pg_errormessage());

	if ( $inres ) {
		return(1);
	} else {
		return(0);
	}

}


function deleteSalesOrder ($sonumber, $conn) {

	if ( ! $sonumber ) {
		logmsg('SYNTAX', 'deleteSalesOrder(): param sonumber not passed');
		return(0);
	}
	
	if ( ! $conn ) {
		logmsg('SYNTAX', 'deleteSalesOrder(): param conn not passed');
		return(0);
	}

	$sql = "DELETE FROM oe WHERE ordnumber='$sonumber' and customer_id != ''";
	$inres = pg_exec($conn, $sql) or die(pg_errormessage());

	if ( $inres ) {
		return(1);
	} else {
		return(0);
	}

}


?>
