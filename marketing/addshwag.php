<!--
==============================================================================

addshwag.php

Adds a marketing email message to the database

$Id: addshwag.php,v 1.12 2003/01/21 19:00:54 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren@younghome.com]

==============================================================================

ChangeLog:

$Log: addshwag.php,v $
Revision 1.12  2003/01/21 19:00:54  youngd
  * Fixed bug so it'll work. dbconn was db.

Revision 1.11  2002/08/22 00:50:25  youngd
* Added additional die statements

Revision 1.10  2002/08/22 00:47:28  youngd
* Added stylesheet link

Revision 1.9  2002/08/20 17:12:14  youngd
* Added insert code

Revision 1.8  2002/08/20 15:27:58  youngd
* Added quotes aroung tag values
* Added php include

Revision 1.7  2002/08/20 15:25:03  youngd
* Added dbprops to be installed

Revision 1.6  2002/08/20 14:29:03  youngd
Minor updates

Revision 1.5  2002/08/20 03:53:06  youngd
* First testing version

Revision 1.4  2002/08/20 02:33:43  youngd
* Added revision meta tag
* Cleaned up changelog

Revision 1.3  2002/08/20 02:32:27  youngd
* Added standard source code header

==============================================================================
-->

<?php
    require_once("dbprops.php");

    // Open a persistent connection to the database
    $dbconn = mysql_pconnect($host, $user, $pass) or
              die ("Unable to connect to $host " . mysql_error() . "<br><br>");

    // Select database
    mysql_select_db($market_database, $dbconn) or
              die ("Unable to select database $market_database " .  mysql_error() . "<br><br>");


    // They gave us the data we need, go ahead and shove it into the database
    if ($action) {
        $insertit = mysql_query("INSERT into shwag \
                    (name,html,subject,replyaddress) \
                    values \
                    ('$name','$html','$subject','$replyaddress')");

        echo "Results: $insertit";

        echo "<br><br>";

        echo "Added";

        echo "<br><br>";
        echo "<a href=addshwag.php>Add another</a>";

        exit;
    }
?>

<html>

    <head>
        <link rel="stylesheet" type="text/css" href="/css/qa.css">
        <meta name="Revision" content="$Revision: 1.12 $">
        <title>Add some shwag</title>
    </head>

    <body>

        <font face="verdana" size="2">

        <form method="post" action="addshwag.php">
            Name of schwag: <input size="20" name="name"><br><br>
            E-mail subject: <input size="20" name="subject"><br><br>
            Reply address: <input size="20" name="replyaddress"><br><br>
            HTML (must contain no carriage returns):<br><br>
            <textarea name="html" rows="30" cols="60"></textarea><br><br>
            <input type="hidden" name="action" value="1">
            <input type="submit">
        </form>

    </body>

</html>
