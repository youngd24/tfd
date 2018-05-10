#!/usr/local/bin/perl
# ===================================================================
#
# RateClient.pl
#
# Simple rating client (tcp based)
#
# Contents Copyright (c) 2000, Digiship Corp.
#
# $Id: RateClient.pl,v 1.4 2002/07/22 13:45:26 youngd Exp $
#
# $Source: /export/cvs/tfd/rating/RateServer/RateClient.pl,v $
#
# Darren Young
# youngd@digiship.com
#
# ===================================================================
#
# Description:
#
# ===================================================================
#
# Usage:
#
# ===================================================================
#
# Changes:
#
# $Log: RateClient.pl,v $
# Revision 1.4  2002/07/22 13:45:26  youngd
# added server and port text to debug output messages on failed connects
#
# Revision 1.3  2002/07/22 13:42:37  youngd
# added server variables to client config
#
# Revision 1.2  2002/07/22 13:40:41  youngd
# * Added use of client configuration file via Config::General
# * Added new client configuration file
#
# Revision 1.1.1.1  2002/07/13 04:30:35  youngd
# initial import
#
# Revision 1.1.1.1  2001/12/15 18:17:56  youngd
# new import
#
# Revision 1.10  2001/01/03 15:27:23  youngd
# Added version check
# Finished transit time calls
#
# Revision 1.9  2001/01/02 22:48:20  youngd
# da
#
# Revision 1.8  2000/12/28 23:46:24  youngd
# Changed server to point to the produciton system
#
# Revision 1.7  2000/12/28 20:56:57  youngd
# Added transtime
#
# Revision 1.6  2000/12/27 22:48:40  youngd
# Start of TransTime function testing
#
# Revision 1.5  2000/12/21 17:15:36  deploy
# Added --help command line option
# Added --lentest command line option
# Added shutdownBad function to simulate improper shutdown (0 length message)
# Added shutdownNice function to properly close the connection
# --lentest calls shutdownBad otherwise shutdownNice is called
#
# Revision 1.4  2000/12/14 18:52:52  youngd
# Added header
# Added RCS tags
# Added shutdown() just before the close
#
#
# ===================================================================
#
# Bugs:
#
# ===================================================================
#
# Todo
#
# ===================================================================
#

BEGIN {

    use strict;
    use warnings;

    use IO::Socket;
    use Config::General;
}

my $sock;
my $config_file = "client.conf";
#my $srczip = "45345";
#my $dstzip = "60614";
#my $weight = "10000";
#my $class = "55";
#my $carrier = "RDWY";

 
# Try and open the config file to see if we can 
open(CFGFILE, "<$config_file") || die "Unable to open $config_file: $!\n"; 
close(CFGFILE); 
  
$conf = new Config::General($config_file); 
%config = $conf->getall(); 
   
$srczip    = $config{srczip}; 
$dstzip    = $config{dstzip}; 
$weight    = $config{weight}; 
$class     = $config{class};
$carrier   = $config{carrier};
$server    = $config{server};
$port      = $config{port};


my $READY_MSG = "READY\n";
my $BASEPRICE_MSG="BASEPRICE?SRCZIP=$srczip&DSTZIP=$dstzip&WEIGHT=$weight&CLASS=$class&LEN=560\n";
my $TRANSTIME_MSG="TRANSTIME?SCAC=$carrier&TYPE=LTL&SRCZIP=$srczip&DSTZIP=$dstzip";
my $translen = length($TRANSTIME_MSG) - 1;
my $TRANSTIME_MSG = $TRANSTIME_MSG . "&LEN=$translen" . "0\n";
my $QUIT_MSG = "QUITT\n";
my $BYE_MSG = "BYE\n";
my $INFO_MSG = "INFO\n";
my $STATS_MSG = "STATS\n";

if ( $ARGV[0] eq "--help" ) {
	print "\n";
	print "Usage: RateClient.pl <options>\n";
	print "Options: --help     This screen\n";
	print "         --lentest  0 length message test\n";
	exit(0);
}


$sock = new IO::Socket::INET(PeerAddr => $server,
				         PeerPort => $port,
				         Proto => 'tcp',
					    );

die "Could not create socket to $server:$port ($!)\n" unless $sock;

$sock->autoflush(1);

print "INFO: Wating for READY signal\n";

