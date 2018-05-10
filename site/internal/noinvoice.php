<?php

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");

$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password") or die(pg_errormessage());

print "DELIVERED SHIPMENTS THAT HAVE NOT BEEN INVOICED<br><br>";

$delivered_shipments = mysql_query("SELECT * FROM shipment WHERE delivered = 1");

while ($shipment_record = mysql_fetch_array($delivered_shipments) ) {

	$invoice_query = pg_exec($conn, "SELECT * FROM ar WHERE ordnumber='$shipment_record[0]'");

	if ( $line = pg_fetch_array($invoice_query) ) {
	} else {
		print "BOL: $shipment_record[0]&nbsp;&nbsp;";
		print "NOT INVOICED<br>";
	}
}








?>