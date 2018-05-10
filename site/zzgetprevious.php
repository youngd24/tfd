<?php
# =============================================================================
#
# zzgetprevious.php
#
# Get previous accessorials, origins and destinations
#
# $Id: zzgetprevious.php,v 1.6 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzgetprevious.php,v $
# Revision 1.6  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.5  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// get accessorials
$accessoriallisting = mysql_query("SELECT * from accessorials where carrierid = $quoteline[6]");


// get origins
$getorigins = mysql_query("SELECT * from address where custid = $userarray[0] and zip = $theoriginzip") or die(mysql_error());

// get destinations
$getdests = mysql_query("SELECT * from address where custid = $userarray[0] and zip = $thedestzip") or die(mysql_error());

?>