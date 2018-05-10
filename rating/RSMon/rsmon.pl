#!/usr/bin/perl
# ===============================================================
#
# RSMON.PL
#
# RateServer Monitor Program
#
# $Id: rsmon.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ===============================================================
#
# This script is used to monitor a running instance of 
# the Digiship rate server. It attempts to establish a 
# connection to the rate server, retrieve a rate and 
# shutdown the connection. If anything goes bad during this
# process, a notification is sent out to an admin.
#
# ===============================================================
#
# Usage:
#    perl RSMON.PL <options>
#
# ===============================================================
#
# $Log: rsmon.pl,v $
# Revision 1.1.1.1  2002/07/13 04:30:35  youngd
# initial import
#
# Revision 1.1.1.1  2001/12/15 18:17:56  youngd
# new import
#
# Revision 1.1.1.1  2001/12/12 19:00:28  youngd
# initial import
#
# Revision 1.16  2001/02/23 20:24:28  deploy
# Added select and autflush statements to all filehandles
#
# Revision 1.15  2001/02/23 20:08:58  deploy
# ADded handlers for additional signals
#
# Revision 1.14  2001/02/23 19:55:52  deploy
# Added signal handler to catch ctrl-c and call a shutdown method
# Added the shutdown method
# Added the ability to specifiy a logfile in the config file, if it's there
# it'll use it, otherwise all messages go to STDOUT
#
# Revision 1.13  2001/02/23 19:28:48  deploy
# Modified header
#
# Revision 1.11  2001/02/23 19:18:13  deploy
# Added perl header for UNIX
# $Log: rsmon.pl,v $
# Revision 1.1.1.1  2002/07/13 04:30:35  youngd
# initial import
#
# Revision 1.1.1.1  2001/12/15 18:17:56  youngd
# new import
#
# Revision 1.1.1.1  2001/12/12 19:00:28  youngd
# initial import
#
# Revision 1.16  2001/02/23 20:24:28  deploy
# Added select and autflush statements to all filehandles
#
# Revision 1.15  2001/02/23 20:08:58  deploy
# ADded handlers for additional signals
#
# Revision 1.14  2001/02/23 19:55:52  deploy
# Added signal handler to catch ctrl-c and call a shutdown method
# Added the shutdown method
# Added the ability to specifiy a logfile in the config file, if it's there
# it'll use it, otherwise all messages go to STDOUT
#
# Revision 1.13  2001/02/23 19:28:48  deploy
# Modified header
#
# Revision 1.12  2001/02/23 19:26:26  deploy
# Modified comment header to be easier to read
#
# Revision 1.11  2001/02/23 19:18:13  deploy
# Added perl header for UNIX
#
# Revision 1.10  2000/12/27 21:21:12  youngd
# Released version ready for testing
# Added email support via Mail::Sender
#
# Revision 1.9  2000/12/27 18:14:45  youngd
# Added some comments on the connection handling
#
# Revision 1.8  2000/12/27 18:12:58  youngd
# Completed main loop
# Added the prev_status variables
# Added the logic to set the prev_status variables
# Added the "server was bad last time" section
# Performed basic testing of most of the logic
#
# Revision 1.7  2000/12/26 16:26:53  youngd
# Basic checks working
# Loop active
# Client completely implemented
#
# Revision 1.6  2000/12/21 21:22:10  youngd
# Added client connection functionality
# Added start of main loop
#
# Revision 1.5  2000/12/21 20:16:52  youngd
# Added the parser for the configuration file
# Added base variables to the top for the values that are to be
# read from the config file
#
# Revision 1.4  2000/12/21 19:59:07  youngd
# Added logmsg function
# Added print_usage function
# Added initial command line parser for
#    --debug
#    --config-file
#    --help
# Added basic global variables
#
# Revision 1.3  2000/12/21 19:00:10  youngd
# Added header and comments
#
# Revision 1.2  2000/12/21 17:31:41  youngd
# Filled in header fields with basic information such as name, author, email, etc
#
# Revision 1.1  2000/12/21 17:28:51  youngd
# Initial file from the perl_template
#
# ===============================================================

# Bring in some modules
use Config;							# Perl configuration module
use IO::Select;							# File handle selection
use IO::Socket;							# Socket interface
use Sys::Hostname;						# Our hostname
use Mail::Sender;

