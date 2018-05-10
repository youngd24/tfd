<?php
# =============================================================================
# 
# adminmaint.php
# 
# Page to maintain site administrators
# 
# $Id: adminmaint.php,v 1.1 2002/10/15 18:30:41 youngd Exp $
# 
# Contents Copyright (c) 2002, Transport Investments, Inc.
# 
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
# 
# ChangeLog:
# 
# $Log: adminmaint.php,v $
# Revision 1.1  2002/10/15 18:30:41  youngd
#   * New file, ready for testing.
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
    <title>Site Administrator Management ($Revision: 1.1 $)</title>
    <script language=JavaScript src=/internal/common.js>
    </script>
    </head>

<body>

<center>
	<font face=verdana size=4><b>Site Administrator Maintenance</b></font>
	<br><br>
</center>


<?php

if ( isset($mode) && $mode == "edit" ) {

	echo "<h3><font face=verdana>EDIT MODE</font></h3>";

	$query = "SELECT * FROM admins where custid='$custid'";
	$result = mysql_query($query) or die (mysql_error());

	echo "<table>";
	echo "<form method='GET' action='$PHP_SELF'>";
	while($row = mysql_fetch_row($result)) {

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>CUSTID:</b></td>";
		echo "<td><input type='text' value='$row[0]' name='custid'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>LEVEL:</b></td>";
		echo "<td><input type='text' value='$row[1]' name='level'></td>";
		echo "<input type='hidden' name='oldlevel' value='$row[1]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>USERNAME:</b></td>";
		echo "<td><input type='text' value='$row[2]' name='username'></td>";
		echo "<input type='hidden' name='oldusername' value='$row[1]'>";
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
	echo "<br><font face=verdana size=1>[ <a href=adminmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";

} elseif ( isset($mode) && $mode == "update" ) {

	echo "<h3><font face=verdana>UPDATED</font></h3>";

	if ( "$username" != "$oldusername" ) {
		$query = "UPDATE admins set username='$username' where custid='$custid'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( "$level" != "$oldlevel" ) {
		$query = "UPDATE admins set level='$level' where custid='$custid'";
		$result = mysql_query($query) or die (mysql_error());
	}

	echo "<br>";
	echo "<br><font face=verdana size=1>[ <a href=adminmaint.php>List</a></font>";

} elseif ( isset($mode) && $mode == "add" ) {

	echo "<table>";
	echo "<form method=GET action=$PHP_SELF>";

		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>CUSTID:</b></td>";
		echo "<td><input type=text value='$row[0]' name=custid></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>LEVEL:</b></td>";
		echo "<td><input type=text value='$row[1]' name=level></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>USERNAME:</b></td>";
		echo "<td><input type=text value='$row[2]' name=username></td>";
		echo "</tr>";

		echo "<input type=hidden name=mode value=addnew>";

		echo "<tr>";
		echo "<td align=right><input type=submit name='addnew' value='Add'></td><td><input type=reset value='Reset' name=reset></td>";
		echo "</tr>";

	echo "</form>";
	echo "</table>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=adminmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";

} elseif ( isset($mode) && $mode == "addnew" ) {

	$query = "INSERT INTO admins VALUES (
					'$custid',
					'$level',
					'$username')";
	
	$result = mysql_query($query) or die (mysql_error());

	echo "<font face=verdana size=2>Administrator $username Added</font>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=adminmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";

} elseif ( isset($mode) && $mode == "delete" ) {

	$query = "DELETE FROM admins WHERE custid='$custid'";

	$result = mysql_query($query) or die (mysql_error());

	echo "<font face=verdana size=2>Administrator $username Deleted</font>";
	echo "<br>";

    echo "<center>";
	echo "<br><font face=verdana size=1>[ <a href=adminmaint.php>List</a> | </font>";
   	echo "<font face=verdana size=1><a href='' onClick='JavaScript:window.close();'>Close</a> ]</font>";
    echo "</center>";
}

else {

	// Just display a list of the known carriers

	echo "<table cellpadding=1 cellspacing=1 align=center>";
	echo "<tr>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=custid'>CUSTID</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=level'>LEVEL</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=username'>USERNAME</a></b></td>";
	echo "<td><font face=verdana size=2><b><a href='$PHP_SELF?sort=action'>ACTION</a></b></td>";
	echo "</tr>";

    if ( $sort == "custid" ) {
        $query = "SELECT * FROM admins ORDER BY custid ASC";
    } elseif ( $sort == "level" ) {
        $query = "SELECT * FROM admins ORDER BY level ASC";
    } elseif ( $sort == "username" ) {
        $query = "SELECT * FROM admins ORDER BY username ASC";
    } else {
        $query = "SELECT * FROM admins";
    }

	$result = mysql_query($query) or die (mysql_error());

	while($row = mysql_fetch_row($result)) {
		if ( isset($color) && $color == "silver" ) {
			$color = "white";
			echo "<tr bgcolor=white>";
		} else {
			$color = "silver";
			echo "<tr bgcolor=silver>";
		}
		echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=edit&custid=$row[0]>$row[0]</td>";
		echo "<td><font face=verdana size=2>$row[1]</td>";
		echo "<td><font face=verdana size=2>$row[2]</td>";
		echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=delete&custid=$row[0]>delete</a></td>";
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