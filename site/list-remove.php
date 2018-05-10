<?php
# ==============================================================================
#
# list-remove.php
#
# Page to remove a user's email address from the mass mailing database
#
# $Id: list-remove.php,v 1.5 2002/10/04 19:20:35 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================
#
# ChangeLog:
# 
# $Log: list-remove.php,v $
# Revision 1.5  2002/10/04 19:20:35  youngd
#   * Testing version.
#
# Revision 1.4  2002/10/04 19:11:45  youngd
#   * Will now add the removed name to another permanent table.
#
# Revision 1.3  2002/10/04 19:01:39  youngd
#   * Added logic to delete from all tables.
#
# Revision 1.2  2002/09/15 07:14:28  webdev
#   * Added source header
#   * Converted to UNIX
#
# ==============================================================================

// define host for db
$host = "localhost";

// define db
$database = "market";

// Database user
$db_user = "php";

// Database password
$db_pass = "password";

// open persistent connection to mysql
$db = mysql_pconnect($host, $db_user, $db_pass) or die("Cannot Connect to $host<br><br>");

// select database
mysql_select_db($database, $db) or die ("Can't connect to $db<br><br>");


if ($email) {
    $result=mysql_query("delete from emails where email='$email'") or die(mysql_error());

    $result=mysql_query("delete from emails2 where email='$email'") or die(mysql_error());

    $result=mysql_query("delete from emails3 where email='$email'") or die(mysql_error());

    $result=mysql_query("delete from emails4 where email='$email'") or die(mysql_error());

	$result=mysql_query("insert into removals values ('', NOW(), '$email')") or die(mysql_error());
}

?>

<form name="remove" method="post" action="<?$PHP_SELF?>">
Enter the email address to be removed:
<br>
    <input type="text" name="email" id="email" size="40">
    <input type="submit" name="submit" id="submit" value="Remove Me">
</form>
