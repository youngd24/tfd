<?php
# =============================================================================
#
# zzreports.php
#
# Report generation page
#
# $Id: zzreports.php,v 1.6 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzreports.php,v $
# Revision 1.6  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.5  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");


// generates reports

if ($report == "outbound") {
	$i = 0;
	$rev = 0;
	$totaldays = 0;
	echo "<tr><td colspan=8 align=center><b><font size='+1'>Historical Outbound Activity Report</b></font></td></tr>";
	echo "<tr><Td><b>Ship Date</td><Td><b>Cust Name</td><Td><b>BOL #</b></td><Td><b>PO #</b></td><Td><b>Consignee</td><Td><b>Est Del Date</td><Td><b>Act Del Date</td><Td><b>Charges</td></tr>";
	echo "<tr height=1><td bgcolor=000000 colspan=8> </td></tr>";
// outbound activity report
	$reportsql = "SELECT shipment.shipmentid, shipment.pickupdate, shipment.ponumber, shipment.deliveryest, shipment.deliverdate, shipment.finalar, shipment.destination, shipment.origin from shipment where customerid = $userarray[0] and shipment.delivered = 1 order by shipment.pickupdate DESC"; 
	$report = mysql_query($reportsql) or die (mysql_error());
	while ($reportline = mysql_fetch_array($report)) {
		$originquery = mysql_query("SELECT company from address where addressid = $reportline[origin]");
		$originline = mysql_fetch_array($originquery);
		$destquery = mysql_query("SELECT company from address where addressid = $reportline[destination]");
		$destline = mysql_fetch_array($destquery);
		$formatest = substr($reportline[deliveryest], 5, 5) . '-' . substr($reportline[deliveryest], 0, 4);
		$formatact = substr($reportline[deliverdate], 5, 5) . '-' . substr($reportline[deliverdate], 0, 4);
		echo "<tr><td>$reportline[pickupdate]</td><td>$originline[company]</td><td>$reportline[shipmentid]</td> <td>$reportline[ponumber]</td><td>$destline[company]</td><td>$formatest</td> <td>$formatact </td><td>$$reportline[finalar]</td></tr>";
		$i++;
		$rev = $rev + $reportline[finalar];
	
	
		// this is the code to determine difference between pickupdate and deliverdate
		$days = 0;
		$m = 1;
		$loopstepdate = $reportline[deliverdate];
		$hidearray = explode("-", $loopstepdate);
		$delyear = $hidearray[0];
		$delmonth = $hidearray[1];
		$delday = $hidearray[2];
		while ($loopstepdate != $reportline[pickupdate]) {
			$heyday = date('l', mktime(0,0,0,$delmonth,($delday - $m),$delyear));
			$heymd = date('md', mktime(0,0,0,$delmonth,($delday - $m),$delyear));
			if (!($heyday == "Saturday" or $heyday == "Sunday" or $heymd == 1225 or $heymd == 0704 or $heymd == 0507 or $heymd == 1122)) {
				$days++;
			}
			$loopstepdate = date('Y-m-d', mktime(0,0,0,$delmonth,($delday-$m),$delyear));
			$m++;
		}

		$totaldays = $totaldays + $days;
		
		
	}
	//summary
	if ($i == 0) {
		echo "<tr><td colspan=8 align=center>YOU HAVE NO SHIPMENTS DURING THIS PERIOD</td></tr>";
	}
	if ($i != 0) {
		$avgdays = $totaldays / $i;
	}
	$avgdays = round($avgdays, 2);
	echo "<tr height=1><td bgcolor=000000 colspan=8> </td></tr>";
	echo "<Tr><td colspan=8 align=right><b>TOTAL SHIPMENTS: $i</b></td></tr>";
	echo "<Tr><td colspan=8 align=right><b>TOTAL REVENUE: $$rev</b></td></tr>";
	echo "<Tr><td colspan=8 align=right><b>AVERAGE TRANSIT DAYS: $avgdays</b></td></tr>";



}

