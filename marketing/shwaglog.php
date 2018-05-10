<?php
# =============================================================================
#
# shwaglog.php
#
# Page to select and display dates when messages (and how many) were
# sent to various email addresses on file.
#
# $Id: shwaglog.php,v 1.7 2002/11/20 19:14:37 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: shwaglog.php,v $
# Revision 1.7  2002/11/20 19:14:37  webdev
#   * Touched up font faces and sizes.
#
# Revision 1.6  2002/08/22 00:53:28  youngd
# * Added stylesheet link
# * Reordered head links
#
# Revision 1.5  2002/08/20 15:36:26  youngd
# * Cleaned html tags
# * Added digiship_database variable
#
# Revision 1.4  2002/08/20 14:29:03  youngd
# * Minor updates
#
# Revision 1.3  2002/08/20 02:44:29  youngd
# * Added standard source header
# * Added revision meta tag
# * Added standard html tags
# * Added title tag
#
# Revision 1.1  2002/08/20 02:30:51  youngd
# * Initial version
#
# =============================================================================

    require_once("dbprops.php");

    // open persistent connection to mysql
    $db = mysql_pconnect($host, php, password) or die("Cannot Connect to $host<br><br>");

    // select database
    mysql_select_db($database, $db) or die (mysql_error());

    //get a list of the emails
    $shwagq = mysql_query("select shwagid, name from shwag");

    //get all send dates - time
    $logqsql = "SELECT sum(totalemails), date from log";
    if ($message) {
	    $logqsql .= " where shwagid=$message";
    }

    $logqsql .= " group by date";
    $logdates = mysql_query($logqsql);

    $i = 0;

    while ($logline = mysql_fetch_array($logdates)) {
    	$datelogs[$i][0] = substr($logline[1], 0, 10);
	    $datelogs[$i][1] = $logline[0];
    	$i++;
    }

?>

<html>

    <head>
        <link rel="stylesheet" type="text/css" href="/css/qa.css">
        <meta name="Revision" content="$Revision: 1.7 $">
        <title>Shwag Log ($Revision: 1.7 $)</title>
    </head>

    <body>


    <?php

    // open persistent connection to mysql
    $db = mysql_pconnect($host, php, password) or die("Cannot Connect to $host<br><br>");

    // select database
    mysql_select_db($digiship_database, $db) or die ("Can't connect to $db<br><br>");

    echo "<table WIDTH=400><tr><td colspan=3><font face=verdana size=2>SHOW LOG FOR:</font>";
    while ($shwags = mysql_fetch_array($shwagq)) {
	    echo " <a href=shwaglog.php?message=$shwags[0]><font face=verdana size=2>$shwags[1]<font></a>";
    }

	echo "<br><br>";

    echo "</tr><tr><td><b><font face=verdana size=2>DATE</font></B></TD><TD ALIGN=RIGHT><b><font face=verdana size=2>EMAILS SENT<font></b></TD><TD ALIGN=RIGHT><b><font face=verdana size=2>REGISTRATIONS</font></b></TD></TR>";

    for ($m = 0; $m < $i; $m++) {
	    $sql = "select count(custid) from customers where regdate like '" . $datelogs[$m][0] . "%'";
    	$custquery = mysql_query($sql) or die(mysql_error());
    	$custline = mysql_fetch_array($custquery);
    	echo "<tr><td><font face=verdana size=2>" . $datelogs[$m][0] . "</font></td><td ALIGN=RIGHT><font face=verdana size=2>" . $datelogs[$m][1] . "</font></td><td ALIGN=RIGHT><font face=verdana size=2>$custline[0]</font></td></tr>";

    }

    ?>


    </body>
</html>
