<?php
# =============================================================================
#
# mikesheet.php
#
# Mike's Rate sheet
#
# $Id: mikesheet.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: mikesheet.php,v $
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

?>

<html>
<head>
<title>Mike's Rate Sheet</title>
</head>
<body bgcolor=ffffff>
<table width=600>
<tr><td colspan=2 align=right>
<img src="../images/logos/mikesheetlogo.gif">
</td></tr>
<tr><td width=480>
<font face=tahoma size=2>

<?php
if ($action) {

echo "<b>Rate Confirmation Sheet:</b><br>";
echo "Carrier: $carrier<br>";
echo "Address: $address<br><br>";
echo "Fax To: $faxnumber<br>";
echo "Contact: $contact<br><br>";
echo "Pickup Location:  $origincity, $originstate<br>";
echo "Destination: $destcity, $deststate <br>";
$dateavailable = $availday . "-" . $availmonth . "-" . $availyear;
echo "Date Available:  $dateavailable<br>";
echo "Equipment Type:  $equipment<br><br>";
echo "<b>Company Name:</b>  	<br>";
				echo "$origincompany<br>";
				echo "$origincontact<br>";
				echo "$originphone<br>";
				echo "$originaddress1<br>";
				echo "$originaddress2<br>";
				echo "$origincity, $originstate, $originzip<br><br>";
$pickupdate = $pickupday . "-" . $pickupmonth . "-" . $pickupyear;
echo "Pickup at $pickuptime at $pickupdate<br><br>";
echo "<b>Deliver to Destination:</b><br>";
				echo "$destcompany<br>";
				echo "$destcontact<br>";
				echo "$destphone<br>";
				echo "$destaddress1<br>";
				echo "$destaddress2<br>";
				echo "$destcity, $deststate, $destzip<br><br>";
echo "Special Info: $info<br><br>";
echo "Shipment Type: $shiptype<br><br>";
echo "Weight: $weight<br>";
echo "Rate: $rate<br>";
echo "AP: $ap<br>";
}
else {
	echo "<table style='font-family: tahoma font-size: 10'>";
	echo "<form method=post action=mikesheet.php>";
	echo "<tr><td>CARRIER: </td><td><input size=20 name=carrier></td></tr>";
	echo "<tr><td>ADDRESS: </td><td><input size=20 name=address></td></tr>";
	echo "<tr><td>FAX TO: </td><td><input size=20 name=faxnumber></td></tr>";
	echo "<tr><td>CONTACT: </td><td><input size=20 name=contact></td></tr>";
	echo "<tr><td>DATE AVAILABE: </td><td><select name=availmonth><option value=01>01<option value=02>02<option value=03>03<option value=04>04<option value=05>05<option value=06>06<option value=07>07<option value=08>08<option value=09>09<option value=10>10<option value=11>11<option value=12>12</select> <select name=availday><option value=01>01<option value=02>02<option value=03>03<option value=04>04<option value=05>05<option value=06>06<option value=07>07<option value=08>08<option value=09>09<option value=10>10<option value=11>11<option value=12>12<option value=13>13<option value=14>14<option value=15>15<option value=16>16<option value=17>17<option value=18>18<option value=19>19<option value=20>20<option value=21>21<option value=22>22<option value=23>23<option value=24>24<option value=25>25<option value=26>26<option value=27>27<option value=28>28<option value=29>29<option value=30>39<option value=31>31</select> <select name=availyear><option value=2002>2002<option value=2003>2003</select></td></tr>";
	echo "<tr><td>EQUIPMENT TYPE: </td><td><input size=20 name=equipment></td></tr>";
	echo "<tr><td>ORIGIN COMPANY: </td><td><input size=20 name=origincompany></td></tr>";
	echo "<tr><td>ORIGIN CONTACT: </td><td><input size=20 name=origincontact></td></tr>";
	echo "<tr><td>ORIGIN PHONE: </td><td><input size=20 name=originphone></td></tr>";
	echo "<tr><td>ORIGIN ADDRESS 1: </td><td><input size=20 name=originaddress1></td></tr>";
	echo "<tr><td>ORIGIN ADDRESS 2: </td><td><input size=20 name=originaddress2></td></tr>";
	echo "<tr><td>ORIGIN CITY: </td><td><input size=20 name=origincity></td></tr>";
	echo "<tr><td>ORIGIN STATE: </td><td><input size=20 name=originstate></td></tr>";
	echo "<tr><td>ORIGIN ZIP: </td><td><input size=20 name=originzip></td></tr>";
	echo "<tr><td>PICKUP TIME: </td><td><select name=pickuptime><option value='7:00'>7:00<option value='8:00'>8:00<option value='9:00'>9:00<option value='10:00'>10:00<option value='11:00'>11:00<option value='12:00'>12:00<option value='1:00'>1:00<option value='2:00'>2:00<option value='3:00'>3:00<option value='4:00'>4:00<option value='5:00'>5:00<option value='6:00'>6:00</select></td></tr>";
	echo "<tr><td>PICKUP DATE: </td><td><select name=pickupmonth><option value=01>01<option value=02>02<option value=03>03<option value=04>04<option value=05>05<option value=06>06<option value=07>07<option value=08>08<option value=09>09<option value=10>10<option value=11>11<option value=12>12</select> <select name=pickupday><option value=01>01<option value=02>02<option value=03>03<option value=04>04<option value=05>05<option value=06>06<option value=07>07<option value=08>08<option value=09>09<option value=10>10<option value=11>11<option value=12>12<option value=13>13<option value=14>14<option value=15>15<option value=16>16<option value=17>17<option value=18>18<option value=19>19<option value=20>20<option value=21>21<option value=22>22<option value=23>23<option value=24>24<option value=25>25<option value=26>26<option value=27>27<option value=28>28<option value=29>29<option value=30>39<option value=31>31</select> <select name=pickupyear><option value=2002>2002<option value=2003>2003</select></td></tr>";
	echo "<tr><td>DESTINATION COMPANY: </td><td><input size=20 name=destcompany></td></tr>";
	echo "<tr><td>DESTINATION CONTACT: </td><td><input size=20 name=destcontact></td></tr>";
	echo "<tr><td>DESTINATION PHONE: </td><td><input size=20 name=destphone></td></tr>";
	echo "<tr><td>DESTINATION ADDRESS 1: </td><td><input size=20 name=destaddress1></td></tr>";
	echo "<tr><td>DESTINATION ADDRESS 2: </td><td><input size=20 name=destaddress2></td></tr>";
	echo "<tr><td>DESTINATION CITY: </td><td><input size=20 name=destcity></td></tr>";
	echo "<tr><td>DESTINATION STATE: </td><td><input size=20 name=deststate></td></tr>";
	echo "<tr><td>DESTINATION ZIP: </td><td><input size=20 name=destzip></td></tr>";
	echo "<tr><td>SPECIAL INFO: </td><td><input size=20 name=info></td></tr>";
	echo "<tr><td>SHIPMENT TYPE: </td><td><input size=20 name=shiptype></td></tr>";
	echo "<tr><td>WEIGHT: </td><td><input size=20 name=weight></td></tr>";
	echo "<tr><td>RATE: </td><td><input size=20 name=rate></td></tr>";
	echo "<tr><td>AP:</td><td><input size=20 name=ap></td></tr>";
	echo "<tr><td><input type=hidden value=1 name=action><input type=submit></td></tr></table>";

}
?>
</td></tr>
<tr><td>
<font face=tahoma size=1>
The Freight Depot<br>
www.thefreightdepot.com<br>
P - 866-445-1212<br>
F - 847-283-0137<BR>
</td>
<td>
<font face=tahoma size=1>
<b>Bill TO:<br>
The Freight Depot<br>
717 Forrest Ave <br>
Lake Forest, IL 60045<br>
866-445-1212<br>
</b></font>
</td></tr>
</table>
</body></html>

