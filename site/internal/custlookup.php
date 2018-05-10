<?php
# =============================================================================
#
# custlookup.php
#
# Customer Lookup Page
#
# $Id: custlookup.php,v 1.8 2002/11/26 20:20:28 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: custlookup.php,v $
# Revision 1.8  2002/11/26 20:20:28  webdev
#   * Added additional sorts.
#
# Revision 1.7  2002/11/21 23:34:12  webdev
#   * Added edit link and javascript function.
#
# Revision 1.6  2002/11/21 14:35:25  webdev
#   * Added the ability to sort by column heading.
#
# Revision 1.5  2002/11/21 00:31:51  webdev
#  * Added status bar and status messages.
#
# Revision 1.4  2002/11/21 00:15:51  webdev
#   * Added mailto link when searching by name.
#
# Revision 1.3  2002/11/21 00:10:32  webdev
#   * First working / testable version.
#
# Revision 1.2  2002/10/16 07:06:40  youngd
#   * Initial version.
#
# Revision 1.1  2002/10/16 06:53:54  youngd
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

?>

<html>
<head>

    <!-- Pull in the stylesheets -->
    <!--<link rel="stylesheet" type="text/css" href="/internal/qa.css">-->
    <link rel="stylesheet" type="text/css" href="/css/main.css">

    <!-- Set standard meta tags -->
    <meta name="Author" content="$Author: webdev $">
    <meta name="Revision" content="$Revision: 1.8 $">
    
    <!-- Set the page title (include the page revision) -->
    <title>Customer Lookup ($Revision: 1.8 $)</title>
    
    <!-- Pull in standard site and internal JavaScript functions -->
    <script language="JavaScript" src="/internal/common.js"></script>
    <script language="JavaScript" src="/js/main/js"></script>
    <script language="JavaScript" src="/js/cookies.js"></script>

	<!-- Customer search functions -->
	<script language="JavaScript">

        /* Validate and dispatch a search request */
		function customerSearch(field) {

			if ( field.value == "" ) {
				if ( field.name == "foo" ) {
					alert("Hey punk, enter something in the company field already...");
					return;
				}
				if ( field.name == "bar" ) {
					alert("All right chode, put something in the name field...");
					return;
				}
			
			} else {
				if ( field.name == "foo" ) {
					url = "/internal/custlookup.php?company=" + field.value;
					window.navigate(url);
				}
				if ( field.name == "bar" ) {
					url = "/internal/custlookup.php?name=" + field.value;
					window.navigate(url);
				}
			}
		}


        /* Open the editor window for a given customer id */
		function openCustomerEditWindow(custid) {

			var url = "/internal/custedit.php?mode=view&custid=" + custid;
			window.open(url, "customerEditWindow", "width=700, height=500");
		}

	</script>

</head>

<body>


<?php

