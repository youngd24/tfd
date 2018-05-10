<?php
# =============================================================================
#
# error_page.php
#
# Standard app error page
#
# $Id: error_page.php,v 1.4 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: error_page.php,v $
# Revision 1.4  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.3  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.2  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.1  2002/09/13 01:30:30  webdev
#   * Initial version
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("error_page.php: done loading requires");


?>

<html>
<head>
	<title>The Freight Depot Error</title>
	
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>

<?php

    debug("error_page.php: loading site header");
    require("zzheader.php");

?>

<font face=verdana size=4><b>*** ERROR ***</b></font>

<br><br>

<table align=left cellpadding=2 cellspacing=7 border=0>
    <tr>
        <td>
            <font face="'Trebuchet MS', tahoma" size=2>An error has occurred, please
            contact customer support with the following information:

            <br><br>
            <?php

                if ( isset($PHPSESSID) ) {

                    echo "SESSIONID: $PHPSESSID";
                }
                  
                ?>
        </td>
    </tr>
</table>

<br><br>

<?php

    debug("error_page.php: loading site footer");
    require("zzfooter.php");

?>
