# =============================================================================
#
# BOLSender.pl
#
# Send BOL documents to carriers
#
# $Id: BOLSender.pl,v 1.19 2003/02/05 19:00:21 youngd Exp $
#
# Contents Copyright (c) 2002-2003, The Freight Depot
#
# Darren Young [dyoung@thefreightdepot.com]
#
# See the README file in this directory for a more detailed 
# explanation of how this program and process operates
#
# See the configuration file, sender.cfg, in this directory for
# more details on the configuration of this program
#
# =============================================================================
#
# vim: set noautoindent:
# vim: set nosmartindent:
#
# =============================================================================
#
# Usage:
#
#    BOLSender.pl <options>
#
# =============================================================================
#
# ChangeLog
#
# $Log: BOLSender.pl,v $
# Revision 1.19  2003/02/05 19:00:21  youngd
#   * Added database section.
#
# Revision 1.18  2003/01/09 22:09:23  youngd
#   * Added a test to see of the carrier for the shipment CAN do EDI 204's.
#
# Revision 1.17  2003/01/09 21:15:03  youngd
#   * Changed the type and paymentmethod to be outside of the transmission.
#
# Revision 1.16  2003/01/09 20:42:29  youngd
#   * Added trans_ack and trans_mode.
#
# Revision 1.15  2003/01/09 20:29:21  youngd
#   * Removed the old commented date range checking code.
#
# Revision 1.14  2003/01/09 20:27:35  youngd
#   * Got rid of the Date::Range. It didn't work.
#   * Added use of the Date::Calc module.
#   * Added comparisons of the pickup date vs the current date.
#
# Revision 1.13  2003/01/08 23:59:59  youngd
#   * Added use of Date::Simple and Date::Range.
#
# Revision 1.12  2003/01/08 23:15:26  youngd
#   * Added getShipmentPickupDate() function.
#   * Added a spot in the processing to use the pickup date as part of
#     the decision whether or not we transmit the BOL. The idea is, what if
#     the pickup date is 3 weeks from now? Do we want to go ahead and send
#     it to the carrier or wait until some time before the date and send it
#     through. From what I've been told by the carriers is that we can send
#     it anytime and it'll get queued up in their system and scheduled for
#     pickup. The thing is, I don't believe them 100%. We'll see.
#
# Revision 1.11  2003/01/08 22:14:18  youngd
#   * Changed the location of the config file to be in /edi like production.
#
# Revision 1.10  2003/01/08 22:11:29  youngd
#   * Fixed bug in hazmat replacement, too many %%'s.
#
# Revision 1.9  2003/01/08 22:08:10  youngd
#   * Added hazmat info.
#
# Revision 1.8  2003/01/08 22:02:47  youngd
#   * Added contact and phone information.
#
# Revision 1.7  2003/01/07 20:51:30  youngd
#   * Removed the package name in here, should be main.
#   * Moved the debug() function over to the shared module logging.
#   * Moved the checkForFailed() function over to the shared module edi.
#
# Revision 1.6  2003/01/06 23:31:33  youngd
#   * Finished all the function comments and descriptions. I think they're all
#     right. We'll see soon enough.
#
# Revision 1.5  2003/01/06 23:07:01  youngd
#   * Added the use of the debug_sql flag in the config file.
#   * Added debug statements to print the SQL in the various functions if
#     the debug_sql flag is set.
#   * Started to add comments for all the functions, got maybe 6 of them
#     done so far.
#   * Fixed the updateSentDate() function. It was bailing without ''
#     around the datetime variable. I should have known that one.
#   * Almost all of the global variables now have a save default value.
#
# Revision 1.4  2003/01/06 21:57:14  youngd
#   * Moved the print if debug to be debug().
#   * Reworked the bol generation for the new database schema.
#   * Added the transmission header info to the bol.
#   * Now using the new 204 template.
#   * Use the default company data for billing instead of the one associated
#     with the shipment.
#   * Reworked the FTP send function to have a timeout.
#
# Revision 1.3  2002/12/07 00:37:51  youngd
#   * Adding changes per Kleinschmidt.
#
# =============================================================================

#package BOLSender;

my $cvsid = '$Id: BOLSender.pl,v 1.19 2003/02/05 19:00:21 youngd Exp $';
my @cvsinfo = split(' ', $cvsid);
my $VERSION = @cvsinfo[2];

BEGIN {

    # Add our standard library path
    use lib '/tfd/modules';
    use edi;
    use logging qw(verbose debug);
    use err;

    # Standard pacakges we use
    use DBI;
    use Getopt::Long;
    use Net::FTP;
    use POSIX qw(strftime setsid);
    use Config::General;
    use Date::Calc qw(Delta_Days);
    
    # Pragmas
    # use vars;
    use warnings; 
    use strict;
    

}


# -------------------------------------------------------------------
#               G L O B A L   V A R I A B L E S
# -------------------------------------------------------------------

$configFile          = "/edi/sender.cfg";
$sleep               = 10;
$skipped             = 0;
$mailfile            = "/tmp/sender.$$";
$debug               = 0;
$debug_sql           = 0;
$help                = 0;
$sleep               = 30;
$interactive         = 0;
$dotick              = 1;
$update_sentflag     = 1;
$update_sentdate     = 1;
$resend_failed       = 1;
$tmpl_204            = "/edi/204.tmpl";
$db_hostname         = "localhost";
$db_database         = "digiship";
$db_username         = "root";
$db_password         = "password";
$db_retry            = 1;
$van_name            = "KLEINSCHMIDT";
$van_ftp             = "ftp.kleinschmidt.com";
$van_username        = "DIGISHIP";
$van_password        = "DIGISHIP";
$queue_root          = "";
$queue_out           = "";
$queue_processed     = "";
$queue_fail          = "";
$ftp_timeout         = 30;
$ftp_pasv            = 0;
$ftp_debug           = 0;
$ftp_test            = 0;
$kli_format          = "";
$kli_sender          = "";
$kli_receiver        = "";
$bol_doctype         = "";
$bol_revid           = "";
$type                = "";
$paymentmethod       = "";
$trans_mode          = "";
$trans_ack           = "";
$use_date_range      = 0;
$date_range_days     = 1;
$skip_old            = 0;



