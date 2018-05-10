<?php
# =============================================================================
#
# mydigiship.php
#
# Personal page
#
# $Id: mydigiship.php,v 1.29 2003/02/14 20:02:21 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: mydigiship.php,v $
# Revision 1.29  2003/02/14 20:02:21  youngd
#   * Added some clean up code.
#
# Revision 1.28  2003/02/14 18:34:12  youngd
#   * Generation and display of accessorials is now dynamic.
#
# Revision 1.27  2003/02/10 20:33:54  youngd
#   * Renamed accessorial fields to new reference codes.
#
# Revision 1.26  2003/01/24 21:05:43  webdev
#   * Changed the method to GET from POST when pushing to the rating.php
#     page.
#
# Revision 1.25  2003/01/06 22:18:46  webdev
#   * Changed typo from - to = in the revision tag
#
# Revision 1.24  2002/12/20 20:05:20  webdev
#   * Added 12-20-2002 news sections
#
# Revision 1.23  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.21.2.4  2002/11/11 18:08:20  webdev
#   * Added test to see if allowPasswordChange and allowProfileEdit is set
#
# Revision 1.21.2.3  2002/11/08 22:02:09  webdev
#   * Added the whole test/read/display of default billing if necessary.
#
# Revision 1.21.2.2  2002/11/07 22:30:28  webdev
#   * Changed profile to include mode=view
#
# Revision 1.21.2.1  2002/11/05 08:44:19  webdev
#   * Changed password link from jscript to be the changepass page
#   * Changed profile link from jscript to the the profile page.
#
# Revision 1.21  2002/11/03 09:11:29  youngd
#   * Added new news and account options sections
#
# Revision 1.20  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.19  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.18  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.17  2002/09/19 14:36:07  youngd
#  * Removed accessorial price display from the 'my' page
#
# Revision 1.16  2002/09/19 02:55:17  youngd
#   * Schedule change updates
#
# Revision 1.15  2002/09/16 23:32:55  youngd
#   * Added headers and other changes
#
# Revision 1.14  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.13  2002/09/13 08:34:54  webdev
#   * Many updates.
#   * Added services section in the rating page
#
# Revision 1.12  2002/09/13 05:49:40  webdev
#   * Added more debug lines.
#
# Revision 1.11  2002/09/13 01:46:18  webdev
#   * Changed requries to be variables instead of file names
#
# Revision 1.10  2002/09/13 01:38:18  webdev
#   * Start of accessorials addition
#
# Revision 1.9  2002/09/13 00:47:56  webdev
#   * Cleaned up page. Added tabs around tables to separate elements in a more
#     physical manner
#
# Revision 1.8  2002/09/12 22:48:21  webdev
#   * Converted to UNIX format
#
# Revision 1.7  2002/09/12 22:48:05  webdev
#   * Added php source header
#
# =============================================================================

$nouser = 0;

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("mydigiship.php: entering after initial requires");

// check for register
if ($email and $pass1 and $pass2) {
    debug("mydigiship.php: email, pass1 and pass2 are set, loading zzregister.php");
    require('zzregister.php');
}

// check for login information
if ($email and $password) {
    debug("mydigiship.php: Loading zznewlogin.php");
    require('zznewlogin.php');
} else {
    debug("mydigiship.php: Loading zzgrabcookie.php");
    require('zzgrabcookie.php');
}

debug("mydigiship.php: Loading zzmydigi.php");
require ('zzmydigi.php');

?>


<html>
<head>
	<title>The Freight Depot > My Freight Depot</title>
	
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <meta name="Revision" content="$Revision: 1.29 $">

    <script language="JavaScript" src="js/main.js"></script>

</head>


<?php

    debug("mydigiship.php: Loading zzheader.php");
    require('zzheader.php');

?>

<table width=700 cellpadding=0 cellspacing=0 border=0>
<tr height=10>
    <td colspan=3>&nbsp;
    </td>
</tr>


