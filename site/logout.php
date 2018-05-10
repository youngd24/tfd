<?php
# =============================================================================
#
# logout.php
#
# User logout page
#
# $Id: logout.php,v 1.6 2003/01/16 22:30:59 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: logout.php,v $
# Revision 1.6  2003/01/16 22:30:59  webdev
#   * Removed session crap.
#
# Revision 1.5  2002/11/03 09:12:09  youngd
#   * Added session destroy and unset code.
#
# Revision 1.4  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.3  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.2  2002/09/16 23:32:55  youngd
#   * Added headers and other changes
#
# Revision 1.1  2002/09/16 07:58:39  webdev
#   * Many updates
#
# 
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("logout.php: entering after initial requires");

// Kill the cookies
debug("logout.php: killing cookies");
setcookie("digishipcookie1", "", 0, "/", "", 0);
setcookie("digishipcookie2", "", 0, "/", "", 0);
setcookie("digishiptransaction", "", 0, "/", "", 0);
setcookie("digishipaccessorials", "", 0, "/", "", 0);

// Send the user to the index page
require_once("index.php");	

?>
