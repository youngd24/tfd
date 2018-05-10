<?php
# =============================================================================
#
# fax.php
#
# BOL Fax Request Page
#
# $Id: fax.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: fax.php,v $
# Revision 1.3  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.2  2002/10/16 06:47:44  youngd
#   * Added standard source header and normalized includes.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");
require_once("zzmysql.php");
?>

<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta name="GENERATOR" content="Microsoft FrontPage 5.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>BOL</title>
</head>

<body>

<form method="POST" action="/internal/bolfax.php">
  <div align="left">
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="20%" id="AutoNumber1">
      <tr>
        <td width="50%">
        <p align="right"><b><font face="Verdana" size="2">BOL:&nbsp; </font></b>
        </td>
        <td width="50%">
        <input type="text" name="shipmentid" size="20" style="font-family: Verdana; font-size: 8pt"></td>
      </tr>
      <tr>
        <td width="50%">
        <p align="right"><input type="submit" value="Submit" name="B1"> </td>
        <td width="50%">&nbsp;<input type="reset" value="Reset" name="B2"></td>
      </tr>
    </table>
  </div>
  <p>&nbsp;</p>
</form>

</body>

</html>
