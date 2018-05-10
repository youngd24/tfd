<?php
# =============================================================================
#
# changeme.php
#
# Page Description
#
# $Id: internal_template.php,v 1.3 2002/10/16 07:06:10 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: internal_template.php,v $
# Revision 1.3  2002/10/16 07:06:10  youngd
#   * Converted back to DOS format.
#
# Revision 1.2  2002/10/16 07:05:07  youngd
#   * Converted to UNIX format from DOS.
#
# Revision 1.1  2002/10/16 07:04:34  youngd
#   * Initial version.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("changeme.php: entering after initial requires");

?>

<html>
<head>

    <!-- Pull in the internal stylesheet -->
    <link rel="stylesheet" type="text/css" href="/internal/qa.css">

    <!-- Set standard meta tags -->
    <meta name="Author" content="$Author: youngd $>
    <meta name="Revision" content="$Revision: 1.3 $">
    
    <!-- Set the page title (include the page revision) -->
    <title>Page Title ($Revision: 1.3 $)</title>
    
    <!-- Pull in standard site and internal JavaScript functions -->
    <script language="JavaScript" src="/internal/common.js"></script>
    <script language="JavaScript" src="/js/main/js"></script>

</head>

<body>


<?php





debug("changeme.php: leaving");

?>

</body>
</html>