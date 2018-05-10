<?php
# =============================================================================
#
# index.php
#
# Default start page
#
# $Id: index.php,v 1.18 2002/12/20 20:05:20 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: index.php,v $
# Revision 1.18  2002/12/20 20:05:20  webdev
#   * Added 12-20-2002 news sections
#
# Revision 1.17  2002/11/15 19:23:48  webdev
#   * Added extended registration forms
#
# Revision 1.16  2002/11/12 21:26:38  youngd
#   * Reverted to previous versions
#
# Revision 1.14  2002/10/30 17:12:37  youngd
#   * Added lost password link
#
# Revision 1.13  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.12  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.11  2002/09/16 07:58:39  webdev
#   * Many updates
#
# Revision 1.10  2002/09/14 06:39:39  webdev
#   * Removed trigger_error().
#
# Revision 1.9  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.8  2002/09/13 00:15:05  webdev
#   * Converted to UNIX format
#
# Revision 1.7  2002/09/12 22:33:53  webdev
#   * Updates
#
# =============================================================================

    $unsecure = 1;

	// Bring in our standard includes
	require_once("config.php");
	require_once("debug.php");
	require_once("error.php");
	require_once("event.php");
	require_once("functions.php");
	require_once("logging.php");
	require("zzmysql.php");

    require("zzgrabcookie.php");

    debug("index.php Debug set");
    debug("index.php: done loading requires");

?>

<html>
<head>
	<title>The Freight Depot</title>
	
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <meta name="Revision" content="$Revision: 1.18 $">

    <script language="JavaScript" src="js/main.js"></script>
    <script language="JavaScript">
        function test_drive() {
	        window.open("testdrive.php", "drive", 'height=500, width=700');
        }
    </script>

</head>

<?php

    debug("index.php: loading site header");
    require("zzheader.php");

?>


<table width=700 cellpadding=0 cellspacing=0 border=0>
<tr height=10><td colspan=3>&nbsp;</td></tr>


<tr valign=top><td width=200 align=center>

	<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>
		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=7390C0 align=center><font face=verdana size=1><b>REGISTERED? LOGIN NOW!</b></font></td></tr>
		<tr><td bgcolor=FAFAFA align=center>
		<form method="POST" action="mydigiship.php">
			<table width=160>
			<tr><td><font face=tahoma size=1>E-MAIL ADDRESS:</font></td></tr>
			<tr><td><input size=20 name="email"></td></tr>
			<tr><td><font face=tahoma size=1>PASSWORD:</font></td></tr>
			<tr><td><input size=20 name="password" type="password"></td></tr>
			<tr><td align=center><input type=image border=0 src="images/buttons/loginnow.gif" width=99 height=21></td></tr>	
			<tr><td align=center><font face=tahoma size=1><a href=lostpass.php style="font-family: tahoma; font-size: 7pt; color:blue; text-decoration:underline">Lost your password?</a></font>
			</table>
				
		</td></form></tr>
	
		</table>
	</td></tr>
	</table>
	<br>
	<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>
		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=7390C0 align=center><font face=verdana size=1><b>CREATE AN ACCOUNT!</b></font></td></tr>
		<tr><td bgcolor=FAFAFA align=center>
		<form method="POST" action="mydigiship.php">
			<table width=160>
			<tr><td><font face=tahoma size=1>YOUR NAME:</font></td></tr>
			<tr><td><input size=20 name="name"></td></tr>
			<tr><td><font face=tahoma size=1>ADDRESS 1:</font></td></tr>
			<tr><td><input size=20 name="address1"></td></tr>
			<tr><td><font face=tahoma size=1>ADDRESS 2:</font></td></tr>
			<tr><td><input size=20 name="address2"></td></tr>
			<tr><td><font face=tahoma size=1>CITY:</font></td></tr>
			<tr><td><input size=20 name="city"></td></tr>
			<tr><td><font face=tahoma size=1>STATE/PROVINCE:</font></td></tr>
			<tr><td><input size=10 name="state"></td></tr>
			<tr><td><font face=tahoma size=1>POSTAL CODE:</font></td></tr>
			<tr><td><input size=10 name="zip"></td></tr>
			<tr><td><font face=tahoma size=1>COUNTRY:</font></td></tr>
			<tr>
				<td>
					<select name="country" style="font-family:verdana; font-size:10pt">
						<?php
							
							// Build the country selection based on the db table country_codes
							// Display the name, the value is the ANSI 3 letter code
							$codequery = mysql_query("SELECT * FROM country_codes WHERE active=1 ORDER BY country");
							while($codelist = mysql_fetch_row($codequery) ) {
								print "<option value=$codelist[2]>$codelist[0]";
							}
						?>
					</select>
				</td>
			</tr>

			<tr><td><font face=tahoma size=1>COMPANY:</font></td></tr>
			<tr><td><input size=20 name="company"></td></tr>
			<tr><td><font face=tahoma size=1>E-MAIL ADDRESS:</font></td></tr>
			<tr><td><input size=20 name="email"></td></tr>
			<tr><td><font face=tahoma size=1>PHONE:</font></td></tr>
			<tr><td><input size=20 name="phone"></td></tr>
			<tr><td><font face=tahoma size=1>PASSWORD:</font></td></tr>
			<tr><td><input size=20 name="pass1" TYPE="PASSWORD"></td></tr>
			<tr><td><font face=tahoma size=1>PASSWORD AGAIN:</font></td></tr>
			<tr><td><input size=20 name="pass2" TYPE="PASSWORD"></td></tr>
			<tr><td align=center><input type=image border=0 src="images/buttons/registernow.gif" width=99 height=21></td></tr>		
			</table>
				
		</td></form></tr>
	
		</table>
	</td></tr>
	</table>





