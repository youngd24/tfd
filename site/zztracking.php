<?php
# =============================================================================
#
# zztrackinglist.php
#
# Shipment tracking page
#
# $Id: zztracking.php,v 1.7 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren_young@yahoo.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zztracking.php,v $
# Revision 1.7  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/19 14:02:07  youngd
#   * Added source header and changed my email address
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// get cookie and user (only if cookie is present)
if (!($digishipcookie1) and !($digishipcookie2)) {

	require('zzmysql.php');
}

// if tracking request was passed
if ($number) {


	// determine shipment
	$shipidsearch = mysql_query("SELECT shipmentid, ponumber from shipment where shipment.shipmentid = '$number'") or die (mysql_error());
	if ($rshipid = mysql_fetch_array($shipidsearch)) {
		$numbertype = "shipmentid";
		$theshipid = $rshipid[0];		
	}
	
	else {
		
		$poidsearch = mysql_query("SELECT shipmentid, ponumber from shipment where shipment.ponumber = '$number'") or die (mysql_error());
		$numbertype = "ponumber";
		if ($rshipid = mysql_fetch_array($poidsearch)) {
			$theshipid = $rshipid[0];
			if ($rshipid = mysql_fetch_array($poidsearch)) {
				$multiplepo = 1;
			}
		}
		else {
			$noinfofound = 1;
		}
	}
	

	// get shipment info
	if ($noinfofound != 1 and $multiplepo != 1) {
	
		$shipmentquery = mysql_query("SELECT shipment.shipmentid, shipment.customerid, quotes.weight, quotes.class, shipment.units, shipment.deliveryest, shipment.origin, shipment.destination from shipment, quotes where shipment.quoteid = quotes.quoteid and shipment.$numbertype = '$number'") or die (mysql_error());
		$indtrackline = mysql_fetch_array($shipmentquery);
		$originquery = mysql_query("SELECT address.company from address where addressid = $indtrackline[origin]") or die (mysql_error());
		$originline = mysql_fetch_array($originquery);
		$destquery = mysql_query("SELECT address.company from address where addressid = $indtrackline[destination]") or die (mysql_error());
		$destline = mysql_fetch_array($destquery);
	
		// get tracking info
		$statusit = mysql_query("select statusdetails, statustime from shipmentstatus where shipmentid = $indtrackline[shipmentid] order by statustime DESC") or die (mysql_error());
		$statusline = mysql_fetch_array($statusit);
	}

	// query database for basic info
	//$tsql = "SELECT shipment.customerid, shipment.pickupdate, shipment.ponumber, shipment.shipmentid, shipment.units, shipment.productdescription, shipment.deliveryest, shipment.origin, shipment.destination, quotes.weight, quotes.class, quotes.transit from shipment, quotes where shipment.quoteid = quotes.quoteid and (shipment.ponumber = '$number' or shipment.shipmentid = '$number')";
	//$indtrack = mysql_query($tsql) or die (mysql_error());
	
	//if (!($indtrackingline = mysql_fetch_array($indtrack))) {
		//$noinfofound = 1;
	//}
	//else {
		//$originget = mysql_query("SELECT address.company from address where addressid = $indtrackline[origin]") or die(mysql_error());
		//$originline = mysql_fetch_row($originget);
		
		//$destget = mysql_query("SELECT address.company from address where addressid = $indtrackline[destination]") or die(mysql_error());
		//$destline = mysql_fetch_row($destget);
	//}

}

?>