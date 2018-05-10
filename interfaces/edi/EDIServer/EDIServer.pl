#!/usr/bin/perl
# =============================================================================
#
# EDIServer.pl
#
# Inbound / outbound EDI Server
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# $Id: EDIServer.pl,v 1.8 2003/01/09 19:22:30 youngd Exp $
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# Usage:
#
# =============================================================================
#
# ChangeLog:
#
# $Log: EDIServer.pl,v $
# Revision 1.8  2003/01/09 19:22:30  youngd
#   * Added use of the CHECK_DAYS features.
#
# Revision 1.7  2003/01/07 23:05:35  youngd
#   * Way too many changes to list in here. If you really want to see them,
#     do a diff between the previous version and this one.
#
# Revision 1.6  2003/01/03 20:51:56  youngd
#   * Reworked fork() and daemon() functions to be a bit cleaner and happen
#     much earlier in the startup.
#   * Added $name to debug output area(s).
#   * Add a param dump in debug mode just before final startup.
#   * Set the default operational mode to inbound since that's what this
#     is going to be used the most for no.
#   * Moved all print() and debug() calls from before to after the dup of the
#     file handles. This way everything goes to the log file in daemon mode.
#
# =============================================================================

$name = 'EDIServer.pl';

$cvsid = '$Id: EDIServer.pl,v 1.8 2003/01/09 19:22:30 youngd Exp $';
@cvsinfo = split(' ', $cvsid);
$version = $cvsinfo[2];

# ------------------------------------------------------------------------------
#                             B E G I N
# ------------------------------------------------------------------------------
BEGIN {

    use lib '/tfd/modules/';

    # Pragmas
    use vars;
    use warnings;
    use strict;

    # "Standard" modules to pull in
    use Cwd;
    use Getopt::Long qw(GetOptions);
    use Sys::Hostname;
    use POSIX qw(strftime setsid);
    use Digest::MD5;
    use Carp;
    use Config::Simple;

    # Local modules to pull in
    use edi;
    use logging;
    use err;
}

# ------------------------------------------------------------------------------
#                              V A R I A B L E S
# ------------------------------------------------------------------------------
$configfile     = "/edi/edi.cfg";
$hostname       = hostname;
$logfile        = "/edi/logs/EDIServer.log";
$sleeptime      = 30;

# These are all variables read from the config file
# and re-read during a restart operation
$sleeptime      = undef;
$user           = undef;
$group          = undef;
$chroot         = undef;
$rootdir        = undef;
$ediroot        = undef;
$inqueue        = undef;
$outqueue       = undef;
$failedqueue    = undef;
$processedqueue = undef;
# EDI transactions
$edi204enabled     = undef;
$edi204processor   = undef;
$edi210enabled     = undef;
$edi210processor   = undef;
$edi210updateap    = undef;
$edi214enabled     = undef;
$edi214processor   = undef;



# ------------------------------------------------------------------------------
#                   C O M M A N D   L I N E   O P T I O N S
# ------------------------------------------------------------------------------
 GetOptions ( "debug"             => \$debug,

              "nofork"            => \$nofork,

              "config-file=s"     => \$configfile,
              "log-file=s"        => \$logfile,

              "help"              => sub { print_usage(); },
              "version"           => sub { print_version(); }
            );



# ------------------------------------------------------------------------------
#                                 M A I N
# ------------------------------------------------------------------------------

# Set autoflush to true on every file handle
select(STDIN); $| = 1;
select(STDERR); $| = 1;
select(STDOUT); $| = 1;


# Only dup if we're forking into the background
if ( ! $nofork ) {
        # dup STDOUT
        # Remember, every print from here on out will go to the logfile
        # not the user's screen. Has to be done so we may truly become a daemon.
        open(STDOUT, ">>$logfile")      or die "$name: Unable to dup STDOUT ($!)\n";

        # Get STDIN from /dev/null (shouldn't be any input)
        open(STDIN, '/dev/null')        or die "$name: Can't read /dev/null: $!";

        # Push STDERR to wherever STDOUT is going
        open STDERR, '>>&STDOUT'        or die "$name: Can't dup stdout: $!";
}


print "*********************************************************************\n";
print "**       F R E I G H T   D E P O T   E D I   S E R V E R           **\n";
print "*********************************************************************\n";
print "\n";


# Read the config file which populates global variables
if ( ! readconfig() ) {
    print "Unable to read config file, puke\n";
    exit(1);
} else {
    debug("$name: Read config file");
}


debug("$name: starting up");

# Latch on to the signals we want to monitor during operation
debug("$name: Hooking signals");
if ( hook_signals() ) {
    debug("$name: All signals registered");
} else {
    error("$name: Couldn't hook signals, exiting...");
    exit(0);
}


# Do a change root if configured to do so
if ( chroot ) {
     # chdir and chroot to the run-time location
     chdir($rootdir) || die "$name: Unable to chdir() to $rootdir ($!)\n";

     # chroot only works on Cygwin and UNIX
     # and barely even works correctly there :-(
     chroot($rootdir) || die "$name: Unable to chroot() to $rootdir ($!)\n";
}

