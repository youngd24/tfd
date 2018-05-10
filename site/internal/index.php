<?php
# =============================================================================
#
# index.php
#
# Intranet Start Page
#
# $Id: index.php,v 1.43 2002/12/23 22:53:32 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: index.php,v $
# Revision 1.43  2002/12/23 22:53:32  webdev
#   * Added payables management link
#
# Revision 1.42  2002/12/06 23:02:43  webdev
#   * Changed bugzilla link to include index.cgi instead of just the directory name.#
# Revision 1.41  2002/11/21 00:10:12  webdev
#   * Cleaned up more of those annoying DOS/UNIX problems.
#
# Revision 1.40  2002/10/28 23:05:02  youngd
#   * Changed the fax links
#
# Revision 1.39  2002/10/28 20:42:17  youngd
#   * Added fax link to index page
#   * Fixed hazmat link on scheudle.
#
# Revision 1.38  2002/10/25 18:52:43  youngd
#   * Added Lake Forest Router Stats link
#
# Revision 1.37  2002/10/23 23:04:40  youngd
#   * Changed carrier management to open in a new window.
#
# Revision 1.36  2002/10/23 19:45:09  youngd
#   * Added marketing links.
#
# Revision 1.35  2002/10/23 16:54:27  youngd
#   * Added bullets
#   * Added EDI link
#
# Revision 1.34  2002/10/16 08:09:59  youngd
#   * Started adding bullets and cleaning up the FrontPage messed HTML.
#
# Revision 1.33  2002/10/16 07:45:20  youngd
#   * Changed admin remote link to be blue with no underline.
#
# Revision 1.32  2002/10/16 07:44:34  youngd
#   * Changed address links to be blue with no underline
#
# Revision 1.31  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.30  2002/10/16 06:47:44  youngd
#   * Added standard source header and normalized includes.
#
# Revision 1.29  2002/10/15 21:21:26  youngd
#   * Added link to production webalizer.
#
# Revision 1.28  2002/10/15 21:01:33  youngd
#   * QuickRater in process.
#
# Revision 1.27  2002/10/15 20:31:15  youngd'
#   * Modified the print bol and print invoice quick links to open the associated
#     document in a new window. Added JavaScript functions ot handle this.
#
# Revision 1.26  2002/10/15 18:33:46  youngd
#   * Added sysadmin link to webmin on maul.
#
# Revision 1.25  2002/10/15 18:31:36  youngd
#   * Added administrator management window.
#
# Revision 1.24  2002/10/15 06:56:54  youngd
#   * Converted to UNIX from DOS.
#
# Revision 1.23  2002/10/15 06:55:35  youngd
#   * Starting to add admin checks.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

?>

<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta name="GENERATOR" content="Microsoft FrontPage 5.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>THE FREIGHT DEPOT - COMPANY INTRANET ($Revision: 1.43 $)</title>
<script language=JavaScript src=/internal/common.js>

</script>
</head>

<body>

