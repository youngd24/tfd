<?php
# =============================================================================
#
# cancelshipment.php
#
# Page to cancel a shipment and remove all traces of it in the system
#
# $Id: cancelshipment.php,v 1.26 2002/10/25 22:26:01 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
# 
# Darren Young [darren@younghome.com]
# 
# =============================================================================
#
# Description:
#
# Right now the system we have does not contain the ability to distinguish
# between active, inactive or cancelled shipments. When s customer tells us they
# want to cancel an order we basically have to delete it from the active database
# in production.
#
# Now, over time this will have to change since it's not the ideal method of
# dealing with cancellations. Additionally, it would be nice to have some way to
# store the reason that they cancelled so we may review them later and figure out
# ways to reduce the rate of cancellation and if there are some major problems
# with the way we operate.
#
# To delete a shipment entirely the process is fairly simple and is broken down
# into the following basic steps:
#
#   1. Delete the record from the shipment table based on the shipmentid (bol)
#   2. Delete all the records from the shipmentstatus tables based on the
#      shipmentid (bol).
#   3. Delete the quote from the quotes table based on the quote number that was
#      previously in the shipment table. Granted, this should happen first since
#      we won't have access to the quote id after the shipment record has been
#      destroyed.
#
# This is the method right now that I'm dealing with it. This could (and should)
# change over time.
#
# ==============================================================================
#
# Todo:
#
# Make sure that shipments that have been delivered can't be deleted.
#
# ==============================================================================
#
# ChangeLog:
# 
# $Log: cancelshipment.php,v $
# Revision 1.26  2002/10/25 22:26:01  youngd
#   * All works now.
#
# Revision 1.25  2002/10/25 22:14:48  youngd
#   * Added delete of shipment accessorials
#
# Revision 1.24  2002/10/15 06:55:04  youngd
#   * Added js include
#   * Added rev to title
#
# Revision 1.23  2002/10/15 06:24:58  youngd
#   * Added rev to title
#
# Revision 1.22  2002/10/14 23:12:01  youngd
#   * Added edit link.
#
# Revision 1.21  2002/10/14 18:11:07  youngd
#   * Added quick links
#
# Revision 1.20  2002/10/09 23:37:13  youngd
#   * Cancel shipment now works and deletes the necessary accounting
#     information.
#
# Revision 1.19  2002/10/09 23:05:31  youngd
#   * Added the TODO section.
#
# Revision 1.18  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.17  2002/10/09 18:21:30  youngd
#   * Cleaned up comment header.
#
# Revision 1.16  2002/10/08 16:47:40  youngd
#   * Working, testable version.
#
# Revision 1.15  2002/08/29 12:37:11  youngd
# no message
#
# Revision 1.14  2002/08/25 21:08:02  youngd
# * updates
#
# Revision 1.13  2002/08/25 21:04:44  youngd
# * Added shipmentcancel.php as the href
#
# Revision 1.12  2002/08/25 21:02:11  youngd
# * Change openReasonEditWin to be openReasonEditWindow
#
# Revision 1.11  2002/08/25 20:57:26  youngd
#3Updates
#
# Revision 1.10  2002/08/21 07:03:41  youngd
# * Dynamic option select list builds dynamically now (correctly)
#
# Revision 1.9  2002/08/21 06:42:19  youngd
# * Added dynamic build of reason options
# 
# Revision 1.8  2002/08/21 06:13:38  youngd
# * Modified links for reason edit and bol search to contain new names
# 
# Revision 1.7  2002/08/20 21:25:26  youngd
# * Added addiditional rypes
# 
# Revision 1.6  2002/08/20 20:25:57  youngd
# working
# 
# Revision 1.5  2002/08/20 20:23:20  youngd
# updates
#
# Revision 1.4  2002/08/20 18:57:00  youngd
# * Added more
#
# Revision 1.3  2002/08/20 18:36:24  youngd
# * Added db connection code
# 
# Revision 1.2  2002/08/20 18:08:12  youngd
# * Added detailed description of the cancellation process
#
# Revision 1.1  2002/08/20 17:31:40  youngd
# * Initial version
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