# Error definitions
# Eventually, these need to be moved to the protocol module
# Categories: 
# 10-19 -> Communication errors
#   10
#   11
# 20-29 -> Protocol Errors
# 30-39 -> Application Errors
#
$ERR_CONNECT  = "11";
$ERR_TIMEOUT  = "21";
$ERR_NOBASE   = "31";
$ERR_SHUTDOWN = "41";
$ERR_NONE     = "51";

# Set default options 
# The next code for cmdline processing will override if necessary
#
$configFile      = "rsmon.cfg";				# Default config file if none specified
$os              = $Config{'osname'};     # Platform we're on
$startTime       = localtime(time());     # Used to indicate when we started
$dbg             = 0;                     # False by default
$hostname        = hostname();            # Our name
$status          = undef;                 # Connection status (GOOD || BAD)
# The following are initialized here just in case, however they are supposed
# to come from the configuration file
$max_sleep       = undef;						# Max sleep time
$default_sleep   = undef;						# How long we sleep
$sleep_increment = undef;                 # Incrementor for the sleep
$rs_server       = undef;                 # Server to connect to
$rs_port         = undef;                 # TCP port on the server to connect to
@email_list      = [];                    # Email notification list

$SIG{INT} = \&shutdown;
$SIG{QUIT} = \&shutdown;
$SIG{KILL} = \&shutdown;

# Parse the command line
# Possible command line options:
#    --help
#    --config-file=<configFile>
#    --debug
#
if ( $#ARGV < 0 ) {
	# No args. That's ok, we take some defaults anyways
} else {
	# There's args, iterate through them and add options as we go
	foreach ( @ARGV ) {

		# Print the help screen
		if ( $_ eq "--help" ) {
			print_usage();
			exit(0);
		} 

		# Check for the debug flag
		if ( $_ eq "--debug" ) {
			$dbg = 1;
			logmsg("Debugging enabled", INFO);
		}

		# Set the location of the config file from the right of the arg
		# If they give us a file, make sure it exists
		if ( $_ =~ "--config-file" ) {
			if ( $dbg ) { logmsg("Setting configFile from command line", DEBUG) }
			($gbg, $configFile) = split('=', $_);
			if ( ! -f $configFile ) {
				die "Unable to set config file ($configFile): $!\n";
			}
		}

	} # End foreach

} # End else


# Open, read and process the config file
# All items read come in the format [key=val] pairs
# We end up dropping the key after we hit it in the expression search
#
open(CONFIGFILE, "<$configFile") || die "Unable to read file ($configFile): $!\n";

while(<CONFIGFILE>) {
	next if ( $_ =~ '#' );                   # Skip comments
	next if ( ord(substr($_,0,1)) == 10 );   # Skip blank lines
	if ( $_ =~ "DEFAULT_SLEEP" ) {
			($gbg, $default_sleep) = split('=', $_);
			chop($default_sleep);
	}
	if ( $_ =~ "MAX_SLEEP" ) {
			($gbg, $max_sleep) = split('=', $_);
			chop($max_sleep);
	}
	if ( $_ =~ "SLEEP_INCREMENT" ) {
			($gbg, $sleep_increment) = split('=', $_);
			chop($sleep_increment);
	}
	if ( $_ =~ "RS_SERVER" ) {
			($gbg, $rs_server) = split('=', $_);
			chop($rs_server);
	}
	if ( $_ =~ "RS_PORT" ) {
			($gbg, $rs_port) = split('=', $_);
			chop($rs_port);
	}
	if ( $_ =~ "EMAIL_LIST" ) {
			($gbg, @email_list) = split('=', $_);
			chop(@email_list);
	}
	if ( $_ =~ "LOGFILE" ) {
			($gbg, $logfile) = split('=', $_);
			chop($logfile);
	}
}

close(CONFIGFILE) || die "Unable to close config file ($configFile): $!\n";

if ( defined $logfile ) {
	open(LOGFILE, ">>$logfile") || warn "Unable to open logfile: $logfile ($!)\n";
}

select(LOGFILE); $| = 1;
select(STDOUT); $| = 1;
select(STDERR); $| = 1;

