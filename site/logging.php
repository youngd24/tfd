<?php
# =============================================================================
#
# logging.php
#
# Application logging methods
#
# $Id: logging.php,v 1.10 2002/11/03 09:12:33 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
#
# $Log: logging.php,v $
# Revision 1.10  2002/11/03 09:12:33  youngd
#   * Added warningerror and syntax error functions
#
# Revision 1.9  2002/10/14 11:39:56  youngd
#   * Added htmlerror() function.
#
# Revision 1.8  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.7  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.6  2002/09/16 23:32:55  youngd
#   * Added headers and other changes
#
# Revision 1.5  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.4  2002/09/12 22:33:53  webdev
#   * Updates
#
# Revision 1.3  2002/09/08 19:35:47  webdev
#   * Changed source header to be in php
#
# =============================================================================


require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("logging.php: done loading requires");


# ---------------------------------------------------------------------------- 
#                            F U N C T I O N S  
# ---------------------------------------------------------------------------- 
 
  
# ---------------------------------------------------------------------------- 
# NAME        : errlog
# DESCRIPTION : Send a message to the standard error log
# ARGUMENTS   : string (type), string (message)
# RETURN      : None
# NOTES       : None
# ---------------------------------------------------------------------------- 
function logerr ( $type, $message ) {

    debug("logging.php: entering logerr()");

    if ( ! $type ) {
        print "Syntax Error: Param 'type' not passed to errlog()\n";
        debug("logging.php: logerr() returning with value of 0");
        return(0);
    }

    if ( ! $message ) {
        print "Syntax Error: Param 'message' not passed to errlog()\n";
        debug("logging.php: logerr() returning with value of 0");
        return(0);
    }

    switch ($type) {
        
        case 'FATAL':
            error_log("FATAL ERROR $message", 0);
            break;

        case 'WARNING':
            error_log("WARNING ERROR $message", 0);
            break;

        case 'NOTICE':
            error_log("NOTICE ERROR $message", 0);
            break;

        case 'SYNTAX':
            error_log("SYNTAX ERROR $message", 0);

    }

    debug("logging.php: logerr() returning with value of 1");
    return(1);
}



# ----------------------------------------------------------------------------  
# NAME        : logmsg 
# DESCRIPTION : Send a message to the standard log
# ARGUMENTS   : string (message)  
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------  
function logmsg ( $message ) {

    debug("logging.php: entering logmsg()");

    if ( ! $message ) {
        logerr("SYNTAX", "Param 'message' not passed to logmsg()\n");
        debug("logging.php: logmsg() returning");
        return;
    } else {
        error_log("LOG: $message", 0);
        debug("logging.php: logmsg() returning");
        return;
    }

}



# ----------------------------------------------------------------------------  
# NAME        : htmlerror
# DESCRIPTION : Print an error message to the browser
# ARGUMENTS   : string (message)  
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------  
function htmlerror ( $message ) {

    debug("logging.php: entering htmlerror()");

    if ( ! $message ) {
        logerr("SYNTAX", "Param 'message' not passed to htmlerror()\n");
        debug("logging.php: htmlerror() returning");
        return;
    } else {
        print "<font face=verdana size=2 color=red><b>ERROR: </b></font>";
        print "<font face=verdana size=2>$message</font>";
        return;
    }

}


# ----------------------------------------------------------------------------  
# NAME        : syntaxerror
# DESCRIPTION : Prints a syntax error
# ARGUMENTS   : string (message)  
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------  
function syntaxerror ($message) {
    debug("logging.php: entering syntaxerror()");

    if ( ! $message ) {
        debug("Param 'message' not passed to syntaxerror()\n");
        return;
    } else {
        logerr("SYNTAX", $message);
        return;
    }
}


# ----------------------------------------------------------------------------  
# NAME        : warningrror
# DESCRIPTION : Prints a warning error
# ARGUMENTS   : string (message)  
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------  
function warningerror ($message) {
    debug("logging.php: entering warningerror()");

    if ( ! $message ) {
        debug("Param 'message' not passed to warningerror()\n");
        return;
    } else {
        logerr("WARNING", $message);
        return;
    }
}

?>
