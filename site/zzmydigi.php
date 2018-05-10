<?php
# =============================================================================
#
# zzmydigi.php
#
# Personal includes page
#
# $Id: zzmydigi.php,v 1.6 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzmydigi.php,v $
# Revision 1.6  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.5  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.4  2002/09/13 01:45:42  webdev
#   * Added requires
#   * Cleaned up sql statements
#   * Added source header
#   * Converted to UNIX format
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

    // sql select 5 recent shipments
    $getrecentshipments = mysql_query("select address.company, 
                                              shipment.shipmentid, 
                                              shipment.ponumber 
                                              FROM address, shipment 
                                              WHERE address.addressid = shipment.destination 
                                              AND shipment.customerid = $userarray[0] 
                                              ORDER BY shipment.submitdate DESC limit 5") 
                                              or die (mysql_error());

?>
