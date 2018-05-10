# ==================================================================
#
# RateServer.pl
#
# Perl :-) script that provides shipment rates
#
# Contents Copyright (c) 2000, Digiship Corp.
# 
# $Id: RateServer.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Darren Young
# youngd@digiship.com
#
# ==================================================================
#
# Description
#
# This program provides a network interface (socket) to other programs
# that wish to obtain shipment prices. This particular program does not
# do the price calculation, but accesses a COM object that in turn 
# obtains prices from our Czar pricing DLL.
#
# This program has been tested on Windows NT 4.0 and Windows 2000
# advanced server. It should run on any other flavor of windows that
# Perl runs on.
#
# This services runs on tcp port 4979
#
# ==================================================================
#
# Usage:
#
# To start it, simply type <location_of_perl> RateServer.PL
# Where <location_of_perl> is the path to perl.exe. In our current
# environment(s), Perl is located in c:\perl. So, we would launch this
# with c:\perl\bin\perl.exe RateServer.pl
#
# Log files are places in c:\ and are rotated on a daily basis
#
# Once started, the program will listen() for connections on port 4979
# and respond to them. 
#
# ==================================================================
#
# Changes:
#
# $Log: RateServer.pl,v $
# Revision 1.1.1.1  2002/07/13 04:30:35  youngd
# initial import
#
# Revision 1.2  2002/01/02 02:09:17  youngd
# Converted to UNIX format
#
# Revision 1.1.1.1  2001/12/15 18:17:56  youngd
# new import
#
# Revision 1.27  2001/01/03 21:08:12  youngd
# Added shutdownServer method
# Added use of ServGd32Initialize method
#
# Revision 1.26  2001/01/03 19:59:12  youngd
# Cosmetic changes
#
# Revision 1.25  2001/01/03 15:34:14  youngd
# Added examples in the help section
# Added help on transit time
#
# Revision 1.24  2001/01/02 22:48:42  youngd
# Added explicit type conversion of data in the getBasePrice method to call as a string
#
# Revision 1.23  2000/12/29 20:08:14  youngd
# Modified getBasePrice call to the OLE object not to call the method
# parameters as int()'s. They are really strings. This was done as in
# some cases zip codes can contain a 0 in the first position. As an
# example: 06323. When converted to an int(), it becomes 6323 which is
# not a valid zip code.
#
# We went ahead and changed all the calls to be strings, both in and out
# so we avoid this with the weight and class parameters. You never know...
#
# Revision 1.22  2000/12/28 20:57:24  youngd
# Added different version indicator in the first server ready message
#
# Revision 1.21  2000/12/27 22:49:33  youngd
# Added getTransitTime method
# Added TRANSTIME protocol command
# Needs to be tested against a client
#
# Revision 1.20  2000/12/26 16:27:27  youngd
# Increased verbosity
#
# Revision 1.19  2000/12/21 17:01:38  deploy
# Removed some annoying header # signs
#
# Revision 1.18  2000/12/21 17:01:08  deploy
# Added 1.15 log header and information
# Necessary we we remember were the 1.8 break took place
#
# Revision 1.17  2000/12/21 16:59:49  deploy
# Added comments
#
# Revision 1.16  2000/12/21 16:58:52  deploy
# Added Log header
#
# Revision 1.15  2000.12.21 16:56:50  youngd
# Reverted to version 1.8
# Added race condition check for 0 length message
# Will add 1.14 functionality back later
#
# ==================================================================
#
# Bugs:
#
# ==================================================================
#
# Todo:
#
# Add an IP address access control list fed from a file
# Add command line parameters to control key variables
# Add administrative shutdown command and crypt(password) protect it
#
# ==================================================================

# Bring in the modules we need
use IO::Socket;                            # Network socket interface
use IO::Select;	                           # File handle select interface
use Sys::Hostname;                         # Local hostname
use Win32::OLE;	                           # Windows OLE (COM) interface
use Config;

