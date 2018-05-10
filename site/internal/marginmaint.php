<?php
# =============================================================================
# 
# marginmaint.php
# 
# Page to maintain the customer / carrier margins.
# interface to the customer_margins database table.
# 
# $Id: marginmaint.php,v 1.8 2002/10/23 21:44:35 youngd Exp $
# 
# Contents Copyright (c) 2002, Transport Investments, Inc.
# 
# Darren Young [darren@younghome.com]
#
# =============================================================================
# 
# ChangeLog:
# 
# $Log: marginmaint.php,v $
# Revision 1.8  2002/10/23 21:44:35  youngd
#   * Added display of customer name, company and carrier name
#
# Revision 1.7  2002/10/15 06:23:12  youngd
#   * added rev to title
#
# Revision 1.6  2002/10/15 06:19:46  youngd
#   * Marginmaint now opens in a small window.
#
# Revision 1.5  2002/10/11 18:00:06  youngd
#   * Added several isset() methods to reduce the errors coming from the
#     script.
#
# Revision 1.4  2002/10/11 16:53:55  youngd
#   * Changed order by to include carrierid (ASC)
#
# Revision 1.3  2002/10/11 16:49:14  youngd
#  * Changed style on index page
#  * First working version of marginmaint.php
#
# Revision 1.2  2002/10/11 15:50:17  youngd
#   * Added additional links.
#
# Revision 1.1  2002/10/11 15:20:46  youngd
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
require_once("zzmysql.php");
require_once("zzmysql.php");

?>

<html>
    <head>
    <title>Customer Margin Management ($Revision: 1.8 $)</title>
    <script language=JavaScript src=/internal/common.js>
    </script>
    </head>

<body>

<center>
	<font face=verdana size=4><b>Customer Margin Maintenance</b></font>
	<br>
	<font face=verdana size=2><u><i>(By Carrier)</i></u></font>
	<br><br>
</center>


<?php

if ( isset($mode) && $mode == "edit" ) {

	echo "<h3><font face=verdana>EDIT MODE</font></h3>";

	$query = "SELECT * FROM customer_margins where id='$id'";
	$result = mysql_query($query) or die (mysql_error());

	echo "<table>";
	echo "<form method='GET' action='$PHP_SELF'>";
	while($row = mysql_fetch_row($result)) {

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>ID:</b></td>";
		echo "<td><input type='text' value='$row[0]' name='id'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>CUSTID:</b></td>";
		echo "<td><input type='text' value='$row[1]' name='custid'></td>";
		echo "<input type='hidden' name='oldcustid' value='$row[1]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>CARRIER:</b></td>";
		echo "<td><input type='text' value='$row[2]' name='carrierid'></td>";
		echo "<input type='hidden' name='oldcarrierid' value='$row[1]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>MARGIN:</b></td>";
		echo "<td><input type='text' value='$row[3]' name='margin'></td>";
		echo "<input type='hidden' name='oldmargin' value='$row[3]'>";
		echo "</tr>";

		echo "<input type='hidden' name='mode' value='update'>";

		echo "<tr>";
		echo "<td align='right'><input type='submit' name='submit' value='Submit'></td>";
		echo "<td><input type='reset' value='reset' name='reset'></td>";
		echo "</tr>";

	}

	echo "</form>";
	echo "</table>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=marginmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";

} elseif ( isset($mode) && $mode == "update" ) {

	echo "<h3><font face=verdana>UPDATED</font></h3>";

	if ( "$custid" != "$oldcustid" ) {
		$query = "UPDATE customer_margins set custid='$custid' where id='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( "$carrierid" != "$oldcarrierid" ) {
		$query = "UPDATE customer_margins set carrierid='$carrierid' where id='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( "$margin" != "$oldmargin" ) {
		$query = "UPDATE customer_margins set margin='$margin' where id='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	echo "<br>";
	echo "<br><font face=verdana size=1>[ <a href=marginmaint.php>List</a></font>";

} elseif ( isset($mode) && $mode == "add" ) {

	echo "<table>";
	echo "<form method=GET action=$PHP_SELF>";

		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>CUSTID:</b></td>";
		echo "<td><input type=text value='$row[1]' name=custid></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>CARRIER:</b></td>";
		echo "<td><input type=text value='$row[2]' name=carrierid></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>MARGIN:</b></td>";
		echo "<td><input type=text value='$row[3]' name=margin></td>";
		echo "</tr>";

		echo "<input type=hidden name=mode value=addnew>";

		echo "<tr>";
		echo "<td align=right><input type=submit name='addnew' value='Add'></td><td><input type=reset value='Reset' name=reset></td>";
		echo "</tr>";

	echo "</form>";
	echo "</table>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=marginmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";

} elseif ( isset($mode) && $mode == "addnew" ) {

	$query = "INSERT INTO customer_margins VALUES (
					'',
					'$custid',
					'$carrierid',
					'$margin')";
	
	$result = mysql_query($query) or die (mysql_error());

	echo "<font face=verdana size=2>Margin Added</font>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=marginmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";

} elseif ( isset($mode) && $mode == "delete" ) {

	$query = "DELETE FROM customer_margins WHERE custid='$custid' and carrierid='$carrierid'";

	$result = mysql_query($query) or die (mysql_error());

	echo "<font face=verdana size=2>Margin Deleted</font>";
	echo "<br>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=marginmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";
}