else if ($report == "transit") {
	$i = 0;
	//get outbound
	echo "<tr><td colspan=7 align=center><b><font size='+1'>Historical Total Transit Report (Outbound & Inbound)</b></font></td></tr>";
	echo "<tr><td colspan=7><b><u>Outbound</u></b></font></td></tr>";
	echo "<tr><Td><b>Ship Date</td><Td><b>Origin</td><Td><b>BOL #</b></td><Td><b>PO #</b></td><Td><b>Consignee</td><td><b>Delivery Date</td><td><b>Days in Transit</td></tr>";
	echo "<tr height=1><td bgcolor=000000 colspan=7> </td></tr>";
	
	$reportsql = "SELECT shipment.shipmentid, shipment.pickupdate, shipment.ponumber, shipment.deliverdate,  shipment.destination, shipment.origin from shipment where customerid = $userarray[0] and shipment.delivered = 1 order by shipment.pickupdate DESC"; 
	$report = mysql_query($reportsql) or die (mysql_error());
	while ($reportline = mysql_fetch_array($report)) {
		$originquery = mysql_query("SELECT company from address where addressid = $reportline[origin]");
		$originline = mysql_fetch_array($originquery);
		$destquery = mysql_query("SELECT company from address where addressid = $reportline[destination]");
		$destline = mysql_fetch_array($destquery);
		$formatact = substr($reportline[deliverdate], 5, 5) . '-' . substr($reportline[deliverdate], 0, 4);
		$i++;
		$rev = $rev + $reportline[finalar];
	
	// this is the code to determine difference between pickupdate and deliverdate
		$days = 0;
		$m = 1;
		$loopstepdate = $reportline[deliverdate];
		$hidearray = explode("-", $loopstepdate);
		$delyear = $hidearray[0];
		$delmonth = $hidearray[1];
		$delday = $hidearray[2];
		
		while ($loopstepdate != $reportline[pickupdate]) {
			
			$heyday = date('l', mktime(0,0,0,$delmonth,($delday - $m),$delyear));
			$heymd = date('md', mktime(0,0,0,$delmonth,($delday - $m),$delyear));
			if (!($heyday == "Saturday" or $heyday == "Sunday" or $heymd == 1225 or $heymd == 0704 or $heymd == 0507 or $heymd == 1122)) {
				$days++;
			}
			$loopstepdate = date('Y-m-d', mktime(0,0,0,$delmonth,($delday-$m),$delyear));
			$m++;
		}
		
		$totaldays = $totaldays + $days;
		echo "<tr><td>$reportline[pickupdate]</td><td>$originline[company]</td><td>$reportline[shipmentid]</td> <td>$reportline[ponumber]</td><td>$destline[company]</td><td align=right>$formatact </td><td align=right>$days</tr>";
		
		
	}
	
	if ($i == 0) {
		echo "<tr><td colspan=8 align=center>YOU HAVE NO OUTBOUND SHIPMENTS DURING THIS PERIOD</td></tr>";
	}
	if ($i != 0) {
		$avgdays = $totaldays / $i;
	}
	$avgdays = round($avgdays, 2);
	echo "<tr height=1><td bgcolor=000000 colspan=7> </td></tr>";
	echo "<Tr><td colspan=7 align=right><b>AVERAGE TRANSIT DAYS: $avgdays</b></td></tr>";
	
	//get inbound
	$avgdays = 0;
	$i = 0;	
	$totaldays = 0;
	echo "<tr><td colspan=7><b><u>Inbound*</u></b></font></td></tr>";
	echo "<tr><Td><b>Ship Date</td><Td><b>Origin</td><Td><b>BOL #</b></td><Td><b>PO #</b></td><Td><b>Consignee</td><td><b>Delivery Date</td><td><b>Days in Transit</td></tr>";
	echo "<tr height=1><td bgcolor=000000 colspan=7> </td></tr>";

	$originslist = mysql_query("select addressid, company, address1, city, state, zip from address, shipment where address.custid = $userarray[0] and address.addressid = shipment.origin") or die (mysql_error());
	$compcheck = "";
	while ($anorigin = mysql_fetch_array($originslist)) {
		
		$compcheck = $compcheck . "(address.company = '$anorigin[company]' and address.address1 = '$anorigin[address1]' and address.city = '$anorigin[city]' and address.state = '$anorigin[state]' and address.zip = '$anorigin[zip]') or "; 
	}
	if ($compcheck != "") {
		$compcheck = substr($compcheck, -0, -3);
		$simsql = "SELECT addressid from address where custid != $userarray[0] and ($compcheck)";
		$simquery = mysql_query($simsql) or die (mysql_error());
	}
	
	
	$compcheckfinal = "";
	if ($simquery) {
	while ($simline = mysql_fetch_array($simquery)) {
		$compcheckfinal = $compcheckfinal . "shipment.destination = $simline[0] or "; 
	}
	}
	$compcheckfinal = substr($compcheckfinal, -0, -3);
 	
	if ($compcheckfinal != "") {
		$reportsql = "SELECT shipment.shipmentid, shipment.pickupdate, shipment.ponumber, shipment.deliverdate,  shipment.destination, shipment.origin from shipment where customerid != $userarray[0] and ($compcheckfinal) and shipment.delivered = 1 order by shipment.pickupdate DESC";
		$report = mysql_query($reportsql) or die (mysql_error());
	
	while ($reportline = mysql_fetch_array($report)) {
		$originquery = mysql_query("SELECT company from address where addressid = $reportline[origin]");
		$originline = mysql_fetch_array($originquery);
		$destquery = mysql_query("SELECT company from address where addressid = $reportline[destination]");
		$destline = mysql_fetch_array($destquery);
		$formatact = substr($reportline[deliverdate], 5, 5) . '-' . substr($reportline[deliverdate], 0, 4);
		$i++;
		$rev = $rev + $reportline[finalar];
	
	// this is the code to determine difference between pickupdate and deliverdate
		$days = 0;
		$m = 1;
		$loopstepdate = $reportline[deliverdate];
		$hidearray = explode("-", $loopstepdate);
		$delyear = $hidearray[0];
		$delmonth = $hidearray[1];
		$delday = $hidearray[2];
		
		while ($loopstepdate != $reportline[pickupdate]) {
			
			$heyday = date('l', mktime(0,0,0,$delmonth,($delday - $m),$delyear));
			$heymd = date('md', mktime(0,0,0,$delmonth,($delday - $m),$delyear));
			if (!($heyday == "Saturday" or $heyday == "Sunday" or $heymd == 1225 or $heymd == 0704 or $heymd == 0507 or $heymd == 1122)) {
				$days++;
			}
			$loopstepdate = date('Y-m-d', mktime(0,0,0,$delmonth,($delday-$m),$delyear));
			$m++;
		}
		
		$totaldays = $totaldays + $days;
		echo "<tr><td>$reportline[pickupdate]</td><td>$originline[company]</td><td>$reportline[shipmentid]</td> <td>$reportline[ponumber]</td><td>$destline[company]</td><td align=right>$formatact </td><td align=right>$days</tr>";
		
		
	}
	}

	if ($i == 0) {
		echo "<tr><td colspan=8 align=center>YOU HAVE NO INBOUND SHIPMENTS DURING THIS PERIOD</td></tr>";
	}
	if ($i != 0) {
		$avgdays = $totaldays / $i;
	}
	$avgdays = round($avgdays, 2);
 	echo "<tr height=1><td bgcolor=000000 colspan=7> </td></tr>";
	echo "<Tr><td colspan=7 align=right><b>AVERAGE TRANSIT DAYS: $avgdays</b></td></tr>";
	echo "<tr><td colspan=7><i>*NOTE: Only shipments booked via thefreightdepot.com will be listed</i></td></tr>";
}

