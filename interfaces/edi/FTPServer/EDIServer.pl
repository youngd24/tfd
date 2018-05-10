#!/usr/bin/perl
# =============================================================================
#
# EDI-FTP Server
#
# FTP Server capable of handling and processing EDI messages
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# $Id: EDIServer.pl,v 1.1 2002/10/20 20:26:27 youngd Exp $
#
# Darren Young
# youngd@digiship.com
#
# =============================================================================

# Our package declaration
package EDIServer;

# Our name as it appears to clients
$name = 'Digiship EDI-FTP Server';

# Our version, grab it from the CVS ID
$cvsid = '$Id: EDIServer.pl,v 1.1 2002/10/20 20:26:27 youngd Exp $';
@cvsinfo = split(' ', $cvsid);
$server_version = $cvsinfo[2];

# ------------------------------------------------------------------------------
#                             B E G I N
# ------------------------------------------------------------------------------
BEGIN {

       # Where we get module from
       # XXX - Perhaps this should be migrated to the configuration file?
       use lib '/digiship/modules';

       # Pragmas
       use warnings;

       # "Standard" modules to pull in
       use Cwd;
       use Getopt::Long qw(GetOptions);
       use Getopt::Std;
       use IO::Select;
       use IO::Socket;
       use Sys::Hostname;
       use POSIX qw(strftime setsid);
       use Digest::MD5;
       use Carp;
       use Config::General;

       # Local modules to pull in
       use XMLEDI;                      # Pulls in all other required modules
       use Passlib;                     # Crypto library for passwords
       use dbmod;                       # The database routine module
       use Digiship::Debug;
       use Digiship::Error;
}

# ------------------------------------------------------------------------------
#                          V A R I B L E S
# ------------------------------------------------------------------------------
# Run-time variables
$configFile   = 'server.conf';          # The server configuration file
$controlPort  = 21;                     # FTP control port
$dataPort     = 20;                     # FTP data port
$reuse        = 1;                      # Reuse the port after exit
$maxClients   = 5;                      # Max concurrent clients
$backlog      = 10;                     # Max clients sitting in the queue
$timeout      = 300;                    # TCP connection timeout
$hostname     = hostname;               # Our local hostname
$userfile     = '/ediroot/etc/users';   # File to authenticate users from
$serverRoot   = '/ediroot';             # Location to chroot()
$listenAddress = "0.0.0.0";             # IP to listen on for connections
$uid           = "edi";                 # User ID to run as
$gid           = "edi";                 # Group ID to run as
$debug         = 0;                     # Defaults to debug off
$chroot        = 0;                     # Chroot defaults to off

# EDI variables
$edienabled    = 1;                     # EDI is on by default
$inbound214    = 1;
$inbound204    = 1;
$outbound214   = 1;
$outbound204   = 1;

# Admin server
$adminPort     = 2121;
$adminUser     = "";
$adminPass     = "";

# Server information
$serveraddr  = 0;                        # Used to STORE information in
$serverport  = 0;                        # Same for this one

# State information
$client{id}              = undef;       # Unique ID for the remote (IO::Socket)
$client{username}        = undef;       # Username they auth()ed with
$client{password}        = undef;       # The client's password
$client{address}         = undef;       # Their address from the peername()
$client{authenticated}   = undef;       # Have they been autheitcated?
$client{hostaddress}     = undef;       # Results from a PORT command
$client{hostport}        = undef;       # Results from a PORT command
$client{type}            = "A";         # Transfer type, default to ASCII
$client{edi}             = 0;           # EDI mode is use? Default is off
$client{passive}         = 0;           # Passive is off by default
$client{passive_sock}    = 0;           # The passive (PASV) socket

# Set the defaults for the command line options
# This prevents the warnings from being kicked out
$opt_f                   = "";          # Alternate configuration file
$opt_d                   = "";          # Enable/Disable debugging
$opt_h                   = "";          # Help option
$opt_u                   = "";          # User to run as
$opt_g                   = "";          # Group to run as


# ------------------------------------------------------------------------------
#                   C O M M A N D   L I N E   O P T I O N S
# ------------------------------------------------------------------------------
getopts('f:dhu:g:');

# Requested to run in debug mode
if ( $opt_d ) {
    print "Running in debug mode, won't fork()\n";
    our $dbg = 1;
}

# Requested help, print it and exit
if ( $opt_h ) {
        print_usage();
        exit(0);
}

# User to run as, make sure it exists, if so, set the value
if ( $opt_u ) {
    if ( getpwnam($opt_u) ) {
        @userinfo = getpwnam($opt_u);
        $uid = $userinfo[3];

        # Set the effective user id
        $> = $uid;
    } else {
        die "User $opt_u does not exist\n";
    }
}

# Group to run as, make sure it exists, if so, set the value
if ( $opt_g ) {
    if ( getgrnam($opt_g) ) {
        @groupinfo = getgrnam($opt_g);
        $gid = $groupinfo[2];
    } else {
        die "Group $opt_g does not exist\n";
    }
}

# Parse the configuration file
if ( $opt_f ) {
        # See if they want to override the default from the command line
        $configFile = $opt_f;
} else {
        $configFile = $configFile;
}

# ------------------------------------------------------------------------------
#                          C O N F I G   F I L E
# ------------------------------------------------------------------------------

# Try and open the config file to see if we can
open(CFGFILE, "<$configFile") || die "Unable to open $cfgfile: $!\n";
close(CFGFILE);

$conf = new Config::General($configFile);
%config = $conf->getall();

$maxClients        = $config{maxClients};
$controlPort       = $config{controlPort};
$dataPort          = $config{dataPort};
$listenAddress     = $config{listenAddress};
$reuse             = $config{reuse};
$backlog           = $config{backlog};
$timeout           = $config{timeout};
$edienabled        = $config{edienabled};
$inbound214        = $config{inbound214};
$inbound204        = $config{inbound204};
$outbound214       = $config{outbound214};
$outbound204       = $config{outbound204};


