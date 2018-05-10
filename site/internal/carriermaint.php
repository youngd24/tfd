<?php
# =============================================================================
# 
# carriermaint.php
# 
# Carrier maintenance page
# 
# $Id: carriermaint.php,v 1.9 2002/12/12 20:05:25 webdev Exp $
# 
# Contents Copyright (c) 2002, Transport Investments, Inc.
# 
# Darren Young [darren@younghome.com]
#
# =============================================================================
# 
# ChangeLog:
# 
# $Log: carriermaint.php,v $
# Revision 1.9  2002/12/12 20:05:25  webdev
#   * Added upgrade fields.
#
# Revision 1.8  2002/10/23 23:04:40  youngd
#   * Changed carrier management to open in a new window.
#
# Revision 1.7  2002/10/14 15:00:58  youngd
#   * Removed the ../ on the zzmysql.php require statement.
#
# Revision 1.6  2002/10/03 22:44:56  youngd
#   * Added fuel surcharge management logic and functions.
#
# Revision 1.5  2002/09/17 21:17:43  youngd
#   * Added lower Intranet link
#
# Revision 1.4  2002/09/17 20:57:40  youngd
#   * Changed zzmysql link to include ../
#
# Revision 1.3  2002/09/17 20:44:38  youngd
#   * Testing version
#
# Revision 1.2  2002/09/17 20:10:12  youngd
#   * Testing version
#
# Revision 1.1  2002/09/17 18:24:55  youngd
#   * Initial version
#
# =============================================================================

require("zzmysql.php");

?>

<center><h2><font face=verdana>Carrier Maintenance</font></h2></center>


<?php

if ( $mode == "edit" ) {

	echo "<h3><font face=verdana>EDIT MODE</font></h3>";

	$query = "SELECT * FROM carriers where carrierid='$carrierid'";
	$result = mysql_query($query) or die (mysql_error());

	echo "<table>";
	echo "<form method='GET' action='$PHP_SELF'>";
	while($row = mysql_fetch_row($result)) {

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>ID:</b></td><td><input type='text' value='$row[0]' name='id'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>NAME:</b></td><td><input type='text' value='$row[1]' name='name'></td>";
		echo "<input type='hidden' name='oldname' value='$row[1]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>DISCOUNT:</b></td><td><input type='text' value='$row[2]' name='discount'></td>";
		echo "<input type='hidden' name='olddiscount' value='$row[2]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>SCAC:</b></td><td><input type='text' value='$row[3]' name='scac'></td>";
		echo "<input type='hidden' name='oldscac' value='$row[3]'>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>TYPE:</b></td><td><input type='text' value='$row[4]' name='type'></td>";
		echo "<input type='hidden' name='oldtype' value='$row[4]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>DESCRIPTION:</b></td><td><input type='text' value='$row[5]' name='description'></td>";
		echo "<input type='hidden' name='olddescription' value='$row[5]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>MINIMUM:</b></td><td><input type='text' value='$row[6]' name='minimum'></td>";
		echo "<input type='hidden' name='oldminimum' value='$row[6]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>CCSCAC:</b></td><td><input type='text' value='$row[7]' name='ccscac'></td>";
		echo "<input type='hidden' name='oldccscac' value='$row[7]'>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>ORMARGIN:</b></td><td><input type='text' value='$row[8]' name='ormargin'></td>";
		echo "<input type='hidden' name='oldormargin' value='$row[8]'>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>FUEL SURCHARGE:</b></td><td><input type='text' value='$row[9]' name='fuel_surcharge'></td>";
		echo "<input type='hidden' name='oldfuel_surcharge' value='$row[9]'>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align='right'><b><font face='verdana' size='2'>UPGRADE:</b></td><td><input type='text' value='$row[10]' name='upgrade'></td>";
		echo "<input type='hidden' name='oldupgrade' value='$row[10]'>";
		echo "</tr>";
		
		echo "<input type='hidden' name='mode' value='update'>";

		echo "<tr>";
		echo "<td align='right'><input type='submit' name='submit' value='Submit'></td>";
		echo "<td><input type='reset' value='reset' name='reset'></td>";
		echo "</tr>";
	}

	echo "</form>";
	echo "</table>";

	echo "<br><a href=carriermaint.php><font face=verdana size=1>Return to the list</font></a>";

} elseif ($mode == "update") {

	echo "<h3><font face=verdana>UPDATED</font></h3>";

	if ( "$name" != "$oldname" ) {
		$query = "UPDATE carriers set name='$name' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( "$discount" != "$olddiscount" ) {
		$query = "UPDATE carriers set discount='$discount' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $scac != $oldscac ) {
		$query = "UPDATE carriers set scac='$scac' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $type != $oldtype ) {
		$query = "UPDATE carriers set type='$type' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $description != $olddescription ) {
		$query = "UPDATE carriers set description='$description' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $minimum != $oldminimum ) {
		$query = "UPDATE carriers set minimum='$minimum' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $ccscac != $oldccscac ) {
		$query = "UPDATE carriers set ccscac='$ccscac' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $ormargin != $oldormargin ) {
		$query = "UPDATE carriers set ormargin='$ormargin' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $fuel_surcharge != $oldfuel_surcharge ) {
		$query = "UPDATE carriers set fuel_surcharge='$fuel_surcharge' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	if ( $upgrade != $oldupgrade ) {
		$query = "UPDATE carriers set upgrade='$upgrade' where carrierid='$id'";
		$result = mysql_query($query) or die (mysql_error());
	}

	echo "<br>";
	echo "<br><a href=carriermaint.php><font face=verdana size=1>Return to the list</font></a>";

} elseif ( $mode == "add" ) {

	echo "<table>";
	echo "<form method=GET action=$PHP_SELF>";

		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>NAME:</b></td><td><input type=text value='$row[1]' name=name></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>DISCOUNT:</b></td><td><input type=text value='$row[2]' name=discount></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>SCAC:</b></td><td><input type=text value='$row[3]' name=scac></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>TYPE:</b></td><td><input type=text value='$row[4]' name=type></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>DESCRIPTION:</b></td><td><input type=text value='$row[5]' name=description></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>MINIMUM:</b></td><td><input type=text value='$row[6]' name=minimum></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>CCSCAC:</b></td><td><input type=text value='$row[7]' name=ccscac></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>ORMARGIN:</b></td><td><input type=text value='$row[8]' name=ormargin></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>FUEL SURCHARGE:</b></td><td><input type=text value='$row[9]' name=fuel_surcharge></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=right><b><font face=verdana size=2>UPGHRADE:</b></td><td><input type=text value='$row[10]' name=upgrade></td>";
		echo "</tr>";

		echo "<input type=hidden name=mode value=addnew>";

		echo "<tr>";
		echo "<td align=right><input type=submit name='addnew' value='Add'></td><td><input type=reset value='Reset' name=reset></td>";
		echo "</tr>";

	echo "</form>";
	echo "</table>";

} elseif ( $mode == "addnew" ) {

	$query = "INSERT INTO carriers VALUES (
					'',
					'$name',
					'$discount',
					'$scac',
					'$type',
					'$description',
					'$minimum',
					'$ccscac',
					'$ormargin',
					'$fuel_surcharge',
					'$upgrade')";
	
	$result = mysql_query($query) or die (mysql_error());

	echo "<font face=verdana size=2>Carrier Added</font>";
	echo "<br>";
	echo "<br><a href=carriermaint.php><font face=verdana size=1>Return to the list</font></a>";

} elseif ( $mode == "delete" ) {

	$query = "DELETE FROM carriers WHERE carrierid=$carrierid";

	$result = mysql_query($query) or die (mysql_error());

	echo "<font face=verdana size=2>Carrier Deleted</font>";
	echo "<br>";
	echo "<br><a href=carriermaint.php><font face=verdana size=1>Return to the list</font></a>";
}

