<?php
# =============================================================================
#
# error.php
#
# Error handling methods
#
# $Id: error.php,v 1.12 2002/11/18 18:07:26 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: error.php,v $
# Revision 1.12  2002/11/18 18:07:26  webdev
#   * Removed some of the debugging message to reduce logging.
#
# Revision 1.11  2002/10/29 16:42:39  youngd
#   * Removed notice error prints.
#
# Revision 1.10  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.9  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.8  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.7  2002/09/12 22:33:53  webdev
#   * Updates
#
# Revision 1.6  2002/09/08 19:35:47  webdev
#   * Changed source header to be in php
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");


// Disable error reporting since we're doing our own
error_reporting($error_reporting);
$old_error_handler = set_error_handler("localErrorHandler");

// These generated far too many log messages
// debug("error.php: Error reporting set to: $error_reporting");
// debug("error.php: Old error handler set to: $old_error_handler");
// debug("error.php: Local error handler set to: localErrorHandler");


# ----------------------------------------------------------------------------
#                            F U N C T I O N S 
# ----------------------------------------------------------------------------


# ----------------------------------------------------------------------------
# NAME        : localErrorHandler
# DESCRIPTION : Function to deal with application errors
# ARGUMENTS   : string errorno
#             : string errmsg
#             : string filename
#             : string linenum
#             : string vard
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------
function localErrorHandler ( $errno, $errmsg, $filename, $linenum, $vars ) {

	// Too many log messages, disabled.
    // debug("error.php: Entering localErrorHandler()");

	$errortype = array (
			1	   =>	"ERROR",
			2	   =>	"WARNING",
			4	   =>	"PARSING ERROR",
			8	   =>	"NOTICE",
			16	   =>	"CORE ERROR",
			32	   =>	"CORE WARNING",
			64	   =>	"COMPILE ERROR",
			128	   =>	"COMPILE WARNING",
			256	   =>	"USER ERROR",
			512	   =>	"USER WARNING",
			1024   =>	"USER NOTICE"
		);

		$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

        // Deal with each error type. Some are fatal and need to go to the
        // browser while others only have to be logged to the application log
        // file. See config.php for more info.
        switch ($errno) {

            // ERROR
            case 1:
                logerr("WARNING", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // WARNING
            case 2:
                logerr("WARNING", "(type $errno): $errmsg in $filename at line $linenum");
                # require("error_page.php");
                break;
            
            // PARSING ERROR
            case 4:
                logerr("WARNING", "(type $errno): $errmsg in $filename at line $linenum");
                # require("error_page.php");
                break;
            
            // NOTICE
            case 8:
				// There were just far too many of these being logged so they're now disabled.
                // logerr("NOTICE", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            //CORE ERROR
            case 16:
                logerr("NOTICE", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // CORE WARNING
            case 32:
                logerr("NOTICE", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // COMPILE ERROR
            case 64:
                logerr("FATAL", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // COMPILE WARNING
            case 128:
                logerr("FATAL", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // USER ERROR
            case 256:
                logerr("NOTICE", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // USER WARNING
            case 512:
                logerr("WARNING", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
            // USER NOTICE
            case 1024:
                # require("error_page.php");
                logerr("NOTICE", "(type $errno): $errmsg in $filename at line $linenum");
                break;
            
        } // end switch

	// Too many log messages.
    // debug("error.php: localErrorhandler returning");

	return(1);

	} // end localErrorHandler

?>