# Dump out what our current settings are
debug("maxClients    -> $maxClients");
debug("controlPort   -> $controlPort");
debug("dataPort      -> $dataPort");
debug("listenAddress -> $listenAddress");
debug("reuse         -> $reuse");
debug("backlog       -> $backlog");
debug("timeout       -> $timeout");

if ( $edienabled ) {
     debug("EDI Processing is enabled");
} else {
     debug("EDI Process is disabled");
}

debug("Inbound 214 -> Yes") if $inbound214 || debug("Inbound 214 -> No") ;



# ------------------------------------------------------------------------------
#                                 M A I N
# ------------------------------------------------------------------------------

print "*********************************************************************\n";
print "**         D I G I S H I P   E D I   F T P   S E R V E R           **\n";
print "*********************************************************************\n";
print "\n";


# Hook signals
#$SIG{INT}   = \&shutdownServer;
#$SIG{HUP}   = \&reloadServer;

# Start listening for connections
$lsn = IO::Socket::INET->new( Listen    => $maxClients,
                              Proto     => 'tcp',
                              Reuse     => $reuse,
                              LocalAddr => $listenAddress,
                              LocalPort => $controlPort ) || die "Unable to create listener ($!)";

# Find out what IP and PORT we're using
# Technically, the port is known (21), but we'll be consistent
# We'll need the server address later for many things.
# I'm only concerned with this since the machine may be multi-homed.
$serverport = $lsn->sockport();
$serveraddr = $lsn->sockaddr();
$serveraddrstring = inet_ntoa($serveraddr);

debug("Server listening on $serveraddrstring:$serverport");

# Create the selector object
$sel = IO::Select->new($lsn);

if ( chroot ) {
     # chdir and chroot to the run-time location
     chdir($serverRoot) || die "Unable to chdir() to $serverRoot ($!)\n";

     # chroot only works on Cygwin and UNIX
     # and barely even works correctly there :-(
     chroot($serverRoot) || die "Unable to chroot() to $serverRoot ($!)\n";
}

# Set autoflush to true on every file handle
select(STDOUT); $| = 1;
select(STDIN); $| = 1;
select(STDERR); $| = 1;


# Only dup the handles outside of debug mode
if ( ! $dbg ) {
        # dup STDOUT
        # Remember, every print from here on out will go to the logfile
        # not the user's screen. Has to be done so we may truly become a daemon.
        open(STDOUT, ">/tmp/EDIServer.log") or die "Unable to dup STDOUT ($!)\n";

        # Get STDIN from /dev/null (shouldn't be any input)
        open(STDIN, '/dev/null')        or die "Can't read /dev/null: $!";

        # Push STDERR to wherever STDOUT is going
        open STDERR, '>&STDOUT'         or die "Can't dup stdout: $!";
}

# Go into the background in daemon mode unless we want to be debug
if ( ! $dbg ) {
    daemonize() || die "Unable to become a deamon ($!)\n";
}

print "Server version $server_version ready and waiting at " . localtime(time()) . "\n";

# Here we go...
while(@ready = $sel->can_read) {
        foreach $remote (@ready) {
                if($remote == $lsn) {
                        # Create a new socket
                        $new = $lsn->accept;
                        debug("Connection from " . $new->peerhost . "\n");
                        $sel->add($new);
                        print $new "220 $hostname $name (Version $cvsinfo[2] " . localtime(time()) . ") ready.\r\n";
                } else {
                        # Grab a single line from the remote
                        $message = <$remote>;
                        if ( length($message) == 0 ) {
                                killClient($remote) || print "Unable to close client " . $remote->peerhost . "\n";
                                next;
                        }
                        # Remove the CRLF from the message
                        # The RFC states that all client messages will be termintaed by
                        # A CRLF combination
                        $message =~ s/[\n\r]+$//;
                        debug("Got: $message from client");
                        $client{id} = $remote;
                        debug("client{id} set to $client{id}");
                        process($remote, $message);
                        # Maybe we have finished with the socket
                        # $sel->remove($remote);
                        # $conn->close;
                } # END IF
        } # END FOREACH
} # END WHILE




exit(0);

### END





# -----------------------------------------------------------------------------
# FUNCTIONS
# -----------------------------------------------------------------------------


# -----------------------------------------------------------------------------
# NAME        : killClient
# DESCRIPTION : Closes the connection to a client
# ARGUMENTS   : String(client)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub killClient {
        my ( $client ) = @_;
        debug("Entering killClient()");

        unless ($client) {
                error("api","killClient usage: killClient(client) [client not supplied]");
                return(0);
        }

        debug("killClient: Closing connection to " . $client->peerhost);

        # Remove it from the IO::Select array
        debug("Removing client from the IO::Select array");
        $sel->remove($client);

        # Close the socket
        debug("Closing the client's socket");
        $client->close;

        return(1);
}


