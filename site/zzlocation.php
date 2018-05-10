<?php
# =============================================================================
#
# zzlocation.php
#
# Previously used locations
#
# $Id: zzlocation.php,v 1.6 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzlocation.php,v $
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

if ($quoteid) {
$getquoteinfo = mysql_query("SELECT origin, destination from quotes where quoteid = $quoteid") or die (mysql_error());
$theqline = mysql_fetch_row($getquoteinfo);

$getorigininfo = mysql_query("SELECT city, state from zipcitystate where zip = $theqline[0]");
$theoriginline = mysql_fetch_row($getorigininfo);
$theorigincity = $theoriginline[0];
$theoriginstate = $theoriginline[1];
$theoriginzip = $theqline[0];

$getdestinfo = mysql_query("SELECT city, state from zipcitystate where zip = $theqline[1]");
$thedestline = mysql_fetch_row($getdestinfo);
$thedestcity = $thedestline[0];
$thedeststate = $thedestline[1];
$thedestzip = $theqline[1];
}
?>