# CVS variables
my $cvsid = '$Id: RateServer.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $';
my ($cid,$cvsName,$prog_version,$cvsDate,$cvsTime,$cvsAuthor,$cvsState) = split(' ',$cvsid);

# Program parameters
my $timeout = 300;                         # Client TCP timeout
my $max_clients = 10;                      # Max number of simultaneous connections
my $reuse = 1;                             # Reuse the socket after close or not
my $port = 4979;                           # TCP port to listen() in
my $logfile = "RateServer.log";            # Server side log file
my $statfile = "RateServer.stats";         # File to store stats to in the current dir
my $prot_version = "1.1";                  # Version of the rating protocol
                                           # 1.0 was the base version with no transtime
                                           # 1.1 has the addition of the transtime
my $lifetime_connects = 0;                 # Total # of connects() while running
my $total_bytes_sent = 0;                  # Total bytes sent to all clients
my $total_bytes_rcvd = 0;                  # Total bytes received from all clients
my $total_rates = 0;                       # Total # of times we rated a shipment
my $handle = STDOUT;                       # Where logmsg output goes
my $hostname = hostname;                   # Our local hostname()
my $startDate = "";                        # The time the server was started
my $nHandle = "";                          # The handle for the GetTransitDays call

# Grab the signal handlers
$SIG{INT} = \&shutdownServer;

# Create an instance of the Digiship rate object
$rateObject = Win32::OLE->new('DigishipRate.RateShipment') || die "Unable to create new DigishipRate object\n";

# Create an instance of the Digiship Carrier Transit Time object
$transObject = Win32::OLE->new('DigiCarrier.CarrierTransit') || die "Unable to create new DigiCarrier.CarrierTransit object\n";

# Get the handle for the GetTransitDays call
$nHandle = $transObject->ServGd32Initialize() || warn $! . "\n";

# Create a new socket
$new_client = IO::Socket::INET->new( Proto     => "tcp", 
                                     LocalPort => $port,
                                     Listen    => $max_clients,
                                     Reuse     => $reuse,
                                     Timeout   => $timeout );

# Create a new handler for the socket
# See IO::Select for more details (basically, an OO interface to select())
$sel = IO::Select->new($new_client);

$startDate = localtime(time());
logmsg("INFO:\nDigiship RateServer version $prog_version ($cvsDate $cvsTime $cvsAuthor) ready and waiting...");

