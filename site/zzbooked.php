<?php
# =============================================================================
#
# zzbooked.php
#
# Previously booked shipment page
#
# $Id: zzbooked.php,v 1.14 2003/01/24 18:49:26 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzbooked.php,v $
# Revision 1.14  2003/01/24 18:49:26  webdev
#   * Added new pallet and qty info to the shipment insertion.
#
# Revision 1.13  2003/01/06 22:19:36  webdev
#   * Added the 204sent field to the insert into shipment operation.
#
# Revision 1.12  2002/12/10 19:51:44  webdev
#   * Changed emails to be at the freight depot
#
# Revision 1.11  2002/10/25 19:59:44  youngd
#   * Fuel surcharges are now inserted into the shipment record.
#
# Revision 1.10  2002/10/25 19:41:22  youngd
#   * Accessorial amounts are correctly commited to the shipment record.
#
# Revision 1.9  2002/10/14 16:39:26  youngd
#   * Added mail to harry on new shipment booking.
#
# Revision 1.8  2002/10/11 20:05:18  youngd
#   * Reworked accessorials which work now.
#
# Revision 1.7  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.6  2002/10/04 16:22:25  youngd
# done for darren.
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

debug("zzbooked.php: entering after initial erquires");

if ($digishiptransaction) {

	// retrieve current time and assign to $timenow
	$timenow = getdate();
	$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];
	
	// parse the cookie
	// the structure looks like this:
	// origin,destination,quoteid,carrierid,billing,finalar,finalap,
	// pickupdate,pickupbefore,ponumber,productdesc,pieces,hazmatyesno,
	// hazmatemergencyphone,pickupafter,spci,fuel_surcharge,packagingtype,palletized,palletqty
	
	$transarray = explode ("|", $digishiptransaction);

	debug("zzbooked.php: total charges here is $totalcharges");
	debug("zzbooked.php: total cost here is $totalcost");
	
	debug("zzbooked.php: fuel surcharge here is $transarray[17]");

	debug("zzbooked.php: finalar here is $transarray[5]");
	debug("zzbooked.php: finalap here is $transarray[6]");

	// lets check some things before we insert
	// has this quote already been booked?
	$beenbooked = mysql_query("SELECT * from quotes where quoteid = '$transarray[2]' and booked = 1") or die (mysql_error());
	
	if ($beenbookedline = mysql_fetch_row($beenbooked)) {
		$beenbookedcheck = 1;
	}
	else {
		$beenbookedcheck = 0;

		$fuel_surcharge = $transarray[17];

		$finalar = $transarray[5] + $fuel_surcharge;
		$finalap = $transarray[6] + $fuel_surcharge;
		debug("zzbooked.php: packaging type here is $transarray[18]");
		debug("zzbooked.php: palletized here is $transarray[19]");
		debug("zzbooked.php: palletqty here is $transarray[20]");
	
		//insert into shipment table
		$sqlstring = "INSERT INTO shipment (origin, destination, quoteid, customerid, carrierid, billing, finalar, finalap, pickupdate, submitdate, pickupbefore, ponumber, productdescription, units, hazmat, hazmatphone, pickupafter, deliveryest, specialinstructions, 204sent, packagingtype, palletized, palletqty) VALUES ('$transarray[0]', '$transarray[1]', '$transarray[2]', '$userarray[0]', '$transarray[3]', '$transarray[4]', '$finalar', '$finalap', '$transarray[7]', '$thetime', '$transarray[8]', '$transarray[9]', '$transarray[10]', '$transarray[11]', '$transarray[12]', '$transarray[13]', '$transarray[14]', '$transarray[15]', '$transarray[16]',0, '$transarray[18]', '$transarray[19]', '$transarray[20]')";
		$shipinsert = mysql_query($sqlstring) or die (mysql_error());
		$shipmentid = mysql_insert_id();

		if ($shipmentid) {
	
			// update the quotes table to make sure it isn't saved any more
			$saveupdates = mysql_query("UPDATE quotes set save = 0, booked = 1 where quoteid = '$transarray[2]' and customerid = '$userarray[0]'") or die (mysql_error());
			
			// Could send the EDI 204 document right here probably if the need for a real-time transaction comes up
			$docarrierb = mysql_query("UPDATE shipment set carrierbooked = 1 where shipmentid = $shipmentid");			

			// insert into initial tracking status
			$shipstatus = mysql_query("INSERT INTO shipmentstatus (shipmentid, statusdetails, statuscode, statustime) VALUES ('$shipmentid', 'DISPATCHING', '1', '$thetime')") or die (mysql_error());

		
			// get the shipment info for the mail message
			// old sql statement-$mailquerysql = "select shipment.finalar, shipment.submitdate, shipment.pickupdate, shipment.pickupbefore, origin.company, origin.address1, origin.address2, origin.city, origin.state, origin.zip, destination.company, destination.address1, destination.address2, destination.city, destination.state, destination.zip, quotes.class, quotes.weight, quotes.transit, carriers.name, carriers.scac, shipment.billing, shipment.ponumber, shipment.deliveryest from shipment, origin, destination, quotes, carriers where shipment.origin = origin.origin and shipment.destination = destination.destination and quotes.quoteid = shipment.quoteid  and carriers.carrierid = shipment.carrierid and shipment.shipmentid = $shipmentid";
			// get shipment info
			$mailquerysql = "select shipment.finalar, shipment.submitdate, shipment.pickupdate, shipment.pickupbefore, shipment.origin, shipment.destination, quotes.class, quotes.weight, quotes.transit, carriers.name, carriers.scac, shipment.billing, shipment.ponumber, shipment.deliveryest from shipment, quotes, carriers where quotes.quoteid = shipment.quoteid  and carriers.carrierid = shipment.carrierid and shipment.shipmentid = $shipmentid";
			
			$mailquery = mysql_query($mailquerysql) or die(mysql_error());
			$mailqueryline = mysql_fetch_row($mailquery);
			
			// let's get the addresses too
			//origin
			$originquery = mysql_query("select company, address1, address2, city, state, zip from address where addressid = $mailqueryline[4]") or die(mysql_error());
			$originline = mysql_fetch_row($originquery);
			$origincompany = $originline[0];
			$originaddress1 = $originline[1];
			$originaddress2 = $originline[2];
			$origincity = $originline[3];
			$originstate = $originline[4];
			$originzip = $originline[5];

			//destination
			$destquery = mysql_query("select company, address1, address2, city, state, zip from address where addressid = $mailqueryline[5]") or die(mysql_error());
			$destline = mysql_fetch_row($destquery);
			$destcompany = $destline[0];
			$destaddress1 = $destline[1];
			$destaddress2 = $destline[2];
			$destcity = $destline[3];
			$deststate = $destline[4];
			$destzip = $destline[5];

			//billing
			$billquery = mysql_query("select company, address1, address2, city, state, zip, contact from address where addressid = $mailqueryline[11]") or die(mysql_error());
			$billline = mysql_fetch_row($billquery);
			$billingcompany = $billline[0];
			$billingaddress1 = $billline[1];
			$billingaddress2 = $billline[2];
			$billingcity = $billline[3];
			$billingstate = $billline[4];
			$billingzip = $billline[5];
			$billingname = $billline[6];
				
		
			// lets modify the date and time fields for better display
			$newpickupdatearray = explode("-", $mailqueryline[2]);
			$newpickupdate = $newpickupdatearray[1] . "/" . $newpickupdatearray[2] . "/" . $newpickupdatearray[0];
			$newpickuptimearray = explode(":", substr($mailqueryline[3], 0, 5));
			if ($newpickuptimearray[0] == 0) {
				$newpickuptimehour = 12;
				$newpickuptimeampm = "am";
			}
			elseif ($newpickuptimearray[0] < 12) {
				$newpickuptimehour = $newpickuptimearray[0];
				$newpickuptimeampm = "am";
			}
			elseif ($newpickuptimearray[0] == 12) {
				$newpickuptimehour = $newpickuptimearray[0];
				$newpickuptimeampm = "pm";
			}
			elseif ($newpickuptimearray[0] > 12) {
				$newpickuptimehour = $newpickuptimearray[0] - 12;
				$newpickuptimeampm = "pm";
			}
			$newpickuptime = $newpickuptimehour . ":" . $newpickuptimearray[1] . $newpickuptimeampm;
	
			
			if ($mailqueryline[12] == "") {
				$numberstring = "number " . $shipmentid;
			}
			else {
				$numberstring = "for PO Number '$mailqueryline[12]'";
			}
			
			$mailmessage = "$userarray[1],\n\nThis is an automated message notifying you that your shipment $numberstring has been scheduled for pickup by $mailqueryline[9] on $newpickupdate before $newpickuptime. The information below details this shipment:\n\nORIGIN\n$origincompany\n$originaddress1 $originaddress2\n$origincity, $originstate $originzip\n\nDESTINATION\n$destcompany\n$destaddress1 $destaddress2\n$destcity, $deststate $destzip\n\nBILLING\n$billingcompany\n$billingaddress1 $billingaddress2\n$billingcity, $billingstate $billingzip\n\nSHIPMENT INFORMATION\nWeight: $mailqueryline[7]\nClass: $mailqueryline[6]\nTransit Time: $mailqueryline[8] days\nScheduled On: $mailqueryline[1]\n\nESTIMATED DELIVERY DATE: $mailqueryline[13]\n\nTOTAL CHARGES: $$mailqueryline[0]\n\n\n\nTo track this shipment, just point your web browser to http://www.thefreightdepot.com/tracking.php?shipmentid=$shipmentid";
			
			mail("$userarray[2]", "SHIPMENT #$shipmentid HAS BEEN BOOKED!", $mailmessage, "FROM: Shipments@TheFreightDepot.com");
			
			$tempmailmessage = "SHIPMENT $shipmentid\n$mailqueryline[9] on $newpickupdate\nbefore $newpickuptime\n$origincompany\n$userarray[1]\n$originaddress1, $originaddress2, $origincity, $originstate $originzip\n$userarray[10]\nTO: $destcity, $deststate\n$transarray[11] pcs, $mailqueryline[7] lbs, class $mailqueryline[6]";

			mail("hpavlos@thefreightdepot.com", "NEW SHIPMENT: $shipmentid. GO MAKE US SOME MONEY!", $tempmailmessage, "FROM: shipments@thefreightdepot.com");

			// insert into accounting
			require('zzaccounting.php');
			// build customer array
			//define the userarray	
			
			$auserarray[name] = addslashes($billingname);
			$auserarray[company] = addslashes($billingcompany);
			$auserarray[address1] = addslashes($billingaddress1);
			$auserarray[address2] = addslashes($billingaddress2);
			$auserarray[city] = addslashes($billingcity);
			$auserarray[state] = addslashes($billingstate);
			$auserarray[zip] = addslashes($billingzip);
			$auserarray[phone] = "";
							   
			// open the postgres connection
			$conn = postgresconnect();

			// add customer if they don't exist
			$addresult = addcustomer($conn, $auserarray);


			// build $trans array
			// we need some more info
			$accquerysql = "select shipment.carrierid, shipment.finalap from shipment where shipment.shipmentid = $shipmentid";
			$accquery = mysql_query($accquerysql) or die(mysql_error());
			$accqueryline = mysql_fetch_array($accquery);
			
			$trans[customerid] = $addresult;
			$trans[vendorid] = $accqueryline[0];
			$trans[amount] = $mailqueryline[0];
			$trans[netamount] = $trans[amount];
			$trans[poamount] = $accqueryline[1];
			$trans[ponetamount] = $trans[poamount];
				$timenow = getdate();
				$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'];
			$trans[reqdate] = $mailqueryline[2];
			$trans[taxincluded] = "t";
			$trans[ordernumber] = "$shipmentid";
			$trans[curr] = "USD";
			$oitems[transid] = "";
			$oitems[partsid] = 10138;
			$oitems[description] = "$mailqueryline[7] lbs, class $mailqueryline[6], $originzip to $destzip";
			$oitems[qty] = 1;
			$oitems[sellprice] = $trans[amount];
			$oitems[posellprice] = $trans[poamount];
			$oitems[discount] = 0;
			// accounting function calls
			// add transaction
			addtransaction($trans, $oitems, $conn);

			// close the postgres connection
			$closeit = postgresclose($conn);

			#
			# Deal with the accessorials
			#
			if (isset($CALL_FOR_PICKUP)) {
				debug("zzbooked.php: CALL_FOR_PICKUP is set");
				$assid = 15;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($CALL_BEFORE_DELIVERY)) {
				debug("zzbooked.php: CALL_BEFORE_DELIVERY is set");
				$assid = 17;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($LIFTGATE_ORIGIN)) {
				debug("zzbooked.php: LIFTGATE_ORIGIN is set");
				$assid = 1;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($LIFTGATE_DESTINATION)) {
				debug("zzbooked.php: LIFTGATE_DESTINATION is set");
				$assid = 3;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($RESIDENTIAL_PICKUP)) {
				debug("zzbooked.php: RESIDENTIAL_DELIVERY is set");
				$assid = 5;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($INSIDE_PICKUP)) {
				debug("zzbooked.php: INSIDE_PICKUP is set");
				$assid = 7;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($INSIDE_DELIVERY)) {
				debug("zzbooked.php: INSIDE_DELIVERY is set");
				$assid = 9;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($HAZMAT)) {
				debug("zzbooked.php: HAZMAT is set");
				$assid = 11;
				addAccessorialToShipment($shipmentid, $assid);
			}

			if ( isset($RESIDENTIAL_DELIVERY)) {
				debug("zzbooked.php: RESIDENTIAL_PICKUP is set");
				$assid = 13;
				addAccessorialToShipment($shipmentid, $assid);
			}
		}
	}


// kill the cookie
setcookie("digishiptransaction", "", 0, "/", "", 0);
// cookie domain = ".digiship.com"          here

}

debug("zzbooked.php: leaving");

?>
