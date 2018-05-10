<?php
# =============================================================================
#
# zzgetprice.php
#
# Determine markup and discounts
#
# $Id: zzgetprice.php,v 1.25 2003/02/06 21:04:49 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzgetprice.php,v $
# Revision 1.25  2003/02/06 21:04:49  youngd
#   * Changed from getdate() to date() for $thetime.
#
# Revision 1.24  2003/01/29 20:27:13  webdev
#   * Changed Roadway discounts.
#
# Revision 1.23  2002/12/12 20:05:55  webdev
#   * Added use of carrier specific upgrades.
#
# Revision 1.22  2002/12/12 00:02:04  webdev
#   * General code cleanup. Added spaces and returns. Added some comments.
#
# Revision 1.21  2002/12/11 21:28:21  webdev
#   * Added the bubblegum to deal with Roadway's discounting. This is a temporary fix
#     until more proper discounting is added.
#
# Revision 1.20  2002/12/10 19:51:44  webdev
#   * Changed emails to be at the freight depot
#
# Revision 1.19  2002/10/30 23:15:51  youngd
#   Test Version
#
# Revision 1.18  2002/10/30 20:37:02  youngd
#   * Testing a direct post.
#
# Revision 1.17  2002/10/10 23:03:46  youngd
#   * Added the initial versions of the code to override the margin for
#     a customer based on the carrier selected.
#
# Revision 1.16  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.15  2002/10/04 21:58:25  youngd
#   * Working on margin & pricing changes still.
#
# Revision 1.14  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.13  2002/10/03 22:10:44  youngd
#   * Added tons of debug lines.
#
# Revision 1.12  2002/10/03 19:01:17  youngd
#   * Added additional debug entries.
#
# Revision 1.11  2002/10/02 22:14:41  youngd
#   * Added lots of debug statements.
#
# Revision 1.10  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.9  2002/09/19 20:47:57  youngd
#   * Changed harry's email to be the new one at aol
#
# Revision 1.8  2002/09/16 22:48:09  youngd
#   * Added Harry's email
#
# Revision 1.7  2002/09/16 07:58:39  webdev
#   * Many updates
#
# Revision 1.6  2002/09/15 07:27:53  webdev
#   * Added source header
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("zzgetprice.php: entering after initial requires");

// fyi - baserate of -1 means no carrier, baserate of -2 means com error

$cantservice = 0;
$truckload = 0;
// retrieve current time and assign to $timenow

// getdate() sucks. Jeff liked that one for some reason.
//$timenow = getdate();
//$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];

// Much better...
$thetime = date('Y-m-d H:i:s'); 


debug("zzgetprice.php: cantservice = $cantservice");
debug("zzgetprice.php: truckload = $truckload");
debug("zzgetprice.php: thetime = $thetime");


// select all carriers with either zip code
// if we've already been passed a predefined carrier
if (isset($precarrierid)) {
	debug("zzgetprice.php: precarrierid given to us as $precarrierid");
	$carrierquery = mysql_query("select carriers.carrierid, carriers.name, carriers.discount, carriers.scac, carriers.description, carriers.type, carriers.minimum, carriers.ccscac from carriers where carrierid = $precarrierid") or die (mysql_error());
}
else {
	debug("zzgetprice.php: selecting carrier based on database information");
	$carrierquery = mysql_query("select carriers.carrierid, carriers.name, carriers.discount, carriers.scac, carriers.description, carriers.type, carriers.minimum, carriers.ccscac, zips.carrierid, zips.zip, zips2.carrierid, zips2.zip from carriers, zips, zips2 where carriers.carrierid = zips.carrierid and carriers.carrierid = zips2.carrierid and zips2.carrierid = zips.carrierid and zips.zip = $origin and zips2.zip = $destination order by carriers.type ASC, carriers.discount DESC") or die(mysql_error());
}


