<?php
# ==============================================================================
#
# shipment.php
#
# Shipment Class
#
# $Id: shipment.php,v 1.1 2002/12/10 22:02:39 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
#
# $Log: shipment.php,v $
# Revision 1.1  2002/12/10 22:02:39  webdev
#   * Initial version.
#
# ==============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");


Class Shipment {


	var $shipmentid;
	var $quoteid;
	var $origin;
	var $destination;

	function Shipment () {

		return(true);
	}


	function BookShipment () {

	}

}

?>