<tr valign=top><td width=200 align=center>

	<table cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td bgcolor=000000>

                <!-- Shipment Rating Section -->
		        <table width="200" cellspacing="1" cellpadding="0">
		            <tr height="20">
                        <td bgcolor="7390C0" align="center">
                            <font face="verdana" size="1"><b>RATE AN LTL SHIPMENT</b></font>
                        </td>
                    </tr>
		            <tr>
                        <td bgcolor="FAFAFA" align="center">
		                    <form method="GET" action="rating.php">
			                <table>
			                    <tr>
                                    <form method="post" action="rating.php" onSubmit="return validate(this);">
			                            <td width="110">
                                            <font face="verdana" size=1 color="000000">Origin Zip Code:</font>
                                        </td>
			                            <td align="left">
                                            <input size="6" maxlength="5" name="origin">
                                        </td>
                                </tr>
			                    <tr>
                                    <td>
                                        <font face="verdana" size="1" color="000000">Destination Zip:</font>
                                    </td>
			                        <td align="left">
                                        <input size="6" maxlength="5" name="destination">
                                    </td>
                                </tr>
			                    <tr>
                                    <td>
                                        <font face="verdana" size="1" color="000000">Weight (lbs):</font>
                                    </td>
			                        <td align="left">
                                        <input size="6" maxlength="5" name="weight">
                                    </td>
                                </tr>
			                    <tr>
                                    <td>
                                        <font face="verdana" size="1" color="000000">Class<font size="1">:</font>
                                    </td>

                                    <!-- Shipment classes. Eventually should be
                                        dynamic from a database table -->
			                        <td align="left">
                                        <select name="shipclass">
                                            <option value="50">50
                                            <option value="55">55
                                            <option value="60">60
			                                <option value="65">65
                                            <option value="70">70
                                            <option value="77">77
                                            <option value="85">85
                                            <option value="92">92
			                                <option value="100">100
                                            <option value="110">110
                                            <option value="125">125
                                            <option value="150">150
			                                <option value="175">175
                                            <option value="200">200
                                            <option value="250">250
                                            <option value="300">300
			                                <option value="400">400
                                            <option value="500">500
                                        </select>
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td>
                                        <font face="verdana" size="1" color="000000">
                                            <u>Additional Services:</u><font size="1">
                                        </font>
                                    </td>
                                </tr>
                                
								<!-- The field names here should match the standard names we've used for
								     the accessorials. Check the refcode field of the accessorials table
									 for these standard charge codes (SCC's). -->
                                <tr>
                                    <td align="left" colspan="2">
                                        <table>

											<!-- Generate the accessorial fields based on the database -->
											<?php
												$accessorials = getAccessorialListing();

												for ($i = 0;$i<count($accessorials);$i++) {
													$accname = getAccessorialNameByRefcode($accessorials[$i]);

													echo "<tr valign=middle>";
													echo "<td align=left valign=middle>";
													echo "<font face=verdana size=1>";
													echo "<input type=checkbox name=\"$accessorials[$i]\">";
													echo "&nbsp;$accname";
													echo "</font>";
													echo "</td>";
													echo "</tr>";
												}

												// Cleanliness is next to uselessness...
												unset($accessorials);
												unset($accname);
												unset($i);
											?>

											<!-- These were hard coded and can go away eventually
                                            <tr valign=middle>
                                                <td align="left" valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="LFTORG">
                                                            &nbsp;Liftgate Origin
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="LFTDST">
                                                            &nbsp;Liftgate Destination
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="RSDPCK">
                                                            &nbsp;Residential Pickup
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="INSPCK">
                                                            &nbsp;Inside Pickup
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="INSDEL">
                                                            &nbsp;Inside Delivery
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="HAZMAT">
                                                            &nbsp;Hazmat
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="RSDDEL">
                                                            &nbsp;Residential Delivery
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="CLLPCK">
                                                            &nbsp;Call For Pickup
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr align=middle>
                                                <td align=left valign=middle>
                                                    <font face=verdana size=1>
                                                        <input type=checkbox name="CLLDEL">
                                                            &nbsp;Call Before Delivery
                                                    </font>
                                                </td>
                                            </tr>
											-->
                                        </table>
                                    </td>
			                    <tr>
                                    <td colspan=2 align=center>
			                            <input type="image" src="images/buttons/rateit.gif" border="0" width="99" height="21">
                                    </td>
                                    </form>
                                </tr>

                                <tr>
                                    <td align="center" colspan="2">
                                        <font face="verdana" size="1">
                                        <a href="rating.php" class="links_inactive">SPECIAL SERVICES<br>(NON-LTL) RATING</a>
                                        </font>
                                    </td>
                                </tr>

                            </table>
		                </td>
                    </form>
                </tr>
		    </table>
	        </td>
        </tr>
	</table>
	
    <br>
	
	<table cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td bgcolor=000000>
		        <table width=200 cellspacing=1 cellpadding=0>
		            <tr height=20>
                        <td bgcolor=A20E07 align=center>
                            <font face=verdana size=1 color=ffffff><b>WHAT'S NEW AT THE FREIGHT DEPOT?</b></font>
                        </td>
                    </tr>
		            <tr>
                        <td bgcolor=FAFAFA>
			                <table>
                                <tr>
                                    <td>
			                            <font face='verdana' size=1><b>12.15.02:</b> 
                                            The Freight Depot offers E-Commerce operators options for automatic rating on their sites. With just a couple of mouse clicks you can give your customers LTL rates from your own site.<br><br>
			                        </td>
                                </tr>
                            </table>
		                </td>
                    </tr>
                </table>
	        </td>
        </tr>
	</table>
	<br>
</td>


<td width=300>
    &nbsp;&nbsp;<img src="images/general/welcome.gif" width=231 height=58><br>
    &nbsp;&nbsp;<font face='"Trebuchet MS",tahoma' size=2>
        The Freight Depot offers unparalleled shipping services at unbeatable rates.
    &nbsp;&nbsp;<br><br>
    &nbsp;&nbsp;<b>OUR SERVICES:</B><BR>
    &nbsp;&nbsp;<a href="rating.php" class="links_general">LTL</a>
    &nbsp;&nbsp;| 
    &nbsp;&nbsp;<a href="ratingspecial.php?service=airfreight" class="links_general">Airfreight</a> 
    &nbsp;&nbsp;| 
    &nbsp;&nbsp;<a href="ratingspecial.php?service=airfreight" class="links_general">Expedited</a>
    &nbsp;&nbsp;<br>
    &nbsp;&nbsp;<a href="ratingspecial.php?service=canada" class="links_general">Canada</a> 
    &nbsp;&nbsp;| 
    &nbsp;&nbsp;<a href="ratingspecial.php?service=mexico" class="links_general">Mexico</a> 
    &nbsp;&nbsp;| 
    &nbsp;&nbsp;<a href="ratingspecial.php?service=truckload" class="links_general">Flatbed</a>
    &nbsp;&nbsp;<br>
    &nbsp;&nbsp;<a href="ratingspecial.php?service=truckload" class="links_general">Truckload</a> 
    &nbsp;&nbsp;| 
    &nbsp;&nbsp;<a href="ratingspecial.php?service=international" class="links_general">International</a>   
    &nbsp;&nbsp;<br>
    &nbsp;&nbsp;<br>
    &nbsp;&nbsp;<b>WHAT WOULD YOU LIKE TO DO?</b><br>
    &nbsp;&nbsp;<a href="rating.php" class="links_general">Rate & book a new shipment</a><br>
    &nbsp;&nbsp;<a href="tracking.php" class="links_general">Track a shipment I've already scheduled</a><br>
    &nbsp;&nbsp;<a href="reports.php" class="links_general">View shipment reports</a><br>
    &nbsp;&nbsp;<a href="contact.php" class="links_general">Speak with customer service</a><br>
    &nbsp;&nbsp;<a href="about.php" class="links_general">Learn more about The Freight Depot</a><br>
    &nbsp;&nbsp;<a href="qanda.php" class="links_general">Discover how our service benefits you</a><br>
    &nbsp;&nbsp;<a href="contact.php" class="links_general">Contact us</a><br>
    &nbsp;&nbsp;<a href="claims.php" class="links_general">Submit a claim</a><br>
</td>

<td width=200 align=right>

    <!-- PERSONAL ACCOUNT OPTIONS TABLE -->
	<table cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td bgcolor=000000>
                <table width=170 cellspacing=1 cellpadding=0>
		            <tr height=20>
                        <td bgcolor=7390C0 align=center>
                            <font face=verdana size=1 color=000000><b>ACCOUNT OPTIONS</b></font>
                        </td>
                    </tr>
		            <tr>
                        <td bgcolor=FAFAFA align=center>
			                <table width=160>
			                    <tr>
                                    <td>
                                        <center>
										
										<!-- Display the change password link if it's enabled -->
										<?php
											if ( getConfigValue("allowPasswordChange") == 1) {
												print "<a href=changepass.php class='links_general'>Change Password</a>";
												print "<br>";
											}
										?>
										
										<!-- Display the change profile link if it's enabled -->
										<?php
											if ( getConfigValue("allowProfileEdit") == 1) {
												print "<a href='profile.php?mode=view' class='links_general'>Edit Profile</a>";
												print "<br>";
											}
										?>

                                        </center>
			                        </td>
                                </tr>
			                </table>
		                </td>
                    </tr>
		        </table>
	        </td>
        </tr>
    </table>
    <br>

    <!-- SHIPMENT TRACKING TABLE -->
	<table cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td bgcolor=000000>
		        <table width=170 cellspacing=1 cellpadding=0>
		            <tr height=20>
                        <td bgcolor=7390C0 align=center><font face=verdana size=1 color=000000><b>SHIPMENT TRACKING</b></font></td>
                    </tr>
		            <tr>
                        <td bgcolor=FAFAFA align=center>
		                    <form method=post action=tracking.php>
			                    <table width=160>
			                        <tr>
                                        <td align=center>
                                            <font face=tahoma size=1>ENTER PRO OR PO NUMBER:</font>
                                        </td>
                                    </tr>
			                        <tr>
                                        <td align=center>
                                            <input size=15 name=number>
                                        </td>
                                    </tr>
			                        <tr>
                                        <td align=center>
                                            <input type=image order=0 src="images/buttons/trackit.gif" width=99 height=21 border=0>
                                        </td>
                                    </tr>		
			                    </table>
		                    </td>
                        </form>
                    </tr>
		        </table>
	        </td>
        </tr>
    </table>
    <br>
	<table cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td bgcolor=000000>
                <table width=170 cellspacing=1 cellpadding=0>
		            <tr height=20>
                        <td bgcolor=7390C0 align=center>
                            <font face=verdana size=1 color=000000><b>RECENT SHIPMENTS</b></font>
                        </td>
                    </tr>
		            <tr>
                        <td bgcolor=FAFAFA align=center>
			                <table width=160>
			                    <tr>
                                    <td>
			                            <?php
			                                $i = 0;
			                                while ($recentline = mysql_fetch_array($getrecentshipments)) {
				                                if ($recentline[ponumber] != "") {
					                                echo "<font face=verdana size=1><b>PO #</b>$recentline[ponumber], </font>"; 
				                                }
				                                echo "<font face=verdana size=1><b>BOL #</b>$recentline[shipmentid] <b>to</b> 
                                                $recentline[company]</b><br>[ <a href=tracking.php?number=$recentline[shipmentid] 
                                                class=links_general2>TRACK IT</a> | <a href=bol.php?shipmentid=$recentline[shipmentid]
                                                class=links_general2>VIEW BOL</a> ]</font><br><br>";
			                                    $i++;
			                                }
			                                if ($i == 0) {
				                                echo "<font face=tahoma size=1>You have no recent shipments. 
                                                Please &nbsp;<a href=rating.php class=links_general>click here</a>&nbsp; 
                                                to rate and schedule a new shipment.<br><br><br>"; 
			                                } else if ($i == 5) {
				                                echo "<center><a href=tracking.php class=links_general2>more recent shipments</a>
                                                </center>";
			                                }
			                            ?>
			                        </td>
                                </tr>
			                </table>
		                </td>
                    </tr>
		        </table>
	        </td>
        </tr>
    </table>
</td>
</tr>

</table>


<?php

require('zzfooter.php');

// If we're set to check the default billing, prompt for it if it's not set
if ( getConfigValue("checkDefaultBilling") == 1 ) {

	if ( isDefaultBillingSet($userarray[0]) ) {
		debug("mydigiship.php: customer $userarray[0] has the default billing set");
	} else {
		debug("mydigiship.php: customer $userarray[0] DOES NOT have the default billing set");
	
		print "<script language=\"JavaScript\">";
		print "var ewin=window.open('nobill.php', 'addressWindow', 'width=350,height=350');";
		print "</script>";

	}
}

debug("mydigiship.php: leaving");

?>
