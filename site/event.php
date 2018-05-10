<?php
# =============================================================================
#
# event.php
#
# Event processing methods
#
# $Id: event.php,v 1.8 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: event.php,v $
# Revision 1.8  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.7  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.6  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.5  2002/09/12 22:33:53  webdev
#   * Updates
#
# Revision 1.4  2002/09/08 19:35:47  webdev
#   * Changed source header to be in php
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("event.php: done loading requires");

?>
