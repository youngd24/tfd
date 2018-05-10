<?php
# =============================================================================
#
# zzmysql.php
#
# MySQL Database Settings
#
# $Id: zzmysql.php,v 1.12 2002/10/03 16:14:34 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzmysql.php,v $
# Revision 1.12  2002/10/03 16:14:34  youngd
#   * Removed requires.
#
# Revision 1.11  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.10  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.9  2002/09/08 19:36:08  webdev
#   * Changed source header to be in php
#
# =============================================================================


    // define host for db
    $host = "localhost";

    // define db
    $database = "digiship";

    // Database user
    $db_user = "php";

    // Database password
    $db_pass = "password";

    // open persistent connection to mysql
    $db = mysql_pconnect($host, $db_user, $db_pass) or die("Cannot Connect to $host<br><br>");

    // select database
    mysql_select_db($database, $db) or die ("Can't connect to $db<br><br>");

?>