// check to see if we got a carrier
if ($thecarrier = mysql_fetch_row($carrierquery) and $weight <= 20000) {

	debug("zzgetprice.php: thecarrier = $thecarrier[0] ($thecarrier[1])");
	
	// Set carrierid
	$carrierid = $thecarrier[0];
	debug("zzgetprice.php: carrierid set to $carrierid");

	
	# -------------------------------------------------------------------------
	#                   U P G R A D E   A M O U N T
	# -------------------------------------------------------------------------
	// XXX - OLD CODE - DAY
	// grab the default upgrade from the digishiptable
	// $getupgrade = mysql_query("select minimumupgrade from digiship");
	// $upgradenumber = mysql_fetch_row($getupgrade);

	$minimum_upgrade = getMinimumUpgrade();
	debug("zzgetprice.php: got minimum upgrade from settings table of $minimum_upgrade");

	$carrier_upgrade = getUpgradeForCarrier($carrierid);
	debug("zzgetprice.php: upgrade from carriers table for carrierid $carrierid is $carrier_upgrade");

	// If the carrier specific upgrade is more than the minimum...
	if ( $carrier_upgrade > $minimum_upgrade ) {
		// Use that
		$upgradenumber[0] = $carrier_upgrade;
		debug("zzgetprice: carrier upgrade ($carrier_upgrade) is more than the minimum ($minimum_upgrade), using that.");
	} else {
		// Otherwise, use the minimum
		$upgradenumber[0] = $minimum_upgrade;
		debug("zzgetprice: minimum upgrade ($minimum_upgrade) is more than the carrier upgrade ($carrier_upgrade), using that.");
	}

	debug("zzgetprice.php: minimum upgrade set to $upgradenumber[0]");
    

	# -------------------------------------------------------------------------
	#                  C A R R I E R   D I S C O U N T
	# -------------------------------------------------------------------------

	// Deal with Roadway's bullshit here

	// If the carrier is Roadway, set some special stuff
	if ( $carrierid == 10000 ) {
	
		debug("zzgetprice.php: Dealing with the Roadway weight discount thing.");
		// If the weight is less than or equal to 500 lbs, set the discount to 32.8%
		
		// 1 to 499 = 32.8%
		if ( $weight <= 499 ) {

			$thecarrier[2] = 32.8;
			$discount = 1 - ($thecarrier[2] / 100);
			debug("zzgetprice.php: Roadway shipment weight is less than 500 lbs, discount set to $discount ($thecarrier[2]%)");

		// 500 to 9999 = 52.9%
		} elseif ( $weight >= 500 && $weight <= 9999 ) {
		
			$thecarrier[2] = 52.9;
			$discount = 1 - ($thecarrier[2] / 100);
			debug("zzgetprice.php: Roadway shipment weight is more than 500 lbs but less than 10000, discount set to $discount ($thecarrier[2]%)");
		
		// 10000 to 19999 = 49%
		} elseif ( $weight >= 10000 && $weight <= 19999 ) {

			$thecarrier[2] = 49.0;
			$discount = 1 - ($thecarrier[2] / 100);
			debug("zzgetprice.php: Roadway shipment weight is more than 10000 lbs but less than 20000, discount set to $discount ($thecarrier[2]%)");		
		}
	
	// Everyone else gets the discount set from the carrier table
	} else {
		$discount = 1 - ($thecarrier[2] / 100);
		debug("zzgetprice.php: carrier discount set to $discount ($thecarrier[2]%) from carrier table");
	}
	

	// go to czar to get base price and carrierconnect to get the transit time
	debug("zzgetprice.php: loading zzrateclient.php");
	require ('zzrateclient.php');
	debug("zzgetprice.php: done loading zzrateclient.php, baseprice is " . sprintf('%01.2f', $baseprice));
	
	// if zzrateclient returns a $0 rate
	if ($baseprice == 0.00) {
		$baseprice = -2;
		$ap = -2;
		$ar = -2;
	}

	// if zzrate client sent back a valid rate!
	else if ($baseprice != "ERROR") {
		// look for and override margin in the carrier table
		// fyi - the next chunk of code can override this...
		$getormargin = mysql_query("SELECT ormargin from carriers where carrierid = $carrierid") or die (mysql_error());
		$ormargin = mysql_fetch_array($getormargin);
		debug("zzgetprice.php: ormargin set to $ormargin[0]");

		if ($ormargin[0] > -1) {
			debug("zzgetprice.php: ormargin[0] is > -1: userarray[9] was $userarray[9] is now $ormargin[0]");
			$userarray[9] = $ormargin[0];
		}


		# ------------------------------------------------------------------------------
		#                C U S T O M E R  /  C A R R I E R  M A R G I N
		# ------------------------------------------------------------------------------
		#
		# Get the customer margin for the given carrier
		# This will override all other pricing logic.
		#
		# We added this so we can raise the rate on a given customer for selected carriers.
		# That data is stored in the customer_margin table by custid and carrierid
		#
		# See the function.php page for the function code
		#
		$cust_carrier_margin = getCustomerMarginForCarrier($userarray[0], $carrierid);
		debug("zzgetrprice.php: call to getCustomerMarginForCarrier returned $cust_carrier_margin");

		# If that value is greater than 0, use it for the customer mnargin
		if ( $cust_carrier_margin > 0 ) {
			debug("zzgetprice.php: overriding ALL margins with $cust_carrier_margin");
			$userarray[9] = $cust_carrier_margin;
		}

		
		// determine our ap and ar
		$ap = round(($baseprice * $discount), 2);
		$discamt = $baseprice * ($thecarrier[2] / 100);
		debug("zzgetprice.php: initial ap (baseprice - discount [" . sprintf('%01.2f',$discamt) . "]) set to " . sprintf('%01.2f', $ap) . " from rounding");

		// if our ap is less than the carrier minimum
		if ($ap < $thecarrier[6]) {
			debug("zzgetprice.php: our ap is less than the carrier minimum so ap is now $thecarrier[6]");
			$ap = $thecarrier[6];
			
			// assign temp var current ap plus the default upgrade
			$artemp = $ap + $upgradenumber[0];
			debug("zzgetprice.php: artemp is now $artemp");
			
			// if the temp var is less than the current ap + customer margin assign ar the value of the customermargin
			
			if ($artemp < ($ap * (1 + ($userarray[9] / 100)))) {
				debug("zzgetprice.php: artemp is less than the current ap + customer margin, using customer margin");
				$ar = round(($ap * (1 + ($userarray[9] / 100))), 2);
				debug("zzgetprice.php: ar is now $ar after rounding");
			}
			// else use the temp price
			else {
				$ar = $artemp;
				debug("zzgetprice.php: using temp price so ar is now $ar");
			}
		}
		// ap is more than the minimum, but check to see if the ar + the default upgrade is greater than the ap * the margin
		else {
			// if the ap + the customer margin is greater than the ap + the upgrade, us the margin
			if (($ap * (1 + ($userarray[9] / 100))) > ($ap + $upgradenumber[0])) {
				$one = round($ap * (1 + ($userarray[9] / 100)),2);
				$two = round($ap + $upgradenumber[0],2);
				debug("zzgetprice.php: ap ($ap) + the customer margin ($userarray[9]) [$one] is greater than the ap ($ap) + the upgrade ($upgradenumber[0]) [$two], using the margin");
				$ar = round(($ap * (1 + ($userarray[9] / 100))), 2);
				debug("zzgetprice.php: ar is now $ar");
			}
			else {
				$ar = ($ap + $upgradenumber[0]);
				debug("zzgetprice.php (line 153): ar is now $ar");
			}
		}
		

        # -----------------------------------------------------------------------------
        #                        F U E L   S U R C H A R G E 
        # -----------------------------------------------------------------------------
        #
        # The fuel surcharge is stored on a per carrier basis in the database table
        # 'carriers'. The value there is a percentage (%) that each carrier posts,
        # typically once a week, that is the current percentage of the baseprice that
        # will be billed for fuel. This percentage is based on the information available
        # at the US Department of Energy's website.
        #
        # From what I have seen, the DOE updates their information weekly on Monday 
        # afternoon. Most carriers update their information the Wednesday just after
        # the DOE posts their findings.
        #
        # To determin the surcharge amount, take the baserate and subtract the discount 
        # amount. Take the remainder and multiply it times the surcharge percent (as a 
        # decimal).

        debug("zzgetprice.php: going to get carrier's fuel surcharge");

        $surcharge_query = mysql_query("select fuel_surcharge from carriers where carrierid=$carrierid");

        if ( $fuel_surcharge = mysql_fetch_row($surcharge_query) ) {

            $surcharge = sprintf('%01.2f', round($ar * ($fuel_surcharge[0] / 100),2));

            debug("zzgetprice.php: got fuel surcharge of $fuel_surcharge[0]% for carrier $carrierid");
            debug("zzgetprice.php: fuel surcharge based on the ar of $ar is $surcharge");

        } else {

            debug("zzgetprice.php: unable to retrieve fuel surcharge for carrier $carrierid");

        }
	
		
		
	}

	// if the base rate returned = 0, then we errored in zzratreclient
	else {
		debug("zzgetprice.php: zzrateclient errored so baseprice is now -2, ar is -2 and ap is -2");
		$baseprice = -2;
		$ap = -2;
		$ar = -2;
	}
}

