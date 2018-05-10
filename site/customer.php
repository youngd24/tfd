<?php
# =============================================================================
#
# customer.php
#
# Customer Object & methods
#
# $Id: customer.php,v 1.3 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: customer.php,v $
# Revision 1.3  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.2  2002/09/16 08:27:44  webdev
#   * New file
#
# =============================================================================

	// Bring in our standard includes
	require_once("config.php");
	require_once("debug.php");
	require_once("error.php");
	require_once("event.php");
	require_once("functions.php");
	require_once("logging.php");

    debug("New customer object");


    class customer {
    
        function customer() {
        
            return true;
        }


        function setCustId($custid) {

            debug("Entering customer::setCustId");

            $this->custid = $custid;

            debug("customer::setCustId(): customer->custid set to $custid");

            return true;
        }


        function add() {
            return true;
        }    
    
    }
?>