# -------------------------------------------------------------------
#               C O M M A N D   L I N E   O P T I O N S
# -------------------------------------------------------------------

GetOptions  (   "debug"          => \$debug,
                "config-file=s"  => \$configFile,
                "help"           => \$help
            );

# If they want help, give it to 'em
if ( $help ) {
    print_usage();
    exit(0);
}

debug("Debug enabled");
debug("Using configuration from $configFile");



# -------------------------------------------------------------------
#               C O N F I G   F I L E   P R O C E S S
# -------------------------------------------------------------------
debug("Processing config file");
open(CFGFILE, "$configFile") || die "Unable to open config file
                                     $configFile ($!)\n";
close(CFGFILE);

$conf = new Config::General($configFile);
%config = $conf->getall();

$debug_sql           = $config{DEBUG_SQL};
$sleep               = $config{SLEEP};
$interactive         = $config{INTERACTIVE};
$dotick              = $config{DOTICK};
$update_sentflag     = $config{UPDATE_SENTFLAG};
$update_sentdate     = $config{UPDATE_SENTDATE};
$resend_failed       = $config{RESEND_FAILED};
$tmpl_204            = $config{TMPL_204};
$db_hostname         = $config{DB_HOSTNAME};
$db_database         = $config{DB_DATABASE};
$db_username         = $config{DB_USERNAME};
$db_password         = $config{DB_PASSWORD};
$db_retry            = $config{DB_RETRY};
$van_name            = $config{VAN_NAME};
$van_ftp             = $config{VAN_FTP};
$van_username        = $config{VAN_USERNAME};
$van_password        = $config{VAN_PASSWORD};
$queue_root          = $config{QUEUE_ROOT};
$queue_out           = $config{QUEUE_OUT};
$queue_processed     = $config{QUEUE_PROCESSED};
$queue_fail          = $config{QUEUE_FAIL};
$ftp_timeout         = $config{FTP_TIMEOUT};
$ftp_pasv            = $config{FTP_PASV};
$ftp_debug           = $config{FTP_DEBUG};
$ftp_test            = $config{FTP_TEST};
$kli_format          = $config{KLI_FORMAT};
$kli_sender          = $config{KLI_SENDER};
$kli_receiver        = $config{KLI_RECEIVER};
$bol_doctype         = $config{BOL_DOCTYPE};
$bol_revid           = $config{BOL_REVID};
$alert               = $config{ALERT};
$alert_level         = $config{ALERT_LEVEL};
$alert_type          = $config{ALERT_TYPE};
$alert_addresses     = $config{ALERT_ADDRESSES};
$type                = $config{TYPE};
$paymentmethod       = $config{PAYMENTMETHOD};
$trans_mode          = $config{EDIMODE};
$trans_ack           = $config{EDIACK};
$use_date_range      = $config{USE_DATE_RANGE};
$date_range_days     = $config{DATE_RANGE_DAYS};
$skip_old            = $config{SKIP_OLD};

debug("CONFIG DUMP:");
debug("---------------------------------------------------");

foreach $key (sort(keys %config)) {
    debug("Key: $key, Value: $config{$key}");
}

debug("---------------------------------------------------");




# -------------------------------------------------------------------
#                       P R O G R A M   M A I N 
# -------------------------------------------------------------------
$now = localtime(time());
debug("Main starting at $now");

if ( $interactive ) {
	print "BOLSender version $VERSION starting in interactive mode...\n";
} else {
	print "BOLSender version $VERSION starting in batch mode...\n";
}