// if we didn't get one
else {
	if ($weight > 20000) {
		debug("zzgetprice.php: the weight is greater than 2000 so it's a truckload");
		$theorigincitysql = mysql_query("SELECT city, state from zipcitystate where zip = $origin");
		$theorigincity = mysql_fetch_array($theorigincitysql);
		$thedestinationcitysql = mysql_query("SELECT city, state from zipcitystate where zip = $destination");
		$thedestinationcity = mysql_fetch_array($thedestinationcitysql);
		$mmess = "THIS IS A TRUCKLOAD RATE REQUEST\n\nFROM: $userarray[1], $userarray[3], $userarray[10]\n\n$theorigincity[0], $theorigincity[1] $origin - $thedestinationcity[0], $thedestinationcity[1] $destination - $weight lbs - Class $shipclass\n\n";
		mail("tjuedes@thefreightdepot.com,hpavlos@thefreightdepot.com","Truckload request",$mmess,"FROM: $userarray[2]");
		debug("zzgetprice.php: truckload email request $mmes sent");
		$truckload = 1;
		$carrierid = -3;
		$baseprice = "0";
		$ap = "0";
		$ar = "0";
		$transit = "0";
		debug("zzgetprice.php: truckload = $truckload");
		debug("zzgetprice.php: carrierid = $carrierid");
		debug("zzgetprice.php: baseprice = $baseprice");
		debug("zzgetprice.php: ap = $ap");
		debug("zzgetprice.php: ar = $ar");
		debug("zzgetprice.php: transit = $transit");
		debug("zzgetprice.php: cantservice = $cantservice");		
	} else {
		$carrierid = -1;
		$baseprice = -1;
		$ap = -1;
		$ar = -1;
		$transit = -1;
		$cantservice = 1;
		debug("zzgetprice.php: truckload = $truckload");
		debug("zzgetprice.php: carrierid = $carrierid");
		debug("zzgetprice.php: baseprice = $baseprice");
		debug("zzgetprice.php: ap = $ap");
		debug("zzgetprice.php: ar = $ar");
		debug("zzgetprice.php: transit = $transit");
		debug("zzgetprice.php: cantservice = $cantservice");
	}
}


// insert into quotes table
debug("zzgetprice.php: adding record to quotes");
$sqlstring = "INSERT INTO quotes (customerid, carrierid, origin, destination, weight, class, baserate, ap, ar, date, save, transit, fuel_surcharge) VALUES ($userarray[0], $carrierid, '$origin', '$destination', $weight, $shipclass, $baseprice, $ap, $ar, '$thetime', 0, '$transit', '$surcharge')";

debug("zzgetprice.php: quote sql: $sqlstring");

$insertquote = mysql_query($sqlstring) or debug("zzgetprice.php: " . mysql_error());

// If the quote addition went ok...
if ( $insertquote ) {
	debug("zzgetprice.php: insertquote passed");
} else {
    // It didn't get inserted...
	debug("zzgetprice.php: insertquote failed for some reason");
}


$quoteid = mysql_insert_id();
debug("zzgetprice.php: quoteid is $quoteid");

$ardisplay = sprintf('%01.2f', $ar);

debug("zzgetprice.php: final ar here is " . sprintf('%01.2f', $ar));
debug("zzgetprice.php: final ap here is " . sprintf('%01.2f', $ap));

debug("zzgetprice.php: leaving");

?>