</td>



<td width=300>
<img src="images/general/welcome.gif" width=231 height=58><br>
<font face='"Trebuchet MS",tahoma' size=2>The Freight Depot's on-line system allows you to rate, ship, and track your freight shipments in real time via the Internet. The Freight Depot gives you the security of great customer service and premium freight carriers to deliver your products on-time and claim free.
<br><br>
The Freight Depot offers unparalleled shipping services at unbeatable rates.
<br><br><br><br>
	<table width="100%">
		<tr>
			<td>
				<font face='"Trebuchet MS", Tahoma' size=2><b>NEWS:</b>
			</td>
		</tr>
		<tr>
			<td>
				<font face='"Trebuchet MS",tahoma' size=2><b><u>12.15.2002:</u></b> The Freight Depot offers E-Commerce operators options for automatic rating on their sites. With just a couple of mouse clicks you can give your customers LTL rates from your own site.
			</td>
		</tr>
		<tr>
			<td>
				<font face='"Trebuchet MS",tahoma' size=2><b><br><u>04.24.2002:</u></b> The Freight Depot now accepts non-LTL shipments to fit your needs. Whether it's airfreight, truckload, flatbed, international or expedited, The Freight Depot's Special Services Department will locate the fastest, most cost-effective mode of transport for your products!
			</td>
		</tr>
	</table><br>
<img src="images/general/learnmore.gif" width=211 height=25>
	<table>
	<tr><Td width=20>&nbsp;</td><td><b>
	<a href="about.php" class="links_general">ABOUT US</a><br>
	<a href="qanda.php" class="links_general">SERVICE BENEFITS</a><br>
	<a href="qanda.php" class="links_general">FREIGHT DEPOT Q&A</a><br>
	<a href="contact.php" class="links_general">CONTACT US</a><br></b>
	</td></tr>
	</table>
<br>

</font>

</td>

<td width=200 align=right>

	<table width=200 cellpadding=0 cellspacing=0 border=0>
	<tr><td align=right>
	<img src="images/general/lady.gif" width=190 height=160><br>
	<a href="qanda.php" class="links_learnmore"><b>LEARN MORE >>></b></a></td></tr></table>
	<br>
	<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>
		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=A20E07 align=center><font face=verdana size=1 color=ffffff><b>READY TO GET STARTED?</b></font></td></tr>
		<tr><td bgcolor=FAFAFA>
			<table><tr><td>
			<font face='"Trebuchet MS",tahoma' size=1>Reviewed the Q & A? Ready to get started? It's easy! Just complete the registration form on the left or <a href="register.php" class="links_inactive">click here</a> to create an account and start shipping now!<br><br>
			If you've already registered, just enter your e-mail and password into the form on the top left or <a href="login.php" class="links_inactive">click here</a> to login.
			</td></tr></table>
		</td></tr>
	
		</table>
	</td></tr>
	</TABLE>
		<BR>
	<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>
		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=A20E07 align=center><font face=verdana size=1 color=ffffff><b>SHIPMENT TRACKING</b></font></td></tr>
		<tr><td bgcolor=FAFAFA align=center>
		<form method=post action=tracking.php>
			<table width=160>
			<tr><td align=center><font face=tahoma size=1>ENTER PRO OR PO NUMBER:</font></td></tr>
			<tr><td align=center><input size=15 name=number></td></tr>
			<tr><td align=center><input type=image order=0 src="images/buttons/trackit.gif" width=99 height=21 border=0></td></tr>		
			</table>
				
		</td></form></tr>
	
		</table>
	</td></tr></table>

</td></tr>

</table>


<?php

    debug("index.php: loading site footer");
    require("zzfooter.php");

?>