# =============================================================================
#                              SEARCH BY COMPANY
# =============================================================================
if ($company) {

	// Find out how many records are there
	$numrowqry = mysql_query("SELECT count(custid) FROM customers WHERE company like '%$company%'");
	$numrows = mysql_fetch_row($numrowqry);

    // No records in the database, tell them and offer to search again
	if ( $numrows[0] == 0 ) {

		echo "<font face=verdana size=3><b>NO RECORDS FOUND</b></font>";
		echo "<br><br>";
		echo "<font face=verdana size=2>Click <a href=/internal/custlookup.php style='text-decoration:none;color:blue;'>here</a> to search again.</font>";
		exit;

	} else {

		echo "<font face=verdana size=4><b><center><u>SEARCH RESULTS</u></center></b></font>";
		echo "<br>";

		echo "<font face=verdana size=1><i><center>(Click on the column header to change the sort order)</center></i></font>";
		echo "<br>";

        // Create the query based on the requested sort order
        if ( $sort == "custid" ) {
            $query = mysql_query("SELECT * FROM customers WHERE company LIKE '%$company%' ORDER BY custid");
        } elseif ( $sort == "name" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE company LIKE '%$company%' ORDER BY name");
        } elseif ( $sort == "email" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE company LIKE '%$company%' ORDER BY email");
        } elseif ( $sort == "company" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE company LIKE '%$company%' ORDER BY company");
        } elseif ( $sort == "phone" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE company LIKE '%$company%' ORDER BY phone");
        } else {
	        $query = mysql_query("SELECT * FROM customers WHERE company LIKE '%$company%'");
        }


		$color = "silver";

		echo "<table border=0 cellpadding=1 cellspacing=1 align=center>";

        // The header row
		echo "<tr bgcolor=7390C0 align=center>";
		echo "<td><a href=$PHP_SELF?company=$company&sort=custid 
              onMouseOver=\"status='Sort by customer id'; return true;\"
              style='text-decoration:none;color:black;'>
              <font face=verdana size=2><b>&nbsp;CUSTID&nbsp;</b></font></a>
              <a href=$PHP_SELF?company=$company&sort=custid&dir=asc><img src=/images/sort_arrow_down.gif border=0 width=13 height=13 onMouseOver=\"status='Sort ascending'; return true;\"></a>
              <img src=/images/sort_arrow_up.gif border=0 width=13 height=13>&nbsp;
              </td>";
		echo "<td><a href=$PHP_SELF?company=$company&sort=name onMouseOver=\"status='Sort by customer name'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>NAME</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?company=$company&sort=email onMouseOver=\"status='Sort by customer email'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>EMAIL</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?company=$company&sort=company onMouseOver=\"status='Sort by customer company'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>COMPANY</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?company=$company&sort=phone onMouseOver=\"status='Sort by customer phone'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>PHONE</b></font></a></td>";
		echo "</tr>";

        // Get the results and put them on the screen
		while ($result = mysql_fetch_array($query) ) {

			if ( $color == "silver" ) {
				$color = "white";
				echo "<tr bgcolor=white>";
			} else {
				$color = "silver";
				echo "<tr bgcolor=silver>";
			}

			echo "<td><a href='JavaScript:openCustomerEditWindow($result[custid]);' onMouseOver=\"status='Edit customer $result[custid]'; return true;\" style='text-decoration:none;color:blue;'><font face=verdana size=2>$result[custid]</font></a></td>";
			echo "<td><font face=verdana size=2>$result[name]</font></td>";
			echo "<td><font face=verdana size=2><a href='mailto:$result[email]' onMouseOver=\"status='Send email to $result[email]'; return true;\">$result[email]</a></font></td>";
			echo "<td><font face=verdana size=2>$result[company]</font></td>";
			echo "<td><font face=verdana size=2>$result[phone]</font></td>";
			echo "</tr>";
		}

		echo "</table>";

		echo "<br>";
		echo "<center>";
		echo "<font face=verdana size=2>[ </font>";
		echo "<font face=verdana size=2><a href=/internal/custlookup.php style='color:blue;text-decoration:none' onMouseOver=\"status='Search again'; return true;\">search</a> | </font>";
		echo "<font face=verdana size=2><a href='JavaScript:window.print();' style='color:blue;text-decoration:none' onMouseOver=\"status='Print this page'; return true;\">print</a> | </font>";
		echo "<font face=verdana size=2><a href='JavaScript:window.close();' style='color:blue;text-decoration:none' onMouseOver=\"status='Close this window'; return true;\">close</a> ]</font>";

		echo "</center>";

		exit;
	}

}