# -----------------------------------------------------------------------------
# NAME        : process
# DESCRIPTION : The main client message processor
# ARGUMENTS   : String(client), String(message)
# RETURNS     :
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub process {
        my ($client, $msg) = @_;
        debug("Entering process()");
        debug("process was passed: $msg ");

        # Make sure they give us a client to process()
        unless ($client) {
                error("api", "process usage: process(client, message) [client not supplied]");
                return(0);
        }

        # Make sure they give us a message to process()
        unless ($msg) {
                error("api", "process usage: process(client, message) [message not supplied]");
                return(0);
        }

        # Process the requested command
        # The command has to be case insensitive, hence all the []'s
        COMMAND: for ( $msg ) {
        # HELP COMMAND
                /[Hh][Ee][Ll][Pp]/ && do {
                                        debug("Got request for HELP");
                                        # Split the help command into the details
                                        my ($gbg, $details) = split(' ', $msg);
                                        ftp_HELP($client, $details) || sendClient($client, "500 Syntax error");
                                        last COMMAND;
                                        };
        # SYST COMMAND
                /[Ss][Yy][Ss][Tt]/ && do {
                                        debug("Got request for SYST");
                                        ftp_SYST($client) || sendClient($client, "500 Syntax error");
                                        last COMMAND;
                                        };
        # USER COMMAND
                /^[Uu][Ss][Ee][Rr]/ && do {
                                        debug("Got request for USER");
                                        ($cmd, $username) = split(' ', $msg);
                                        ftp_USER($client, $username) || sendClient($client, "530 Not logged in");
                                        last COMMAND;
                                        };
        # PASS COMMAND
                /^[Pp][Aa][Ss][Ss]/ && do {
                                        debug("Got request for PASS");
                                        ($cmd, $password) = split(' ', $msg);
                                        ftp_PASS($client, $password) || sendClient($client, "530 Not logged in");
                                        last COMMAND;
                                        };
        # SITE COMMAND
                /^[Ss][Ii][Tt][Ee]/ && do {
                                        debug("Got request for SITE");
                                        ftp_SITE($client, $msg) || sendClient($client, "500 Syntax error. Try SITE HELP for usage.");
                                        last COMMAND;
                                        };
        # ABOR COMMAND
                /^[Aa][Bb][Oo][Rr]/ && do {
                                        debug("Got request for SITE");
                                        ftp_ABOR($client) || sendClient($client, "500 Syntax error. Try SITE HELP for usage.");
                                        last COMMAND;
                                        };
        # STAT COMMAND
                /^[Ss][Tt][Aa][Tt]/ && do {
                                        debug("Got request for STAT");
                                        ftp_STAT($client) || sendClient($client, "500 Syntax error. Try SITE HELP for usage.");
                                        last COMMAND;
                                        };
        # QUIT COMMAND
                /^[Qq][Uu][Ii][Tt]/ && do {
                                        debug("Got request for QUIT");
                                        ftp_QUIT($client) || sendClient($client, "426 Connection closed");
                                        last COMMAND;
                                        };
        # CWD COMMAND
                /^[Cc][Ww][Dd]/ && do {
                                        print "Got request for CWD\n";
                                        ($cmd, $dir) = split(' ', $msg);
                                        ftp_CWD($client, $dir) || error("info", "Unable to do CWD");
                                        last COMMAND;
                                        };
        # PWD COMMAND
                /^[Pp][Ww][Dd]/ && do {
                                        print "Got request for PWD\n";
                                ftp_PWD($client) || error("info", "Unable to do PWD");
                                        last COMMAND;
                                        };
        # XPWD COMMAND
                /^[Xx][Pp][Ww][Dd]/ && do {
                                        print "Got request for XPWD\n";
                                ftp_XPWD($client) || error("info", "Unable to do XPWD");
                                        last COMMAND;
                                        };
        # PORT COMMAND
                /^[Pp][Oo][Rr][Tt]/ && do {
                                        print "Got request for PORT\n";
                                        ($cmd, $portdata) = split(' ', $msg);
                                        ftp_PORT($client, $portdata) || error("info", "PORT command failed");
                                        last COMMAND;
                                        };
        # LIST COMMAND
                /^[Ll][Ii][Ss][Tt]/ && do {
                                        print "Got request for LIST\n";
                                        ftp_LIST($client, $msg) || error("info", "LIST command failed");
                    last COMMAND;
                    };
        # NLST COMMAND
                /^[Nn][Ll][Ss][Tt]/ && do {
                                        print "Got request for NLST\n";
                                        ftp_NLST($client, $msg) || error("info", "NLST command failed: ($!)");
                    last COMMAND;
                    };
        # STOR COMMAND
                /^[Ss][Tt][Oo][Rr]/ && do {
                                        print "Got request for STOR\n";
                                        ftp_STOR($client, $msg) || error("info", "STOR command failed: ($!)");
                    last COMMAND;
                    };
        # TYPE COMMAND
                /^[Tt][Yy][Pp][Ee]/ && do {
                                        print "Got request for TYPE\n";
                                        ftp_TYPE($client, $msg) || error("info", "TYPE command failed");
                    last COMMAND;
                    };
        # PASV COMMAND
                /^[Pp][Aa][Ss][Vv]/ && do {
                                        print "Got request for PASV\n";
                                        ftp_PASV($client, $msg) || error("info", "TYPE command failed");
                    last COMMAND;
                    };
        # ACCT COMMAND
                /^[Aa][Cc][Cc][Tt]/ && do {
                                        print "Got request for ACCT\n";
                                        ftp_ACCT($client, $msg) || error("info", "ACCT command failed");
                    last COMMAND;
                    };
        # RETR COMMAND
                /^[Rr][Ee][Tt][Rr]/ && do {
                                        print "Got request for RETR\n";
                                        ftp_RETR($client, $msg) || error("info", "RETR command failed");
                    last COMMAND;
                    };
        # FEAT COMMAND
                /^[Ff][Ee][Aa][Tt]/ && do {
                                        print "Got request for FEAT\n";
                                        ftp_FEAT($client, $msg) || error("info", "FEAT command failed");
                    last COMMAND;
                    };
        }; # END COMMAND
    debug("LEAVING PROCESS()");
}