if ( $dbg ) {
	logmsg("DEFAULT_SLEEP: $default_sleep", DEBUG);
	logmsg("SLEEP_INCREMENT: $sleep_increment", DEBUG);
	logmsg("MAX_SLEEP: $max_sleep", DEBUG);
	logmsg("RS_SERVER: $rs_server", DEBUG);
	logmsg("RS_PORT: $rs_port", DEBUG);
	logmsg("EMAIL_LIST: @email_list", DEBUG);
}

# Check to make sure we can reach the target before we start monitoring
if ( ! checkServer() ) {
	logmsg("Initial check was bad, trying again.", ERROR);
	if ( ! checkServer() ) {
		logmsg("Both initial checks bad, check the server and try later.", ERROR);
		exit(1);
	}
} else {
	logmsg("Initial tests passed, proceeding", INFO);
}


# Start the main program loop
# Set the sleep to the default to start with
# Set the default status to GOOD

$sleep = $default_sleep;
$status = "GOOD";
$prev_status = "GOOD";

while(true) {

	# The status as we entered here was good, we assume that the previous
	# check connection proved to be successful
	if ( $prev_status eq "GOOD" ) {
		logmsg("Last was good", INFO);
		if ( ! checkServer() ) {
			logmsg("Check was bad, trying again.", ERROR);
			# Check the server a second time
			if ( ! checkServer() ) {
				logmsg("Second check was bad, sending notice to an admin", ERROR);
				$admsg = "Server down on $rs_server\n";
				sendNotification("EMAIL", @email_list, $admsg);
				# Since the server was down, increment the sleep time
				if ( $sleep <= $max_sleep ) {
					logmsg("Increasing the sleep by $sleep_increment", INFO);
					$sleep = $sleep * $sleep_increment;
				} else {
					$sleep = $sleep;
				}
			} # End second check
		} # End first check
	}

	# The last status was bad so we have to check that way
	if ( $prev_status eq "BAD" ) {
		logmsg("Last was bad", INFO);
		if ( ! checkServer() ) {
			logmsg("Server still down, checking again", ERROR);
			if ( ! checkServer() ) {
				logmsg("Server still down second time", ERROR);
				$admsg = "Server still down on $rs_server\n";
				sendNotification("EMAIL", @email_list, $admsg);
				if ( $sleep <= $max_sleep ) {
					logmsg("Increasing the sleep by $sleep_increment", INFO);
					$sleep = $sleep * $sleep_increment;
				} else {
					$sleep = $sleep;
				}
			} else {
				logmsg("Server back up", INFO);
				$admsg = "Server back up on $rs_server\n";
				sendNotification("EMAIL", @email_list, $admsg);
				$sleep = $default_sleep;
			}
		} else {
			logmsg("Server back up", INFO);
			$admsg = "Server back up on $rs_server\n";
			sendNotification("EMAIL", @email_list, $admsg);
			$sleep = $default_sleep;
		}
	}

	# Update the prev status 
	if ( $status eq "GOOD" ) {
		$prev_status = "GOOD";
	}
	if ( $status eq "BAD" ) {
		$prev_status = "BAD";
	}


	logmsg("Going to sleep for $sleep seconds", INFO);
	sleep($sleep);

}















# High level function to check a connection and check the result
# This one wraps around the low-level protocol functions and simply
# returns good or bad. At 10,000 feet we really don't care about the 
# specific error messages, all we care about is the fact that it failed
#
sub checkServer {

	# Go get a price
	$result = getPrice();

	SWITCH: for ($result) {
		# Connection error
		/$ERR_CONNECT/ && do { 
			logmsg("Got 11", INFO);
			$status = "BAD";
			return(0);
		};

		# Timeout
		/$ERR_TIMEOUT/ && do {
			logmsg("Got 21", INFO);
			$status = "BAD";
			return(0);
		};

		# Baserate blew up for some reason
		# Could be remote, could be checksum, could be anything
		# Anyways, we really don't care about the details
		# All we know is it blew up and should be looked at
		/$ERR_NOBASE/ && do {
			logmsg("Got 31", INFO);
			$status = "BAD";
			return(0);
		};

		# Socket shutdown failed
		/$ERR_SHUTDOWN/ && do {
			logmsg("Got 41", INFO);
			$status = "BAD";
			return(0);
		};

		# Actually, not an error but good
		/$ERR_NONE/ && do {
			logmsg("Got 51", INFO);
			$status = "GOOD";
			return(1);
		};
	}
}