# Go into the background in daemon mode unless told not to.
if ( ! $nofork ) {
    daemonize() || die "$name: Unable to become a deamon ($!)\n";
}


# Print some crap out
print "$name: Server version $version ready and waiting at " . localtime(time()) . "\n";
debug("$name: going into main loop");


# -----------------------------------------------------------------------------
#                              M A I N   L O O P
# -----------------------------------------------------------------------------
while(1) {

    debug("$name: Waking up");

    # Check for files in the incoming directory (generate a file list)
    @infiles = getFilesInDir("$inqueue");

    # If the first element of the array is empty, there are no files
    # Otherwise, start the inbound processing.
    if ( $infiles[0] eq "" ) {
        debug("Nothing to do in the in queue");
    } else {
        foreach $file ( @infiles ) {
            $type = getEdiDocType("$inqueue/$file");
            debug("File in the in queue: $file is type: $type");

            SWITCH: for ( $type ) {
                /214/ && do {
                    debug("Processing 214 file $file");
                    if ( $debug ) {
                        @cmd = ("$edi214processor", "$inqueue/$file", "--debug");
                        debug("system command: @cmd");
                    } else {
                        @cmd = ("$edi214processor", "$inqueue/$file");
                        debug("system command: @cmd");
                    }

                    unless ( $test ) {
                        system(@cmd);
                    }

                    # Remember to divide the return val ($?) by 256 to get the "real" exit value
                    # You could always shift the bits as well (<< 8) if you really want to
                    $exit_value  = $? / 256;

                    # The exit should be 0, if not the system call failed.
                    if ( $exit_value == 0 ) {
                        debug("214 processor ($edi214processor) ran fine on file $file");
                    } else {
                        debug("214 processor ($edi214processor) ran badly with exit of $exit_value");;
                        debug("command was: @cmd");
                    }
                    last SWITCH;
                };

            }
        }
    }



    # Check for files in the failed directory (generate a file list)

    # foreach file in the failed directory

        # determine their type

        # process them



    debug("$name: going to sleep for $sleeptime seconds");
    sleep($sleeptime);
}




exit(0);

### END





# -----------------------------------------------------------------------------
# FUNCTIONS
# -----------------------------------------------------------------------------


sub readconfig {

    if ( defined($main::configfile) ) {

        if ( -f $main::configfile ) {
            $cfg = new Config::Simple(filename=>$main::configfile) or die "Unable to open config file $main::configfile($!)\n";
        } else {
            error("$name: Failed to open config file $main::configfile");
            exit(0);
        }
    }

    # Populate the global variables with what we found.
    $main::sleeptime      = $cfg->param("main.sleep");
    $main::user           = $cfg->param("main.user");
    $main::group          = $cfg->param("main.group");

    $main::chroot         = $cfg->param("chroot.enabled");
    $main::rootdir        = $cfg->param("chroot.dir");

    $main::ediroot        = $cfg->param("edi.root");
    $main::inqueue        = $cfg->param("edi.inqueue");
    $main::outqueue       = $cfg->param("edi.outqueue");
    $main::failedqueue    = $cfg->param("edi.failedqueue");
    $main::processedqueue = $cfg->param("edi.processedqueue");

    $main::edi204enabled     = $cfg->param("204.enabled");
    $main::edi204processor   = $cfg->param("204.processor");
    $main::edi210enabled     = $cfg->param("210.enabled");
    $main::edi210processor   = $cfg->param("210.processor");
    $main::edi210updateap    = $cfg->param("210.updateap");
    $main::edi214enabled     = $cfg->param("214.enabled");
    $main::edi214processor   = $cfg->param("214.processor");


    # Dump the config params if in debug mode
    debug("$name: PARAM: main.sleep: "           . $cfg->param("main.sleep"));
    debug("$name: PARAM: main.user: "            . $cfg->param("main.user"));
    debug("$name: PARAM: main.group: "           . $cfg->param("main.group"));
    debug("$name: PARAM: edi.root: "             . $cfg->param("edi.root"));
    debug("$name: PARAM: edi.inqueue: "          . $cfg->param("edi.inqueue"));
    debug("$name: PARAM: edi.outqueue: "         . $cfg->param("edi.outqueue"));
    debug("$name: PARAM: edi.failedqueue: "      . $cfg->param("edi.failedqueue"));
    debug("$name: PARAM: edi.processedqueue: "   . $cfg->param("edi.processedqueue"));
    debug("$name: PARAM: 204.enabled: "          . $cfg->param("204.enabled"));
    debug("$name: PARAM: 204.processor: "        . $cfg->param("204.processor"));
    debug("$name: PARAM: 210.enabled: "          . $cfg->param("210.enabled"));
    debug("$name: PARAM: 210.processor: "        . $cfg->param("210.processor"));
    debug("$name: PARAM: 210.updateap: "         . $cfg->param("210.updateap"));
    debug("$name: PARAM: 214.enabled: "          . $cfg->param("214.enabled"));
    debug("$name: PARAM: 214.processor: "        . $cfg->param("214.processor"));

    return(1);

}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub timefmt {
    my $mtime = shift;
    my $time = time;
    my $fmt;

    if ($time > $mtime + 6 * 30 * 24 * 60 * 60 || $time < $mtime - 60 * 60) {
            $fmt = "%b %e  %Y";
    } else {
            $fmt = "%b %e %H:%M";
    }
    my $fmt_time = strftime $fmt, gmtime ($mtime);
    return($fmt_time);
}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub getuid {
    my $uid = shift;

    SWITCH: for ( $uid ) {
        /0/ && do {
            return "root";
            last SWITCH;
            };
    };

}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub getgid {
    my $gid = shift;

    SWITCH: for ( $gid ) {
        /0/ && do {
            return "root";
            last SWITCH;
            };
    };

}


