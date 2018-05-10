# ----------------------------------------------------------------------------
#
# SiteMon.pl
#
# Web site URL monitor program
#
# Contents Copyright (c) 2000, Digiship Corp.
#
# $Id: sitemon.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Darren Young
# youngd@digiship.com
#
# ----------------------------------------------------------------------------
#
# ChangeLog
#
#    $Log: sitemon.pl,v $
#    Revision 1.1.1.1  2002/07/13 04:30:35  youngd
#    initial import
#
#    Revision 1.1.1.1  2001/12/15 18:17:56  youngd
#    new import
#
#    Revision 1.1.1.1  2001/12/12 19:00:28  youngd
#    initial import
#
#    Revision 1.4  2001/02/08 15:52:19  youngd
#    Now tests PING's
#
#    Revision 1.3  2001/02/02 21:20:40  youngd
#    Changed the messages to reflect more of an HTTP kind of thing
#
#    Revision 1.2  2001/02/02 21:17:48  youngd
#    Working version, checks the page and let's us know if it's dead
#
#    Revision 1.1  2001/02/02 20:56:58  youngd
#    Initial versions stolen from RSMon.
#    These will be modified to become a site monitor
#
# ----------------------------------------------------------------------------

# Bring in some modules
use Config;	                              # Perl configuration module
use Sys::Hostname;                        # Our hostname
use Mail::Sender;                         # Sending SMTP
use LWP::Simple;                          # Perl LibWWW

# Set default options 
# The next code for cmdline processing will override if necessary
#
$configFile      = "sitemon.cfg";         # Default config file if none specified
$os              = $Config{'osname'};     # Platform we're on
$startTime       = localtime(time());     # Used to indicate when we started
$dbg             = 0;                     # False by default
$hostname        = hostname();            # Our name
$status          = undef;                 # Connection status (GOOD || BAD)

# The following are initialized here just in case, however they are supposed
# to come from the configuration file
$max_sleep       = undef;                 # Max sleep time
$default_sleep   = undef;                 # How long we sleep
$sleep_increment = undef;                 # Incrementor for the sleep
$url             = undef;                 # URL to test
@email_list      = [];                    # Email notification list

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
	if ( $_ =~ "URL" ) {
			($gbg, $url) = split('=', $_);
			chop($url);
	}
	if ( $_ =~ "EMAIL_LIST" ) {
			($gbg, @email_list) = split('=', $_);
			chop(@email_list);
	}
}

close(CONFIGFILE) || die "Unable to close config file ($configFile): $!\n";

if ( $dbg ) {
	logmsg("DEFAULT_SLEEP: $default_sleep", DEBUG);
	logmsg("SLEEP_INCREMENT: $sleep_increment", DEBUG);
	logmsg("MAX_SLEEP: $max_sleep", DEBUG);
	logmsg("URL: $url", DEBUG);
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
				$admsg = "Page $url unavailable\n";
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
				$admsg = "Page $url is still unavailable\n";
				sendNotification("EMAIL", @email_list, $admsg);
				if ( $sleep <= $max_sleep ) {
					logmsg("Increasing the sleep by $sleep_increment", INFO);
					$sleep = $sleep * $sleep_increment;
				} else {
					$sleep = $sleep;
				}
			} else {
				logmsg("Server back up", INFO);
				$admsg = "Server back up on $url\n";
				sendNotification("EMAIL", @email_list, $admsg);
				$sleep = $default_sleep;
			}
		} else {
			logmsg("Server back up", INFO);
			$admsg = "Page $url became available again\n";
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















sub checkServer {

    # Try and retrieve the page using LWP::Simple
	$stat = get($url);

    # It will only be defined if it was successful
	if ( defined($stat) ) {
		logmsg("Page check passed", INFO);
		$status = "GOOD";
		return(1);
	} else {
		logmsg("Page check failed", INFO);
		$status = "BAD";
		return(0);
	}

}


# Prints out how to use us
sub print_usage {
	print "\n";
	print "Usage: sitemon.pl <options>\n";
	print "Options:   --help                 This screen\n";
	print "           --config-file=<file>   Path of the config file\n";
	print "           --debug                Enables debugging\n";
	print "\n";
}


# Prints out a properly formatted log message
sub logmsg {
	my ($msg, $level) = @_;
	
	$now = localtime(time());
	print $now . ": " . $hostname . ": " .  $level . ": " .  $msg . "\n";
}

# Sends a notification message to an admin
sub sendNotification {
	my ($type, $rcpt, $msg) = @_;

	# Type options are currently EMAIL

	if ( $type eq "EMAIL" ) {
		print "NOTIFY: Sending email message to $rcpt\n";
		$mailer = new Mail::Sender { from => 'youngd@digiship.com',
				                             smtp => 'mail.digiship.com'};
		$mailer->Open({to=>"$rcpt", subject=>'Web Site Status'});
		$mailer->SendLine("$msg");
		$mailer->SendLine();
		$mailer->Close();
		return(1);
	}
}