# -----------------------------------------------------------------------------
# NAME        : ftp_USER
# DESCRIPTION : Process the ftp USER command
# ARGUMENTS   : String(client), String(username)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_USER {
    my ($client, $username) = @_;
    debug("Entering ftp_USER()");

    # Make sure we get the client
    unless ($client) {
        error('api', "ftp_USER usage: ftp_USER(client, username) [client not supplied]");
        return(0);
    }

    # Make sure we get the username
    unless ($username) {
        error("api", "ftp_USER usage: ftp_USER(client, username) [username not supplied]");
        return(0);
    }

    # Make sure they are a valid user
    # Look in the Passlib for a more details explanation
    if ( isUser($userfile, $username) ) {
        # User is good, send client OK to send PASS
        sendClient($client, "331 User ok, send password");
        $client{username} = $username;
        debug("client{username} set to $client{username}");
        return(1);
    } else {
        debug("Invalid user, telling client");
        sendClient($client, "530 Not logged in");
        $client{authenticated} = 0;
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : ftp_PASS
# DESCRIPTION : Process the ftp PASS command
# ARGUMENTS   : String(client), String(password)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_PASS {
    my ($client, $password) = @_;
    debug("Entering ftp_PASS()");

    # Make sure we get the client
    unless ($client) {
        error("api", "ftp_PASS usage: ftp_PASS(client, password) [client not supplied]");
        return(0);
    }

    # Make sure we get the password
    unless ($password) {
        error("api", "ftp_PASS usage: ftp_PASS(client, password) [password not supplied]");
        return(0);
    }

    #  They haven't given us a username yet
    if ( ! $client{username} ) {
        debug("Someone sent us a PASS without doing USER.");
        sendClient($client, "530 USER not sent yet.");
        return(0);
    }

    # Get the crypted password from the file for the user
    my $crypted = getPassFromFile($userfile, $client{username});
    debug("Got crypted password from file: $crypted");
    $client{password} = $crypted;

    # At this point we have the plain and crypted passwords
    # Now we need to validate them
    if ( checkPass($crypted, $password) ) {
        debug("PASSWORD VALIDATION PASSED.");
        sendClient($client, "230 User logged in, proceed.");
        $client{authenticated} = 1;
        return(1);
    } else {
        debug("PASWORD VALIDATION FAILED.");
        sendClient($client, "530 not logged in.");
        $client{authenticated} = 0;
        $client{password} = undef;
        debug("client{password} set to undef.");
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        : ftp_FEAT
# DESCRIPTION : Process the ftp FEAT command
# ARGUMENTS   : String(client), String(rest)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_FEAT {
    my ($client, $rest) = @_;
    debug("Entering ftp_FEAT()");

    unless ($client) {
        error("api", "ftp_FEAT usage: ftp_FEAT(client, rest) [client not supplied]");
        return(0);
    }

    unless ($rest) {
        error("api", "ftp_FEAT usage: ftp_FEAT(client, rest) [rest not supplied]");
        return(0);
    }

    sendClient($client, "500 Command not implemented.");
    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_RETR
# DESCRIPTION : Process the ftp RETR command
# ARGUMENTS   : String(client), String(rest)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_RETR {
    my ($client, $rest) = @_;
    debug("Entering ftp_RETR()");

    unless ($client) {
        error("api", "ftp_RETR usage: ftp_RETR(client, rest) [client not supplied]");
        return(0);
    }

    unless ($rest) {
        error("api", "ftp_RETR usage: ftp_RETR(client, rest) [rest not supplied]");
            return(0);
    }

    sendClient($client, "500 Command not implemented.");
    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_ACCT
# DESCRIPTION : Process the ftp ACCT command
# ARGUMENTS   : String(client, String(rest)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_ACCT {
    my ($client, $rest) = @_;
    debug("Entering ftp_ACCT()");

    unless ($client) {
        error("api", "ftp_ACCT usage: ftp_ACCT(client, rest) [client not supplied]");
        return(0);
    }

    unless ($rest) {
        error("api", "ftp_ACCT usage: ftp_ACCT(client, rest) [rest not supplied]");
        return(0);
    }

    sendClient($client, "500 Command not implemented.");
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
sub ftp_PASV {
    my ($client, $rest) = @_;
    debug("Entering ftp_PASV()");

    unless ($client) {
        error("api", "ftp_PASV usage: ftp_PASV(client) [client not supplied]");
    }

    my ($sock, $count);
    $count = 100;

    until(defined $sock || --$count == 0) {
        my $port = int(rand(65535 - 49152)) + 49152;
        debug("Passive port set to $port");

        $sock = IO::Socket::INET->new(Listen    => 1,
                                      LocalPort => $port,
                                      Reuse     => 1);
    }

    unless ($sock) {
        sendClient($client, "550 Can't open a listening socket.");
        return(1);
    }

    $client{passive} = 1;
    $client{passive_sock} = $sock;

    my $sockport = $sock->sockport;
    debug("sockport set to $sock->sockport");

    my $p1 = int ($sockport / 256);
    my $p2 = $sockport % 256;

    debug("p1 set to $p1");
    debug("p2 set to $p2");

    debug("Server address: $EDIServer::serveraddrstring");
    my ($ip1, $ip2, $ip3, $ip4) = split('\.', $EDIServer::serveraddrstring);
    debug("IP1 = $ip1, IP2 = $ip2, IP3 = $ip3, IP4 = $ip4");

    # Be very precise about this error message, since most clients
    # will have to parse the whole of it.
    sendClient($client, "227 Entering Passive Mode ($ip1, $ip2, $ip3, $ip4, $p1, $p2)");

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_SYST
# DESCRIPTION : Process the ftp SYST command
# ARGUMENTS   : String(client)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_SYST {
    my ($client) = @_;
    debug("Entering ftp_SYST()");

    # Make sure we get the client
    unless ($client) {
        error("api", "ftp_SYST usage: ftp_SYST(client) [client not supplied]");
        return(0);
    }

    debug("Informing the client that we are a UNIX L8 machine");
    sendClient($client, "211 UNIX L8");
    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_CWD
# DESCRIPTION : Process the ftp CWD command
# ARGUMENTS   : String(client), String(path)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_CWD {
    my ($client, $path) = @_;
    debug("Entering ftp_CWD()");

    # Options for the CWD are:
    #  CWD  -> No options - stay here
    #  CWD  -> path/to/dir (relative - UNIX style)
    #  CWD  -> path\to\dir (relative - Win32 style)
    #  CWD  -> /path/to/dir (absolute - UNIX style)
    #  CWD  -> \path\to\dir (absolute - Win32 style)

    # Make sure we get the client
    unless ($client) {
        error("api", "ftp_CWD usage: ftp_CWD(client, dir) [client not supplied]");
        return(0);
    }

    # Make sure we get the dir
    unless ($path) {
        error("api", "ftp_CWD usage: ftp_CWD(client, dir) [dir not supplied]");
        return(0);
    }

    my $curdir = cwd();
    my $newdir;

    # It's an absolute path
    if (substr ($path, 0, 1) eq "/") {
        debug("The client requested an absolute path.");
    }

    # XXX - Need to add some additional bounds checking somewhere around
    # here on the received path. Perhaps there should be a configuration
    # option on where clients can go to.

    # Try and chdir() there
    if ( chdir($path) ) {
        sendClient($client, "212 CWD command successful");
        return(1);
    } else {
        sendClient($client, "212 $path: no such file or directory");
        return(0);
    }

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_HELP
# DESCRIPTION : Process the ftp HELP command
# ARGUMENTS   : String(client), String(details)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : The String(details) is the second half of the help command, as
#             : in HELP SITE or HELP PWD.
#             : XXX - Needs more work here.
# -----------------------------------------------------------------------------
sub ftp_HELP {
    my ($client, $details) = @_;
    debug("Entering ftp_HELP()");

    # Make sure we get the client
    unless ($client) {
        error("api", "ftp_HELP usage: ftp_HELP(client) [client not supplied]");
        return(0);
    }

    # If there are no details, then they want general help
    # If there are details, send them that specific help
    if ( ! $details ) {
        $helpmsg  = "214-HELP\n";
        $helpmsg .= "214-\n";
        $helpmsg .= "214-COMMANDS IMPLEMENTED:\n";
        $helpmsg .= "214-\n";
        $helpmsg .= "214-  PWD CWD TYPE MODE SITE\n";
        $helpmsg .= "214";
    }  else {
        SWITCH: for ($details) {
            /[Ss][Ii][Tt][Ee]/ && do {
                    $helpmsg  = "214-SITE DETAILS\n";
                    $helpmsg .= "214-\n";
                    $helpmsg .= "214-SITE EDI => Sets the server in EDI processing mode\n";
                    $helpmsg .= "214-SITE STD => Sets the server in STANDARD (normal) mode\n";
                    $helpmsg .= "214-\n";
                    $helpmsg .= "214";
                    last SWITCH;
            };

            /[Pp][Ww][Dd]/ && do {
                    $helpmsg  = "214-PWD DETAILS\n";
                    $helpmsg .= "214-\n";
                    $helpmsg .= "214-Prints working directory\n";
                    $helpmsg .= "214";
                    last SWITCH;
            };

            $helpmsg  = "214-ERROR\n";
            $helpmsg .= "214-\n";
            $helpmsg .= "214-No details for $details\n";
            $helpmsg .= "214";

        }; # SWITCH END
    }

    sendClient($client, $helpmsg);

    return(1);
}

# -----------------------------------------------------------------------------
# NAME        : ftp_QUIT
# DESCRIPTION : Process the ftp QUIT command
# ARGUMENTS   : String(client)
# RETURNS     : True or False
# STATUS      : Experimental
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_QUIT {
    my ($client) = @_;
    debug("Entering ftp_QUIT()");

    unless ($client) {
        error("api", "ftp_QUIT usage: ftp_QUIT(client) [client not supplied]");
        return(0);
    }

    sendClient($client, "221 Service closing control connection");

    # XXX- Add some additional code somewhere here to free resources
    # up and maybe even set some variables back to some sane defaults.
    killClient($client);

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_LIST
# DESCRIPTION : Process the ftp LIST command
#             : This command lists the contents of a directory in short format
# ARGUMENTS   : String(client), String(data)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_LIST {
    my ($client, $data) = @_;
    debug("Entering ftp_LIST()");

    unless ($client) {
        error("api", "ftp_LIST usage: ftp_LIST(client) [client not supplied]");
        return(0);
    }

    unless ($data) {
        error("api", "ftp_LIST usage: ftp_LIST(client) [data not supplied]");
        return(0);
    }

    # Simply pass the call to the NLST method
    debug("Passing the call on to ftp_NLST");
    ftp_NLST($client, $data) || return(0);

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
sub ftp_NLST {

    # What we could get:
    # NLST <argument> => -l, -la, etc.
    # NLST <empty>    => Current dir
    # NLST file       => List a single file
    # NLST *          => Wildcard: *, *.dat, ls.*, dd?.12?, etc.
    # NLST dir/       => Directory listing
    # NLST dir/file   => File in a directory

    my ($client, $rest) = @_;

    unless ($client) {
        error("api", "ftp_NLST usage: ftp_NLST(client, rest) [client not supplied]");
        return(0);
    }

    my $host = $client{hostaddress};
    my $port = $client{hostport};
    my $path;

    my ( $cmd, $data ) = split(' ', $rest);

    # What we received was an argument
    if ( substr($data, 0, 1) eq "-" ) {
        $arg = substr($data, 1, 1);
        if ( $arg eq "l" ) {
            print "Received request for long listing.\n";
            @listing = _long_list( cwd() );

            print "Opening and sending data to client.\n";
            if ( $client{type} eq "A" ) {$mode = "ASCII"};
            if ( $client{type} eq "I" ) {$mode = "Binary"};

            sendClient($client, "150 Opening $mode mode data connection for directory listing.");

            my $sock = open_active_data_connection($client{hostaddress}, $client{hostport});

            unless($sock) {
                sendClient($client, "425 Can't open data connection.");
                return(0);
            }

            $sock->print(@listing);

            $sock->close;
            sendClient($client, "226 Transfer complete.");
            return(1);
        }
    }

    # <empty> option, current dir
    if ( $path eq "" ) {
        if ( ! opendir( DIR, cwd() ) ) {
            print "Opening of dir failed: ($!).\n";
            sendClient($client, "456 Directory listing failed.");
            return(0);
        } else {
            @files = readdir(DIR);
            print "Opening and sending data to client.\n";
            sendClient($client, "150 Opening data connection for file listing.");
            my $sock = open_active_data_connection($client{hostaddress}, $client{hostport});
            unless($sock) {
                sendClient($client, "425 Can't open data connection.");
                return(0);
            }
            foreach $file ( @files ) {
                next if (/^\n/);
                next if (/^\r/);
                next if $file eq ".";
                next if $file eq "..";
                $sock->print($file . "\r\n");
            }
            $sock->close;
            sendClient($client, "226 Transfer complete");
            return(1);
        }
    }
}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub _long_list {
    my $dir = shift;
    my @data;

    # Listing format
    # drwxr-xr-x   3 administ 544             0 Aug 27 19:51 younghome.com

    if ( ! opendir(DIR, $dir) ) {
        print "Unable to open dir: $dir.\n";
        return(0);
    } else {
        @files = readdir(DIR);
        push(@data, "total $#files\r\n");
        foreach $file ( @files ) {
            next if $file eq ".";   # Skip current dir
            next if $file eq "..";  # Skip parent dir
            my ($dev,$ino,$mode,$nlink,$uid,$gid,$rdev,$size,$atime,$mtime,$ctime,$blksize,$blocks) = stat($file);
            push(@data, sprintf("%s%s%s%s%s%s%s%s%s %-6s %-6s %-10s %-12s %-10s\r\n",
                            ($mode & 0400 ? 'r' : '-'),
                            ($mode & 0200 ? 'w' : '-'),
                            ($mode & 0100 ? 'x' : '-'),
                            ($mode & 040 ? 'r' : '-'),
                            ($mode & 020 ? 'w' : '-'),
                            ($mode & 010 ? 'x' : '-'),
                            ($mode & 04 ? 'r' : '-'),
                            ($mode & 02 ? 'w' : '-'),
                            ($mode & 01 ? 'x' : '-'),
                            getuid($uid),
                            getgid($gid),
                            $size,
                            timefmt($mtime),
                            $file));
        }
        closedir(DIR);
        print @data;
        return @data;
    }
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
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub ftp_STOR {
    my $client = shift;
    my $cmd = shift;

    my ($t, $file) = split(' ', $cmd);

    # client{edi} is automatically assumed when a doctype has been issued
    # Changed since kleinschmidt can't handle multiple site commands
    if ( $client{edi} ) {
        if ( ! $client{doctype} ) {
            print "Client has to set SITE DOCTYPE first.\n";
            sendClient($client, "452 You must set the SITE DOCTYPE first.");
            return(0);
        }
        print "Processing inbound file as an XMLEDI file\n";
    } else {
        print "NOT processing inbound file as an XMLEDI file\n";
    }

    # ASCII transfer mode
    if ( $client{type} eq "A" ) {
        sendClient($client, "150 Opening data connection for $file");

        my $sock = open_active_data_connection($client{hostaddress}, $client{hostport});
        unless($sock) {
            sendClient($client, "425 Can't open data connection.");
            return(0);
        }

        open(FILE, ">$file");
        print "Retrieving file as ASCII.\n";
        print "Writing file $file\n";
        while ( $_ = $sock->getline() ) {
            print FILE $_;
        }
        $sock->close;
        close(FILE);
        print "Done writing file $file\n";
    } else {
        sendClient($client, "425 Not transferring file (bad transfer mode $client{type}).");
        return(0);
    }

    # If SITE EDI has been issued, this will be set
    # Remember again this is assumed when a site doctype has been set
    if ( $client{edi} ) {
        # Enable or disable debugging of the XML parsing layer here
        XMLEDI->debug(1);

        # Create a new XMLEDI object based of the file they just gave us
        # The transfer has to be completed for this to happen
        my $xmledi = XMLEDI->new($file);

        # Get the document header, type and rev
        # Make sure they are sending a supported type
        $xmledi->getDocInfo();
        my $doctype = $xmledi->getDocType();

        # Duh, you asked for one but sent another?
        if ( $doctype ne $client{doctype} ) {
            sendClient($client, "451 You set SITE DOCTYPE to $client{doctype} but sent a $doctype?.");
            return(0);
        }

        # Chunk down into the 214
        if ( $doctype eq "214" ) {
            print "Received a 214 document, good.\n";

            # Get the transmission header & detail
            print "Retrieving transmission info\n";
            $xmledi->getTransInfo();                    # The header
            $sender = $xmledi->getTransSender();        # SCAC code
            print "SENDER: $sender\n";

            # Get the shipment header & detail
            print "Retrieving shipment info\n";
            $xmledi->getShipmentHeader();               # The header
            $pronumber = $xmledi->getProNumber();       # Their PRO number
            $bolnumber = $xmledi->getBolNumber();       # Our BOL number
            $ponumber = $xmledi->getPoNumber();         # Customer PO number
            print "PRO Number: $pronumber\n";
            print "BOL Number: $bolnumber\n";
            print "PO Number: $ponumber\n";

            # Open a connection to the database via the dbmod module
            $dbo = dbmod->new();
            $dbh = $dbo->dbConnect();
            unless($dbh) {
                die "Failed to connect to the database\n";
            } else {
                print "Connected to database\n";
            }

            # Make sure they sent us a valid order
            # If not, tell them and delete the file
            if ( $dbo->isValidShipment($dbh, $bolnumber) ) {
                print "Shipment validated, it's been booked already\n";
            } else {
                print "They sent us a status for a shipment that doesn't exist\n";
                sendClient($client, "455 Invalid shipment $bolnumber");
                unlink($file);
                return(0);
            }

            # Grab the status
            print "Retrieving status info\n";

            # Create a new status object, see that one for more details
            # basically, it's a reference to an object
            $stat = $xmledi->getAllStatuses();
            print "XML STATUS CODE: " . $stat->{code} . "\n";
            print "XML DATE: " . $stat->{date} . "\n";
            print "XML TIME: " . $stat->{time} . "\n";

            # Place the file in its permananet location
            # Should be /ediroot/filestor/carrierdata/<scac>/<bolnumber>/<type>-<date>.xml
            # Where:
            #   scac = the sender's scac code
            #   bolnumber = our bol number
            #   type = 204 or 214
            #   date = epoch date
            # Example:
            #   /ediroot/filestor/carrierdata/RDWY/32445/214-72033245.xml
            $now = time();
            $permfile = "/ediroot/xmlstor/carrierdata/$sender/$bolnumber/214-$now.xml";

            # Store the status data in the database
            # The only fields we need right now are
            # shipmentid, statusdetails, statuscode, xmlstore, statustime
            $dbo->writeStatusToDB($dbh, $bolnumber, $stat->{code}, '3', $permfile, $stat->{date} . " " . $stat->{time});

            # Check and see if the directory for the carrier exists
            # /ediroot/xmlstor/carrierdata/$sender/
            # If not, create it
            if ( ! -d "/ediroot/xmlstor/carrierdata/$sender/" ) {
                unless ( mkdir("/ediroot/xmlstor/carrierdata/$sender/") ) {
                    warn("Unable to create /ediroot/xmlstor/carrierdata/$sender");
                    sendClient($client, "425 Not transferring file (failed to create target directory for $sender).");
                    return(0);
                }
            }

            # Check and see if the directory for the bol number
            # /ediroot/xmlstor/carrierdata/$sender/
            # If not, create it
            if ( ! -d "/ediroot/xmlstor/carrierdata/$sender/$bolnumber/" ) {
                unless ( mkdir("/ediroot/xmlstor/carrierdata/$sender/$bolnumber/") ) {
                    warn("Unable to create /ediroot/xmlstor/carrierdata/$sender/$bolnumber");
                    sendClient($client, "425 Not transferring file (failed to create target directory for $bolnumber).");
                    return(0);
                }
            }

            # Rename the file to it's permanent location
            # This will get rid of the temp file they sent us ($file)
            rename($file, $permfile) || warn "Unable to rename file ($!)\n";

            # Close the db connection
            if ( $dbo->dbClose($dbh) ) {
               print "Closed database connection\n";
            } else {
                die "Failed to close database connection\n";
            }

            # Tell the client all done
            sendClient($client, "226 File store complete. Data connection has been closed.");
        } else {
            print "Received a $doctype document (not supported) informing client.\n";
            sendClient($client, "451 Specifed XMLEDI type not supported.");
            return(0);
        }
    } else {
        sendClient($client, "226 File store complete. Data connection has been closed.");
        return(1);
    }
}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub ftp_PWD {
    my ($client) = @_;

    sendClient($client, "257 \"" . cwd() ."\" is current directory" );
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
sub ftp_XPWD {
    my ($client) = @_;

    ftp_PWD($client);
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
sub ftp_TYPE {
    my $client = shift;
    my $data = shift;

    # Extract the type from the supplied param
    # Client will send TYPE I or TYPE A
    my ($cmd, $type) = split(' ', $data);

    if ( $type eq "I" ) {
        # Binary type
        $client{type} = "I";
        print "Setting TYPE I per client request\n";
        sendClient($client, "224 Type set to $type.");
        return(1);
    } elsif ( $type eq "A" ) {
        # ASCII type
        $client{type} = "A";
        print "Setting TYPE A per client request\n";
        sendClient($client, "224 Type set to $type.");
        return(1);
    } else {
        # Invalid / unsupported type
        print "Client requested invalid type of $type\n";
        sendClient($client, "150 Invalid type $type.");
        return(0);
    }
}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub ftp_SITE {
    my $client = shift;
    my $cmd = shift;

    # Break down the command
    # Format is SITE <type> <rest>
    my ( $site, $sitecmd, $rest ) = split(' ', $cmd);

    SITECMD: for ( $sitecmd ) {
        # SITE EDI
        # Enabled EDI message parsing, pretty much deprecated now
        # since KS can't take multiple site commands
        /[Ee][Dd][Ii]/
            && do {
                print "Received request for SITE EDI\n";
                sendClient($client, "200 Command okay, received files will be treated as XMLEDI messages.");
                $client{edi} = 1;
                $retval = 1;
                last SITECMD;
        };
        # SITE STD
        # Turns EDI mode off so we won't parse inbound docs
        /[Ss][Tt][Dd]/
            && do {
                print "Received request for SITE STD\n";
                sendClient($client, "200 Command okay, EDI mode turned off.");
                $client{edi} = 0;
                $retval = 1;
                last SITECMD;
        };
        # SITE STATUS
        # Gives the client a report on what's going on
        /[Ss][Tt][Aa][Tt][Uu][Ss]/
            && do {
                print "Received request for SITE STATUS\n";

                $msg  = "214-CONNECTION STATUS\r\n";
                $msg .= "214-\r\n";
                $msg .= "214-EDI INFORMATION\r\n";
                if ( $client{edi} ) {
                    $msg .= "214-EDI -> Yes\r\n";
                } else {
                    $msg .= "214-EDI -> No\r\n";
                }
                if ( $client{doctype} ) {
                    $msg .= "214-DOCTYPE -> $client{doctype}\r\n";
                } else {
                    $msg .= "214-DOCTYPE -> Not set\r\n";
                }
                $msg .= "214-\r\n";
                $msg .= "214-CLIENT INFORMATION\r\n";
                $msg .= "214-USERNAME -> $client{username}\r\n";
                $msg .= "214 \r\n";

                sendClient($client, $msg);
                $retval = 1;
                last SITECMD;
        };
        # SITE DOCTYPE
        # sets the doctype for the inbound EDI transfer
        /[Dd][Oo][Cc][Tt][Yy][Pp][Ee]/
            && do {
                print "Received request for SITE DOCTYPE\n";
                print "Parsing mode $rest\n";
                $client{edi} = 1;
                # Here's the chunk I removed to handle bug id 12
                # if ( ! $client{edi} ) {
                #     sendClient($client, "452 Set SITE EDI first.");
                #     $retval = 1;
                #     last SITECMD;
                # }
                # Traverse into the message and determine the exact mode

                if ( $rest eq "214" ) {
                    print "DOCTYPE set to 214\n";
                    $doctype = $rest;
                    $client{doctype} = $doctype;
                    sendClient($client, "200 Command okay, DOCTYPE set to 214.");
                    $retval = 1;
                    last SITECMD;
                } elsif ( $rest eq "204" ) {
                    $client{doctype} = undef;
                    sendClient($client, "452 DOCTYPE 204 is not currently supported.");
                    $retval = 1;
                    last SITECMD;
                } else {
                    print "Received invalid DOCTYPE $rest\n";
                    sendClient($client, "452 DOCTYPE $rest is not supported.");
                    $retval = 1;
                    last SITECMD;
                }
            return($retval);
            last SITECMD;
        };
        return($retval);
        last SITECMD;
    };
    return($retval);
    last SITECMD;
}


# -----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# -----------------------------------------------------------------------------
sub ftp_PORT {
    my ($client, $portset) = @_;
    debug("Entering ftp_PORT");

    # The arguments to PORT are a1,a2,a3,a4,p1,p2 where a1 is the
    # most significant part of the address (eg. 127,0,0,1) and
    # p1 is the most significant part of the port.
    my ($a1, $a2, $a3, $a4, $p1, $p2) = split(',', $portset);

    # Construct the host address
    my $hostaddr = "$a1.$a2.$a3.$a4";
    $client{hostaddress} = $hostaddr;

    # Construct port number.
    my $hostport = $p1 * 256 + $p2;
    $client{hostport} = $hostport;

    debug("Reponses will be sent to $client{hostaddress}:$client{hostport}");
    sendClient($client, "200 PORT command OK.");

    debug("LEAVING ftp_PORT()");
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
sub open_active_data_connection {
    my ($host, $port) = @_;
    my $sock;
    debug("Entering open_active_data_connection");

    debug("open_active_data_connection: Opening connection to $host:$port");
        $sock = new IO::Socket::INET->new ( PeerAddr => $host,
                                        PeerPort => $port,
                                        Proto => "tcp",
                                        Reuse => 1) or return undef;

    return $sock;
}



# -----------------------------------------------------------------------------
# NAME        : ftp_SMNT
# DESCRIPTION : Unknown
# ARGUMENTS   : String(rest)
# RETURNS     : True
# STATUS      : Stable
# NOTES       : Don't know what it's for, it's not in the RFC
#             : but Microsoft clients seem to want it alot.
#             : Hmm, another deviation for MS$ :-(
# -----------------------------------------------------------------------------
sub ftp_SMNT {
    my ($rest) = @_;
    debug("Entering ftp_SMNT");

    # Not a very useful command.
    sendClient("500 Command not implemented.");
    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : ftp_UNKNOWN
# DESCRIPTION : Handles received commands that the server is clueless about
# ARGUMENTS   : String(client), String(command)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub ftp_UNKNOWN {
    my ($client, $cmd ) = @_;
    debug("Entering ftp_UNKNOWN");

    sendClient($client, "502 Command not implemented");

    unless ($client) {
        error("api", "ftp_UNKNOWN usage: ftp_UNKNOWN(client, command) [client not supplied]");
        return(0);
    }

    unless ($cmd) {
        error("api", "ftp_UNKNOWN usage: ftp_UNKNOWN(client, command) [command not supplied]");
        return(0);
    }

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : sendClient
# DESCRIPTION : Sends a message to a client
# ARGUMENTS   : String(client), String(message)
# RETURNS     : True or False
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub sendClient {
        my ($client, $msg) = @_;
        debug("Entering sendClient()");


        # Make sure they give us a client to send to
        unless ($client) {
                error("api", "sendClient usage: sendClient(client, message) [client not supplied]");
                return(0);
        }

        # Make sure they give us a message to send
        unless ($message) {
                error("api", "sendClient usage: sendClient(client, message) [message not supplied]");
                return(0);
        }

        # Have to add the \r \n to the end of the message
        debug("Sending message $msg to client");
        print $client $msg . "\r\n";

        return(1);
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
        debug("Entering reloadServer");

        debug("Reloading server");
        return(1);
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
    print "Shutting down listener\n";

    # XXX - Not working correctly, see why later.
    #        $lsn->close;
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
    debug("Entering daemonize");

    debug("Trying to fork()");
    defined(my $pid = fork)         or die "Can't fork: $!";

    exit if $pid;

    debug("Doing a setsid");
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
    print "Usage: EDIServer.pl <options>\n";
    print "   Options:\n";
    print "      -u   user to run as\n";
    print "      -g   group to run as\n";
    print "      -d   debug mode, won't fork\n";
    print "      -h   help\n";
}


# -----------------------------------------------------------------------------
# NAME        : debug
# DESCRIPTION : Sends a properly formatted message to the correct debug stram
# ARGUMENTS   : String(message)
# RETURNS     : True or False
# STATUS      : Experimental
# NOTES       : None
# -----------------------------------------------------------------------------
sub debug {
    my ($message) = @_;
    unless ($message) {
        error("api", "debug usage: debug(message) [message not given]");
        return(0);
    }

    # STDOUT could be changed to a trace file if necessary
    # Could be useful when running in the background
    print STDOUT localtime(time()) . " " . __PACKAGE__ . " DebugMessage: $message\n";
    return(1);
}


sub error {
    my ($type, $message) = @_;
    debug("Entering error()");

    unless ($type) {
        print "error usage: error(type, message) [type not supplied]\n";
    }

    unless ($message) {
        print "error usage: error(type, message) [message not supplied]\n";
    }

    SWITCH : for ($type) {
        /^api/                && do {
                print STDOUT localtime(time()) . " " . __PACKAGE__ . " API ERROR: $message\n";
                return(1);
                last SWITCH;
        };

        /^info/                && do {
                print STDOUT localtime(time()) . " " . __PACKAGE__ . " INFO ERROR: $message\n";
                return(1);
                last SWITCH;
        };

        /^critical/        && do {
                print STDOUT localtime(time()) . " " . __PACKAGE__ . " CRITICAL ERROR: $message\n";
                return(1);
                last SWITCH;
        };

        /^fatal/        && do {
                print STDOUT localtime(time()) . " " . __PACKAGE__ . " FATAL ERROR: $message\n";
                exit(1);
                last SWITCH;
        };
    };
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


# ----------------------------------------------------------------------------
#
# ChangeLog
#
# $Log: EDIServer.pl,v $
# Revision 1.1  2002/10/20 20:26:27  youngd
# new
#
# Revision 1.1.1.1  2002/07/13 04:30:35  youngd
# initial import
#
# Revision 1.27  2002/02/18 16:38:22  youngd
# laptop update
#
# Revision 1.26  2002/02/16 02:29:27  youngd
# converted back to UNIX format
#
# Revision 1.25  2002/02/16 02:28:35  youngd
# added changelog
#
# ----------------------------------------------------------------------------