<p align="center"><b><font face="Verdana" size="5"><br>
<u>THE FREIGHT DEPOT</u><br><br>
<!--<img border="0" src="../images/xlogo-2.gif" width="42" height="33">--></font></b></p>
<p align="center"><b><font face="Verdana" size="4">COMPANY INTRANET FOR <br><?php echo $_SERVER['PHP_AUTH_USER']; ?><br>
&nbsp;</font></b></p>
<div align="center">
  <center>
  
  <table border="0" cellspacing="1" style="border-collapse: collapse" bordercolor="#111111" width="90%" id="AutoNumber1" height="113">
    
    <tr>
      <th width="50%" colspan="2" height="19" style="border-style: ridge; border-width: 1" bgcolor="#C0C0C0">
        <p align="center">
        <b>
        <font face="Verdana" size="2">
        SHIPMENT MAINTENANCE
        </font>
        </b>
      </th>
      <th width="50%" colspan="2" height="19" style="border-style: ridge; border-width: 1; padding-left:4; padding-right:4; padding-top:1; padding-bottom:1" bgcolor="#C0C0C0">
        <p align="center">
        <b>
        <font face="Verdana" size="2">
        CUSTOMER MAINTENANCE
        </font>
        </b>
      </th>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-top-style: solid; border-top-width: 1">
        <p align="left">
          <img src="/images/red_bullet_1.gif" height="10" width="10">
          <font size="2" face="Verdana"><a href="ships.php" style="text-decoration: none; color:blue">
          List Shipments
          </a>
          </font>
      </td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1">
        <p align="left">
      </td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1; border-top-style: solid; border-top-width: 1" align="left">
        <img src="/images/red_bullet_1.gif" height="10" width="10">
        <font size="2" face="Verdana"><a href="custs.php" style="text-decoration: none; color:blue">
        List Customers
        </a>
        </font>
      </td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1" align="center">
      </td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1">
        <p align="left">
        <img src="/images/red_bullet_1.gif" height="10" width="10">
        <font size="2" face="Verdana">
        <a href="JavaScript:openCancelShipmentWindow();" style="text-decoration: none; color:blue">
        Cancel Shipment
        </a>
        </font>
      </td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1">
        <p align="left"></td>
        <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1" align="left">
        <img src="/images/red_bullet_1.gif" height="10" width="10">
        <font size="2" face="Verdana">
        <a href="accountmgr.php" style="text-decoration: none; color:blue">
        Account Management
        </a>
        </font>
      </td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1" align="left">
      </td>
    </tr>

    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1">
        <p align="left">
          <img src="/images/red_bullet_1.gif" height="10" width="10">
          <font face="Verdana" size="2">
          <a href="/internal/cancelled_shipments.php" style="text-decoration: none; color:blue">
          Cancelled Shipments
          </a>
          </font>
      </td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1">
        <p align="center">
      </td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1" align="left">
        <font face="Verdana" size="2">
        <img src="/images/red_bullet_1.gif" height="10" width="10">
        <a href="JavaScript:openMarginManagementWindow();" style="text-decoration: none; color:blue">
        Margin Management
        </a>
        </font>
      </td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1" align="left">
      </td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-bottom-style: solid; border-bottom-width: 1">
        <p align="left">
      </td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">
        <p align="left">
      </td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1; border-bottom-style: solid; border-bottom-width: 1" align="left">
        <font face="Verdana" size="2">
        <img src="/images/red_bullet_1.gif" height="10" width="10">
        <a href="JavaScript:openCustomerLookupWindow();" style="text-decoration: none; color:blue">
        Customer Lookup
        </a>
        </font>
      </td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1" align="center">
      </td>
    </tr>
   
    <tr>
      <th width="50%" height="18" colspan="2" style="border-style: ridge; border-width: 1" bgcolor="#C0C0C0">
      <p align="center"><b><font face="Verdana" size="2">CARRIER MAINTENANCE</font></b></th>
      <th width="50%" height="18" colspan="2" style="border-style: ridge; border-width: 1" bgcolor="#C0C0C0">
      <p align="center"><b><font face="Verdana" size="2">FINANCIAL TOOLS</font></b></th>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-top-style: solid; border-top-width: 1">
      <p align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="JavaScript:openCarrierManagementWindow();" style="text-decoration: none; color:blue">Manage Carriers</a></font></td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1">
      <p align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1; border-top-style: solid; border-top-width: 1" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="../sql-ledger/login.pl" style="text-decoration: none; color:blue">SQL Ledger Login</a></font></td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1" align="center">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1">
      <p align="left">&nbsp;</td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1">
      <p align="left">&nbsp;</td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="../sql-ledger/admin.pl" style="text-decoration: none; color:blue">SQL Ledger Admin</a></font></td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1" align="left">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1">
      <p align="center">&nbsp;</td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1">
      <p align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="pastdue.php" style="text-decoration: none; color:blue">Past Due Invoices</a></font></td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1" align="left">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1">
      <p align="center">&nbsp;</td>
      <td width="27%" height="19" style="border-right-style: solid; border-right-width: 1">
      <p align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-left-style: solid; border-left-width: 1" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="aptrack.php" style="text-decoration: none; color:blue">Payables Reports</a></font></td>
      <td width="25%" height="19" style="border-right-style: solid; border-right-width: 1" align="left">&nbsp;</td>
    </tr>
    
    <tr>
      <th width="50%" height="19" colspan="2" style="border-style: ridge; border-width: 1" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2"><b>MARKETING TOOLS</b></font></th>
      <th width="50%" height="19" colspan="2" style="border-style: ridge; border-width: 1" bgcolor="#C0C0C0">
      <p align="center"><b><font size="2" face="Verdana">SYSTEM UTILITIES</font></b></th>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium">
      <p align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a style="text-decoration: none; color:blue" href="/internal/marketing/viewshwag.php"><span style="color: #0000FF">View 
      Emails</span></a></font></td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">
      <a style="text-decoration:none; color:blue" href="/internal/marketing/shwaglog.php">View Log</a></font></td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
      <font face="Verdana" size="2">
      <img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="http://maul.lakeforest.thefreightdepot.com/bugzilla/index.cgi" style="text-decoration: none; color:blue">Bug Tracking</a></font></td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
      <font face="Verdana" size="2">
      <img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="http://www.thefreightdepot.com/webalizer/index.html" style="text-decoration: none; color:blue">Web Site 
      Statistics</a></font></td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium">
		<p align="left"><font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">
        <a style="text-decoration: none; color:blue" href="/internal/marketing/addshwag.php">Add Email</a></font></td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left"><font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">
      <a style="text-decoration:none; color:blue" href="/list-remove.php">Remove Someone</a></font></td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
		<font face="Verdana" size="2">
		<img src="/images/red_bullet_1.gif" height="10" width="10">
		<a href="http://maul:10000" style="text-decoration: none; color:blue">System Administration</a>
		</font>
	  </td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
		  <font face="Verdana" size="2">
		  <img src="/images/red_bullet_1.gif" height="10" width="10">
		  <a href="/edi/" style="text-decoration: none; color:blue">EDI Management</a>
		</font>
	  </td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium">
      <p align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">
      <a style="text-decoration:none; color:blue" href="/internal/marketing/sendshwag.php">Send Email</a></font></td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">&nbsp;</td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left"><font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="JavaScript:openAdminManagementWindow();" style="text-decoration: none; color:blue">Administrator 
      Management</a></font></td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="center">
      <p align="left">
		  <font face="Verdana" size="2">
		  <img src="/images/red_bullet_1.gif" height="10" width="10">
      <a style="text-decoration: none; color:blue" href="http://maul/mrtg/workdir/router_3.html">Lake Forest Router Stats</a></font></td>
    </tr>
   
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium">
      <p align="center">&nbsp;</td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium" align="center">&nbsp;</td>
    </tr>
    
    <tr>
      <th width="50%" height="19" style="border-style: solid; border-width: 1" colspan="2" bgcolor="#C0C0C0">
      <p align="center"><b><font size="2" face="Verdana">REPORTING</font></b></th>
      <th width="50%" height="19" style="border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1" colspan="2" bgcolor="#C0C0C0">
      <p align="center"><b><font face="Verdana" size="2">QUOTING</font></b></th>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-bottom-style: none; border-bottom-width: medium">
      <p align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="tomreport.php" style="text-decoration: none; color:blue">Tom's Reports</a></font></td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-bottom-style: none; border-bottom-width: medium" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="nonltl.php" style="text-decoration: none; color:blue">Non LTL Quotes</a></font></td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-bottom-style: none; border-bottom-width: medium" align="center">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium">
      <p align="center">&nbsp;</td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="mikesheet.php" style="text-decoration: none; color:blue">Mike's Rate Sheet</a></font></td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="center">&nbsp;</td>
    </tr>
   
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium">
      <p align="center">&nbsp;</td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="JavaScript:openQuickRater" style="text-decoration: none; color:blue">Quick 
      Rater</a></font></td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: none; border-bottom-width: medium" align="center">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="23%" height="19" style="border-left-style: solid; border-left-width: 1; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: solid; border-bottom-width: 1">
      <p align="center">&nbsp;</td>
      <td width="27%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: solid; border-bottom-width: 1" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom-style: solid; border-bottom-width: 1" align="center">&nbsp;</td>
      <td width="25%" height="19" style="border-left-style: none; border-left-width: medium; border-right-style: solid; border-right-width: 1; border-top-style: none; border-top-width: medium; border-bottom-style: solid; border-bottom-width: 1" align="center">&nbsp;</td>
    </tr>
  
  </table>
  </center>
