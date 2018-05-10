<?php
# =============================================================================
#
# invoicepd.php
#
# Past Due Invoice Listing
#
# $Id: invoicepd.php,v 1.5 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: invoicepd.php,v $
# Revision 1.5  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.4  2002/10/16 06:47:44  youngd
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

if ($ordnumber) {
   	require('../zzmysql.php');
	$fillin = mysql_query("select shipment.pickupdate, shipment.ponumber, shipment.units, shipment.hazmat, shipment.productdescription, quotes.weight, shipment.origin, shipment.destination from shipment, quotes where shipment.quoteid = quotes.quoteid and shipmentid = $ordnumber") or die(mysql_error());
   	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password");
	$invstr = "select id from ar where ordnumber = '$ordnumber'";
	$invidq = pg_exec($invstr) or die(pg_errormessage());
	$invidqu = pg_fetch_array($invidq);
	$invoiceid = $invidqu[0];
	$somesql = "SELECT id, invnumber, ordnumber, transdate, duedate, customer_id, netamount - paid as netamount from ar where id = $invoiceid";
	$checkquery = pg_exec($somesql) or die(pg_errormessage());
	
	$shipline = mysql_fetch_array($fillin);
	$billine = pg_fetch_array($checkquery);
	
	#getaddresses
	$billing = pg_exec("SELECT name, addr1, addr2, addr3, contact from customer where id = $billine[customer_id]") or die(pg_errormessage());
	$originq = mysql_query("select company, address1, address2, city, state, zip, contact from address where addressid = $shipline[origin]") or die(mysql_error());
	$destq = mysql_query("select company, address1, address2, city, state, zip, contact from address where addressid = $shipline[destination]") or die(mysql_error());
	$origin = mysql_fetch_array($originq);
	$dest = mysql_fetch_array($destq);
	$bill = pg_fetch_array($billing); 
	$check = pg_close($conn);
	$pos = strrpos($billine[netamount], '.');
	$totst = strlen($billine[netamount]);
	if ($pos === false) {
	
		$billine[netamount] .= ".00";
		
	}
	elseif ($pos == $totst - 2) {
	
		$billine[netamount] .= "0";
	
	}
	

}
else {
	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=depot user=postgres password=password");
	$somesql = "SELECT id, invnumber, ordnumber, transdate, duedate from ar";
	$checkquery = pg_exec($somesql) or die(pg_errormessage());
	$check = pg_close($conn);

}
//format the dates
$billine[transdate] = substr($billine[transdate], 5, 5) . '-' . substr($billine[transdate], 0, 4);
$shipline[pickupdate] = substr($shipline[pickupdate], 5, 5) . '-' . substr($shipline[pickupdate], 0, 4);
$billine[duedate] = substr($billine[duedate], 5, 5) . '-' . substr($billine[duedate], 0, 4);
?>

	<html>
<head>
	<title>I N V O I C E</title>
<LINK REL="stylesheet" HREF="../sql-ledger/css/sql-ledger.css" TYPE="text/css" TITLE="SQL-Ledger style sheet">
<STYLE>
.text {
	font-size:	11;
	font-family: arial;

}
.textsm {
	font-size:	11;
	font-family: arial;

}
</style>
	
</head>

<body bgcolor=ffffff>