# Start the loop based on every readable client
while (@ready = $sel->can_read) {

	foreach $client (@ready) {

		if ($client == $new_client) {
			# If it's a new conection, do some setup
			$add = $client->accept;
			$lifetime_connects = $lifetime_connects + 1;
			$add->autoflush(1);
			$count = $sel->count;
			logmsg("INFO: Connect from " . $add->peerhost);
			logmsg("INFO: There are $count active connections");
			logmsg("INFO: Lifetime connects: $lifetime_connects");
			$sendmsg = "READY\n";
			print $add $sendmsg;
			$total_bytes_sent = $total_bytes_sent + length($sendmsg);
			$sel->add($add);

		} else {
			# Otherwise, it's an existing client
			$message = <$client>;
			my $client_ip = $client->peerhost;
			chop($message);
			chop($message);
			$msglen = length($message);
			$total_bytes_rcvd = $total_bytes_rcvd + length($message);
			logmsg("RECV: Got $msglen byte raw message: $message from $client_ip");
			# Added to avoid race condition
			if ( $msglen == 0 ) {
				logmsg("ERROR: Got 0 byte message, aborting client");
				$sel->remove($client);
				$client->close;
			}

			# Parse the received message
			if ($message eq "QUIT") {
				sendclient("BYE\n", $client);
				$sel->remove($client);
				$client->close;

			} elsif ( substr($message, 0, 9 ) eq "TRANSTIME" ) {
				logmsg("INFO: doing transtime");
				$data = substr($message, 10, $msglen);
				if ( length($data) < 1 ) {
					sendclient("ERROR: INVALID TRANSIT TIME USAGE\n", $client);
				}
				logmsg("RECV: Transtime param data: $data");
				( $tscac, $ttype, $tsrczip, $tdstzip, $treqlen ) = split("&", $data);
				( $garbage, $scac ) = split("=", $tscac);
				( $garbage, $type ) = split("=", $ttype);
				( $garbage, $srczip ) = split("=", $tsrczip);
				( $garbage, $dstzip ) = split("=", $tdstzip);
				( $garbage, $reqlen ) = split("=", $treqlen);
				$checksum = length($data) + 2;
				logmsg("CHECKSUM: Checksum is $checksum");
				logmsg("CHECKSUM: Request length is $reqlen");
				if ( $checksum != $reqlen ) {
					logmsg("CHECKSUM: Checksum failed");
					# CHECKSUM ERROR:14 -> The 14 is the length of the words CHECKSUM ERROR
					# The client has to have that on the way back...
					sendclient("CHECKSUM ERROR:14\n", $client);
				} else {
					logmsg("CHECKSUM: Checksum passed");
					logmsg("TIME: SCAC: $scac");
					logmsg("TIME: Type: $type");
					logmsg("TIME: Srczip: $srczip");
					logmsg("TIME: Dstzip: $dstzip");

					# Go get the transit time
					$result = getTransitTime($scac, $type, $srczip, $dstzip );
				
					# data_len is the checksum sent back to the client for them to check
					$data_len = length($result);
					if ( $result eq "" ) {
						logmsg("TIME: Error getting transit time");
						sendclient("ERROR GETTING TRANSTIME\n", $client);
					} else {
						logmsg("TIME: Got transit time of $result from getTransitTime()");
						$return_string = "$result:" . $data_len . "\n";
						logmsg("INFO: Sending client: $return_string");
						sendclient("$return_string", $client);
					}
				} # End checksum & client send of the transit time
 
			} elsif ( substr($message, 0, 9 ) eq "BASEPRICE" ) {
				# BASEPRICE Command Format:
				# BASEPRICE?SRCZIP=60601&DSTZIP=45345&WEIGHT=10000&CLASS=50

				logmsg ("INFO: Doing baseprice");
				$data = substr($message, 10, $msglen);
				if ( length($data) < 1 ) {
					sendclient("ERROR: INVALID BASE PRICE USAGE\n", $client);
				}
				logmsg("RECV: Baseprice param data: $data");
				( $tsrczip, $tdstzip, $tweight, $tclass, $treqlen ) = split("&", $data);
				( $garbage, $srczip ) = split("=", $tsrczip);
				( $garbage, $dstzip ) = split("=", $tdstzip);
				( $garbage, $weight ) = split("=", $tweight);
				( $garbage, $class ) = split("=", $tclass);
				( $garbage, $reqlen ) = split("=", $treqlen);

				# Process the checksum and the request if it's good
				$checksum = length($data) + 2;
				logmsg("CHECKSUM: Checksum is $checksum");
				logmsg("CHECKSUM: Request length is $reqlen");
				if ( $checksum != $reqlen ) {
					logmsg("CHECKSUM: Checksum failed");
					# CHECKSUM ERROR:14 -> The 14 is the length of the words CHECKSUM ERROR
					# The client has to have that on the way back...
					sendclient("CHECKSUM ERROR:14\n", $client);
				} else {
					logmsg("CHECKSUM: Checksum passed");
					logmsg("RATE: Origin zip: $srczip");
					logmsg("RATE: Destination zip: $dstzip");
					logmsg("RATE: Weight: $weight");
					logmsg("RATE: Class: $class");

					# Go get the price
					$result = getBasePrice($srczip, $dstzip, $weight, $class );
				
					# data_len is the checksum sent back to the client for them to check
					$data_len = length($result);
					if ( $result eq "" ) {
						logmsg("RATE: Error getting base price");
						sendclient("ERROR GETTING BASEPRICE\n", $client);
					} else {
						logmsg("RATE: Got base price of $result from getBasePrice()");
						$return_string = "$result:" . $data_len . "\n";
						logmsg("INFO: Sending client: $return_string");
						sendclient("$return_string", $client);
					}
				} # End checksum & client send

			} elsif ( $message eq "HELP" ) {
				logmsg("CMD: Received request for HELP");
				$sendmsg =            "BASEPRICE(PARAMS) - RETURNS SHIPMENT BASE PRICE\n";
				$sendmsg = $sendmsg . "  Example: BASEPRICE?SRCZIP=60546&DSTZIP=45345&WEIGHT=10000&CLASS=55&LEN=55\n";
				$sendmsg = $sendmsg . "TRANSTIME(PARAMS) - RETURNS SHIPMENT TRANSIT TIME\n";
				$sendmsg = $sendmsg . "  Example: TRANSTIME?SCAC=RDWY&TYPE=LTL&SRCZIP=60546&DSTZIP=45345&LEN=53\n";
				$sendmsg = $sendmsg . "HELP              - THIS SCREEN\n";
				$sendmsg = $sendmsg . "INFO              - SERVER INFORMATION\n";
				$sendmsg = $sendmsg . "VERSION           - PROGRAM AND PROTOCOL INFO\n";
				$sendmsg = $sendmsg . "SAVESTATS         - SAVE STATS TO FILE\n";
				$sendmsg = $sendmsg . "QUIT              - END CONNECTION\n";
				sendclient($sendmsg, $client);

			} elsif ( $message eq "VERSION" ) {
				logmsg("CMD: Received request for VERSION");
				sendclient("PROTOCOL VERSION $prot_version PROGRAM VERSION $prog_version\n", $client);

			} elsif ( $message eq "INFO" ) {
				logmsg("CMD: Received request for INFO");
				$sendmsg = "SERVER INFORMATION:\n";
				$sendmsg = $sendmsg . "Hostname: $hostname\n";
				$sendmsg = $sendmsg . "Started on: $startDate\n";
				$sendmsg = $sendmsg . "Active Connections: $count\n";
				$sendmsg = $sendmsg . "Lifetime Connections: $lifetime_connects\n";
				$sendmsg = $sendmsg . "Total bytes sent: $total_bytes_sent\n";
				$sendmsg = $sendmsg . "Total bytes rcvd: $total_bytes_rcvd\n";
				$sendmsg = $sendmsg . "Total rates: $total_rates\n";
				sendclient($sendmsg, $client);

			} elsif ( $message eq "SAVESTATS" ) {
				logmsg("INFO: Request to save stats");
				if ( ! saveStats() ) {
					logmsg("ERROR: Could not save stats");
					sendclient("ERROR SAVING STATS\n", $client);
				} else {
					logmsg("INFO: Stats saved");
					sendclient("STATS SAVED\n", $client);
				}

			} else {
				# We got something else
				sendclient("BAD COMMAND ($message)\n", $client);

			} # End message parse
			logmsg ("INFO: Done parsing message");
		} # End existing client 
		logmsg ("INFO: Done existing client");
	} # End new_client
	logmsg ("INFO: Done new_client");
} # End while
logmsg ("INFO: Exiting");
exit(0);


