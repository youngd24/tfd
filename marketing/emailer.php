<!--
==============================================================================

emailer.php

Page to send email messages. sendshwag.php posts to this page

$Id: emailer.php,v 1.28 2003/01/16 22:32:21 webdev Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren@younghome.com]

==============================================================================

ChangeLog:

$Log: emailer.php,v $
Revision 1.28  2003/01/16 22:32:21  webdev
  * Added debug.

Revision 1.27  2003/01/08 21:21:02  webdev
  * Added br after sent message is printed.

Revision 1.26  2003/01/08 19:09:08  webdev
  * Changed my email to be the Yahoo one.

Revision 1.25  2003/01/08 19:07:56  webdev
  * People in the remove list won't get an email now.

Revision 1.24  2002/10/23 16:54:34  youngd
  * Fixed removals

Revision 1.23  2002/10/21 22:10:11  youngd
  * Changed to remove list

Revision 1.22  2002/10/21 22:05:29  youngd
  * Added check to see if the address is in the remove table.

Revision 1.21  2002/08/29 22:48:00  youngd
* Added Tom to the admin email list

Revision 1.20  2002/08/29 22:30:14  youngd
* Added a counter print for each one sent

Revision 1.19  2002/08/29 22:16:10  youngd
* Added calculation of total script run time in seconds via mktime()

Revision 1.18  2002/08/29 21:43:00  youngd
* Changed I to i in dates since I is for daylight savings time not minutes.
  The O'Reilly PHP book was wrong.

Revision 1.17  2002/08/29 21:30:24  youngd
* Still trying to add more comments and docs

Revision 1.16  2002/08/29 21:27:57  youngd
* Added more comments

Revision 1.15  2002/08/29 21:09:18  youngd
* Added start and end times

Revision 1.14  2002/08/29 20:46:43  youngd
* Updated some stuff. Can't remember what exactly. Do a diff if you really want
  to know

Revision 1.13  2002/08/29 20:41:36  youngd
* Added html to the output

Revision 1.12  2002/08/29 20:39:02  youngd
* Added Tom to the results list

Revision 1.11  2002/08/29 16:28:36  youngd
* Updates here an there. Diff it if you're interested.

Revision 1.10  2002/08/22 06:36:03  youngd
* Added test of return code from mail function and printing of a message
  based on that return code.

Revision 1.8  2002/08/21 20:46:34  youngd
* Changed admin email address

Revision 1.7  2002/08/21 18:00:05  youngd
* Cleaned up and formatted document according to tfd standards

Revision 1.6  2002/08/20 02:38:03  youngd
* Revision tag is now correct

Revision 1.5  2002/08/20 02:37:36  youngd
* Changed administrative email addresses
* Fixed some typos

Revision 1.4  2002/08/20 02:36:54  youngd
* Added revision meta tag
* Added standard html tags

Revision 1.3  2002/08/20 02:34:58  youngd
* Added standard source code header

Revision 1.1  2002/08/20 02:30:51  youngd
* Initial version