# Read data and try to get READY
while(sysread($sock, $buf,1)) {
	$msg = $msg . $buf;
	if ( ord($buf) == 10 ) {
		last;
	}
}

print "RECV: Got raw message: $msg";

# Make sure what we got is READY
if ( $msg =~ "READY" ) { 
	print "INFO: Good, got READY\n";
} else {
	print "ERROR: Bad, didn't get READY\n";
	close($sock);
	exit(1);
}

# Reset the message
$msg = "";

# Check the version of the server, we need 1.1 of the protocol
print "SEND: Attempting retrieve VERSION\n";
print $sock "VERSION\r\n";

while(sysread($sock, $buf, 1)) {
	$msg = $msg . $buf;
	if ( ord($buf) == 10 ) {
		last;
	}
}

print "RECV: Got raw message: $msg";

# Grab the fields produced by the VERSION command separated by white space
# i.e. PROTOCOL VERSION 1.1 PROGRAM VERSION 1.23
#      ^^1      ^^2     ^^3 ^^4     ^^5     ^^6
#
($field1,$field2,$srvrProtVer,$field4,$field5,$srvrProgVer) = split(' ', $msg);

if ( $srvrProtVer eq "1.1" ) { 
	print "INFO: Good, server supports version 1.1\n";
} else {
	print "ERROR: Server doesn't support version 1.1\n";
	close($sock);
	exit(1);
}

# Reset the return message
$msg = "";

# Try and get a BASEPRICE back
print "SEND: Attempting to retrieve a BASEPRICE\n";
print $sock $BASEPRICE_MSG;
while(sysread($sock, $buf, 1)) {
	$msg = $msg . $buf;
	if ( ord($buf) == 10 ) {
		last;
	}
}

print "RECV: Got raw message: $msg";

# Parse the baseprice message and validate the checksum
($price, $right) = split(":", $msg);
if ( length($price) != $right ) {
	print "ERROR: Checksum bad\n";
	close($sock);
	exit(1);
} else {
	print "INFO: Checksum passsed\n";
}

$msg = "";

# Send for a transit time
print "SEND: Attempting to retrieve a TRANSTIME\n";
print $sock $TRANSTIME_MSG;
while(sysread($sock, $buf, 1)) {
	$msg = $msg . $buf;
 	if ( ord($buf) == 10 ) {
		last;
	}
}

print "RECV: Got raw message: $msg";

# Parse the baseprice message and validate the checksum
($transTime, $right) = split(":", $msg);
if ( length($transTime) != $right ) {
	print "ERROR: Checksum bad\n";
	close($sock);
	exit(1);
} else {
	print "INFO: Checksum passsed\n";
}

$msg = "";



if ( $ARGV[0] eq "--lentest" ) {
	print "INFO: Executing length test shutdown\n";
	displayDetails();
	shutdownBad();
} else {
	print "INFO: Shutting down properly\n";
	displayDetails();
	shutdownNice();
}


sub displayDetails {
	print "*****\n";
	print "To ship $weight lbs. of class $class goods with $carrier\n";
	print "from $srczip to $dstzip\n";
	print "will cost \$$price and will take $transTime days\n";
	print "*****\n";
	return(1);
}

# Function to shutdown properly
#
sub shutdownNice {

	# End the session
	print "SEND: Sending QUIT\n";
	print $sock "$QUIT_MSG\n";

	while(sysread($sock, $buf,1)) {
		$msg = $msg . $buf;
		if ( ord($buf) == 10 ) {
			print "RECV: Got message: $msg";
			last;
			$sock->close; 
		}
	}
	chop($msg);
	chop($msg);
	if ( $msg eq "BYE" ) { 
		print "INFO: Server said BYE, great!\n";
		exit(0);
	} else {
		print "INFO: Server didn't say BYE, how rude!\n";
		exit(1);
	}
}


# Function to kill the connection early
# Should introduce the 0 length message to the server
# Use this to see if the server is reacting properly
# to that particular condition
#
sub shutdownBad {

	# By simply closing the socket and exiting without
	# sending a QUIT message, we effectively break the server
	close($sock);
	exit(0);
}


# Function to check the protocol version of the remote
# Use this to make sure the server we connect to implements
# the correct version
# Requires: String version
# Returns: Status code (0 or 1)
#
sub checkProtVersion {
	my ( $version ) = @_;
	return(1);
}

sub isConnected {
	return(1);
}
