<!--
==============================================================================

viewshwag.php

Page to display an email message from the database

$Id: viewshwag.php,v 1.6 2002/08/22 00:56:57 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren@younghome.com]

==============================================================================

ChangeLog:

$Log: viewshwag.php,v $
Revision 1.6  2002/08/22 00:56:57  youngd
* Added html headers
* Added stylesheet link

Revision 1.5  2002/08/20 18:41:27  youngd
* Removed marketing.php links

Revision 1.4  2002/08/20 18:27:48  youngd
* Changed index link

Revision 1.3  2002/08/20 17:12:52  youngd
* Added lower navigation menu

Revision 1.2  2002/08/20 17:06:38  youngd
* Working version

Revision 1.1  2002/08/20 16:09:39  youngd
* Initial version

==============================================================================
-->

<html>

    <head>
        <meta name="Revision" content="$Revision: 1.6 $">
        <link rel="stylesheet" type="text/css" href="/css/qa.css">
        <title>View Shwags</title>
    </head>

    <body>

<?php

    // Pull in the standard database properties
    require_once("dbprops.php");

    // open persistent connection to mysql 
    $db = mysql_pconnect($host, $user, $pass) or die(mysql_error()); 
         
    // select database 
    mysql_select_db($market_database, $db) or die (mysql_error()); 

    if ( $shwag ) {

        // Since they passed us the name to look for, query that info for
        // display.
        $shwag_query = mysql_query("SELECT * from shwag where name='$shwag'") or die (mysql_error());

        // Go get the data
        $shwagline = mysql_fetch_array($shwag_query);

        print "<font face=verdana size=2><b>ID:</b> $shwagline[shwagid]</font>";
        print "<br>";
        print "<font face=verdana size=2><b>NAME:</b> $shwagline[name]</font>";
        print "<br>";
        print "<font face=verdana size=2><b>SUBJECT:</b> $shwagline[subject]</font>";
        print "<br>";
        print "<font face=verdana size=2><b>REPLY ADDRESS:</b> $shwagline[replyaddress]</font>";
        print "<br>";
        print "<br>";
        print "<font face=verdana size=2>The stored email appears below the line:</font>";
        print "<hr>";
        print "<br>";
        print "$shwagline[html]";
        print "<br>";
        print "<br>";
        print "<hr>";

        // Lower menu
        print "<font face=verdana size=1>
               <center>
               <a href=http://maul/internal/marketing/>Marketing Main</a>
               |
               <a href=viewshwag.php>View Another</a>
               </center>
               </font>";

        print "</body>";
        print "</html>";

        exit;

    } else {

        $shwag_query = mysql_query("SELECT * from shwag") or die (mysql_error());

        print "<font face=verdana size=2>Select an email message to view:</font>";
        print "<br><br>";
        while ( $shwagline = mysql_fetch_array($shwag_query) ) {
            print "<font face=verdana size=2>
                   <a href='viewshwag.php?shwag=$shwagline[name]'>$shwagline[name]</a>
                   </font>
                   <br><br>";
        }

        print "<br>";
        print "<br>";
        print "<br>";
        print "<br>";
        print "<br>";
        print "<br>";

        print "<hr>";
        print "<font face=verdana size=1>
               <center>
               <a href=http://maul//internal/marketing/>Marketing Main</a>
               </center>
               </font>";


        print "</body>";
        print "</html>";

        exit;
    }
?>