# -----------------------------------------------------------------------------
# -----------------------------------------------------------------------------
#
sub getTransitTime {
	my ( $scac, $type, $srczip, $dstzip ) = @_;
	my $transResult;

	$transResult = $transObject->GetTransitDays($nHandle,$scac,$type,int($srczip),int($dstzip));
	logmsg("METHOD: getTransitTime: Got result of $transResult");
	if ( $transResult <= 0 ) {
		logmsg("METHOD: Error obtaining transit time");
		return(0);
	} else {
		return($transResult);
	}
}


# -----------------------------------------------------------------------------
# Name: getBasePrice
# Description: Retrieves a base price from CzarLite
# Needs: string srczip, string dstzip, string class, string weight
# Returns: string basePrice
# -----------------------------------------------------------------------------
#
sub getBasePrice {
	
	my ( $srczip, $dstzip, $weight, $class ) = @_;

	$tsrczip = scalar($srczip);
	$tdstzip = scalar($dstzip);
	$tweight = scalar($weight);
	$tclass = scalar($class);

	my $rateResult;
	my $tmpString;
	$total_rates = $total_rates + 1;

	# Rate the shipment from the OLE object
	# OLE Method: RateShipment
	# Method parameters:
	#			string(srczip)
	#			string(dstzip)
	#			string(class)
	#			string(weight)
	# Returns string(baseprice)
	#
	# See CVS/rating/COM for the source code
	$rateResult = $rateObject->RateShipment($tsrczip, $tdstzip, $tclass, $tweight);
	logmsg("METHOD: getBasePrice: Got _first_ result of $rateResult");

	# See if there's a decimal in the result
	# If there is, simply return it
	# If not, append a .00 on and return that
	# This is the workaround for the fact that the OLE DLL returns it that way
	if ( $rateResult =~ '\.' ) {
		logmsg("METHOD: getBasePrice: The result has a . in it, returning it.");
		return $rateResult;
	} else {
		logmsg("METHOD: getBasePrice: The result has no . in it, appending and returning it.");
		$tmpString = $rateResult . ".00";
		logmsg("METHOD: getBasePrice: Changed result to $tmpString");
		$tmpString_len = length($tmpString);
		logmsg("METHOD: getBasePrice: length of new result is $tmpString_len");
		return $tmpString;
	}
}

