<?php
# =============================================================================
#
# accountmgr.php
#
# Account Manager Management Page
#
# $Id: accountmgr.php,v 1.3 2002/10/16 06:52:58 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: accountmgr.php,v $
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


if ($action) {
	if ($action == "add") {
		$sqltemp = "insert into acctmanagers (name, phone, email) values ('$name', '$phone', '$email')";
		$aadd = mysql_query($sqltemp) or die(mysql_error());
	
	}
	if ($action == "modify") {
		$getind = mysql_query("select * from acctmanagers where acctmgrid = $acctmg");
		$indline = mysql_fetch_array($getind);
	
	}
	if ($action == "modifyfinal" and $mgrid) {
		$update = mysql_query("update acctmanagers set name='$name', phone='$phone', email='$email' where acctmgrid=$mgrid");
	
	}

}

// get account managers
$accmgrsq = mysql_query("select * from acctmanagers");

// get customers & account managers
$thesql = "select customers.custid, customers.name as name, customers.company, customers.email, acctmanagers.name as mgr from customers left join acctmgrs on customers.custid = acctmgrs.custid left join acctmanagers on acctmgrs.acctmgrid = acctmanagers.acctmgrid";
$assignedq = mysql_query($thesql) or die(mysql_error());




?>

<html>
<head>
<title>Account Management</title>
<script language=javascript>
function windowopen(custid) {

	url = "accountassign.php?customerid=" + custid;
	window.open(url, "new", "width=300, height=300");

}

</script>

</head>
<body bgcolor=ffffff>
<font face=tahoma size=2>
<table style="font-family: tahoma; font-size: 12px;" width=600>
<tr valign=top><td>
<b>Account Managers</b><br>Click on name to update<br>

<?php
while ($accmgr = mysql_fetch_array($accmgrsq)) {

	echo "<a href=accountmgr.php?action=modify&acctmg=$accmgr[acctmgrid]>$accmgr[name]</a><br>";
	
}
?>
<br>
<?php
	echo "<form method=post action=accountmgr.php>";
	if ($action and $action == "modify") {
		echo "</td><td><b>Modify Record for $indline[name]</b><br>";
		echo "<input type=hidden name=mgrid value=$indline[acctmgrid]>";
		echo "<input type=hidden name=action value=modifyfinal>";
	}
	else {
		echo "</td><td><b>Add an Account Manager</b><br>";
		echo "<input type=hidden name=action value=add>";
	}
	
	echo "Name: <input size=20 name=name value='$indline[name]'><br>";
	echo "Phone: <input size=20 name=phone value='$indline[phone]'><br>";
	echo "Email: <input size=20 name=email value='$indline[email]'><br>";
	echo "<input type=submit value=SUBMIT></form>";
?>
</td></tr>
</table>
<br>
<b>ASSIGNED ACCOUNT MANAGERS TO CUSTOMERS</B><br><br>
<table style="font-family: tahoma; font-size: 11px;" width=640>
<tr><td><b>NAME</B></td><td><b>COMPANY</B></TD><TD><B>EMAIL</B></TD><TD><B>ACCOUNT MANAGER</B></TD></TR>
<?php
while ($assigned = mysql_fetch_array($assignedq)) {
	echo "<tr><td>$assigned[name]</td><td>$assigned[company]</td>";
	echo "<td>$assigned[email]</td>";
	if ($assigned[mgr] == "") {
		echo "<td><a href='javascript:windowopen($assigned[custid]);'>ASSIGN</a></td></tr>";
	}
	else {
		echo "<td><a href='javascript:windowopen($assigned[custid]);'>$assigned[mgr]</td></tr>";
	}

}
?>
</table>
<br><br><a href="index.php">INTERNAL HOME</a>
</body>
</html>