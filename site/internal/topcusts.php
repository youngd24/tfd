<?php

require("zzmysql.php");

$qry = mysql_query("SELECT shipment.customerid AS CUSTID, customers.company AS COMPANY, count(shipment.customerid) AS NUMSHIPS, sum(shipment.finalar) AS FINALAR FROM shipment,customers WHERE shipment.customerid=customers.custid GROUP BY shipment.customerid ORDER BY NUMSHIPS DESC");

print "<table cellpadding=1 cellspacing=1 border=0>";

print "<tr bgcolor=silver>";
print "<td><font face=verdana size=2><b>CUSTID</b></font></td>";
print "<td><font face=verdana size=2><b>COMPANY</b></font></td>";
print "<td><font face=verdana size=2><b>NUMSHIPS</b></font></td>";
print "<td><font face=verdana size=2><b>FINALAR</b></font></td>";
print "</tr>";

while($result=mysql_fetch_row($qry)) {
	print "<tr>";
	print "<td><font face=verdana size=2>$result[0]</font></td>";
	print "<td><font face=verdana size=2>$result[1]</font></td>";
	print "<td><font face=verdana size=2>$result[2]</font></td>";
	print "<td><font face=verdana size=2>$$result[3]</font></td>";
	print "</tr>";
}

print "</table>";

$qry = mysql_query("SELECT count(shipment.shipmentid) from shipment");
$result = mysql_fetch_row($qry);
print "<br>";
print "<font face=verdana size=2><b>TOTAL SHIPMENTS: $result[0]</b>";

$qry = mysql_query("SELECT sum(shipment.finalar) from shipment");
$result = mysql_fetch_row($qry);
print "<br>";
print "<font face=verdana size=2><b>TOTAL AR: $$result[0]</b>";

?>