<?php
if ($ordnumber) {

	echo '<table width=640 border=0 class="text">';

	echo '<tr valign=top><td>';
	echo '<table width=100% border=0 class="text">';
	echo '<tr valign=top><td width=320><img src="../images/logos/invlogo.gif"><br><br>';
	echo '<b>REMIT PAYMENT TO:</b><BR>';
	echo 'THE FREIGHT DEPOT<br>';
	echo '191 E. DEERPATH<br>';
	echo 'SUITE 302<BR>';
	echo 'LAKE FOREST, IL 60045<bR>';
	echo 'www.thefreightdepot.com<br>';
	echo 'EIN 20-0022785';
	echo '</td>';
	echo '<td width=320>';
	echo '<table width=320 bgcolor=000000 cellpadding=0 cellspacing=0>';
	echo '<tr valign=top><td>';
	echo '<table width=320 cellpadding=2 cellspacing=1 border=0 class="text">';
	echo '<tr bgcolor=acacac><td align=center><b>PAST DUE INVOICE</b></td></tr>';
	echo '<tr bgcolor=ffffff><td class="text">';
	echo "<b>INVOICE DATE: $billine[transdate]</B></TD></tr>";
	echo '<tr bgcolor=ffffff><TD class="text">';
	echo "<B>PICKUP DATE: $shipline[pickupdate]</B></TD></TR>";
	echo '</table>';
	echo '</td></tr>';
	echo '</table>';
	echo '<BR>';
	echo "<B><FONT COLOR=RED>PAST DUE INVOICE</B><BR>";
	echo 'THIS INVOICE IS PAST DUE. PLEASE REMIT PAYMENT IMMEDIATELY'; 
	echo '</td></tr>';
	echo '</table>';
	echo '</td></tr>';
	echo '<tr><td align=right>';
	echo '<table width=200 bgcolor=000000 cellpadding=0 cellspacing=0 class="text">';
	echo '<tr><td>';
	echo '<table cellspacing=1 width=100% class="text">';
	echo '<tr><td bgcolor=acacac align=center>';
	echo '<b><FONT COLOR=RED>AMOUNT PAST DUE</FONT></B></TD></TR>';
	echo "<tr><td bgcolor=ffffff align=center>$$billine[netamount]</td></tr>";
	echo '</table>';
	echo '</td></tr>';
	echo '</table>';
	echo '</td></tr>';
	echo '<tr><td class="text">';
	echo '<b>BILL TO</b><Br>';
	echo 'ACCOUNTS PAYABLE DEPARTMENT<br>';
	$bill[name] = strtoupper($bill[name]);
	$bill[addr1] = strtoupper($bill[addr1]);
	$bill[addr2] = strtoupper($bill[addr2]);
	$bill[addr3] = strtoupper($bill[addr3]);
	echo "$bill[name]<br>$bill[addr1] $bill[addr2]<br>$bill[addr3]<br>";
    echo '<br>';
	echo '<br><br>';
	
	echo '</td></tr>';
	
	echo '<tr><Td>';
	echo '<table width=640 class="text" cellpadding=0 cellspacing=0>';
	echo '<tr><td WIDTH=430 class="text"><b>SHIPPER</B><br>';
	$origin[contact] = strtoupper($origin[contact]);
	$origin[company] = strtoupper($origin[company]);
	$origin[address1] = strtoupper($origin[address1]);
	$origin[address2] = strtoupper($origin[address2]);
	$origin[city] = strtoupper($origin[city]);
	$origin[state] = strtoupper($origin[state]);
	echo "$origin[contact]<br>$origin[company]<br>$origin[address1] $origin[address2]<br>$origin[city], $origin[state] $origin[zip]";
		
	echo '<br></td>';
	echo '<td WIDTH=210 class="text"><b>CONSIGNEE</B><Br>';
	$dest[contact] = strtoupper($dest[contact]);
	$dest[company] = strtoupper($dest[company]);
	$dest[address1] = strtoupper($dest[address1]);
	$dest[address2] = strtoupper($dest[address2]);
	$dest[city] = strtoupper($dest[city]);
	$dest[state] = strtoupper($dest[state]);
	echo "$dest[contact]<br>$dest[company]<br>$dest[address1] $dest[address2]<br>$dest[city], $dest[state] $dest[zip]";
	echo '<br></td></tr>';
	echo '<tr><td colspan=2><img src="../images/pixels/blackpixel.gif" width=640 height=1></td></tr>';
	echo '<tr>';
	echo '<td>';
	echo '</td>';
	echo '<td align=right>';
	echo '</td></tr>';
	echo '</table>';
	echo '</td></tr>';
	echo '<tr><td><br><b>COMMENTS:</b> THIS INVOICE IS PAST DUE. REMIT PAYMENT IMMEDIATELY<br>';
 	echo '<br>';
	echo '</td></tr>';
	echo '<tr><td>';
	echo "<b>BOL #: $ordnumber</B><BR>";
	echo "<b>PO #: $shipline[ponumber]</b><br>";
	echo "<b>CONTACT: $bill[contact]</B><br><BR>";
	echo '</td></tr>';
	echo '<tr><td>';
	echo '<TABLE WIDTH=640 BGCOLOR=000000 CELLPADDING=0 CELLSPACING=0 class="text">';
	echo '<TR><TD>';
	echo '<table width=100% CELLSPACING=1 class="text">';
	echo '<tr bgcolor=acacac>';
	echo '<td><b>PCS</b></td>';
	echo '<td><b>HM</b></td>';
	echo '<td><b>DESCRIPTION OF ARTICLES</b></td>';
	echo '<td><b>WEIGHT</b></td>';
	echo '<td><b>AMOUNT</b></td>';
	echo '</tr>';
	echo '<tr bgcolor=FFFFFF>';
	echo "<td><b>$shipline[units]</b></td>";
	echo "<td><b>$shipline[hazmat]</b></td>";
	$shipline[productdescription] = strtoupper($shipline[productdescription]);
	echo "<td><b>$shipline[productdescription]</b></td>";
	echo "<td><b>$shipline[weight]</b></td>";
	echo "<td><b>$$billine[netamount]</b></td>";
	echo '</tr>';
	echo '</table>';
	echo '</TD></TR>';
	echo '</TABLE>';
	echo '<BR><BR><br><br><br><br><br><br>';
	echo '</td></tr>';
	echo '<tr><td align=center><img src="../images/general/cuthere.gif"></td></tr>';
	echo '<tr><td align=center><i>PLEASE REMIT THIS PORTION WITH PAYMENT</I></td></tr>';
	echo '<tr><td>';
	echo '<table width=640 CLASS="text">';
	echo '<tr><td width=440>';
	echo '<B>FROM:</B><BR>';
	echo "$bill[name]<br>$bill[addr1] $bill[addr2]<br>$bill[addr3]<br>";
    echo '<BR>';
	echo '<b>PLEASE REMIT TO:</B><BR>';
	echo 'THE FREIGHT DEPOT<br>';
	echo '191 E. DEERPATH<br>';
	echo 'SUITE 302<BR>';
	echo 'LAKE FOREST, IL 60045<bR>';
	echo '</TD>';
	echo '<TD>';
	echo '<table bgcolor=000000 cellpadding=0 cellspacing=0 class="textsm" BORDER=0>';
	echo '<tr><td>';
	echo '<table cellspacing=1 class="textsm" WIDTH=200>';
	echo "<tr bgcolor=ffffff><td><b>DUE DATE: IMMEDIATELY</B></TD></TR>";
	echo "<TR bgcolor=ffffff><TD><B>BOL NUMBER: $ordnumber</B></TD></TR>";
	echo "<TR bgcolor=ffffff><TD><B>AMOUNT DUE: $$billine[netamount]</B></TD></TR>";
	echo '</TABLE>';
	echo '</td></tr>';
	echo '</table>';
	echo '<br><BR><BR><BR>';
	echo '<table width=200 bgcolor=000000 cellpadding=0 cellspacing=0 class="text">';
	echo '<tr><td>';
	echo '<table cellspacing=1 width=100% class="text">';
	echo '<tr><td bgcolor=acacac align=center>';
	echo '<b>AMOUNT ENCLOSED</B></TD></TR>';
	echo '<tr height=20><td bgcolor=ffffff align=center></td></tr>';
	echo '</table>';
	echo '</td></tr>';
	echo '</table>';
			
	echo '</td></tr>';
	echo '</table>';
	
	echo '</td></tr>';
	echo '</table>';

}
else {
	echo "<table WIDTH=640>";
	while($line = pg_fetch_array($checkquery)) {
		
		echo "<tr><td><a href=invoice.php?ordnumber=$line[ordnumber]>$line[invnumber]</a></td>";
		echo "<td>$line[ordnumber]</td><td>$line[transdate]</td><td>$line[duedate]</td>";
		echo "</tr>";
	
	}
	echo "</table>";

}
?>

</body></html>