# Sends a request to a server and returns a response code
#
sub getPrice {

	logmsg("Attempting connection", INFO); 

	my $sock;
	my $READY_MSG = "READY\n";
	my $SRCZIP="60601";
	my $DSTZIP="45345";
	my $WEIGHT="10000";
	my $CLASS="55";
	my $BASEPRICE_MSG="BASEPRICE?SRCZIP=$SRCZIP&DSTZIP=$DSTZIP&WEIGHT=$WEIGHT&CLASS=$CLASS&LEN=560\n";
	my $QUIT_MSG = "QUITT\n";
	my $BYE_MSG = "BYE\n";
	my $INFO_MSG = "INFO\n";
	my $STATS_MSG = "STATS\n";
	my $EXPECTED_RESULT="1107.00:7";

	$sock = new IO::Socket::INET( PeerAddr => $rs_server,
                                 PeerPort => $rs_port,
                                 Proto => 'tcp');

	return $ERR_CONNECT unless $sock;

	logmsg("Wating for READY signal", INFO);

	# Read data and try to get READY
	while(sysread($sock, $buf,1)) {
		$msg = $msg . $buf;
		if ( ord($buf) == 10 ) {
			last;
		}
	}

	logmsg("Got raw message: ". $msg, RECV);

	# Make sure what we got is READY
	if ( $msg =~ "READY" ) { 
		logmsg("Good, got READY", INFO);
	} else {
		logmsg("Bad, didn't get READY", ERROR);
		$sock->close;
		return($ERR_NOBASE);
	}

	# Reset the return message
	$msg = "";

	# Try and get a BASEPRICE back
	logmsg("Attempting to retrieve a BASEPRICE", SEND);
	print $sock $BASEPRICE_MSG;
	while(sysread($sock, $buf, 1)) {
		$msg = $msg . $buf;
		if ( ord($buf) == 10 ) {
			last;
		}
	}

	logmsg("Got raw message: ". $msg, RECV);

	# Veryify the checksum, see the protocol spec for detail
	($left, $right) = split(":", $msg);
	if ( length($left) != $right ) {
		logmsg("Checksum bad", ERROR);
		$sock->close;
		return($ERR_NOBASE);
	} else {
		logmsg("Checksum passsed", INFO);
	}

	$msg = "";

	# End the session
	logmsg("Sending QUIT", SEND);
	print $sock "$QUIT_MSG\n";

	while(sysread($sock, $buf,1)) {
		$msg = $msg . $buf;
		if ( ord($buf) == 10 ) {
			logmsg("Got message: $msg", RECV);
			$sock->close; 
			#return($ERR_NONE);
		}
	}
	if ( $msg == "BYE" ) {
		return($ERR_NONE);
	}

}




# Prints out how to use us
sub print_usage {
	print "\n";
	print "Usage: rsmon.pl <options>\n";
	print "Options:   --help                 This screen\n";
	print "           --config-file=<file>   Path of the config file\n";
	print "           --debug                Enables debugging\n";
	print "\n";
}


# Prints out a properly formatted log message
sub logmsg {
	my ($msg, $level) = @_;
	
	$now = localtime(time());
	if ( defined $logfile ) {
		print LOGFILE $now . ": " . $hostname . ": " .  $level . ": " .  $msg . "\n";
	} else {
		print STDOUT $now . ": " . $hostname . ": " .  $level . ": " .  $msg . "\n";
	}
}

# Sends a notification message to an admin
sub sendNotification {
	my ($type, $rcpt, $msg) = @_;

	# Type options are currently EMAIL

	if ( $type eq "EMAIL" ) {
		print "NOTIFY: Sending email message to $rcpt\n";
		$mailer = new Mail::Sender { from => 'youngd@digiship.com',
				                             smtp => 'mail.digiship.com'};
		$mailer->Open({to=>"$rcpt", subject=>'Rate Server Status'});
		$mailer->SendLine("$msg");
		$mailer->SendLine();
		$mailer->Close();
		return(1);
	}
}

sub shutdown {
	print STDOUT "Shutting down...\n";
	if ( defined $logfile ) {
		close(LOGFILE);
	}

	print STDOUT "Done.\n";
	exit(0);
}