else {

	// Just display a list of the known carriers

	echo "<table cellpadding=1 cellspacing=1 align=center>";
	echo "<tr>";
	echo "<td><font face=verdana size=2><b>ID</b></td>";
	echo "<td><font face=verdana size=2><b>NAME</b></td>";
	echo "<td><font face=verdana size=2><b>DISCOUNT</b></td>";
	echo "<td><font face=verdana size=2><b>SCAC</b></td>";
	echo "<td><font face=verdana size=2><b>TYPE</b></td>";
	echo "<td><font face=verdana size=2><b>DESCRIPTION</b></td>";
	echo "<td><font face=verdana size=2><b>MINIMUM</b></td>";
	echo "<td><font face=verdana size=2><b>CCSCAC</b></td>";
	echo "<td><font face=verdana size=2><b>ORMARGIN</b></td>";
	echo "<td><font face=verdana size=2><b>FUEL SURCH.</b></td>";
	echo "<td><font face=verdana size=2><b>UPGRADE</b></td>";
	echo "</tr>";

	$query = "SELECT * FROM carriers";
	$result = mysql_query($query) or die (mysql_error());

	while($row = mysql_fetch_row($result)) {
		if ( $color == "silver" ) {
			$color = "white";
			echo "<tr bgcolor=white>";
		} else {
			$color = "silver";
			echo "<tr bgcolor=silver>";
		}
		echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=edit&carrierid=$row[0]>$row[0]</td>";
		echo "<td><font face=verdana size=2>$row[1]</td>";
		echo "<td><font face=verdana size=2>$row[2]</td>";
		echo "<td><font face=verdana size=2>$row[3]</td>";
		echo "<td><font face=verdana size=2>$row[4]</td>";
		echo "<td><font face=verdana size=2>$row[5]</td>";
		echo "<td><font face=verdana size=2>$row[6]</td>";
		echo "<td><font face=verdana size=2>$row[7]</td>";
		echo "<td><font face=verdana size=2>$row[8]</td>";
		echo "<td><font face=verdana size=2>$row[9]</td>";
		echo "<td><font face=verdana size=2>$row[10]</td>";
		echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=delete&carrierid=$row[0]>delete</a></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<br>";
	echo "<center>";
	echo "<font face=verdana size=1>[ <a href=$PHP_SELF?mode=add>Add new</a> | </font>";
	echo "<a href=JavaScript:window.close()><font face=verdana size=1>Close</a> ] </font>";
	echo "</center>";
}


?>