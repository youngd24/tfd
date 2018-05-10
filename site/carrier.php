<?php
# =============================================================================
#
# carrier.php
#
# Carrier Object & methods
#
# $Id: carrier.php,v 1.2 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: carrier.php,v $
# Revision 1.2  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.1  2002/09/16 08:27:44  webdev
#   * New file
#
# =============================================================================

	require_once("config.php");
	require_once("debug.php");
	require_once("error.php");
	require_once("event.php");
	require_once("functions.php");
	require_once("logging.php");

    debug("New carrier object");


    class carrier {
    
        function carrier() {

    		return true;
        }


        function setCarrierId($carrierid) {

            debug("Entering carrier::setCarrierId");

            $this->carrierid = $carrieid;

            debug("carrier::setCarrierId(): carrier->custid set to $carrierid");

            return true;
        }


        function add() {
            return true;
        }    
    
    }
?>