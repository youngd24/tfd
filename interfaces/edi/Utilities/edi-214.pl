#!/usr/local/bin/perl
# ===================================================================
#
# edi-214.pl
#
# Script to process an XML EDI 214 file
#
# $Id: edi-214.pl,v 1.5 2003/02/05 19:00:21 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# ===================================================================
#
# Usage:
#
#     edi-214.pl <file> --debug
#
# ===================================================================
#
# ChangeLog:
#
# $Log: edi-214.pl,v $
# Revision 1.5  2003/02/05 19:00:21  youngd
#   * Added database section.
#
# Revision 1.4  2003/01/02 20:26:44  youngd
#   * Added the name of the script to the debug output.
#
# Revision 1.3  2003/01/02 20:11:59  youngd
#   * Changed the q() to qq().
#
# Revision 1.2  2003/01/02 19:07:42  youngd
#   * Changed headers to reflect the name change.
#
# Revision 1.1  2003/01/02 19:06:54  youngd
#   * Renamed to edi-214.pl and adjusted code so it does just 214 processing.
#
# Revision 1.14  2002/10/22 20:34:46  youngd
#   * fixed problem where status messages that contained quotes (" or ') would
#     cause the sql insert to fail. Changed the insert to use the perl q()
#     function.
#
# Revision 1.13  2002/10/08 22:56:43  youngd
#   * Added comments on the code I just added for the weight checks and
#     such.
#
# Revision 1.12  2002/10/08 22:54:07  youngd
#   * Added the retrieval of the quote id for the shipment statused.
#   * Took that quoteid and perform a lookup of the current weight.
#   * Added the sending of an email and logging if the received status
#     weight is different than what we have on file in the original
#     quote.
#
# Revision 1.11  2002/10/08 22:09:55  youngd
#   * Added product weight parsing code.
#
# Revision 1.10  2002/08/01 18:29:31  youngd
#    * update
#
# Revision 1.9  2002/08/01 18:23:30  youngd
#    * Added dbh quote around bol
#
# Revision 1.8  2002/08/01 18:22:00  youngd
#    * Added dbh quote around bol
#
# Revision 1.7  2002/08/01 18:18:18  youngd
#    * Complete pro update
#
# Revision 1.6  2002/08/01 18:13:44  youngd
#    * Added update or carrierpro in shipment table during update process
#
# Revision 1.5  2002/07/31 21:01:21  youngd
#    * Updated to use local perl
#
# Revision 1.4  2002/07/31 21:00:02  youngd
#    * Removed Config::General usage
#
# Revision 1.3  2002/07/31 19:26:31  youngd
#    * converted
#
# Revision 1.2  2002/07/31 19:25:21  youngd
#    * Version ready to test
#
# Revision 1.1  2002/07/31 19:18:55  youngd
#    * changed from cgi
#
# Revision 1.4  2002/07/31 18:13:48  youngd
#    * Added header sections
#
# ===================================================================

$name = "edi-214.pl";

$cvsid = '$Id: edi-214.pl,v 1.5 2003/02/05 19:00:21 youngd Exp $';
@cvsinfo = split(' ', $cvsid);
$version = $cvsinfo[2];

BEGIN {
    use Cwd;
    use XML::DOM;
    use Getopt::Long;
    use DBI;
	use Mail::Send;
    use Error qw(:try);
}


# -------------------------------------------------------------------
#                       V A R I A B L E S
# -------------------------------------------------------------------
$db_host      = "localhost";
$db_name      = "digiship";
$db_user      = "php";
$db_pass      = "password";
$db_port      = "3306";

$debug        = 0;




# Parse the command Line
$file = $ARGV[0];


# Get the command line options
GetOptions ('debug' => \$debug);

if ( $debug ) {
    print "$name: Debug enabled\n";
}

