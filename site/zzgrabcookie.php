<?php
# =============================================================================
#
# zzgrabcookie.php
#
# Cookie management page
#
# $Id: zzgrabcookie.php,v 1.7 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzgrabcookie.php,v $
# Revision 1.7  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.4  2002/09/13 08:34:54  webdev
#   * Many updates.
#   * Added services section in the rating page
#
# =============================================================================

$nouser = 0;

// Pull in standard pages
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");

if ($digishipcookie1 and $digishipcookie2) {
	
	// grab user
	$grabuser = mysql_query("select * from customers where custid = '$digishipcookie2' and loginstring = '$digishipcookie1'");

    // did we get a user?
	if ($userarray = mysql_fetch_row($grabuser)) {
	
        // test for mutiple entries
		if (mysql_fetch_row($grabuser)) {
			die ("Multiple customer entry error.");
		}
	} else {

		// if we didn't get a user
        $nouser = 1;
		debug("zzgrabcookie.php: nouser set to 1");
	}
} else {
	$nouser = 1;
	debug("zzgrabcookie.php: nouser set to 1");
}

if ($nouser == 1 and !($unsecure)) {

	debug("zzgrabcookie.php: unauthenticated user, sending to login page");
    require('zzlogin.php');
	die();
}

?>