# -----------------------------------------------------------------------------
# NAME        : reloadServer
# DESCRIPTION : Reloads the server
# ARGUMENTS   : None
# RETURNS     : True
# STATUS      : Experimental
# NOTES       : None
# -----------------------------------------------------------------------------
sub reloadServer {

    debug("Reloading server");
        
    if ( ! readconfig() ) {
        critical("$name: Couldn't read config file");
        exit(1);
    } else {
        debug("$name: re-read config file");
        return(1);
    }
}


# -----------------------------------------------------------------------------
# NAME        : shutdownServer
# DESCRIPTION : Correctly stops the server
# ARGUMENTS   : None
# RETURNS     : Nothing
# STATUS      : Experimental
# NOTES       : None
# -----------------------------------------------------------------------------
sub shutdownServer {

    debug("Entering shutdownServer");

    print "Server shutting down\n";

    exit(0);
}


# -----------------------------------------------------------------------------
# NAME        : daemonize
# DESCRIPTION : Correctly sends the program in the background
# ARGUMENTS   : None
# RETURNS     : Nothing
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub daemonize {
    debug("$name: Entering daemonize()");

    debug("$name: daemonize(): Trying to fork()");
    defined(my $pid = fork)         or die "Can't fork: $!";

    exit if $pid;

    debug("$name: daemonize(): Doing a setsid");
    setsid                          or die "Can't start a new session: $!";
}


# -----------------------------------------------------------------------------
# NAME        : print_usage
# DESCRIPTION : Prints out how to use the program
# ARGUMENTS   : None
# RETURNS     : Nothing
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub print_usage {
    print "Usage: $name <options>\n";
    print "   Options:\n";
    exit(0);
}


# -----------------------------------------------------------------------------
# NAME        : hook_signals
# DESCRIPTION : Set up handlers for the various signals we want to
#             : watch
# ARGUMENTS   : None
# RETURN      : 1
# STATUS      : Development
# NOTES       : None
# -----------------------------------------------------------------------------
sub hook_signals {
debug("$name: hook_signals(): entering hook_signals");

    # SIGHUP - Signal #1 - Restart the process
    $SIG{HUP}  = \&sig_hup_handler;

    # SIGINT - Signal #2 - CTRL-C raises this one
    $SIG{INT}  = \&sig_int_handler;

    # SIGQUIT - Signal #3 - No description
    $SIG{QUIT} = \&sig_quit_handler;

    # SIGKILL - Signal #9 - Forced program
    # termination
    $SIG{KILL} = \&sig_kill_handler;

    # SIGUSR1 - Signal #10 - No description
    $SIG{USR1} = \&sig_usr1_handler;

    # SIGUSR2 - Signal #12 - No description
    $SIG{USR2} = \&sig_usr2_handler;

    # SIGTERM - Signal #15 - Default kill signal
    $SIG{TERM} = \&sig_term_handler;

    return(1);
}


sub sig_hup_handler {
    debug("sig_hup_handler(): Entering sig_hup_handler()");
    reloadServer();
    return(1);
}


sub sig_int_handler {
    debug("sig_int_handler(): Entering sig_int_handler()");
    print "Caught interrupt signal, exiting...\n";
    cleanup();
    exit(0);
}


sub sig_quit_handler {
    debug("sig_quit_handler(): Entering sig_quit_handler()");
    print "Caught quit signal, exiting...\n";
    cleanup();
    exit(0);
}


sub sig_kill_handler {
    debug("sig_kill_handler(): Entering sig_kill_handler()");
    print "Caught kill signal, exiting...\n";
    cleanup();
    exit(0);
}


sub sig_term_handler {
    debug("sig_term_handler(): Entering sig_term_handler()");
    print "Caught term signal, exiting...\n";
    cleanup();
    exit(0);
}


sub sig_usr1_handler {
    debug("sig_usr1_handler(): Entering sig_usr1_handler()");
    return(1);
}


sub sig_usr2_handler {
    debug("sig_usr2_handler(): Entering sig_usr2_handler()");
    return(1);
}


sub cleanup {
    print "$name: Cleaning up\n";
    return(1);
}