# MAIN PROGRAM STARTS HERE
# Go into the background and work
while(1) {

    # XXX - Should this be up front or in the end?
    debug("Going to sleep for $sleep");
    sleep $sleep;

    # We just woke up, do the work
    print "Waking up from a sleep...\n";
    
    # Log into the database
    # Keep trying until we get in
    if ( $db_retry ) {
        while ( ! dbConnect($db_hostname, $db_database, $db_username, $db_password) ) {
            sleep 3;
        }
    } else {
        if ( ! dbConnect($db_hostname, $db_database, $db_username, $db_password) ) {
            print "Database connection failed, and we're not configured to retry\n";
            exit(1);
        } 
    }
    
    # Check for previously failed BOL transmissions
    # Transmit any old BOLS if we are configured to do so
    if ( $resend_failed ) {
        print "Checking for previously failed transactions\n";
        
        # Simply get a result here
        if ( checkForFailed("$queue_root/$queue_fail") ) {
            print "There are failed BOLS left\n";

            # No, actually generate the listing of failed files
            @failed = checkForFailed("$queue_root/$queue_fail");

            # Iterate through the listing
            foreach $failed_bol ( @failed ) { 
                if ( $interactive ) {
                print "Standing by to transmit failed bol $failed_bol\n";
                print "Ready [y/n]? ";
                $answer = <STDIN>;
                chop($answer);
    
                # If they say no, skip this shipment
                    if ( $answer eq "n" ) {
                        print "Skipping shipment $shipment\n";
                        $skipped++;
                        next;
                    } else {
                        # Transmit the bol to the VAN
                        if ( transmitBOL($failed_bol) ) {
                            print "Transmitted old bol $failed_bol\n";
                            # Now that is has been send, move it to the outq dir
                            print "Renaming $queue_root/$queue_fail/$failed_bol to $queue_root/$queue_out/$failed_bol\n";
                            if ( rename("$queue_root/$queue_fail/$failed_bol", "$queue_root/$queue_out/$failed_bol") ) {
                                print "Moved it out of the failed queue\n";
                            } else {
                                print "Move of failed bol died ($!)\n";
                            }
                        } else {
                            print "Failed to transmit $failed_bol, back in failed queue\n";
                        }
                    }
                }
            }
        }
    } # End resend old bol's
    
    # Find out how many new shipments there are
    # if more than 0, move on, otherwise go back to sleep
    if ( newShipments() == 0 ) {
    
        # Blast out if we're in interactive mode
        if ( $interactive ) {
            print "No new shipments, exiting\n";
            exit(0);
        } else {
            print "No new shipments to send out, falling asleep\n";
            dbClose();
            next;           # Breaks out of this while() loop
        }
        
    } else {
        # There are new shipments, let them know and move on
        print "There are " . newShipments() . " new shipments to send out\n";
    }
    
    # Get a list of new shipments that we need to deal with
    debug("Generating array of new shipments");
    @shipments = getNewShipments();
    
    # Iterate through the shipments and generate BOL's for those orders
    foreach $shipment (@shipments) {

        # Get the carrierid for the shipment, need that for the next chunk
        $carrierid = getCarrierForShipment($shipment);

        # See if we're set up to send 204's to the carrier, of not, why bother moving on?
        if ( getCarrier204Status($carrierid) ) {
            debug("EDI 204's are enabled for carrierid $carrierid");
        } else {
            debug("EDI 204's are NOT enabled for carrierid $carrierid, skipping shipment $shipment");
            $skipped++;
            next;
        }

        # Get the pickup date for the shipment
        $pickupdate = getShipmentPickupDate($shipment);
        debug("Pickup date: $pickupdate");

        # Set up some variables for the date comparison
        $now = strftime "%Y-%m-%d", localtime;
        ($now_year, $now_month, $now_day) = split("-", $now);
        ($pickup_year, $pickup_month, $pickup_day) = split("-", $pickupdate);

        # Use Date::Calc to determine the number of days between now and the pickup date
        # 0 = today
        # < 0 = number of days in the past
        # > 0 = number of days in the future
        $range = Delta_Days($now_year, $now_month, $now_day, $pickup_year, $pickup_month, $pickup_day);

        debug("Date range for pickup: $range");

        # The pickup is today
        if ( $range == 0 ) {
            # Not sure what to do with these yet.
            debug("The shipment is scheduled for pickup today!");
        }

        # The pickup is in the past, don't send it if configured to do so
        if ( $skip_old ) {
            if ( $range < 0 ) {
                debug("The shipment is scheduled for pickup in the past, skipping shipment $shipment");
                $skipped++;
                next;
            }
        }


        if ( $date_range_check ) {

            # Check if it's more than the specified days out
            if ( $range > $date_range_days ) {
                debug("Date range checking is enabled and the pickup date is $date_range_days days away, skipping");
                next;
            }
        }


        print "Processing shipmentid $shipment - Ready for pickup on $pickupdate\n";
        
        # If we're in interactive mode, ask the user for the action    
        if ( $interactive ) {
            print "Ready [y/n]? ";
            $answer = <STDIN>;
            chop($answer);
    
            # If they say no, skip this shipment
            if ( $answer eq "n" ) {
                print "Skipping shipment $shipment\n";
                $skipped++;
                next;
            }
        }


        # Use the pickup date to determine if we want to transmit now or wait
        # This isn't used yet, just here as a place holder in case it is.

        
        # Generate the actual text of the BOL and store it in a file
        # genBOL returns a pointer to the new BOL
        if ( $bol = genBOL($shipment) ) {
            debug("Generated BOL file $bol");
        } else {
            print "Failed to generate BOL file\n";
            next;
        }
        
        # Transmit the BOL
        if ( transmitBOL($bol) ) {
            print "BOL: $bol transmitted\n";
        } else {
            print "Failed to transmit bol $bol\n";
        }
              
    } # End of the shipment foreach loop
    
    # Logout from the database
    dbClose();

    # If configured to send email alerts, send a status report
    
    # If in interactive mode, exit
    if ( $interactive ) {
        print "You skipped $skipped shipments\n";
        exit(0);
    } else {
        print "*** GOING TO SLEEP ***\n";
    }    
}


# END OF SCRIPT





# -----------------------------------------------------------------------------
# NAME        : dbConnect
# DESCRIPTION : Connect a handle to the database using DBI
# ARGUMENTS   : string(hostname), string(database),
#               string(username), string(password)
# RETURNS     : Fills in the global $dbh
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub dbConnect {
    my $hostname = shift;
    my $database = shift;
    my $username = shift;
    my $password = shift;

    # These need to be "local" variables
    my ($driver, $port, $dsn);
    
    $driver   = "mysql";
    $port     = undef;

    debug("Attempting connection to database");
    $dsn = "DBI:$driver:database=$database;host=$hostname;port=$port";
    $dbh = DBI->connect($dsn, $username, $password);
    
    unless($dbh) {
        print "Connection failed\n";
        return(0);
    } else {
        print "Connected to database\n";
        return(1);
    }
}


# -----------------------------------------------------------------------------
# NAME        : dbClose
# DESCRIPTION : Closes a database connection handle $dbh
# ARGUMENTS   : $dbh is global
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub dbClose {

    if ( $dbh->disconnect() ) { 
        print "Database disconnected\n";
        return(1);
    } else {
        print "Database disconnect failed\n";
        return(0);
    }       
}


# -----------------------------------------------------------------------------
# NAME        : newShipments
# DESCRIPTION : Returns the number of new shipments in the database via $dbh
# ARGUMENTS   : $dbh is global
# RETURNS     : 0 or the number of new shipments (rows)
# STATUS      : Stable
# NOTES       : This looks at the 204sent field of the shipment table. They 
#             : have to be 0 to be selected. NULL values will be excluded.
# -----------------------------------------------------------------------------
sub newShipments {

    my $sql;
    my $sth;
    my $numRows;

    unless($dbh) {
        print "Sorry, connect to the database first\n";
        return(0);
    }
    
    # Select statement to retrieve all shipment statuses
    $sql = "SELECT * from shipment WHERE 204sent = 0";
        
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
   
    $numRows = $sth->rows;
    
    debug("NUM NEWSHIPMENT ROWS: $numRows");
    
    $sth->finish();
    
    # Send back the total number of new shipments to the caller
    return($numRows);
}