require_once("zzmysql.php");
require_once("zzaccounting.php");


    // We've been called from the post so we can go ahead and cancel the
    // shipment.
    if ( $bolnumber ) {

		?>
		<head>
            <title>Shipment Cancellation Screen ($Revision: 1.26 $)</title>
            <link rel="stylesheet" type="text/css" href="qa.css">
		    <script language="JavaScript" src="/internal/common.js"></script>
        </head>


		<?php        

		$findq = mysql_query("SELECT * FROM shipment where shipmentid=$bolnumber");
		$findset = mysql_fetch_array($findq);

		# If we found the shipment record, go to delete it.
		if ( $findset[0] ) {


			#
			# Delete the shipment record
			#
			$remove_ship_q = mysql_query("DELETE FROM shipment WHERE shipmentid='$bolnumber'") or
				die(mysql_error());

			# That query should only return 1
			if ( $remove_ship_q == 1 ) {
				print "<center><div class=t2>Shipment record for $bolnumber deleted...</div></center>";
			} else {
				print "<center><div class=t2>Failed to delete shipment record, please contact support</div></center>";
			}



			#
			# Delete the shipment status records associated with that shipment
			#
			$remove_stat_q = mysql_query("DELETE FROM shipmentstatus WHERE shipmentid=$bolnumber") or
				die(mysql_error());

			# That query can return several results
			if ( $remove_stat_q ) {
				print "<center><div class=t2>Removed status record(s) for $bolnumber...</div></center>";
			} else {
				print "<center><div class=t2>Failed to delete shipment status record(s), please contact support</div></center>";
			}


			#
			# Delete the shipment accessorial records associated with that shipment
			#
			$remove_stat_q = mysql_query("DELETE FROM shipmentaccessorials WHERE shipmentid=$bolnumber") or
				die(mysql_error());

			# That query can return several results
			if ( $remove_stat_q ) {
				print "<center><div class=t2>Removed accessorial record(s) for $bolnumber...</div></center>";
			} else {
				print "<center><div class=t2>Failed to delete shipment accessorial record(s), please contact support</div></center>";
			}

			#
			# Open up a connection to the Postgres database
			#
			if ( $conn = postgresconnect() ) {
				print "<center><div class=t2>Connected to the accounting database</div></center>";
			} else {
				print "<center><div class=t2>Failed to connect to Postgres, please contact support</div></center>";
			}

			#
			# Delete the purchase order from the shipment
			#
			if ( deletePurchaseOrder($bolnumber, $conn) ) {
				print "<center><div class=t2>Removed purchase order $bolnumber...</div></center>";
			} else {
				print "<center><div class=t2>Failed to delete purchase order $bolnumber, please contact support</div></center>";
			}

			#
			# Delete the sales order from the shipment
			#
			if ( deleteSalesOrder($bolnumber, $conn) ) {
				print "<center><div class=t2>Removed sales order $bolnumber...</div></center>";
			} else {
				print "<center><div class=t2>Failed to delete sales order $bolnumber, please contact support</div></center>";
			}
			
			#
			# Add the record to tell what we did and when we did it
			#
			$reasonq = mysql_query("INSERT INTO cancellations VALUES ('', NOW(), '$bolnumber', '$reason')");
			
			# Just make sure it ran.
			if ( ! $reasonq ) {
				print "<center><div class=t2>Failed to add cancellation record, please contact support</div></center>";
			}

			# Just some closing information
			print "<center><div class=t2>All Done</div></center>";
			print "<br>";
			print "<center>";
			print "<a class=t2 href=/internal/cancelshipment.php>Delete Another</a>";
			print "<font face=verdana size=1> || </font>";
			print "<a href='' class=t2 onClick='JavaScript: window.close();'>Close Window</a>";
			print "</center>";

		} 

		# Otherwise, tell the user it's an invalid shipment id
		else {
			print "<center><div class=t2>BOL $bolnumber does not exist</div></center>";
			print "<br>";
			print "<center><a class=t2 href=/internal/cancelshipment.php>Try Again</a></center>";
		}

	# Have to leave here otherwise the rest of the HTML will be displayed.
	exit;

    }


?>

<html>

    <head>
        <meta name="Revision" content="$Revision: 1.26 $">
        <title>Shipment Cancellation Screen</title>
        <link rel="stylesheet" type="text/css" href="qa.css">
    </head>

	<script language="JavaScript" src="/internal/common.js"></script>

    <body bgcolor=white>

        <font face=verdana size=3><b><u><center>SHIPMENT CANCELLATION</center></u></b></font>
        <br>

        <!-- Start the cancel shipment form -->
        <form name=cancelshipment method=post action=cancelshipment.php>
            <table>
                <tr>
                    <td align=right class="t2">BOL:</td>
                    <td><input type=text size=10 name=bolnumber class="t2"></td>
                    <!--<td><a class="t1" href="cancelshipment.php" onClick="openBolSearchWindow();">[search]</a></td>-->
                </tr>
                <tr>
                    <td align=right class="t2">Reason:</td>
                    <td>
                        <select name=reason size=1 class="t2">
                            <?php
                                // Get all the cancellation reasons from the
                                // database and populate several option inputs
                                // from the results. The text displayed is the
                                // name from the database, the id is the record
                                // id from the database.
                                $reason_query = mysql_query("select * from cancel_reasons order by reason") or die (mysql_error());

                                while ( $reasons = mysql_fetch_array($reason_query) ) {
                                    print "<option class=t2 value=$reasons[id]>$reasons[reason]";
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <a class="t1" href="cancelshipment.php" onClick="openReasonEditWindow();">[edit]</a>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type=submit value=submit class="t2"></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </form>

        <br><br>
        <center><a href="" class=t1 onClick="JavaScript: window.close();">close</a></center>

    </body>
</html>