</div>

<p>&nbsp;</p>
<div align="center">
  <center>
  <table border="0" cellspacing="1" style="border-collapse: collapse" bordercolor="#111111" width="90%" id="AutoNumber2">
    <tr>
      <td width="50%" colspan="2" bgcolor="#C0C0C0" style="border-style: solid; border-width: 1">
      <p align="center">
      <b><font face="Verdana" size="2">Quick Link Tools:</font></b></p>
      </td>
      <td width="50%" colspan="2" bgcolor="#C0C0C0" style="border-style: solid; border-width: 1">
        <p align="center"><font face="Verdana" size="2"><b>Other Tools:</b></font></p>
      </td>
    </tr>
    <tr>
      <td width="15%" style="border-top-style: solid; border-top-width: 1" align="right"><font face="Verdana" size="2"><b>Print BOL:</b></font><font size="2">
      </font>
      </td>
      <td width="35%" style="border-top-style: solid; border-top-width: 1" align="left">
      &nbsp; <input type="text" size="15" name="shipmentid" style="font-family: verdana; font-size:10px">
      <font face="Verdana" size="1">[<a href="JavaScript:displayBillOfLading(shipmentid)" style="text-decoration:none;color:blue">print it</a>]</font></td>
      <td width="25%" style="border-top-style: solid; border-top-width: 1">
      <p align="left">
      <font face="Verdana" size="2"><img src="/images/red_bullet_1.gif" height="10" width="10">&nbsp;<a href="JavaScript:openAdminRemoteWindow()" style="text-decoration: none; color:blue">Admin Remote</a></font></td>
      <td width="25%" style="border-top-style: solid; border-top-width: 1">&nbsp;</td>
    </tr>
    <tr>
      <td width="15%" align="right"><b><font face="Verdana" size="2">Print Invoice:</font></b></td>
      <td width="35%" align="left">
      &nbsp; <input type="text" size="15" name="ordnumber" style="font-family: verdana; font-size:10px">
      <font size="1" face="Verdana">[<a href="JavaScript:displayInvoice(ordnumber)" style="text-decoration:none;color:blue">print it</a>]</font></td>
      <td width="25%">&nbsp;</td>
      <td width="25%">&nbsp;</td>
    </tr>
    <tr>
      <td width="15%" align="right"><b><font face="Verdana" size="2">Fax BOL:</font></b></td>
      <td width="35%" align="left">
      &nbsp; <input type="text" size="15" name="faxshipmentid" style="font-family: verdana; font-size:10px">
      <font size="1" face="Verdana">[<a href="JavaScript:faxBillOfLading(faxshipmentid)" style="text-decoration:none;color:blue">fax it</a>]</font></td>
      <td width="25%">&nbsp;</td>
      <td width="25%">&nbsp;</td>
    </tr>
  </table>
  </center>
</div>

</body>

</html>
