<?php
# =============================================================================
#
# functions.php
#
# Shared functions
#
# $Id: functions.php,v 1.24 2003/02/14 18:34:47 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: functions.php,v $
# Revision 1.24  2003/02/14 18:34:47  youngd
#   * Added getAccessorialListing().
#   * Added getAccessorialNameByRefcode().
#
# Revision 1.23  2003/02/10 20:34:15  youngd
#   * Added getAccessorialCharge and getAccessorialId fields.
#
# Revision 1.22  2003/01/16 22:30:39  webdev
#   * Added getOfficeInfo().
#
# Revision 1.21  2002/12/12 20:06:37  webdev
#   * Added getMinimumUpgrade
#   * Added getCarrierMinimum
#
# Revision 1.20  2002/12/12 00:01:36  webdev
#   * Added getCarrierUpgrade function.
#
# Revision 1.19  2002/12/11 21:28:40  webdev
#   * updates
#
# Revision 1.18  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.17.2.3  2002/11/08 22:05:49  webdev
#   * Added comments and docs.
#
# Revision 1.17.2.2  2002/11/08 22:01:42  webdev
#   * Added isDefaultBillingSet
#   * Added getConfigValue
#
# Revision 1.17.2.1  2002/11/08 18:14:10  webdev
#   * Added new addChange() function.
#
# Revision 1.17  2002/11/03 10:07:34  youngd
#   * Added getDefaultCustomerMargin function.
#
# Revision 1.16  2002/11/03 09:11:09  youngd
#   * Added isValidCustomer and changePassword functions
#
# Revision 1.15  2002/10/30 18:15:50  youngd
#   * Added getCustomerNameByEmail.
#
# Revision 1.14  2002/10/30 17:01:20  youngd
#   * Added getCustomerPasswordByEmail function.
#   * Added isValidEmail function.
#
# Revision 1.13  2002/10/29 19:08:21  youngd
#   * Added documentation and debug to new functions
#
# Revision 1.12  2002/10/28 22:51:52  youngd
#   * Working version of fax page
#
# Revision 1.11  2002/10/15 18:46:57  youngd
#   * Added getSecurityLevel functions.
#
# Revision 1.10  2002/10/15 07:21:12  youngd
#   * Added docs.
#
# Revision 1.9  2002/10/15 06:54:32  youngd
#   * Added several admin methods.
#
# Revision 1.8  2002/10/14 12:03:53  youngd
#   * Added getShipmentCarrier() and getCarrierNameById methods.
#
# Revision 1.7  2002/10/11 20:05:18  youngd
#   * Reworked accessorials which work now.
#
# Revision 1.6  2002/10/10 23:03:46  youngd
#   * Added the initial versions of the code to override the margin for
#     a customer based on the carrier selected.
#
# Revision 1.5  2002/10/10 22:43:25  youngd
#   * Added getCustomerMarginForCarrier() function.
#
# Revision 1.4  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.3  2002/10/09 18:21:11  youngd
#   * Added getCarrierFuelSurcharge function.
#
# Revision 1.2  2002/09/16 23:32:17  youngd
#   * AddedgetQuoteCarrierId function
#
# Revision 1.1  2002/09/16 07:58:39  webdev
#   * Many updates
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");





