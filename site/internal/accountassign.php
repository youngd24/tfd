<?php
# =============================================================================
#
# accountassign.php
#
# Account Manager Assignment Page
#
# $Id: accountassign.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: accountassign.php,v $
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

if ($actmg) {
	if ($exists) {
		$update = mysql_query("UPDATE acctmgrs set acctmgrid = $actmg where custid = $customerid"); 
	
	}
	else {
		$insert = mysql_query("INSERT into acctmgrs values ($customerid, $actmg)");
	}

}
// get current status using $customerid

$thesql = "select customers.custid, customers.name as name, customers.company, customers.email, acctmanagers.name as mgr from customers left join acctmgrs on customers.custid = acctmgrs.custid left join acctmanagers on acctmgrs.acctmgrid = acctmanagers.acctmgrid where customers.custid = $customerid";
$assignedq = mysql_query($thesql) or die(mysql_error());
$assigned = mysql_fetch_array($assignedq);

//getactmanagers
$accmgrsq = mysql_query("select * from acctmanagers");
?>

<html>
<head>
<title>ASSIGN ACCOUNT</title>
</head>
<body bgcolor=ffffff onLoad=window.focus()>
<font face=tahoma size=2>
<form method=post action=accountassign.php>
<?php

if ($assigned[mgr] == "") {

	$assigned[mgr] = "<font color=red>No One</font>";
}
else {
	echo "<input type=hidden name=exists value=1>";

}
echo "<input type=hidden name=customerid value=$customerid>";
echo "THIS ACCOUNT FOR <b>$assigned[name]</b> IS CURRENTLY ASSIGNED TO <b>$assigned[mgr]</b><br><br>";
echo "<b>CHANGE:</b><br><br>";
echo "<select name=actmg>";
while ($acclin = mysql_fetch_array($accmgrsq)) {
	echo "<option value=$acclin[acctmgrid]";
	if ($acclin[name] == $assigned[mgr]) {
		echo " selected";
	}
	echo ">$acclin[name]";

}
echo "</select>";
echo "<br><input type=submit value=CHANGE>";
?>
</form>
</body></html>