# -----------------------------------------------------------------------------
# Name: logmsg
# Description: Sends a properly formatted message to a log handler
# Needs: string messge
# Returns: result code (0 or 1)
# -----------------------------------------------------------------------------
#
sub logmsg { 
	my ( $logmsg ) = @_;
	my $datetime = localtime(time);

	print $handle "$datetime $hostname $logmsg\n";
	return;
}


# -----------------------------------------------------------------------------
# Name: sendclient
# Description: Sends a properly formatted message to a client socket
#              Use this to send messages to connected clients
#              Don't print directly to the socket, this takes care of
#              the formatting, logging and counter incrementing
# Needs: string message, string client
# Returns: result code (0 or 1)
# -----------------------------------------------------------------------------
#
sub sendclient {
	my ( $msg ) = @_;

	if ( ! print $client $msg ) {
		return 0;
	} else {
		$total_bytes_sent = $total_bytes_sent + length($msg);
		logmsg("SEND: " . length($msg) . " bytes of data to " . $client->peerhost);
		return 1;
	}
}


# -----------------------------------------------------------------------------
# Name: saveStats
# Description: Saves server statistics to a file in the current dir
# Needs: Nothing
# Returns: result code (0 or 1)
# -----------------------------------------------------------------------------
#
sub saveStats {
	open(BOOGER, ">>$statfile") || return 0;
	print BOOGER "SERVER INFORMATION:\n";
	print BOOGER "Hostname: $hostname\n";
	print BOOGER "Started on: $startDate\n";
	print BOOGER "Active Connections: $count\n";
	print BOOGER "Lifetime Connections: $lifetime_connects\n";
	print BOOGER "Total bytes sent: $total_bytes_sent\n";
	print BOOGER "Total bytes rcvd: $total_bytes_rcvd\n";
	print BOOGER "Total rates: $total_rates\n";

	close $statfile;
	return 1;
}


# -----------------------------------------------------------------------------
# Name: shutdownServer
# Description: Shuts down the server properly
# Needs: Nothing
# Returns: nothing
# -----------------------------------------------------------------------------
#
sub shutdownServer {

	logmsg("INFO: Server shutting down");
	
	# Terminate the transit time engine
	logmsg("INFO: Terminating tranit time engine");
	$transObject->ServGd32Terminate($nHandle);
	$err = Win32::OLE->LastError();
	logmsg("ERROR: OLE Error: $err") if $err;
	
	# Save out the stats
	logmsg("INFO: Saving stats");
	saveStats();
	
	exit(0);
}
