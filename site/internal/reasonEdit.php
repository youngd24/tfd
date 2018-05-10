<?php
# =============================================================================
#
# reasonEdit.php
#
# Cancellation edit page
#
# $Id: reasonEdit.php,v 1.8 2002/10/14 23:13:07 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: reasonEdit.php,v $
# Revision 1.8  2002/10/14 23:13:07  youngd
#   * View and edit modes work now.
#
# Revision 1.7  2002/08/25 21:48:56  youngd
# * updates
#
# Revision 1.6  2002/08/25 21:48:14  youngd
# * updates
#
# Revision 1.5  2002/08/25 21:45:03  youngd
# * updates
#
# Revision 1.4  2002/08/25 21:08:01  youngd
# * updates
#
# Revision 1.3  2002/08/25 21:04:44  youngd
# * Added shipmentcancel.php as the href
#
# Revision 1.2  2002/08/25 20:57:26  youngd
# Updates
# 
# Revision 1.1  2002/08/21 06:13:49  youngd
# * New files from template
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
        <link rel="stylesheet" type="text/css" href="qa.css">
        <meta name="Author" content="$Author: youngd $">
        <meta name="Revision" content="$Revision: 1.8 $">
        <title>Edit Shipment Cancellation Reasons</title>
    </head>

    <body bgcolor=ffffff>
    
		<center>
        <font face=verdana size=3><b><u>CANCELLATION REASONS</u></b></font>
		</center>
        <br>

        <?php

			#
			# Figure out the mode and deal with it
            switch ($mode) {

				#
				# View all the existing records
				#
                case "view":
					
					if ( $sort == "id" ) {
						$query = "select * from cancel_reasons order by id";
					} elseif ( $sort == "reason" ) {
						$query = "select * from cancel_reasons order by reason";
					} elseif ( $sort == "description" ) {
						$query = "select * from cancel_reasons order by description";
					} else {
						$query = "select * from cancel_reasons";
					}

					echo "<table cellpadding=2 cellspacing=2 align=center>";
					echo "<tr>";
					echo "<th style='border-style: ridge; border-width: 1' bgcolor=#C0C0C0><font face=verdana size=2><b><a 	href=$PHP_SELF?mode=view&sort=id>ID</a></b></th>";
					echo "<th style='border-style: ridge; border-width: 1' bgcolor=#C0C0C0><font face=verdana size=2><b><a href=$PHP_SELF?mode=view&sort=reason>REASON</b></td>";
					echo "<th style='border-style: ridge; border-width: 1' bgcolor=#C0C0C0><font face=verdana size=2><b><a href=$PHP_SELF?mode=view&sort=description>DESCRIPTION</b></td>";
					echo "</tr>";

					$result = mysql_query($query) or die (mysql_error());

					while($row = mysql_fetch_row($result)) {
						if ( $color == "silver" ) {
							$color = "white";
							echo "<tr bgcolor=white>";
						} else {
							$color = "silver";
							echo "<tr bgcolor=silver>";
						}
					
						echo "<td><font face=verdana size=2><a href=$PHP_SELF?mode=edit&id=$row[0]>$row[0]</a></td>";
						echo "<td><font face=verdana size=2>$row[1]</td>";
						echo "<td><font face=verdana size=2>$row[2]</td>";
						echo "</tr>";
					}
					
					echo "</table>";
                    break;


				#
				# Add a new record
				#
                case "add":
                    break;


				#
				# Delete an existing record
				#
				case "delete":
					break;
                
                
				#
				# Edit an existing record
				#
				case "edit":

					$query = "select * from cancel_reasons where id=$id";
					$result = mysql_query($query) or die (mysql_error());
					$row = mysql_fetch_row($result);

					echo "<form name=reasonedit method=POST action=$PHP_SELF>";
					echo "<table cellpadding=1 cellspacing=1 align=center border=0>";

					echo "<tr>";
						echo "<td align=right>";
						echo "<font face=verdana size=2><b>ID:</b></font>";
						echo "</td>";
						echo "<td>";
						echo "<input style='font-family: verdana; font-size: 11px' type=text name=id value=$row[0] size=2 disabled>";
						echo "</td>";
					echo "</tr>";

					echo "<tr>";
						echo "<td align=right>";
						echo "<font face=verdana size=2><b>REASON:</b></font>";
						echo "</td>";
						echo "<td>";
						echo "<input style='font-family: verdana; font-size: 11px' type=text name=reason value='$row[1]' size=15>";
						echo "</td>";
					echo "</tr>";

					echo "<tr>";
						echo "<td align=right>";
						echo "<font face=verdana size=2><b>DESCRIPTION:</b></font>";
						echo "</td>";
						echo "<td>";
						echo "<input type=text style='font-family: verdana; font-size: 11px' name=description value='$row[2]' size=45>";
						echo "</td>";
					echo "</tr>";

					echo "<tr>";
						echo "<td align=right>";
						echo "&nbsp;";
						echo "</td>";
						echo "<td align=left>";
						echo "<input type=submit style='font-family: verdana; font-size: 11px' name=change value=change>";
						echo "&nbsp;";
						echo "<input type=reset style='font-family: verdana; font-size: 11px' name=reset value=reset>";
						echo "</td>";
					echo "</tr>";

					echo "</table>";
					echo "</form>";

                    break;
            }
        ?>

        <br><br>
        <div class="t1">
            <center>
                [
                <a href="reasonEdit.php?mode=view">View</a>
                |
                <a href="reasonEdit.php?mode=add">Add</a>
                |
                <a href="reasonEdit.php?mode=add" onClick="JavaScript: window.close();">Close</a>
                ]
            <center>
        </div>

    </body>

</html>