==============================================================================
-->
<html>

    </head>
        <title>Email Marketing Mailer</title>
        <meta name="Revision" content="$Revision: 1.28 $">
        <meta name="Author" content="$Author: webdev $">
        <link rel="stylesheet" type="text/css" href="/css/qa.css">
    </head>

    <body>

    <?php

		// Bring in standard functions
		require_once("debug.php");
		require_once("error.php");
		require_once("event.php");
		require_once("functions.php");
		require_once("logging.php");

		debug("emailer.php: starting up...");

		// Change the timeout of the script from the default of 30 seconds to
		// either the setting given to us or to 120 seconds
		if ( $timeout ) {
			debug("emailer.php: someone called me with a timeout value of $timeout.");
			debug("emailer.php: calling set_time_limit($timeout) for this script.");
			set_time_limit($timeout);
		} else {
			debug("emailer.php: calling set_time_limit(120) since I wasn't told otherwise.");
			set_time_limit(120);
		}


		// Register a shutdown function for this script
		debug("emailer.php: registering shutdown function 'shutdown_script'.");
		register_shutdown_function("shutdown_script");

        // Pull in out standard database properties
		debug("emailer.php: pulling in our database properties.");
        require_once("dbprops.php");

        // open persistent connection to mysql
		debug("emailer.php: opening database connection.");
        $dbconn = mysql_pconnect($host, $user, $pass) or die("Cannot Connect to $host " . mysql_error() . "<br><br>");

        // select database
		debug("emailer.php: selecting database.");
        mysql_select_db($market_database, $dbconn) or die ("Can't connect to $market_datbase " . mysql_error() . "<br><br>");


        // Get all the emails from the desired database table
		debug("emailer.php: retrieving shwag id $shwag.");
        $theshwag = mysql_query("SELECT * from shwag where shwagid='$shwag'") or die (mysql_error());
        $shwagline = mysql_fetch_array($theshwag);
        
        // Not sure why Jeff picked this as the option here.
        $timenow = getdate();
        $date = $timenow['year'] 
                . '-' .
                $timenow['mon']
                . '-' .
                $timenow['mday']
                . ' ' .
                $timenow['hours']
                . ':' .
                $timenow['minutes']
                . ':' .
                $timenow['seconds'];
        
        // Set the SMTP email header to us ascii
        $i = 0;
		debug("emailer.php: setting the email header to US ASCII.");
        $header = "From: $shwagline[replyaddress]\nContent-Type: text/html; charset=us-ascii";

        // If it's a test message, do that here
        if ($test and $testaddress) {
			debug("emailer.php: IN TEST MODE.");
	        echo "This is a test for $testaddress<br><br>";

			$end = date('m-d-Y H:i:s');
	        $end_epoch = mktime(date('H'),date('i'),date('s'),date('M'),date('d'),date('Y'));
		    echo "<br>Ended at $end (epoch: $end_epoch)<br>";
			debug("emailer.php: run complete at $end, epoch = $end_epoch.");

			debug("emailer.php: sending mail to $testaddress.");
    	    $thereturn = mail($testaddress, $shwagline[subject], $shwagline[html], $header);
        	$i++;
        	echo "MAIL SENT TO $testaddress: ";
            if ( $thereturn == 1 ) {
				debug("emailer.php: mail return came back *GOOD*.");
                echo "[OK]<br>";
            } else {
				debug("emailer.php: mail return came back *BAD*");
                echo "[ERROR]<br>";
            }

        // Otherwise we're doing it for real
        } else {
            if ($count and $test == 0 and $dbindex) {
				debug("emailer.php: IN LIVE MODE.");
				debug("emailer.php: sending $count messages from table $dbindex.");

				debug("emailer.php: adding log record of this run");
		        $intolog = mysql_query("INSERT INTO log values ($shwagline[shwagid], $count, '$date')");
        		echo "<font color=red>This is not a test<br><br></font>";

                $start = date('m-d-Y H:i:s');
                $start_epoch = mktime(date('H'),date('i'),date('s'),date('M'),date('d'),date('Y'));
                echo "<br>Started at $start (epoch: $start_epoch)<br><br>";
				debug("emailer.php: start time is $start, epoch is $start_epoch");


				debug("emailer.php: querying for emails from table $dbindex limiting by $count");
	        	$getmails = mysql_query("SELECT emailid, email from $dbindex where shwagid = 0 order by emailid desc limit $count") or die (mysql_error());
				debug("emailer.php: done with email selection query");

                // Iterate through every resulting email and send the mail to
                // them.
				debug("emailer.php: entering into mail send loop.");
		        while ($newmail = mysql_fetch_array($getmails)) {
			        $mailaddress = $newmail[email];
					debug("emailer.php: sending to address $mailaddress.");
					
					// Check to see if the email is in the remove table
					// If so, don't send to them
					debug("emailer.php: checking to see if $mailaddress is in the remove table.");
					$isthere_query = mysql_query("SELECT email FROM removals WHERE email='$mailaddress'");
					$numrows = mysql_num_rows($isthere_query);

					if ( $numrows > 0 ) {
						print "Hit mail address $mailaddress that's in the remove list<br>";
						debug("emailer.php: address $address IS in the remove table, skipping (via next).");
						next;
					} else {
						$thereturn = mail($mailaddress, $shwagline[subject], $shwagline[html], $header);
    					echo "($i): MAIL SENT TO $mailaddress: <br>";
						debug("emailer.php: address $address is NOT in the remove table, moving on.");
					}

					debug("emailer.php: updating table $dbindex to say we went this email.");
		    	    $upit = mysql_query("UPDATE $dbindex set shwagid = '$shwagline[shwagid]', date = '$date' where emailid = $newmail[emailid]") or die (mysql_error());
			   	    $i++;

		        }
	        }

            // Didn't make sense to have this here.
	        // mail('darren@younghome.com,tjjuedes@aol.com',
            //      'Email run complete',
            //      "completed email run of $i messages:<br><br> message: <br><br> $shwagline[html]", $header);
        }

        // Print some info out about the run we just made and send an email
        // message to certain people informing them of what just went on.

        $end = date('m-d-Y H:i:s');
        $end_epoch = mktime(date('H'),date('i'),date('s'),date('M'),date('d'),date('Y'));
        echo "<br>Ended at $end (epoch: $end_epoch)<br>";
		debug("emailer.php: run complete at $end, epoch = $end_epoch.");

        // How long did the script take to run?
        $total_time_in_seconds = $end_epoch - $start_epoch;

        echo "<br><b>$i emails sent in $total_time_in_seconds second(s)</b><br><br>";
		debug("emailer.php: run took $total_time_in_seconds seconds to complete.");

		debug("emailer.php: sending mail to some people to let them know we're all done.");
        mail('darren_young@yahoo.com,tjjuedes@aol.com',
             'Email run complete',
             "completed email run of $i messages:<br>
             started at $start and ended at $end ($total_time_in_seconds second run time)<br>
             message: <br><br> $shwagline[html]", $header);


		// Function to gracefully shutdown the script. Haven't had time to
        // fiddle with this although it needs to be done. If someone clicks on
        // the stop button in the middle of the run strange things could
        // happen.
		function shutdown_script() {
//
//			// The user stopped the script
//			if ( connection_aborted() ) {
//				echo "Why did you stop this script?";
//				return TRUE;
//			}
//
//			// The script timed out
//			if ( connection_timeout() ) {
//				echo "Connection timed out";
//				return TRUE;
//			}
//
			return TRUE;
//
		}
//
    ?>

    </body>

</html>