else if ($report == "revenue") {
	$i=0;
	 //get this date
	 if ($month && $year) {
	 	$today = $year . "-" . $month;
	 	$hidearray = explode("-", $today);
	 }
	 else {
		 $today = date('Y-m-d', time());
		 $hidearray = explode("-", $today);
	 }
	 
	 $todayforsql = $hidearray[0] . "-" . $hidearray[1] . "%";
	 
	 //generate top menu
	 $thisyear = date('Y', time());
	 $thismonth = date('m', time());
	 $j = 0;
	 echo "<tr><td colspan=6><table width='100%'><tr><td><a href=reports.php?report=revenue&month=01&year=2002 class=links_general>JAN</a> | <a href=reports.php?report=revenue&month=02&year=2002 class=links_general>FEB</a> | <a href=reports.php?report=revenue&month=03&year=2002 class=links_general>MAR</a> | <a href=reports.php?report=revenue&month=04&year=2002 class=links_general>APR</a> | <a href=reports.php?report=revenue&month=05&year=2002 class=links_general>MAY</a> | <a href=reports.php?report=revenue&month=06&year=2002 class=links_general>JUN</a> | <a href=reports.php?report=revenue&month=07&year=2002 class=links_general>JUL</a> | <a href=reports.php?report=revenue&month=08&year=2002 class=links_general>AUG</a> | <a href=reports.php?report=revenue&month=09&year=2002 class=links_general>SEP</a> | <a href=reports.php?report=revenue&month=10&year=2002 class=links_general>OCT</a> | <a href=reports.php?report=revenue&month=11&year=2002 class=links_general>NOW</a> | <a href=reports.php?report=revenue&month=12&year=2002 class=links_general>DEC</a></td></tr></table></td></tr>";
	 

	 
	 $report = mysql_query("SELECT shipment.pickupdate, shipment.origin, shipment.destination, shipment.ponumber, shipment.shipmentid, shipment.finalar from shipment where customerid = $userarray[0] and pickupdate like '$todayforsql' order by pickupdate DESC") or die(mysql_error());
	echo "<tr><td colspan=6 align=center><b><font size='+1'>Monthly Revenue Report For $hidearray[1]-$hidearray[0]</b></font></td></tr>";
	echo "<tr><Td><b>Ship Date</td><Td><b>Origin</td><Td><b>BOL #</b></td><Td><b>PO #</b></td><Td><b>Consignee</td><td><b>Charges</td></tr>";
	echo "<tr height=1><td bgcolor=000000 colspan=6> </td></tr>";
	while ($reportline = mysql_fetch_array($report)) {
		$originquery = mysql_query("SELECT company from address where addressid = $reportline[origin]") or die(mysql_error());
		$originline = mysql_fetch_array($originquery);
		$destquery = mysql_query("SELECT company from address where addressid = $reportline[destination]") or die(mysql_error());
		$destline = mysql_fetch_array($destquery);
		$formatest = substr($reportline[deliveryest], 5, 5) . '-' . substr($reportline[deliveryest], 0, 4);
		$formatact = substr($reportline[deliverdate], 5, 5) . '-' . substr($reportline[deliverdate], 0, 4);
		echo "<tr><td>$reportline[pickupdate]</td><td>$originline[company]</td><td>$reportline[shipmentid]</td> <td>$reportline[ponumber]</td><td>$destline[company]</td><td>$$reportline[finalar]</td></tr>";
		$i++;
		$rev = $rev + $reportline[finalar];
			
	}
	if ($i == 0) {
		echo "<tr><td colspan=6 align=center>YOU HAVE NO SHIPMENTS DURING THIS PERIOD</td></tr>";
	}
	
	//summary
	echo "<tr height=1><td bgcolor=000000 colspan=6> </td></tr>";
	echo "<Tr><td colspan=6 align=right><b>TOTAL REVENUE: $$rev</b></td></tr>";
	
}

?>