# =============================================================================
#                               SEARCH BY NAME
# =============================================================================
elseif ($name) {

	// Find out how many records are there
	$numrowqry = mysql_query("SELECT count(custid) FROM customers WHERE name like '%$name%'");
	$numrows = mysql_fetch_row($numrowqry);

	if ( $numrows[0] == 0 ) {

		echo "<font face=verdana size=3><b>NO RECORDS FOUND</b></font>";
		echo "<br><br>";
		echo "<font face=verdana size=2>Click <a href=/internal/custlookup.php style='text-decoration:none;color:blue;'>here</a> to search again.</font>";
		exit;

	} else {

		echo "<font face=verdana size=4><b><center><u>SEARCH RESULTS</u></center></b></font>";
		echo "<br>";

		echo "<font face=verdana size=1><i><center>(Click on the column header to change the sort order)</center></i></font>";
		echo "<br>";

        // Create the query based on the requested sort order
        if ( $sort == "custid" ) {
            $query = mysql_query("SELECT * FROM customers WHERE name LIKE '%$name%' ORDER BY custid");
        } elseif ( $sort == "name" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE name LIKE '%$name%' ORDER BY name");
        } elseif ( $sort == "email" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE name LIKE '%$name%' ORDER BY email");
        } elseif ( $sort == "company" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE name LIKE '%$name%' ORDER BY company");
        } elseif ( $sort == "phone" ) {
	        $query = mysql_query("SELECT * FROM customers WHERE name LIKE '%$name%' ORDER BY phone");
        } else {
	        $query = mysql_query("SELECT * FROM customers WHERE name LIKE '%$name%'");
        }

		$color = "silver";

        // The header row
		echo "<table border=0 cellpadding=1 cellspacing=1 align=center>";
		echo "<tr bgcolor=7390C0 align=center>";
		echo "<td><a href=$PHP_SELF?name=$name&sort=custid onMouseOver=\"status='Sort by customer id'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>CUSTID</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?name=$name&sort=name onMouseOver=\"status='Sort by customer name'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>NAME</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?name=$name&sort=email onMouseOver=\"status='Sort by customer email'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>EMAIL</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?name=$name&sort=company onMouseOver=\"status='Sort by customer company'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>COMPANY</b></font></a></td>";
		echo "<td><a href=$PHP_SELF?name=$name&sort=phone onMouseOver=\"status='Sort by customer phone'; return true;\" style='text-decoration:none;color:black;'><font face=verdana size=2><b>PHONE</b></font></a></td>";
		echo "</tr>";

		while ($result = mysql_fetch_array($query) ) {

			if ( $color == "silver" ) {
				$color = "white";
				echo "<tr bgcolor=white>";
			} else {
				$color = "silver";
				echo "<tr bgcolor=silver>";
			}

			echo "<td><a href='JavaScript:openCustomerEditWindow($result[custid]);' onMouseOver=\"status='Edit customer $result[custid]'; return true;\" style='text-decoration:none;color:blue;'><font face=verdana size=2>$result[custid]</font></a></td>";
			echo "<td><font face=verdana size=2>$result[name]</font></td>";
			echo "<td><font face=verdana size=2><a href='mailto:$result[email]' onMouseOver=\"status='Send mail to $result[email]'; return true;\">$result[email]</a></font></td>";
			echo "<td><font face=verdana size=2>$result[company]</font></td>";
			echo "<td><font face=verdana size=2>$result[phone]</font></td>";
			echo "</tr>";
		}

		echo "</table>";

		echo "<br>";
		echo "<center>";
		echo "<font face=verdana size=2>[ </font>";
		echo "<font face=verdana size=2><a href=/internal/custlookup.php style='color:blue;text-decoration:none' onMouseOver=\"status='Search again'; return true;\">search</a> | </font>";
		echo "<font face=verdana size=2><a href='JavaScript:window.print();' style='color:blue;text-decoration:none' onMouseOver=\"status='Print this page'; return true;\">print</a> | </font>";
		echo "<font face=verdana size=2><a href='JavaScript:window.close();' style='color:blue;text-decoration:none' onMouseOver=\"status='Close this window'; return true;\">close</a> ]</font>";
		echo "</center>";

		exit;
	}

}

# =============================================================================
#                             JUST PAINT THE SCREEN
# =============================================================================
else {

	$form = <<< END_OF_FORM

	<font face=verdana size=3><b><u>CUSTOMER SEARCH:</u></b></font>
	<br><br>
	
	<table>

		<tr>
			<td align=right><font face=verdana size=2><b>BY COMPANY:&nbsp;</b></font></td>
			<td><input type=text size=25 style='font-family:verdana; font-size: 9pt' name=foo></td>
			<td>&nbsp;<a href='JavaScript:customerSearch(foo)' style='font-family:verdana; font-size:7pt; color:blue; text-decoration:none' onMouseOver="status='Search by company name'; return true;">[go]</a></td>
		</tr>

		<tr>
			<td align=right><font face=verdana size=2><b>BY NAME:&nbsp;</b></font></td>
			<td><input type=text size=25 style='font-family:verdana; font-size: 9pt' name=bar></td>
			<td>&nbsp;<a href='JavaScript:customerSearch(bar)' style='font-family:verdana; font-size:7pt; color:blue; text-decoration:none' onMouseOver="status='Search by person\'s name'; return true;">[go]</a></td>
		</tr>

	</table>

END_OF_FORM;

	echo $form;

}



?>

</body>
</html>
