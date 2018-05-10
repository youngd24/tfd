<?php
# =============================================================================
#
# zztracking.php
#
# Generates the full shipment and tracking list for a customer
#
# $Id: zztrackinglist.php,v 1.7 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zztrackinglist.php,v $
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


// if the time frame isn't specified, set it to the default

if (!$timeframedays or $timeframedays < 0) {
	$timeframedays = 14;
	$timeframedisp = "LAST $timeframedays DAYS";
}
else if ($timeframedays == 31) {
	$timeframedays = -1;
	$timeframedisp = "TODAY";
}
else if ($timeframedays == 32) {
	$timeframedays = 10000;
	$timeframedisp = "TOTAL HISTORY";
}
else {
	$timeframedisp = "LAST $timeframedays DAYS";
}



// figure out what the date was $timeframedaysago
$timeframedaysago = mktime (0,0,0,date("m"),date("d") - ($timeframedays + 1),  date("Y"));
$datecomp = strftime ("%Y%m%d", $timeframedaysago) . "000000";


// big query
$allships = Array();
$indx = 0;
$loopquery = mysql_query("select shipmentid from shipment where customerid = $userarray[0] and shipment.submitdate > $datecomp order by shipment.submitdate DESC") or die (mysql_error());
while ($theshpid = mysql_fetch_row($loopquery)) {

	$trackinglistquerysql = "select shipment.shipmentid, shipment.ponumber, shipment.units, shipment.submitdate, quotes.weight, quotes.class, quotes.transit, shipment.origin, shipment.destination from shipment, quotes where  shipment.quoteid = quotes.quoteid and shipment.customerid = $userarray[0] and shipment.shipmentid = $theshpid[0]";
	$trackingquery = mysql_query($trackinglistquerysql);
	$trackingline = mysql_fetch_array($trackingquery);
	$originquery = mysql_query("select company, city, state from address where addressid = $trackingline[origin]");
	$originline = mysql_fetch_array($originquery);
	$destquery = mysql_query("select company, city, state from address where addressid = $trackingline[destination]");
	$destline = mysql_fetch_array($destquery);
	$allships[$indx][0] = $trackingline[0]; 
	$allships[$indx][1] = $trackingline[1];
	$allships[$indx][2] = $trackingline[2];
	$allships[$indx][3] = $formatdate = substr($trackingline[3], 5,5);;
	$allships[$indx][4] = $trackingline[4];
	$allships[$indx][5] = $trackingline[5];
	$allships[$indx][6] = $trackingline[6];
	$allships[$indx][7] = $trackingline[7];
	$allships[$indx][8] = $trackingline[8];
	$allships[$indx][9] = $originline[0];
	$allships[$indx][10] = $originline[1];
	$allships[$indx][11] = $originline[2];
	$allships[$indx][12] = $destline[0];
	$allships[$indx][13] = $destline[1];
	$allships[$indx][14] = $destline[2];
	$indx++;

}
echo $allships[$indx][14];
?>