<?php
# =============================================================================
#
# debug.php
#
# Page & app debugging methods
#
# $Id: debug.php,v 1.6 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
#
# $Log: debug.php,v $
# Revision 1.6  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.5  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.4  2002/09/12 22:33:53  webdev
#   * Updates
#
# Revision 1.3  2002/09/08 19:35:28  webdev
#   * Changed source header to be in php
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");


# ----------------------------------------------------------------------------  
# NAME        : debug
# DESCRIPTION : Sends a debug message to the app log
# ARGUMENTS   : string (message)
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------  
function debug ( $message ) {

    if ( ! $message ) {
        logerr("SYNTAX", "Param 'message' not passed to debug()");
        return(0);
    } else {
        error_log("DEBUG: $message", 0);
        return(1);
    }

}

?>