# -----------------------------------------------------------------------------
# NAME        : getCustomerMargin
# DESCRIPTION : Returns the margin for a given customer
# ARGUMENTS   : string custid
# RETURN      : string margin
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerMargin ( $custid ) {
    
    debug("functions.php: Entering getCustomerMargin()");

    if ( ! $custid ) {
        logerr("SYNTAX", "Param 'custid' not passed to getCustomerMargin()");
        return(0);
    } else {
        error_log("DEBUG: getCustomerMargin() received: custid: $custid", 0);
        $query = "SELECT margin from customers where custid = '$custid'"or die (mysql_error());
        $result = mysql_query($query) or die (mysql_error());
        debug("functions.php: getCustomerMargin(): RESULT: $result");
        $margin = mysql_fetch_row($result);
        return($margin[0]);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getDefaultCustomerMargin
# DESCRIPTION : Returns the default customer margin for new customers
# ARGUMENTS   : None
# RETURN      : string margin
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getDefaultCustomerMargin () {
    
    debug("functions.php: Entering getDefaultCustomerMargin()");

    if ($result = mysql_query("SELECT defaultcustomermargin from digiship") ) {
        $margin = mysql_fetch_row($result);
        debug("functions.php: getDefaultCustomerMargin(): got default customer margin of $margin[0]");
        return($margin[0]);
    } else {
        $errmsg = mysql_error();
        debug("functions.php: getDefaultCustomerMargin(): could NOT retrieve default customer margin");
        debug("functions.php: getDefaultCustomerMargin(): mysql error: $errmsg");
        return(0);
    }

    debug("functions.php: getDefaultCustomerMargin(): something bad happened");
    return(0);
}


# -----------------------------------------------------------------------------
# NAME        : getMinimumUpgrade
# DESCRIPTION : Returns the minimum upgrade from the digiship table
# ARGUMENTS   : None
# RETURN      : string(upgrade)
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getMinimumUpgrade () {
    
    debug("functions.php: Entering getMinimumUpgrade()");

    $query = mysql_query("SELECT minimumupgrade FROM digiship");
    $result = mysql_fetch_row($query);
	
	if ( $result[0] != "" ) {
	    debug("functions.php: getMinimumUpgrade(): returning minimum upgrade of $result[0]");
		return($result[0]);
	} else {
	    debug("functions.php: getMinimumUpgrade(): nothing on file, returning 0");
		return(false);
	}
}


# -----------------------------------------------------------------------------
# NAME        : getCarrierDiscount
# DESCRIPTION : Returns the discount for a given carrier
# ARGUMENTS   : string carrierid
# RETURN      : string discount
# STATUS      : Under Development
# NOTES       : This needs to be changed to account for tiered discounts
# -----------------------------------------------------------------------------
function getCarrierDiscount ( $carrierid ) {
    
    debug("functions.php: Entering getCarrierDiscount()");

    if ( ! $carrierid ) {
        logerr("SYNTAX", "Param 'carrierid' not passed to getCarrierDiscount()");
        return(0);
    } else {
        error_log("functions.php: getCarrierDiscount() received: carrierid: $carrierid", 0);
        $query = "SELECT discount from carriers where carrierid = '$carrierid'"or die (mysql_error());
        $result = mysql_query($query) or die (mysql_error());
        debug("RESULT: $result");
        $discount = mysql_fetch_row($result);
        return($discount[0]);
    }
}



# -----------------------------------------------------------------------------
# NAME        : getCarrierMinimum
# DESCRIPTION : Returns the minimum for a given carrier
# ARGUMENTS   : string carrierid
# RETURN      : string discount
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCarrierMinimum ( $carrierid ) {
    
    debug("functions.php: Entering getCarrierMinimum()");

    if ( ! $carrierid ) {
        logerr("SYNTAX", "Param 'carrierid' not passed to debug()");
        return(0);
    } else {
        error_log("functions.php: getCarrierMinimum() received: carrierid: $carrierid", 0);
        $query = "SELECT minimum from carriers where carrierid = '$carrierid'"or die (mysql_error());
        $result = mysql_query($query) or die (mysql_error());
        debug("functions.php: getCarrierMinimum(): RESULT: $result");
        $discount = mysql_fetch_row($result);
        return($discount[0]);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getCustomerIdByEmail
# DESCRIPTION : Returns the customer id for a given customer
# ARGUMENTS   : string email
# RETURN      : string custid
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerIdByEmail ( $email ) {
    
    debug("functions.php: Entering getCustomerIdByEmail()");

    if ( ! $email ) {
        logerr("SYNTAX", "Param 'email' not passed to getCustomerIdByEmail()");
        return(0);
    } else {
        error_log("functions.php: getCustomerIdByEmail() received: email: $email", 0);
        $query = "SELECT custid from customers where email like '%$email%'"or die (mysql_error());
        debug("functions.php: getCustomerIdByEmail(): QUERY: $query");
        $result = mysql_query($query) or die (mysql_error());
        debug("functions.php: getCustomerIdByEmail(): RESULT: $result");
        $custid = mysql_fetch_row($result);
        return($custid[0]);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getCustomerIdByName
# DESCRIPTION : Returns the customer id for a given customer
# ARGUMENTS   : string name
# RETURN      : string custid
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerIdByName ( $name ) {
    
    debug("functions.php: Entering getCustomerIdByName()");

    if ( ! $name ) {
        logerr("SYNTAX", "Param 'name' not passed to getCustomerIdByName()");
        return(0);
    } else {
        error_log("functions.php:  getCustomerIdByName() received: name: $name", 0);
        $query = "SELECT custid from customers where name like '%$name%'"or die (mysql_error());
        debug("functions.php: getCustomerIdByName(): QUERY: $query");
        $result = mysql_query($query) or die (mysql_error());
        debug("functions.php: getCustomerIdByName(): RESULT: $result");
        $custid = mysql_fetch_row($result);
        return($custid[0]);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getCustomerPassword
# DESCRIPTION : Returns the password for a given customer
# ARGUMENTS   : string custid
# RETURN      : string password
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerPassword ( $custid ) {
    
    debug("functions.php: Entering getCustomerPassword()");

    if ( ! $custid ) {
        logerr("SYNTAX", "Param 'custid' not passed to getCustomerPassword()");
        return(0);
    } else {
        error_log("functions.php: getCustomerPassword() received: custid: $custid", 0);
        $query = "SELECT password from customers where custid = $custid" or die (mysql_error());
        debug("functions.php: getCustomerPassword(): QUERY: $query");
        $result = mysql_query($query) or die (mysql_error());
        debug("functions.php: getCustomerPassword(): RESULT: $result");
        $password = mysql_fetch_row($result);
        return($password[0]);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getQuoteCarrierId
# DESCRIPTION : Returns the carrier id for a given quote
# ARGUMENTS   : string quoteid
# RETURN      : string carrierid
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getQuoteCarrierId ( $quoteid ) {
    
    debug("functions.php: Entering getQuoteCarrierId()");

    if ( ! $quoteid ) {
        logerr("SYNTAX", "Param 'quoteid' not passed to getQuoteCarrierId()");
        return(0);
    } else {
        error_log("functions.php: getQuoteCarrierId() received: quoteid: $quoteid", 0);
        $query = "SELECT carrierid from quotes where quoteid = $quoteid" or die (mysql_error());
        debug("functions.php: getQuoteCarrierId(): QUERY: $query");
        $result = mysql_query($query) or die (mysql_error());
        debug("functions.php: getQuoteCarrierId(): RESULT: $result");
        $row = mysql_fetch_row($result);
        return($row[0]);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getQuoteFuelSurcharge
# DESCRIPTION : Returns the surcharge for a given carrier
# ARGUMENTS   : string carrierid
# RETURN      : string surcharge
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCarrierFuelSurcharge ($carrierid) {

    debug("functions.php: Entering getCarrierFuelSurcharge()");
    $surcharge_query = mysql_query("select fuel_surcharge from carriers where carrierid=$carrierid");

    if ( $fuel_surcharge = mysql_fetch_row($surcharge_query) ) {
		debug("functions.php: getCarrierFuelSurcharge(): got carriers surcharge as $fuel_surcharge");
		return($fuel_surcharge[0]);

    } else {
        debug("functions.php:getCarrierFuelSurcharge(): unable to retrieve fuel surcharge for carrier $carrierid");
		return(0);

    }

}


# -----------------------------------------------------------------------------
# NAME        : getCustomerMarginForCarrier
# DESCRIPTION : Returns the margin for a given customer and carrier
# ARGUMENTS   : string custid, string carrierid
# RETURN      : string margin
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerMarginForCarrier ($custid, $carrierid) {

    debug("functions.php: Entering getCustomerMarginForCarrier()");

    $margin_query = mysql_query("SELECT margin FROM customer_margins WHERE custid=$custid AND carrierid=$carrierid");

    if ( $margin = mysql_fetch_row($margin_query) ) {
		debug("functions.php: getCustomerMarginForCarrier(): got customer $custid margin for carrier $carrierid as $margin[0]");
		return($margin[0]);
    } else {
        debug("functions.php: getCustomerMarginForCarrier(): no margin for customer $custid via carrier $carrierid");
		return(0);
    }

}


# -----------------------------------------------------------------------------
# NAME        : addAccessorialToShipment
# DESCRIPTION : Adds a record to the shipmentaccessorials table
# ARGUMENTS   : string shipmentid, stringassid
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function addAccessorialToShipment ($shipmentid, $assid) {

    debug("functions.php: Entering addAccessorialToShipment()");
    $query = "INSERT INTO shipmentaccessorials VALUES ($shipmentid, $assid)";

    if ( mysql_query($query) ) {
		debug("functions.php: addAccessorialToShipment(): added accessorial id $assid to shipment $shipmentid");
		return(1);
    } else {
        debug("functions.php: addAccessorialToShipment(): couldn't add accessorial id $assid to shipment $shipmentid");
		return(0);
    }

}



# -----------------------------------------------------------------------------
# NAME        : getShipmentCarrier
# DESCRIPTION : Returns the carrier assigned to a shipment
# ARGUMENTS   : string carrierid
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------

function getShipmentCarrier ($shipmentid) {
    
    debug("functions.php: Entering getShipmentCarrier()");
    $query = mysql_query("SELECT carrierid FROM shipment WHERE shipmentid=$shipmentid");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getShipmentCarrier(): got carrierid $result[0] for shipment $shipmentid");
        return($result[0]);
    } else {
        debug("functions.php: getShipmentCarrier(): didn't get a carrier for shipment $shipmentid");
        return(0);
    }

}


# -----------------------------------------------------------------------------
# NAME        : getShipmentPickupDate
# DESCRIPTION : Returns the pickup date for a shipment
# ARGUMENTS   : string shipmentid
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getShipmentPickupDate ($shipmentid) {

    debug("functions.php: Entering getShipmentPickupDate()");
    $query = mysql_query("SELECT pickupdate FROM shipment WHERE shipmentid=$shipmentid");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getShipmentPickupDate(): got pickupdate $result[0] for shipment $shipmentid");
        return($result[0]);
    } else {
        debug("functions.php: getShipmentPickupDate(): didn't get a pickupdate for shipment $shipmentid");
        return(0);
    }

}



# -----------------------------------------------------------------------------
# NAME        : getCarrierNameById
# DESCRIPTION : Returns the name of a carrier based on the carrierid
# ARGUMENTS   : string carrierid
# RETURN      : string carrier_name
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCarrierNameById ($carrierid) {

    debug("functions.php: entering getCarrierNameById()");
    $query = mysql_query("SELECT name FROM carriers WHERE carrierid=$carrierid");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getCarrierNameById(): got carrier name $result[0] for carrier $carrierid");
        return($result[0]);
    } else {
        debug("functions.php: getCarrierNameById(): didn't get a carrier name for carrier $carrierid");
        return(0);
    }
}



# -----------------------------------------------------------------------------
# NAME        : getAdminIdByName
# DESCRIPTION : Returns the custid for an admin based on their unix username
# ARGUMENTS   : string username
# RETURN      : string custid
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAdminIdByName ($username) {

    debug("functions.php: entering getAdminIdByName()");
    $query = mysql_query("SELECT custid FROM admins WHERE username = $username");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getAdminIdByName(): got admin id of $result[0] for $username");
        return($result[0]);
    } else {
        debug("functions.php: getAdminIdByName(): didn't get id for $username");
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getAdminNameById
# DESCRIPTION : Returns the name for an admin based on their custid
# ARGUMENTS   : string custid
# RETURN      : string username
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAdminNameById ($custid) {

	debug("functions.php: entering getAdminNameById()");
    $query = mysql_query("SELECT username FROM admins WHERE custid = $custid");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getAdminNameById(): got admin name of $result[0] for $custid");
        return($result[0]);
    } else {
        debug("functions.php: getAdminNameById(): didn't get username for $custid");
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getAdminSecurityLevelByName
# DESCRIPTION : Returns the admin security level based on their username
# ARGUMENTS   : string username
# RETURN      : string security_level
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAdminSecurityLevelByName ($username) {

    debug("functions.php: entering getAdminSecurityLevelByName()");
    $query = mysql_query("SELECT level FROM admins WHERE username = $username");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getAdminSecurityLevelByName(): got level of $result[0] for $username");
        return($result[0]);
    } else {
        debug("functions.php: getAdminSecurityLevelByName(): didn't get level for $username");
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getAdminSecurityLevelById
# DESCRIPTION : Returns the admin security level based on their custid
# ARGUMENTS   : string custid
# RETURN      : string security_level
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAdminSecurityLevelById ($custid) {

    debug("functions.php: entering getAdminSecurityLevelById()");
    $query = mysql_query("SELECT level FROM admins WHERE custid = $custid");

    if ( $result = mysql_fetch_row($query) ) {
        debug("functions.php: getAdminSecurityLevelById(): got level of $result[0] for $custid");
        return($result[0]);
    } else {
        debug("functions.php: getAdminSecurityLevelByName(): didn't get level for $custid");
        return(0);
    }
}



# -----------------------------------------------------------------------------
# NAME        : getTerminalFaxNumber
# DESCRIPTION : Returns the fax number associated with an origin terminal
# ARGUMENTS   : string carrierid, string originzip
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getTerminalFaxNumber ($carrierid, $originzip) {

	debug("functions.php: entering getTerminalFaxNumber()");
    $codeqry = mysql_query("select termcode from zips where carrierid=$carrierid and zip=$originzip");

    if ( $result = mysql_fetch_row($codeqry) ) {
		debug("functions.php: getTerminalFaxNumber(): got termcode $result[0] for carrier $carrierid and zip $originzip");
		$faxqry = mysql_query("select fax from terminals where carrierid=$carrierid and code=$result[0]");

		if ( $faxresult = mysql_fetch_row($faxqry) ) {
			debug("functions.php: getTerminalFaxNumber(): got fax $faxresult[0] for carrier $carrierid and code $result[0]");
			return($faxresult[0]);
		}

    }

}


# -----------------------------------------------------------------------------
# NAME        : getTerminalPhoneNumber
# DESCRIPTION : Returns the phone number associated with an origin terminal
# ARGUMENTS   : string carrierid, string originzip
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getTerminalPhoneNumber ($carrierid, $originzip) {

	debug("functions.php: entering getTerminalPhoneNumber()");
    $codeqry = mysql_query("select termcode from zips where carrierid=$carrierid and zip=$originzip");

    if ( $result = mysql_fetch_row($codeqry) ) {
		debug("functions.php: getTerminalPhoneNumber(): got termcode $result[0] for carrier $carrierid and zip $originzip");
		$phoneqry = mysql_query("select phone from terminals where carrierid=$carrierid and code=$result[0]");

		if ( $phoneresult = mysql_fetch_row($phoneqry) ) {
			debug("functions.php: getTerminalPhoneNumber(): got phone $phoneresult[0] for carrier $carrierid and code $result[0]");
			return($phoneresult[0]);
		} else {
			debug("functions.php: getTerminalPhoneNumber(): didn't get phone for carrier $carrierid and code $result[0]");
			return("N/A");
		}
    } else {
		debug("functions.php: getTerminalPhoneNumber(): didn't get termcode for carrier $carrierid and zip $originzip");
		return("N/A");
	}

}



# -----------------------------------------------------------------------------
# NAME        : getShipmentOriginZip
# DESCRIPTION : Returns the origin zip for a shipment
# ARGUMENTS   : string shipmentid
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getShipmentOriginZip ($shipmentid) {

	debug("functions.php: entering getShipmentOriginZip()");
	$query = mysql_query("SELECT origin FROM shipment WHERE shipmentid=$shipmentid");
    
	if ( $result = mysql_fetch_row($query) ) {

		debug("functions.php: getShipmentOriginZip(): got origin addressid of $result[0] for shipment $shipmentid");
		$addrqry = mysql_query("SELECT zip FROM address WHERE addressid=$result[0]");

		if ( $org = mysql_fetch_row($addrqry) ) {
			debug("functions.php: getShipmentOriginZip(): got origin zip of $org[0] for shipment $shipmentid");
			return($org[0]);
		} else {
			debug("functions.php: getShipmentOriginZip(): didn't get origin zip for shipment $shipmentid");
			return(0);
		}
    } else {
		debug("functions.php: getShipmentOriginZip(): didn't get origin addressid for shipment $shipmentid");
        return(0);
	}
}


# -----------------------------------------------------------------------------
# NAME        : isValidEmail
# DESCRIPTION : Checks to see if a customer email is a valid one
# ARGUMENTS   : string email
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function isValidEmail ($email) {

	debug("functions.php: entering isValidEmail() with argument $email");
	$query = mysql_query("SELECT email FROM customers WHERE email='$email'");

	if ( $result = mysql_fetch_row($query) ) {
		debug("functions.php: isValidEmail(): email $email is valid and stored as $result[0]");
		return(1);
	} else {
		debug("functions.php: isValidEmail(): email $email is not valid");
		return(0);
	}
}


# -----------------------------------------------------------------------------
# NAME        : getCustomerPasswordByEmail
# DESCRIPTION : Finds the customer's password based on their email address
# ARGUMENTS   : string email
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerPasswordByEmail ($email) {

	debug("functions.php: entering getCustomerPasswordByEmail() with argument $email");

	# Make sure it's a valid email address
	debug("functions.php: getCustomerPasswordByEmail(): making sure $email is a valid one");
	if ( isValidEmail($email) ) {
		debug("functions.php: getCustomerPasswordByEmail(): $email is valid");
		$query = mysql_query("SELECT password FROM customers WHERE email='$email'");

		if ( $result = mysql_fetch_row($query) ) {
			debug("functions.php: getCustomerPasswordByEmail(): email $email has password of XXXXXXX");
			return($result[0]);
		} else {
			debug("functions.php: getCustomerPasswordByEmail(): could NOT get password for email $email, huh?");
			return(0);
		}
	} else {
		debug("functions.php: getCustomerPasswordByEmail(): $email is NOT valid");
		return(0);
	}
}


# -----------------------------------------------------------------------------
# NAME        : getCustomerNameByEmail
# DESCRIPTION : Finds the customer's name based on their email address
# ARGUMENTS   : string email
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCustomerNameByEmail ($email) {

	debug("functions.php: entering getCustomerNameByEmail() with argument $email");

	# Make sure it's a valid email address
	debug("functions.php: getCustomerNameByEmail(): making sure $email is a valid one");
	if ( isValidEmail($email) ) {
		debug("functions.php: getCustomerNameByEmail(): $email is valid");
		
		$query = mysql_query("SELECT name FROM customers WHERE email='$email'");
		if ( $result = mysql_fetch_row($query) ) {
			debug("functions.php: getCustomerNameByEmail(): email $email has password of XXXXXXX");
			return($result[0]);
		} else {
			debug("functions.php: getCustomerNameByEmail(): could NOT get password for email $email, huh?");
			return(0);
		}
	} else {
		debug("functions.php: getCustomerNameByEmail(): $email is NOT valid");
		return(0);
	}
}



# -----------------------------------------------------------------------------
# NAME        : isValidCustomer
# DESCRIPTION : Checks to see of a customer id os valid
# ARGUMENTS   : string custid
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function isValidCustomer ($custid) {
    debug("functions.php: entering isValidCustomer() with arguments $custid");

    if ( ! $custid ) {
        syntaxerror("functions.php: isValidCustomer(): param custid not given");
        return(0);
    }

	$query = mysql_query("SELECT custid FROM customers WHERE custid='$custid'");
	$result = mysql_fetch_row($query) or htmlerror("Query in isValidCustomer() failed");

    if ( $result[0] != "" ) {
        debug("functions.php: isValidCustomer(): custid $custid is valid");
        return(1);
    } else {
        debug("functions.php: isValidCustomer(): custid $custid is not valid");
        return(0);
    }
}



# -----------------------------------------------------------------------------
# NAME        : changePassword
# DESCRIPTION : Change a customer password
# ARGUMENTS   : string custid, string password
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function changePassword ($custid, $password) {
    debug("functions.php: entering changePassword() with arguments $custid, $password");

    if ( ! $custid ) {
        syntaxerror("functions.php: changePassword(): param custid not given");
        return(0);
    }

    if ( ! $password ) {
        syntaxerror("functions.php: changePassword(): param password not given"); 
        return(0);
    }

    // Make sure they gave us a valid customer, if so, move on.
    if ( isValidCustomer($custid) ) {

        // Update the password for the customer record
        debug("functions.php: changePassword(): updating password for custid $custid");
        if ( $query = mysql_query("UPDATE customers SET password='$password' WHERE custid=$custid") ) {
            debug("functions.php: changePassword(): updated password for custid $custid");
            return(1);
        } else {
            debug("functions.php: changePassword(): update of password for custid $custid failed");
            return(0);
        }
    } else {
        debug("functions.php: changePassword(): custid $custid is NOT valid");
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : addChange
# DESCRIPTION : Add a system change message
# ARGUMENTS   : string user, string message
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function addChange ($user, $message) {
	debug("functions.php: entering addChange() with arguments user=$user, message=$message");

    if ( ! $user ) {
        syntaxerror("functions.php: addChange(): param user not given");
        return(0);
    }

    if ( ! $message ) {
        syntaxerror("functions.php: addChange(): param message not given"); 
        return(0);
    }

	$timenow = getdate();
	$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];

	if ( $qry = mysql_query("INSERT INTO changes (date, user, message) VALUES ('$thetime', '$user', '$message')") ) {
		debug("functions.php: addChange(): added new change, user=$user, message=$message");
		return(1);
	} else {
		debug("functions.php: addChange(): FAILED to add new change, user=$user, message=$message");
		return(0);
	}
}


# -----------------------------------------------------------------------------
# NAME        : isDefaultBillingSet
# DESCRIPTION : Checks to see if a customer's default billing address is set
# ARGUMENTS   : string custid
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function isDefaultBillingSet($custid) {
	debug("functions.php: entering isDefaultBillingSet() with arguments custid=$custid");

    if ( ! $custid ) {
        syntaxerror("functions.php: isDefaultBillingSet(): param custid not given");
        return(0);
    }

	if ( $qry = mysql_query("SELECT default_billing FROM customers WHERE custid=$custid") ) {
		$result = mysql_fetch_row($qry);
		if ( $result[0] != "" ) {
			debug("functions.php: isDefaultBillingSet(): default_billing for custid $custid is set");
			return(1);
		} else {
			debug("functions.php: isDefaultBillingSet(): default_billing for custid $custid is NOT set");
			return(0);
		}
	}
}


# -----------------------------------------------------------------------------
# NAME        : getConfigValue
# DESCRIPTION : Returns the value of a config variable
# ARGUMENTS   : string val
# RETURN      : 0 or 1
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getConfigValue ($val) {

	debug("functions.php: entering getConfigValue() with arguments val=$val");

    if ( ! $val ) {
        syntaxerror("functions.php: getConfigValue(): param val not given");
        return(0);
    }

	// Try to open the app config file, it's in the include_path (or should be)
	if ( ! $cfgfile = fopen("tfd.cfg", "r", true) ) {
		htmlerror("Error reading app configuration file, please contact customer support");
		die();
	}

	while (! feof ($cfgfile)) {

		// Trim the newlines off while we read into the buffer
		$buffer = rtrim(fgets($cfgfile, 4096));

		// Skip comments, check the rest
		// Comments can only be at the beginning of the line
		if ( ereg("^#", $buffer)) {
			next;
		} elseif ( ereg("$val", $buffer) ) {
			debug("functions.php: getConfigValue(): HIT VALUE (buffer==$buffer)");
			$cfg = explode("=", $buffer);
			debug("functions.php: getConfigValue(): VAL ($val) = $cfg[1]");
			return($cfg[1]);
		}
	}

	fclose($cfgfile);
}


# -----------------------------------------------------------------------------
# NAME        : getUpgradeForCarrier
# DESCRIPTION : Returns the upgrade amount for a specific carrier
# ARGUMENTS   : string(carrierid)
# RETURN      : string(amount) or 0 if not set
# STATUS      : Under Development
# NOTES       : The actual amount could be 0
# -----------------------------------------------------------------------------
function getUpgradeForCarrier ($carrierid) {
	debug("functions.php: entering getUpgradeForCarrier() with arguments carrierid=$carrierid");

    if ( ! $carrierid ) {
        syntaxerror("functions.php: getUpgradeForCarrier(): param carrierid not given");
        return(0);
    }

	if ( $qry = mysql_query("SELECT upgrade FROM carriers WHERE carrierid=$carrierid") ) {
		$result = mysql_fetch_row($qry);
		if ( $result[0] != "" ) {
			debug("functions.php: getUpgradeForCarrier(): returning $result[0]");
			return($result[0]);
		} else {
			debug("functions.php: getUpgradeForCarrier(): no upgrade for carrier $carrierid found, returning 0");
			return(0);
		}
	}
}


# -----------------------------------------------------------------------------
# NAME        : getCompanyInfo
# DESCRIPTION : Returns an array of all the office information
# ARGUMENTS   : None
# RETURN      : Array officeInfo
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getCompanyInfo () {

    debug("functions.php: entering getCompanyInfo()");

    $query = mysql_query("SELECT * FROM digiship");
    $result = mysql_fetch_array($query);

	$companyInfo = array(
							'mainphone'             => $result[mainphone],
							'faxphone'              => $result[faxphone],
							'minimumupgrade'        => $result[minimumupgrade],
							'defaultcustomermargin' => $result[defaultcustomermargin],
							'companyname'           => $result[companyname],
							'address1'              => $result[address1],
							'address2'              => $result[address2],
							'city'                  => $result[city],
							'state'                 => $result[state],
							'zip'                   => $result[zip],
							'tollfree'              => $result[tollfree],
							'carrieremail'          => $result[carrieremail],
							'csremail'              => $result[csremail],
							'billingemail'          => $result[billingemail],
							'webmasteremail'        => $result[webmasteremail]
						);

	return($companyInfo);
}


# -----------------------------------------------------------------------------
# NAME        : getAccessorialCharge
# DESCRIPTION : Return the charge associated with a carrier's service
# ARGUMENTS   : string(carrierid), string(refcode)
# RETURN      : string(charge) or 0
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAccessorialCharge ($carrierid, $refcode) {

	debug("functions.php: entering getAccessorialCharge($carrierid, $refcode)");

	$query = mysql_query("SELECT * FROM accessorials WHERE carrierid='$carrierid' AND refcode='$refcode'");
	$result = mysql_fetch_row($query);

	if ( $result[2] ) {
		debug("functions.php: getAccessorialCharge(): price is $result[2]");
		return($result[2]);
	} else {
		debug("functions.php: getAccessorialCharge(): NO PRICE ON FILE FOR ($carrierid, $refcode) UH OH!");
		return(0);
	}
}


# -----------------------------------------------------------------------------
# NAME        : getAccessorialId
# DESCRIPTION : Returns the internal ID associated with a carrier's service
# ARGUMENTS   : string(carrierid), string(refcode)
# RETURN      : string(id)
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAccessorialId ($carrierid, $refcode) {

	debug("functions.php: entering getAccessorialId($carrierid, $refcode)");

	$query = mysql_query("SELECT assid FROM accessorials WHERE carrierid='$carrierid' AND refcode='$refcode'");
	$result = mysql_fetch_row($query);

	if ( $result[0] ) {
		debug("functions.php: getAccessorialId(): price is $result[0]");
		return($result[0]);
	} else {
		debug("functions.php: getAccessorialId(): NO ACCESSORIAL ON FILE FOR ($carrierid, $refcode) UH OH!");
		return(0);
	}
}



function getAccessorialNameByRefcode ($refcode) {

	debug("functions.php: entering getAccessorialNameByRefcode($refcode)");

	$query = mysql_query("SELECT name FROM accessorials WHERE refcode='$refcode'");
	$result = mysql_fetch_row($query);

	if ( $result[0] ) {
		debug("functions.php: getAccessorialNameByRefcode(): name is $result[0]");
		return($result[0]);
	} else {
		debug("functions.php: getAccessorialNameByRefcode(): NO ACCESSORIAL ON FILE FOR ($refcode) UH OH!");
		return(0);
	}
}


# -----------------------------------------------------------------------------
# NAME        : getAccessorialListing
# DESCRIPTION : Returns an array of all of the current accessorial codes
# ARGUMENTS   : None
# RETURN      : Array(refcodes)
# STATUS      : Under Development
# NOTES       : None
# -----------------------------------------------------------------------------
function getAccessorialListing () {

	debug("functions.php: entering getAccessorialListing()");

	$query = mysql_query("SELECT DISTINCT(refcode) FROM accessorials");

	while ($result = mysql_fetch_row($query)) {
		$refcode[] = $result[0];
	}

	return $refcode;

}

?>
