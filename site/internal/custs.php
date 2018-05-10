<?php
# =============================================================================
#
# custs.php
#
# Customer Listing Page
#
# $Id: custs.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: custs.php,v $
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


if (!($order)) {

	$order = "lastlogindate";
	$orderd = "desc";
}



$custsql = "select customers.custid, customers.name, customers.company, customers.phone, customers.email, customers.password, customers.regdate, customers.lastlogindate, acctmanagers.name as mgr from customers left join acctmgrs on customers.custid = acctmgrs.custid left join acctmanagers on acctmgrs.acctmgrid = acctmanagers.acctmgrid";
if ($search) {
	$custsql .= " where (customers.name like '%$search%' or customers.company like '%$search%' or customers.email like '%$search%')";
}
if ($actmg and !($search)) {
	$custsql .= " where (0=0)";
}

if ($actmg) {
	$custsql .= " and acctmgrs.acctmgrid = $actmg";

}

$custsql .= " order by $order $orderd";
$customerquery = mysql_query($custsql) or die(mysql_query());

$getshippers = mysql_query("select distinct customerid from shipment") or die(mysql_query());
$i = 0;

while ($shipper = mysql_fetch_array($getshippers)) {
	$temparray[$i] = $shipper[0];
	$i++;
}
//getactmanagers
$accmgrsq = mysql_query("select * from acctmanagers");

// define host for db
$host = "localhost";
// define db
$database = "custnotes";
// open persistent connection to mysql
$db = mysql_pconnect($host, php, password) or die("Cannot Connect to $host<br><br>");
// select database
mysql_select_db($database, $db) or die ("Can't connect to $db<br><br>");
$notesquery = mysql_query("select custid from note");
$l = 0;

while ($noter = mysql_fetch_array($notesquery)) {
	$temparray2[$l] = $noter[0];
	$l++;
}

?>

<html>
<head><title>Customers</title>
<script language="JavaScript">
function changelogininfo(email, password) {

	document.forms.loginit.email.value = email;
	document.forms.loginit.password.value = password;

}

</script>
</head>
<body bgcolor=ffffff>
<form method=get action=custs.php>
<center>
<table style="font-size: 11px; font-family: tahoma;" cellpadding = 3 cellspacing = 2>
<tr><td><b>VIEW ONLY MY CUSTOMERS!</b></td><td><select name=actmg>
<?php
while ($acclin = mysql_fetch_array($accmgrsq)) {
	echo "<option value=$acclin[acctmgrid]>$acclin[name]";

}
?>

</select></td><td><input type=submit value=GO></td></form>
<td align=center><form method=get action=custs.php><B>SEARCH:</b> <input size=20 name=search> <input type=submit></td></form></tr>
</table></center><br>

<table style="font-size: 11px; font-family: tahoma;" cellpadding = 3 cellspacing = 2>
<form name=loginit method=post action=http://www.thefreightdepot.com/mydigiship.php> 
<tr><td colspan=7 align=center><i>RED INFO DENOTES ACTIVE SHIPPER</i></td></tr>
<tr><td><b><a href="custs.php?order=name&orderd=asc">NAME</a></b></td><td><b><a href="custs.php?order=company&orderd=asc">COMPANY</a></b></td><td><b>PHONE</b></td><td><b><a href="custs.php?order=regdate&orderd=desc">REG. DATE</a></b></td><td><b><a href="custs.php?order=lastlogindate&orderd=desc">LAST LOGIN</a></b></td><td><b>ACCT. MGR.</td></tr>
<?php

while ($custline = mysql_fetch_array($customerquery)) {
	$fontcolor="000000";
	$noteast = 0;
	for ($m = 0; $m <= $i; $m++) {
		if ($custline[0] == $temparray[$m]) {
			$fontcolor = "red";
		}
	}
	for ($g = 0; $g <= $l; $g++) {
		if ($custline[0] == $temparray2[$g]) {
			$noteast = 1;
		}
	}
	
	$custline[lastlogindate] =  substr($custline[lastlogindate], 5, 11);
	$custline[regdate] =  substr($custline[regdate], 5, 11);
	echo "<tr><td><a href=mailto:$custline[email]>$custline[name]</a></td><td><font color=$fontcolor>$custline[company]</font></td><td><font color=$fontcolor>$custline[phone]</font></td><td><font color=$fontcolor>$custline[regdate]</font></td><td><font color=$fontcolor>$custline[lastlogindate]</font></td><td><font color=$fontcolor>$custline[mgr]</font></td><td><a href=quotes.php?customerid=$custline[custid]>[ HISTORY ]</a><td><input type=radio name=login onClick=\"changelogininfo('$custline[email]', '$custline[password]');\"></td><td>";
	echo "<a href=notes.php?custid=$custline[custid]>Notes</a>";
	
	if ($noteast == 1) {
		echo "*";
	}
	echo "</td></tr>";

}
?>
<input type=hidden name=email value=''>
<input type=hidden name=password value=''>
	
<tr><td colspan=7 align=right><input type=submit value="LOGIN"></td></tr>
</table>

</form>
</body>