# -----------------------------------------------------------------------------
# NAME        : getNewShipments
# DESCRIPTION : Returns an array of shipments to process
# ARGUMENTS   : $dbh is global
# RETURNS     : array @shipments
# STATUS      : Stable
# NOTES       : Use the newShipments() function before this one to determine
#             : if there are new shipments out there. This function _assumes_
#             : there are new ones out there.
# -----------------------------------------------------------------------------
sub getNewShipments {

    unless($dbh) {
        print "Sorry, connect to the database first\n";
        return(0);
    }
    
    my $sql;
    my $sth;
    my $ref;
    my @shipments;
    
    # Select statement to retrieve all shipment statuses
    $sql = "SELECT * from shipment WHERE 204sent = 0";
        
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    # Iterate through the result set and push the shipment id into the stack
    while ($ref = $sth->fetchrow_hashref()) { 
        debug("Found a row: shipmentid = " . $ref->{'shipmentid'});
        push(@shipments, $ref->{'shipmentid'});
    }
    
    $sth->finish();
    
    # Send back an array of the new shipments to the caller
    return(sort(@shipments));
}


# -----------------------------------------------------------------------------
# NAME        : print_usage
# DESCRIPTION : Prints how to use this program
# ARGUMENTS   : None
# RETURNS     : 0
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub print_usage {
    print "Usage: BOLSender.pl <options>\n";
    print "   Options:\n";
    print "      --help                Help\n";
    print "      --debug           Enable / disable debugging\n";
    print "      --config-file=<file>  Alternate configuration file\n";
    
    return(0);
}



