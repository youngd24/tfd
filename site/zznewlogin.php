<?php
# =============================================================================
#
# zznewlogin.php
#
# User authentication page
#
# $Id: zznewlogin.php,v 1.10 2003/01/16 22:30:50 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zznewlogin.php,v $
# Revision 1.10  2003/01/16 22:30:50  webdev
#   * Removed session crap.
#
# Revision 1.9  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.8.2.1  2002/11/05 15:15:12  webdev
#   * New registration in progress.
#
# Revision 1.8  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.7  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.6  2002/09/15 07:27:53  webdev
#   * Added source header
#
# Revision 1.5  2002/09/15 07:12:28  webdev
#   * Added source header
#   * Started adding session code
# 
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

require('zzmysql.php');

// kill cookie in case it exists
	setcookie("digishipcookie1", "", 0, "/", "", 0);
	setcookie("digishipcookie2", "", 0, "/", "", 0);
	
	// grab user
	$grabuser = mysql_query("select * from customers where email = '$email' and password = '$password'");

	// did we get a user?
	if ($userarray = mysql_fetch_row($grabuser)) {

		// test for mutiple entries
		if (mysql_fetch_row($grabuser)) {
			die ("Multiple customer entry error. Please call 312.224.2932.");
		}

		// retrieve current time and assign to $timenow
		$timenow = getdate();
		$thetime = $timenow['year'] . '-' . $timenow['mon'] . '-' . $timenow['mday'] . ' ' . $timenow['hours'] . ':' . $timenow['minutes'] . ':' . $timenow['seconds'];

		// drop cookie and update login info
		if(setcookie("digishipcookie1", $userarray[8], 0, "/", "", 0) and setcookie("digishipcookie2", $userarray[0], 0, "/", "", 0)) {
			$sqlstr = "UPDATE customers set lastloginip = '$REMOTE_ADDR', lastlogindate = '$thetime' WHERE custid = $userarray[0]";
			$loginupdate = mysql_query($sqlstr) or die (mysql_error() . "<br>" . $sqlstr);
			$browser = $HTTP_USER_AGENT;
			$browserupdate = mysql_query("INSERT into browser (customerid, browser) values ($userarray[0],'$browser')") or die(mysql_error());
		}
	}
	// if we didn't get a user
	else {
		$nouser = 1;
	}
	if ($nouser == 1) {
		require ('zzlogin.php');
		die();
	}

?>
