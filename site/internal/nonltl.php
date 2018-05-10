<?php
# =============================================================================
#
# nonltl.php
#
# Non LTL shipment Page
#
# $Id: nonltl.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: nonltl.php,v $
# Revision 1.3  2002/10/16 06:52:58  youngd
#   * Converted to UNIX format (again). EditPlus at my house was set to do
#     everything in PC file format, not UNIX. Annoying...
#
# Revision 1.2  2002/10/16 06:47:44  youngd
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


$nonltlsql = "SELECT customers.company, customers.name, customers.phone, customers.email, quotes.quoteid, quotes.origin, quotes.destination, quotes.weight, quotes.class, quotes.ar, quotes.ap, quotes.baserate, quotes.date, quotes.transit from customers, quotes where customers.custid = quotes.customerid and carrierid = -3";

if ($submit and $quoteid) {

	$updatq = mysql_query("update quotes set origin = '$origin', destination = '$desc', weight = '$weight', class = '$class', ar = $ar, ap = $ap, transit = '$transit' where quoteid = $quoteid") or die(mysql_error());

	// see if quotenotes existed in the past
	$qncheck = mysql_query("select * from quotenotes where quoteid = $quoteid");
	if ($qnc = mysql_fetch_array($qncheck)) {
		$tempsql = "update quotenotes set carrier='$asset', id='$qid' where quoteid = '$quoteid'";	
	
	}
	else {
		$tempsql = "insert into quotenotes values ('$quoteid', '$asset', '$qid')";
	
	}

	$updatenq = mysql_query($tempsql) or die(mysql_error());
	if ($email) {
		$mailmessage = "Hello,\n\nYou recently submitted a rate request for the following shipment:\nORIGIN: $origin\nDESTINATION: $desc\nWEIGHT: $weight\n\nYour rate from thefreightdepot.com for this shipment will be $$ar. We expect the shipment to take $transit days to arrive to its destination. To schedule this shipment, or contact us with questions, call 866-445-1212.\n\nThanks!\n\nMike Smith\nThe Freight Depot";
		mail($email, "YOUR RATE FROM THEFREIGHTDEPOT.COM", $mailmessage, "FROM: msmith@thefreightdepot.com");
	
	}

}

if ($delete) {
	$delq = mysql_query("UPDATE quotes set carrierid = -1, ar = -1, ap = -1, baserate = -1, transit = -1 where quoteid = $quoteid") or die(mysql_error());

}
if ($quoteid) {

	$nonltlsql .= " and quotes.quoteid = $quoteid";
	$quotenotesq = mysql_query("SELECT * from quotenotes where quoteid = $quoteid");
	$quotenote = mysql_fetch_array($quotenotesq);
}
$nonltl = mysql_query($nonltlsql) or die(mysql_error());
?>

<html>
<head><title>Non-LTL Quotes</title></head>
<body bgcolor=ffffff>
<table style="font-family: arial; font-size: 11px;">
<tr><td><b>NUM</b></TD><td><b>COMPANY</b></TD><TD><B>NAME</B></TD><TD><B>PHONE</B></TD><TD><B>ORIGIN</B></TD><TD><B>DEST</B></TD><TD><B>WEIGHT</B></TD><TD><B>CLASS</B></TD><TD><B>AR</B></TD><TD><B>AP</B></TD><TD align=right><B>TRANSIT</B></TD><TD><B>DATE</B></TD></TR>
<?php
while ($nonline = mysql_fetch_array($nonltl)) {
if ($quoteid) {
	echo "<tr><form method=post action=nonltl.php><input type=hidden name=quoteid value=$quoteid>";
	echo "<td>$nonline[quoteid]</td><td>$nonline[company]</td><td><a href='mailto:$nonline[email]'>$nonline[name]</a></td><td>$nonline[phone]</td><td><input size=5 name=origin value=$nonline[origin]></td><td><input size=5 name=desc value=$nonline[destination]></td><td><input size=5 name=weight value=$nonline[weight]></td><td><input size=5 name=class value=$nonline[class]></td><td><input size=7 name=ar value=$nonline[ar]></td><td><input size=7 name=ap value=$nonline[ap]></td><td align=right><input size=2 name=transit value=$nonline[transit]></td><td>$nonline[date]</td></tr>";
	echo "<tr><td colspan=2>CARRIER:</td><TD COLSPAN=4><input size=20 name=asset value=$quotenote[carrier]></td><td colspan=2>ID #:</td><TD COLSPAN=4><input size=20 name=qid value=$quotenote[id]></td></tr>";
	echo "<tr><td colspan=10>EMAIL RATE TO CUSTOMER? <input type=checkbox name=email value=$nonline[email]><input type=hidden name=submit value=1><input type=submit></td><td colspan=2><a href=nonltl.php?delete=1&quoteid=$quoteid>DELETE</a></td></tr>";
	echo "</form>";
	}
else {
	echo "<tr><td><a href=nonltl.php?quoteid=$nonline[quoteid]>$nonline[quoteid]</a></td><td>$nonline[company]</td><td><a href='mailto:$nonline[email]'>$nonline[name]</a></td><td>$nonline[phone]</td><td>$nonline[origin]</td><td>$nonline[destination]</td><td>$nonline[weight]</td><td>$nonline[class]</td><td>$nonline[ar]</td><td>$nonline[ap]</td><td align=right>$nonline[transit]</td><td>$nonline[date]</td></tr>";

	}

}
if ($quoteid) {
	echo "<tr><td colspan=12><br><A HREF=nonltl.php>BACK TO NON-LTL QUOTES</A></TD></TR>";
}
?>


</table>



</body>
</html>