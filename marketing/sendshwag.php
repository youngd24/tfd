<!--
==============================================================================

sendshwag.php

Page to select messages, databases and options for sending marketing
mail messages.

$Id: sendshwag.php,v 1.12 2002/08/29 21:23:40 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren@younghome.com]

==============================================================================

ChangeLog:

$Log: sendshwag.php,v $
Revision 1.12  2002/08/29 21:23:40  youngd
* Added more comments

Revision 1.11  2002/08/29 16:28:36  youngd
* Fixed error handler that wasn't working (syntax errors)

Revision 1.10  2002/08/22 00:52:29  youngd
* Reordered head links

Revision 1.9  2002/08/21 20:46:57  youngd
* Fixed query that kept failing

Revision 1.8  2002/08/21 19:43:56  youngd
* Cleaned up some of the text so it didn't wrap lines

Revision 1.7  2002/08/20 15:32:55  youngd
* More html cleansing

Revision 1.6  2002/08/20 15:31:47  youngd
* Added db require
* Cleaned up html tags

Revision 1.5  2002/08/20 14:36:11  youngd
* Changed database host to be localhost not 127.0.0.1

Revision 1.4  2002/08/20 02:45:38  youngd
* Corrected revision meta tag. Was missing a $ sign

Revision 1.3  2002/08/20 02:41:18  youngd
* Added standard source header
* Added revision meta tag

Revision 1.1  2002/08/20 02:30:51  youngd
* Initial version

==============================================================================
-->

<?php

    // If debug is anything other than 0 tell them
	if ( $debug ) {
		print "<font face=verdana size=2>*** Debug enabled ***</font><br>";
	}

	// Pull in shared code
    require("dbprops.php");			// Database properties
	require("error_handler.php");	// Our error handler

	// Enable our own error handler
	error_reporting(0);
	$old_error_handler = set_error_handler("localErrorHandler");

    // open persistent connection to mysql
    $dbconn = mysql_pconnect($host, $user, $pass) or 
			  die("Cannot Connect to $host " . mysql_error() . "<br><br>");

    // select database
    mysql_select_db($market_database, $dbconn) or 
			   die ("Unable to select MySQL database $market_database " . mysql_error() . "<br><br>");

    $shwagava = mysql_query("SELECT name, shwagid from shwag") or 
	           die ("Query failed " . mysql_error() . "<br><br>");

    // get available databases
    $dbindex = mysql_query("SELECT * from dbindex") or 
	           die ("Query failed " . mysql_error() . "<br><br>");

?>

<html>

    <head>
		<link rel="stylesheet" type="text/css" href="/css/qa.css">
        <meta name="Revision" content="$Revision: 1.12 $">
        <title>Send Email Marketing</title>
    </head>

    <body>
    
        <font face="verdana" size="2">

        <form method="post" action="emailer.php">

        <?php

            // Add entries to the dbindex table for these to appear in the page
	        echo "Please select a database to pull from:"; 
	        echo "<blockquote>";
	        while ($indexl = mysql_fetch_array($dbindex)) {
	
        		// get available
		        $avail = mysql_query("SELECT count(emailid) from $indexl[0] where shwagid = 0");
        		$sent = mysql_query("SELECT count(emailid) from $indexl[0] where shwagid != 0");
		        $availval = mysql_fetch_array($avail);
		        $sentval = mysql_fetch_array($sent);

		        echo "<input type=radio name=dbindex value=$indexl[0]>
                                                           $indexl[0] - 
                                                           $indexl[1] - 
                                                           $availval[0] emails available, 
                                                           $sentval[0] emails sent<br>";
	        }
	
            echo "</blockquote><br>";

        	echo "How many e-mails would you like to send?<br><br>";
        	echo "<input size=\"5\" name=\"count\"><br><br>";
        	echo "Select your shwag:<blockquote>";

        	while ($shwaglist = mysql_fetch_array($shwagava)) {
		        echo "<input type=radio name=shwag value=$shwaglist[shwagid]> $shwaglist[name]<br>";
	        }
	
            echo "</blockquote>";

            // If there are large amounts of email messages, the script timeout
            // has to be raised some.
			echo "Script timeout: <input type=text name=timeout value=120>";

			echo "<br><br>";
	    
            echo "IS THIS A TEST? <input type=radio name=test value=1 checked> YES <input type=radio name=test value=0> NO<br><br>";
	        echo "IF THIS IS A TEST, ENTER TEST EMAIL ADDRESS <input size=\"20\" name=\"testaddress\"><br><br>";

        ?>

        <b>WARNING! CLICKING THE BUTTON BELOW WILL SEND OUT THE EMAILS!!!</B><BR><BR>
        <input type="submit">
        
        </form>
    </body>
</html>
