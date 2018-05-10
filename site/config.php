<?php
# ==============================================================================
#
# config.php
#
# Freight Depot Site Configuration
#
# $Id: config.php,v 1.10 2002/09/16 07:58:39 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
# 
# Darren Young [darren@younghome.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: config.php,v $
# Revision 1.10  2002/09/16 07:58:39  webdev
#   * Many updates
#
# Revision 1.9  2002/09/14 06:39:11  webdev
#   * Changed error level to E_ALL.
#
# Revision 1.8  2002/09/13 08:33:28  webdev
#   * Added remote debug option
#
# Revision 1.7  2002/09/08 19:57:18  webdev
#   * Added page mappings section and values
#
# Revision 1.6  2002/09/08 19:35:17  webdev
#   * Changed source header to be in php
#
# Revision 1.5  2002/09/08 08:33:01  webdev
#   * Still working on error logging
# 
# Revision 1.4  2002/09/08 04:39:27  webdev
#   * Added error reporting section and basic values.
#   * Added the general section
#   * Added the section headers
#
# Revision 1.3  2002/09/08 04:14:46  webdev
#   * Added logging configuration variables
#
# Revision 1.2  2002/09/08 04:05:51  webdev
#   * Started adding variables
#
# Revision 1.1  2002/09/08 04:03:14  webdev
#   * Initial version
# 
# ==============================================================================


# ------------------------------------------------------------------------------
#                              G E N E R A L  
# ------------------------------------------------------------------------------

// Is the Secure Socket Layer (ssl) enabled?
$ssl = 0; 

// The starting point for this site. Used for constructing dynamic URL's.
$baseurl = "http://www.thefreightdepot.com";

// The IP address of the server we're running on.
$server_ip = $GLOBALS['SERVER_ADDR'];


# ------------------------------------------------------------------------------
#                         P A G E   M A P P I N G S 
# ------------------------------------------------------------------------------

$about_page          = "about.php";
$bol_page            = "bol.php";
$booked_page         = "booked.php";
$claims_page         = "claims.php";
$class_page          = "class.php";
$config_page         = "config.php";
$confirm_page        = "confirm.php";
$contact_page        = "contact.php";
$debug_page          = "debug.php";
$error_page          = "error.php";
$event_page          = "event.php";
$functions_page      = "functions.php";
$index_page          = "index.php";
$logging_page        = "logging.php";
$login_page          = "login.php";
$my_page             = "mydigiship.php";
$qanda_page          = "qanda.php";
$rating_page         = "rating.php";
$rating_special_page = "ratingspecial.php";
$register_page       = "register.php";
$reports_page        = "reports.php";
$schedule_page       = "schedule.php";
$tracking_page       = "tracking.php";


# ------------------------------------------------------------------------------
#                             L O G G I N G 
# ------------------------------------------------------------------------------

// The location of the application logfile. This also has to be set to the
// value in the php.ini file. The php.ini file is checked into CVS in the
// sysconfig directory and installed on a per environment basis. It's usually
// in /etc but could be in /usr/local/lib.
// $logfile = "/tmp/php.log"; 



# ------------------------------------------------------------------------------
#                            D E B U G G I N G 
# ------------------------------------------------------------------------------

// Flip this on to enable application debugging.
$debug = 1;

// Enable remote debugging
$remote_debug = 0;

// If remote debugging is enabled, this is where the message will go
$debug_host = "localhost";
$debug_port = "16816";



# ------------------------------------------------------------------------------
#                      E R R O R   R E P O R T I N G 
# ------------------------------------------------------------------------------

// Use PHP's error reporting or ours?
// Set to 1 to use PHP's internal reporting.
// Set to 0 to use ours. Make sure to include error.php in the page then.
$error_reporting = E_ALL;

# When we do our own error reporting we save the previous error handler, if you
# want to restore it at the end of error.php set this to 1.
$restore_previous_error_handler = 1;


# ------------------------------------------------------------------------------
#                      M Y   D I G I   O P T I O N S 
# ------------------------------------------------------------------------------

// When a user logs in, this is how many of their recent shipments will be
// displayed on the "my" page.
$myrecentshipments = 5;

?>