# -----------------------------------------------------------------------------
# NAME        : genBOL
# DESCRIPTION : Generate a BOL file from the database
# ARGUMENTS   : string(shipmentid)
# RETURNS     : string(filename)
# STATUS      : Stable
# NOTES       : The shipmentid passed has to be a valid shipmentid.
#             : The filename is relative, not the full path of the one created.
#             : You should get back something like 152444.xml then you can 
#             : use that along with $queue_root to find the file.
# -----------------------------------------------------------------------------
sub genBOL {

    my $shipmentid = shift;
    
    my $sql;
    my $sth;
    my $numRows;
    my $bol;    
    
    unless($shipmentid) {
        print "Give me a shipment number!\n";
        return(0);
    }

    unless($dbh) {
        print "Sorry, connect to the database first\n";
        return(0);        
    }
    
    $bol = "$shipmentid.xml";
    if ( ! open(BOL, ">$queue_root/$queue_out/$bol") ) {
        print "BOL open failed ($!)\n";
        exit(1);
    }
    
    # ================================================
    # GATHER THE BASIC SHIPMENT DATA FROM THE DATABASE
    # ================================================
    $shipsql = "SELECT \
                  carriers.name                AS carriername, \
                  carriers.scac                AS carrierscac, \
                  shipment.customerid          AS customerid, \
                  shipment.pickupdate          AS pickupdate, \
                  shipment.pickupbefore        AS pickupbefore, \
                  shipment.pickupafter         AS pickupafter, \
                  shipment.ponumber            AS ponumber, \
                  shipment.productdescription  AS productdescription, \
                  shipment.hazmat              AS hazmat, \
                  shipment.hazmatphone         AS hazmatphone, \
                  shipment.units               AS units, \
                  quotes.weight                AS weight, \
                  quotes.class                 AS classa, \
                  shipment.origin              AS origin, \
                  shipment.destination         AS destination, \
                  shipment.billing             AS billing, \
                  shipment.specialinstructions AS specialinstructions \
               FROM \
                  carriers, \
                  shipment, \
                  quotes \
               WHERE \
                  shipment.carrierid = carriers.carrierid AND \
                  shipment.quoteid = quotes.quoteid AND \
                  shipment.shipmentid = $shipmentid";

            
    if ( $debug_sql ) {
        debug("genBOL(): shipsql = $shipsql");
    }
            
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($shipsql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    $numRows = $sth->rows;
    debug("NUM BOL ROWS: $numRows");

    # If there's no rows, something happened
    if ( $numRows == 0 ) {
        print "0 rows returned from query, something happened!\n";
        return(0);
    }
    
    # There should only only be a single row
    if ( $numRows > 1 ) {
        print "There's more than 1 row, bad stuff happened!\n";
        return(0);
    }

    # Iterate through the result set and assign the variables
    while ($ref = $sth->fetchrow_hashref()) {  
    
        $carriername    = $ref->{'carriername'};
        $carrierscac    = $ref->{'carrierscac'};
        $ponumber       = $ref->{'ponumber'};
        $customerid     = $ref->{'customerid'};

        $weight         = $ref->{'weight'};
        $class          = $ref->{'classa'};

        $units          = $ref->{'units'};
        $productdesc    = $ref->{'productdescription'};
        $pickupdate     = $ref->{'pickupdate'};
        $pickupbefore   = $ref->{'pickupbefore'};
        $pickupafter    = $ref->{'pickupafter'};
        
        $hazmat         = $ref->{'hazmat'};
        $hazmatphone    = $ref->{'hazmatphone'};

        $originid       = $ref->{'origin'};
        $destid         = $ref->{'destination'};
        $billingid      = $ref->{'billing'};
    }



    # ==================================
    # GET THE ORIGIN ADDRESS INFORMATION
    # ==================================
    debug("GETHERING ORIGIN INFORMATION FOR ID $originid");
    $originsql = "SELECT \
                     company, \
                     address1, \
                     address2, \
                     city, \
                     state, \
                     zip, \
                     contact, \
                     phone \
                  FROM \
                     address \
                  WHERE \
                     addressid = $originid";
     
    if ( $debug_sql ) {
        debug("genBOL(): originsql = $originsql");
    }
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($originsql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    $numRows = $sth->rows;
    debug("NUM ORIGIN ROWS: $numRows");

    # If there's no rows, something happened
    if ( $numRows == 0 ) {
        print "0 rows returned from query, something happened!\n";
        return(0);
    }
    
    # There should only only be a single row
    if ( $numRows > 1 ) {
        print "There's more than 1 row, bad stuff happened!\n";
        return(0);
    }

    # Iterate through the result set and assign the variables
    while ($ref = $sth->fetchrow_hashref()) {
        $origincompany  = $ref->{'company'};
        $origincontact  = $ref->{'contact'};
        $originaddress1 = $ref->{'address1'};
        $originaddress2 = $ref->{'address2'};
        $origincity     = $ref->{'city'};
        $originstate    = $ref->{'state'};
        $originzip      = $ref->{'zip'};
        $originphone    = $ref->{'phone'};
    }


    # =======================================
    # GET THE DESTINATION ADDRESS INFORMATION
    # =======================================
    debug("GETHERING DESTINATION INFORMATION FOR ID $destid");
    $destsql = "SELECT \
                    company, \
                    address1, \
                    address2, \
                    city, \
                    state, \
                    zip, \
                    contact, \
                    phone \
                 FROM \
                    address \
                 WHERE \
                    addressid = $destid";

    if ( $debug_sql ) {
        debug("genBOL(): shipsql = $shipsql");
    }
    
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($destsql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    $numRows = $sth->rows;
    debug("NUM DEST ROWS: $numRows");

    # If there's no rows, something happened
    if ( $numRows == 0 ) {
        print "0 rows returned from query, something happened!\n";
        return(0);
    }
    
    # There should only only be a single row
    if ( $numRows > 1 ) {
        print "There's more than 1 row, bad stuff happened!\n";
        return(0);
    }

    # Iterate through the result set and assign the variables
    while ($ref = $sth->fetchrow_hashref()) {
        $destcompany  = $ref->{'company'};
        $destcontact  = $ref->{'contact'};
        $destaddress1 = $ref->{'address1'};
        $destaddress2 = $ref->{'address2'};
        $destcity     = $ref->{'city'};
        $deststate    = $ref->{'state'};
        $destzip      = $ref->{'zip'};
        $destphone    = $ref->{'phone'};
    }



    # ===================================
    # GET THE BILLING ADDRESS INFORMATION
    # ===================================
    # Remember, from the carrier perspective WE are the billing (third party)
    # So, don't use the billingid associated with the order
    debug("GETHERING BILLING INFORMATION FOR ID $billingid");
    $billingsql = "SELECT \
                       companyname, \
                       address1, \
                       address2, \
                       city, \
                       state, \
                       zip, \
                       mainphone \
                    FROM \
                       digiship";
    
    if ( $debug_sql ) {
        debug("genBOL(): shipsql = $shipsql");
    }
    
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($billingsql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    $numRows = $sth->rows;
    debug("NUM BILLING ROWS: $numRows");

    # If there's no rows, something happened
    if ( $numRows == 0 ) {
        print "0 rows returned from query, something happened!\n";
        return(0);
    }
    
    # There should only only be a single row
    if ( $numRows > 1 ) {
        print "There's more than 1 row, bad stuff happened!\n";
        return(0);
    }

    # Iterate through the result set and assign the variables
    while ($ref = $sth->fetchrow_hashref()) {
        $billingcompany  = $ref->{'companyname'};
        $billingcontact  = "CUSTOMER SERVICE";
        $billingaddress1 = $ref->{'address1'};
        $billingaddress2 = $ref->{'address2'};
        $billingcity     = $ref->{'city'};
        $billingstate    = $ref->{'state'};
        $billingzip      = $ref->{'zip'};
        $billingphone    = $ref->{'mainphone'};
    }
    



    # Now that we have all the data, open the template
    # and do a search/replace on the keywords
    # Basically, we read in from the template, replace the 
    # % fields then write everything out to the BOL file handle 
    if ( ! open(TEMPLATE, "<$tmpl_204") ) {
        print "Unable to open 204 template $tmpl_204 ($!)\n";
        exit(1);
    }

    # Generate the KleinSchmidt header
    # Adjustable in the config file    
    $kli_header = "<?kli format=$kli_format sender=$kli_sender receiver=$carrierscac?>";

    # Go get the accessorials from the database
    # Will be a list of accessorials or empty
    # If it's a list, it'll be in XML already
    @accessorials = getAccessorials($shipmentid);
    
    # The current time for the transaction
    $trans_timestamp = timefmt();

    # Generate a unique transmission id based on the epoch + the shipmentid
    $trans_transmissionid = $shipmentid . "-" . time();

    # Run through the template replacing keywords that contain %% tags
    while(<TEMPLATE>) {
    
        # Remove the trailing newline
        chop();

        $_ =~ s/%KLI_HEADER%/$kli_header/;
                
        $_ =~ s/%DOCTYPE%/$bol_doctype/;
        $_ =~ s/%REVID%/$bol_revid/;

        $_ =~ s/%TRANS_TRANSMISSIONID%/$trans_transmissionid/;
        $_ =~ s/%TRANS_TIMESTAMP%/$trans_timestamp/;
        $_ =~ s/%TRANS_MODE%/$trans_mode/;
        $_ =~ s/%TRANS_ACK%/$trans_ack/;

        $_ =~ s/%CARRIERNAME%/$carriername/;
        $_ =~ s/%CARRIERSCAC%/$carrierscac/;

        $_ =~ s/%TYPE%/$type/;
        $_ =~ s/%PAYMENTMETHOD%/$paymentmethod/;
        $_ =~ s/%BOLNUMBER%/$shipmentid/;
        $_ =~ s/%PONUMBER%/$ponumber/;
        
        $_ =~ s/%PRODUCTDESCRIPTION%/$productdesc/;
        $_ =~ s/%PRODUCTPACKAGING%/PALLET/;
        $_ =~ s/%PRODUCTWEIGHT%/$weight/;
        $_ =~ s/%PRODUCTCLASS%/$class/;
        $_ =~ s/%PRODUCTUNITS%/$units/;
        $_ =~ s/%PRODUCTHAZMAT%/$hazmat/;
        $_ =~ s/%PRODUCTHAZMATPHONE%/$hazmatphone/;
        
        $_ =~ s/%ORIGINNAME%/$origincompany/;
        $_ =~ s/%ORIGINADDRESS1%/$originaddress1/;
        $_ =~ s/%ORIGINADDRESS2%/$originaddress2/;
        $_ =~ s/%ORIGINCITY%/$origincity/;
        $_ =~ s/%ORIGINSTATE%/$originstate/;        
        $_ =~ s/%ORIGINZIP%/$originzip/;
        $_ =~ s/%ORIGINCONTACT%/$origincontact/;
        $_ =~ s/%ORIGINPHONE%/$originphone/;

        $_ =~ s/%DESTNAME%/$destcompany/;
        $_ =~ s/%DESTADDRESS1%/$destaddress1/;
        $_ =~ s/%DESTADDRESS2%/$destaddress2/;
        $_ =~ s/%DESTCITY%/$destcity/;
        $_ =~ s/%DESTSTATE%/$deststate/;        
        $_ =~ s/%DESTZIP%/$destzip/;
        $_ =~ s/%DESTCONTACT%/$destcontact/;
        $_ =~ s/%DESTPHONE%/$destphone/;

        $_ =~ s/%BILLINGNAME%/$billingcompany/;
        $_ =~ s/%BILLINGADDRESS1%/$billingaddress1/;
        $_ =~ s/%BILLINGADDRESS2%/$billingaddress2/;
        $_ =~ s/%BILLINGCITY%/$billingcity/;
        $_ =~ s/%BILLINGSTATE%/$billingstate/;        
        $_ =~ s/%BILLINGZIP%/$billingzip/;
        $_ =~ s/%BILLINGCONTACT%/$billingcontact/;
        $_ =~ s/%BILLINGPHONE%/$billingphone/;
        
        $_ =~ s/%PICKUPDATE%/$pickupdate/;
        $_ =~ s/%PICKUPBEFORE%/$pickupbefore/;
        $_ =~ s/%PICKUPAFTER%/$pickupafter/;
        
        if ( $_ =~ /%ACCESSORIALS/ ) {
            s/%ACCESSORIALS%/@accessorials/;
        }

        print BOL $_ . "\n";
    }


    # Close the template out
    if ( ! close(TEMPLATE) ) {
        print "Error closing template ($!)\n";
        return(0);
    }
    
    # Close the new BOL out 
    if ( !close(BOL) ) {
        print "Unable to close BOL\n";
        return(0);
    } else {
        $sth->finish();
        return($bol);
    }
} # Now is that a nasty function or what?


# -----------------------------------------------------------------------------
# NAME        : ftpSend
# DESCRIPTION : Send a BOL out to the VAN via FTP
# ARGUMENTS   : string(bol), string(van)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : The BOL you pass to this function should be just the name of
#             : the file, not the full path to it. We'll tack that on based on
#             : the config file.
# -----------------------------------------------------------------------------
sub ftpSend {

    my $bol = shift;
    my $van = shift;
    my $ftp;
   
    unless($bol) {
        print "You have to give be a bol file to send!\n";
        return(0);
    }

    debug("ftpSend(): bol given to me is $bol");
    debug("ftpSend(): van given to me is $van");
    debug("ftpSend(): ftp given to me is $ftp");

    # Don't really send if this flag is set
    if ( $ftp_test ) {
        print "Inside the send method, but we're not going to send\n";
        return(1);
    }
    
    print "Sending $bol to $van_name via $van_ftp\n";
 

    #if ( ! chdir($queue_root . "/" . $queue_out) ) {
    #    print "Unable to change to queue dir ($!)\n";
    #    return(0);
    #}   
 
    $ftp = Net::FTP->new(   $van_ftp,
                            Debug   => $ftp_debug,
                            Timeout => $ftp_timeout,
                            Passive => $ftp_passive
                        ) || warn "FTP creation failed\n";
 
    if ( ! $ftp->login($van_username, $van_password) ) {
        print "Login to $van_ftp failed ($!)\n";
        return(0);
    }
 
    if ( ! $ftp->put("$queue_root/$queue_out/$bol") ) {
        print "Put of $bol from $queue_root/$queue_out to $van_ftp failed ($!)\n";
        return(0);
    }
    
    # Close the FTP connection
    $ftp->quit;

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : updateSentflag
# DESCRIPTION : Update the 204sent field associated with a shipmet
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : Make sure it's a valid shipmentid. Or even better, add some
#             : logic to make sure it is a valid shipment...
# -----------------------------------------------------------------------------
sub updateSentflag {

    my $shipmentid = shift;
    my $sql;
    my $sth;
    
    unless($shipmentid) {
        print "You have to give me a shipmentid!\n";
        return(0);
    }
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }

    debug("updateSentflag(): shipmentid given to me is $shipmentid");
    
    $sql = "UPDATE shipment SET 204sent=1 WHERE shipmentid=$shipmentid";
    debug("updateSentflag(): sql = $sql");

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);

    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    if ( $sth->finish() ) {
        debug("updateSentflag(): updated sent flag in database");
        return(1);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }
}



# -----------------------------------------------------------------------------
# NAME        : updateSentDate
# DESCRIPTION : Update the 204sentdate field associated with a shipment
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : Again, make sure it's a valid shipmentid
# -----------------------------------------------------------------------------
# Takes a shipmentid and updates the 204sentdate field
sub updateSentDate {

    my $shipmentid = shift;
    my $sql;
    my $sth;
    
    unless($shipmentid) {
        print "You have to give me a shipmentid!\n";
        return(0);
    }
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }

    debug("updateSentDate(): shipmentid given to me is $shipmentid");
    
    $now = timefmt();
    $sql = "UPDATE shipment SET 204sentdate=\'$now\' WHERE shipmentid=$shipmentid";
    if ( $debug_sql ) {
        debug("updateSentDate(): sql = $sql");
    }

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);

    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    if ( $sth->finish() ) {
        debug("updateSentDate(): update sentdate field in database");
        return(1);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : update_carrierbooked
# DESCRIPTION : Update the carrierbooked field associated with a shipment
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub update_carrierbooked {
    my $shipmentid = shift;
    my $sql;
    my $sth;
    
    unless($shipmentid) {
        print "You have to give me a shipment id!\n";
        return(0);
    }
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }

    debug("update_carrierbooked(): shipmentid given to me is $shipmentid");
    
    $sql = "UPDATE shipment SET carrierbooked=1 WHERE shipmentid=$shipmentid";
    
    if ( $debug_sql ) {
        debug("update_carrierbooked(): sql = $sql");
    }
    

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    if ( $sth->finish() ) {
        debug("update_carrierbooked(): updated carrierbooked flag in database");
        return(1);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : updateBookedDate
# DESCRIPTION : Update the carrierbookeddate associated with a shipment
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub updateBookedDate {
    my $shipmentid = shift;
    
    unless($shipmentid) {
        print "You must give me a shipment id!\n";
        return(0);
    }
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }
    
    $booked_date = timefmt();
    $sql = "UPDATE shipment SET carrierbookeddate=\'$booked_date\' WHERE shipmentid=$shipmentid";
    
    if ( $debug_sql ) {
        debug("updateBookedDate(): sql = $sql");
    }

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    if ( $sth->finish() ) {
        debug("updateBookedDate(): updated the carrier booked date in the database");
        return(1);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : timefmt
# DESCRIPTION : Returns the current date/time suitable for the MySQL database
#             : datetime field type.
# ARGUMENTS   : None
# RETURNS     : string(time)
# STATUS      : Stable
# NOTES       : Should be in the format YYYY-MM-DD HH:MM:SS
# -----------------------------------------------------------------------------
sub timefmt {
    my $time = time();;

    my $fmt = "%Y-%m-%d %H:%M:%S";
    
    my $fmt_time = strftime $fmt, localtime($time);
    return($fmt_time);

} 


# -----------------------------------------------------------------------------
# NAME        : addPickupStatus
# DESCRIPTION : Add an awaiting pickup status associated with a shipment
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub addPickupStatus {
    my $shipmentid = shift;
    
    unless($shipmentid) {
        print "You have to give me a shipment id!\n";
        return(0);
    }
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }

    debug("addPickupStatus(): shipmentid given to me is $shipmentid");
    
    my $statusdetails = "AWAITING PICKUP";
    my $statuscode = 2;
    my $xmlstore = "$queue_root/$queue_processed/$shipmentid.xml";
    my $statustime = timefmt();
    
    $sql = "insert into shipmentstatus values (\'\', \'$shipmentid\', \'$statusdetails\', $statuscode, \'$xmlstore\', \'$statustime\')";
    
    if ( $debug_sql ) {
        debug("addPickupStatus(): sql = $sql");
    }

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    if ( $sth->finish() ) {
        debug("addPickupStatus(): added AWAITING PICKUP record for shipment $shipmentid");
        return(1);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : getAccessorials
# DESCRIPTION : Returns an array list of the accessorials for a shipment
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or array(accessorials)
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub getAccessorials {
    my $shipmentid = shift;
    my @accessorials;
    my $numRows;
    
    unless($shipmentid) {
        print "You have to give me a shipment id!\n";
        return(0);
    }
    
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }
    
    $sql = "SELECT \
                accessorials.name AS accname \
            FROM \
                accessorials, \
                shipmentaccessorials \
            WHERE \
                accessorials.assid=shipmentaccessorials.assid \
            AND \
                shipmentaccessorials.shipmentid=$shipmentid";


    if ( $debug_sql ) {
        debug("getAccessorials(): sql = $sql");
    }

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    $numRows = $sth->rows;
    debug("NUM ACCESSORIAL ROWS: $numRows");

    # If there's none there, simply return an empty list    
    if ( $numRows == 0 ) {
        @accessorials = "";
        return(@accessorials);
    }

    # Iterate through the result set and push them onto the stack
    while ($ref = $sth->fetchrow_hashref()) {
        $data = "<accessorial>" . $ref->{'accname'} . "</accessorial>\n";
        push(@accessorials, $data);
    }


    # Close and exit
    if ( $sth->finish() ) {
        debug("getAccessorials(): accessorials for shipment are: @accessorials");
        return(@accessorials);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }

}



# -----------------------------------------------------------------------------
# NAME        : getShipmentPickuDate
# DESCRIPTION : Returns the pickupdate for a given shipment
# ARGUMENTS   : string(shipmentid)
# RETURNS     : 0 or string(pickupdate)
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub getShipmentPickupDate {
    my $shipmentid = shift;
    my $pickupdate;
    my $numRows;
    
    unless($shipmentid) {
        print "You have to give me a shipment id!\n";
        return(0);
    }
    
    
    unless($dbh) {
        print "Connect to the database first!\n";
        return(0);
    }
    
    $sql = "SELECT pickupdate FROM shipment WHERE shipmentid=$shipmentid";

    if ( $debug_sql ) {
        debug("getShipmentPickupDate(): sql = $sql");
    }

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }

    $numRows = $sth->rows;
    debug("NUM SHIPMENT PICKUPUPDATE ROWS: $numRows");

    # If there's none there, simply return an empty list    
    if ( $numRows == 0 ) {
        debug("getShipmentPickupDate(): no rows for shipment $shipmentid? Is it valid?");
        return(0);
    }

    # Iterate through the result set and push them onto the stack
    while ($ref = $sth->fetchrow_hashref()) {
        $pickupdate = $ref->{'pickupdate'};
    }


    # Close and exit
    if ( $sth->finish() ) {
        debug("getShipmentPickupDate(): pickupdate for shipment $shipmentid is: $pickupdate");
        return($pickupdate);
    } else {
        print "Statement finish failed ($!)\n";
        return(0);
    }

}


# -----------------------------------------------------------------------------
# NAME        : moveFailed
# DESCRIPTION : Move a BOL to a failed location
# ARGUMENTS   : string(bol), string(dir)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
# Moves a BOL to a failed location
sub moveFailed {
    my $bol = shift;
    my $dir = shift;
    
    unless($bol) {
        print "You have to give me a bol file to move!\n";
        return(0);
    }
    
    unless($dir) {
        print "You have to give me a dir to move it to!\n";
        return(0);
    }

    # Simply rename the file, return 0 if it fails    
    if ( rename("$queue_root/$queue_out/$bol", "$dir/$bol") ) {
        print "Moved $bol to $dir\n";
        return(1);
    } else {
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : transmitBOL
# DESCRIPTION : All the work necessary to send a BOL to a VAN
# ARGUMENTS   : string(bol)
# RETURNS     : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
# Wraps up all the work necessary to send a BOL to the van
sub transmitBOL {
    my $bol = shift;
    
    unless ($bol) {
        print "You have to give me a BOL to send!\n";
        return(0);
    }
    
    # Get the shipmentid out of the bol file name
    my ($shipmentid, $ext) = split('\.', $bol);\
    debug("transmitBOL(): shipmentid: $shipmentid");
    
    # Transmit the doc to the VAN
    if ( ftpSend($bol, $van_name) ) {     
     
        # Update the BOL generated field in the database
        # 204sent field in the shipment table
        if ( $update_sentflag ) {
            if ( updateSentflag($shipmentid) ) {
                debug("204sent flag updated");
            } else {
                print "Update of 204sent flag failed ($!)\n";
            }
        } else {
            debug("Not updating sent flag in database");
        }

        # Update the date in which the bol was transmitted to the VAN.
        # 204sentdate field in the shipment table
        if ( $update_sentdate ) {
            if ( updateSentDate($shipmentid) ) {
                debug("204sentdate updated");
            } else {
                print "Update of 204sentdate update failed ($!)\n";
            }
        } else {
            debug("Not updating 204sentdate in database");
        }
     
        # Add an awaiting pickup status to the shipmentstatus table
        if ( ! addPickupStatus($shipmentid) ) {
            print "Error while setting pickup status!\n";
            return(0);
        }
        
        # Add the carrierbookeddate
        if ( ! updateBookedDate($shipmentid) ) {
            print "Error while setting the booked data\n";
            return(0);
        }
        
        # Update the carrierbooked flag
        if ( ! update_carrierbooked($shipmentid) ) {
            print "Carrier booked update failed ($!)\n";
            return(0);
        }
 
    } else {
        # The transmittal failed
        print "FTP failed, moving to failed directory\n";
                
        # Move the file to the fail queue
        if ( moveFailed($bol, "$queue_root/$queue_fail") ) {
            print "BOL: $bol moved to failed queue\n";
        } else {
            print "BOL move failed!\n";
            return(0);
        }
    }

    return(1);
}


# Get the carrierid for a given shipment
sub getCarrierForShipment {
    my $shipmentid = shift;

    debug("GETHERING CARRIERID FOR SHIPMENT $shipmentid");
    $sql = "SELECT carrierid FROM shipment WHERE shipmentid=$shipmentid";
    
    if ( $debug_sql ) {
        debug("getCarrierForShipment(): sql = $sql");
    }
    
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    $numRows = $sth->rows;
    debug("NUM SHIPMENT ROWS: $numRows");

    # If there's no rows, something happened
    if ( $numRows == 0 ) {
        print "0 rows returned from query, something happened!\n";
        return(0);
    }
    
    # There should only only be a single row
    if ( $numRows > 1 ) {
        print "There's more than 1 row, bad stuff happened!\n";
        return(0);
    }

    # Iterate through the result set and assign the variables
    while ($ref = $sth->fetchrow_hashref()) {
        $carrierid  = $ref->{'carrierid'};
    }

    if ( $carrierid ne "" ) {
        return($carrierid);
    } else {
        return(0);
    }


}


# Determine if 204's are enabled for a given carrier
sub getCarrier204Status {
    my $carrierid = shift;
    my $carrier204enabled;

    unless($carrierid) {
        syntax("getCarrier204Status(): you have to give me a carrierid to check!");
        return(0);
    }


    debug("getCarrier204Status(): GETHERING 204 STATUS FOR CARRIER $carrierid");
    $sql = "SELECT 204enabled FROM carriers WHERE carrierid=$carrierid";
    
    if ( $debug_sql ) {
        debug("getCarrier204Status(): sql = $sql");
    }
    
    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if ( ! $sth ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    # Execute the statement, print the error and return false if it fails
    if ( ! $sth->execute() ) {
        print "Error: " . $dbh->errstr . "\n";
        return(0);
    }
    
    $numRows = $sth->rows;
    debug("NUM CARRIER ROWS: $numRows");

    # If there's no rows, something happened
    if ( $numRows == 0 ) {
        print "0 rows returned from query, something happened!\n";
        return(0);
    }
    
    # There should only only be a single row
    if ( $numRows > 1 ) {
        print "There's more than 1 row, bad stuff happened!\n";
        return(0);
    }

    # Iterate through the result set and assign the variables
    while ($ref = $sth->fetchrow_hashref()) {
        $carrier204enabled  = $ref->{'204enabled'};
    }

    if ( $carrier204enabled ne "" ) {
        return($carrier204enabled);
    } else {
        return(0);
    }

}