# -------------------------------------------------------------------
#                       S C R I P T   M A I N
# -------------------------------------------------------------------
if ($file) {


    # Open the database connection
    $dsn = "DBI:mysql:database=$db_name:host=$db_host:$port=$db_port";
    $dbh = DBI->connect($dsn,$db_user,$db_pass);

    unless($dbh) {
        print "$name: Failed to connect to database";
        exit(0);
    }


    foreach $file ($ARGV[0]) {


		# ---------------------------------------------------------------------
		# General setup
		# ---------------------------------------------------------------------

        $parser        = new XML::DOM::Parser;
        $doc           = $parser->parsefile("$file");

        $transmissions = $doc->getElementsByTagName("transmission");
        $numtrans      = $transmissions->getLength();
        $shipments     = $doc->getElementsByTagName("shipment");
        $numshipments  = $shipments->getLength();
        $statuses      = $doc->getElementsByTagName("status");
        $numstatuses   = $statuses->getLength();

        print "$name: $file has $numshipments shipment(s) with $numstatuses status messages\n";
        print "\n";

	
		# ---------------------------------------------------------------------
		# Basic transmission information
		# ---------------------------------------------------------------------
        print "\n";
        print "TRANSMISSION INFO:\n";
        print "------------------\n";
        $transmission = $transmissions->item(0);
        for $t ($transmission->getChildNodes()) {
            if ($t->getNodeName() =~ "sender") {
                $r = $t->getFirstChild();
                print "Sender: " . $r->getNodeValue() . "\n";
            }
            if ($t->getNodeName() =~ "receiver") {
                $r = $t->getFirstChild();
                print "Receiver: " . $r->getNodeValue() . "\n";
            }
            if ($t->getNodeName() =~ "date") {
                $r = $t->getFirstChild();
                print "Date: " . $r->getNodeValue() . "\n";
            }
            if ($t->getNodeName() =~ "time") {
                $r = $t->getFirstChild();
                print "Time: " . $r->getNodeValue() . "\n";
            }
        }
        print "\n";



		# There can be multiple shipments in a single file..
        for ($i=0; $i<$numshipments; $i++ ) {

			# Use the current shipment
            $shipment = $shipments->item($i);

			# -----------------------------------------------------------------
			# Gather basic shipment information
			# -----------------------------------------------------------------
            print "\n";
            print "SHIPMENT INFO:\n";
            print "--------------\n";
            for $kid ($shipment->getChildNodes()) {
                if ($kid->getNodeName() =~ 'header') {
                    for $d ($kid->getChildNodes()) {
                        if ($d->getNodeName() =~ 'pronumber') {
                            $a = $d->getFirstChild();
                            $pro = $a->getNodeValue;
                            print "PRO: $pro" . "\n";
                        }
                        if ($d->getNodeName() =~ 'bolnumber') {
                            $a = $d->getFirstChild();
                            $bol = $a->getNodeValue;
                            print "BOL: $bol" . "\n";
                        }
                    }
                }

				# -------------------------------------------------------------
				# Process the product information
				# -------------------------------------------------------------
				if ( $kid->getNodeName() =~ 'product') {
                    print "\n";
                    print "PRODUCT INFO:\n";
                    print "-------------\n";
                    for $d ($kid->getChildNodes()) {

                        $nodename = $d->getNodeName();
                        $a = $d->getFirstChild();

                        SWITCH: for ($nodename) {
                            /^weightqualcode$/ && do {
                                $weightqualcode = $a->getNodeValue();
                                print "WEIGHTQUALCODE: $weightqualcode" . "\n";
                                last SWITCH;
                            };
                            /^weightunitcode$/ && do {
                                $weightunitcode = $a->getNodeValue();
                                print "WEIGHTUNITCODE: $weightunitcode" . "\n";
                                last SWITCH;
                            };
		                  /^weight$/ && do {
                                $weight = $a->getNodeValue();
                                print "WEIGHT: $weight" . "\n";
                                last SWITCH;
                            };
                        }
                    }
				}


				# -------------------------------------------------------------
				# Process all shipment status messages
				# -------------------------------------------------------------
                elsif ( $kid->getNodeName() =~ 'status') {
                    print "\n";
                    print "STATUS MESSAGE:\n";
                    print "---------------\n";
                    for $d ($kid->getChildNodes()) {

                        $nodename = $d->getNodeName();
                        $a = $d->getFirstChild();

                        SWITCH: for ($nodename) {
                            /^code$/ && do {
                                $code = $a->getNodeValue();
                                print "CODE: $code" . "\n";
                                last SWITCH;
                            };
                            /^reason$/ && do {
                                $reason = $a->getNodeValue();
                                print "REASON: $reason" . "\n";
                                last SWITCH;
                            };
                            /^date$/ && do {
                                $date = $a->getNodeValue();
                                print "DATE: $date" . "\n";
                                last SWITCH;
                            };
                            /^time$/ && do {
                                $time = $a->getNodeValue();
                                print "TIME: $time" . "\n";
                                last SWITCH;
                            };
                            /^timezone$/ && do {
                                $timezone = $a->getNodeValue();
                                print "TIMEZONE: $timezone" . "\n";
                                last SWITCH;
                            };
                            /^city$/ && do {
                                $city = $a->getNodeValue();
                                print "CITY: $city" . "\n";
                                last SWITCH;
                            };
                            /^state$/ && do {
                                $state = $a->getNodeValue();
                                print "STATE: $state" . "\n";
                                last SWITCH;
                            };
                            /^equipmentnum$/ && do {
                                $equipmentnum = $a->getNodeValue();
                                print "EQUIPMENTNUM: $equipmentnum" . "\n";
                                last SWITCH;
                            };
                        }
                    }



					# ---------------------------------------------------------
					# STATUS ADDITION
					# ---------------------------------------------------------

                    # Insert the received / parsed status into the database
                    $sql = "INSERT INTO shipmentstatus VALUES ('', '$bol', '$code $city $state', '$reason', '$file', '$date $time')\n";
                    
                    if ( $debug ) {
                        print $sql . "\n";
                    }

                    $sth = $dbh->prepare($sql);
                    if (!$sth) {
                        print "Error: " . $dbh->errstr . "\n";
                    }
                    if (!$sth->execute()) {
                        print "Error: " . $dbh->errstr . "\n";
                    }
                    $sth->finish();


					# ---------------------------------------------------------
					# Checks and balances
					# ---------------------------------------------------------
					print "\n";
					print "STARTING CHECKS\n";
					print "---------------\n";

					# Here we check various parts of the shipment to see if something has changed since
					# we either booked the order or since the last status.

					# ---------------------------------------------------------
					# Check the shipment weight and make sure nothing has
					# changed. Have to start with the quoteid to get the weight
					# ---------------------------------------------------------

					# Go retrieve the quoteid via the bol number
					$sql = "SELECT quoteid FROM shipment WHERE shipmentid=$bol";
					$sth = $dbh->prepare($sql);
					$quoteid = $dbh->selectrow_array($sth);
					
					# If we got the quoteid...
					if ( $quoteid ) {

						print "GOT QUOTEID: $quoteid\n";


						# Now we can go get the original weight from the rating process
						$sql = "SELECT weight FROM quotes WHERE quoteid=$quoteid";
						$sth = $dbh->prepare($sql);
						$curweight = $dbh->selectrow_array($sth);

						# If we got that value...
						if ( $curweight ) {
							print "GOT CURRENT WEIGHT: $curweight\n";

							# If the weight we have on file is not the same as the one the status
							# message contained, log it and send an email message.
							if ( $curweight != $weight ) {
								print "EXCEPTION: Received a status with a weight that's not the same as what we have\n";
								$msg = new Mail::Send;
								$msg->to('youngd');
								$msg->subject("Weight change on BOL $bol");

								$fh = $msg->open;
								print $fh "Received an EDI 214 status for BOL $bol with a weight of $weight.\n The weight we have on file is $curweight.\n";

								$fh->close;

							} else {
								debug("Weights check out OK");
							}
						} else {
							print "ERROR: Failed to retrieve current weight for shipment $bol (quote $quoteid)\n";
						}
					} else {
						print "ERROR: Failed to retrieve quote for shipment $bol\n";
					}



					# ---------------------------------------------------------
					# PRO NUMBER
					# ---------------------------------------------------------

                    # Update the received PRO number based on the received info. This is especially
					# important on the very first 214 (pickup).
                        $sql = "UPDATE shipment
                                SET carrierpro=\'$pro\'
                                WHERE shipmentid=\'$bol\'\n";

                        $sth = $dbh->prepare($sql);
                        if (!$sth) {
                            print "Error: " . $dbh->errstr . "\n";
                        }
                        if (!$sth->execute()) {
                            print "Error: " . $dbh->errstr . "\n";
                        }
                        $sth->finish();
                        if ( $debug ) {
                            print $sql . "\n";
                        }

                }
            }
        }

        # Move file to processed directory
        system("mv $file /edi/processed");

        print "Moved $file to /edi/processed\n";


    } # end foreach $q->param()


    # Close the database connection
    print "Disconnected from database\n";
    $dbh->disconnect();
    exit(0);

} else {

    print "Usage: $0 <options>\n";
    exit(0);
}












sub debug {
    my $message = shift;
    if ( $main::debug ) {
        print "DEBUG: $message\n";
    }
    return(1);
}