else {

	// Just display a list of the known carriers

	echo "<table cellpadding=1 cellspacing=1 align=center>";
	echo "<tr>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=id'>ID</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=custid'>CUSTID</a></b></td>";
	echo "<td><font face=verdana size=2><b>NAME</a></b></td>";
	echo "<td><font face=verdana size=2><b>COMPANY</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=carrierid'>CARRIER</a></b></td>";
	echo "<td><font face=verdana size=2><b>NAME</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=margin'>MARGIN</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=action'>ACTION</a></b></td>";
	echo "</tr>";

    if ( $sort == "id" ) {
        $query = "SELECT * FROM customer_margins ORDER BY id ASC";
    } elseif ( $sort == "custid" ) {
        $query = "SELECT * FROM customer_margins ORDER BY custid ASC";
    } elseif ( $sort == "carrierid" ) {
        $query = "SELECT * FROM customer_margins ORDER BY carrierid ASC";
    } elseif ( $sort == "margin" ) {
        $query = "SELECT * FROM customer_margins ORDER BY margin ASC";
    } else {
        $query = "SELECT * FROM customer_margins ORDER BY custid ASC, carrierid DESC";
    }

	$result = mysql_query($query) or die (mysql_error());

	while($row = mysql_fetch_row($result)) {

		// Get the name and company of the customer
		$cusname_query = mysql_query("SELECT name,company FROM customers where custid=$row[1]");
		$cusname = mysql_fetch_array($cusname_query);

		// Get the name of the carrier for display
		$carriername_query = mysql_query("SELECT name FROM carriers where carrierid=$row[2]");
		$carriername = mysql_fetch_array($carriername_query);

		if ( isset($color) && $color == "silver" ) {
			$color = "white";
			echo "<tr bgcolor=white>";
		} else {
			$color = "silver";
			echo "<tr bgcolor=silver>";
		}
		echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=edit&id=$row[0]>$row[0]</td>";
		echo "<td><font face=verdana size=2>$row[1]</td>";
		echo "<td><font face=verdana size=2>$cusname[name]</td>";
		echo "<td><font face=verdana size=2>$cusname[company]</td>";
		echo "<td><font face=verdana size=2>$row[2]</td>";
		echo "<td><font face=verdana size=2>$carriername[name]</td>";
		echo "<td><font face=verdana size=2>$row[3]</td>";
		echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=delete&custid=$row[1]&carrierid=$row[2]>delete</a></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<br>";
	echo "<center>";
	echo "<font face=verdana size=1>[ <a href=$PHP_SELF?mode=add>Add</a> | </font>";
	echo "<a href='' onClick='JavaScript:window.close();'><font face=verdana size=1>Close</a> ] </font>";
	echo "</center>";
}


?>

</body>

</html>