#!/usr/bin/perl
# ==============================================================================
#
# EDIClient.pl
#
# Perl based EDI client
#
# $Id: EDIClient.pl,v 1.3 2003/01/07 23:02:47 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# ==============================================================================

# Our name
$name = "EDI FTP Client";

# Version information
$cvsid   = '$Id: EDIClient.pl,v 1.3 2003/01/07 23:02:47 youngd Exp $';
@cvsinfo = split (' ', $cvsid);
$version = $cvsinfo[2];

BEGIN {

     # Standard modules
     use Net::FTP;
     use Getopt::Long;
     use Config::General;
     use Carp;

     use lib '/tfd/modules';
     use logging qw(verbose debug);
     use err;
     use edi;
}

# Global variables
# See the config file for more details
$cfgfile   = "client.cfg";
$xmldoc    = "";
$host      = "localhost";
$user      = "";
$pass      = "";
$firewall  = "";
$blocksize = "10240";
$port      = "21";
$timeout   = "120";
$passive   = "0";
$hash      = "0";

# Process the config file
open(CFGFILE, "<$cfgfile") || die "Unable to open $cfgfile: $!\n";
close(CFGFILE);

$conf   = new Config::General($cfgfile);
%config = $conf->getall();

# Set variables based on the config file
$host      = $config{host};
$user      = $config{user};
$pass      = $config{pass};
$firewall  = $config{firewall};
$blocksize = $config{blocksize};
$port      = $config{port};
$timeout   = $config{timeout};
$passive   = $config{passive};
$hash      = $config{hash};

# Process the command line
# these take over the config file options
GetOptions(
     "debug"         => \$debug,
     "help"          => \$opt_h,
     "config-file=s" => \$cfgfile,
     "version"       => \$opt_v,
     "about"         => \$opt_a
);

if ($opt_h) {
     print_usage();
     exit(0);
}

if ($opt_a) {
     print_about();
     exit(0);
}

if ($opt_v) {
     print_version();
     exit(0);
}

debug("Config file set to $cfgfile");

debug("Setting DOCTYPE to $doctype");

if ($ARGV[0]) {
    $xmldoc = $ARGV[0];
    debug("Transfer file is $xmldoc");
} else {
    print_usage();
    exit(1);
}

debug("Firewall  : $firewall");
debug("BlockSize : $blocksize");
debug("Port      : $port");
debug("Timeout   : $timeout");
debug("Passive   : $passive");
debug("Hash      : $hash");

# Get to work
$ftp = Net::FTP->new(
     "$host", Debug => $debug,
     Firewall  => $firewall,
     BlockSize => $blocksize,
     Port      => $port,
     Timeout   => $timeout,
     Passive   => $passive,
     Hash      => $hash) || die "Connect failed: $!\n";

$ftp->login("$user", "$pass") || die "Login failed: $!\n";

$ftp->cwd("/ediroot/incoming") || die "Change dir failed: $!\n";

$ftp->put("$xmldoc") || die "Unable to transfer document\n";

$ftp->quit || die "Quit failed: $!\n";







exit(0);

# ----------------------------------------------------------------------------
#                              F U N C T I O N S
# ----------------------------------------------------------------------------

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub print_usage {
     print "Usage: EDIClient <options> <xmledi_file>\n";
     print "Where <options> are:\n";
     print "   --help        Help (this screen)\n";
     print "   --version     Displays the program version\n";
     print "   --about       Prints version, copyright and author info.\n";
     return (1);
}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub print_about {
     print "\n";
     print "EDIClient version $version\n";
     print "Copyright (c) 2000-2003, The Freight Depot\n";
     print "Darren Young [darren_young\@yahoo.com]\n";
     print "\n";
     return (1);
}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub print_version {
     print "\n";
     print "EDIClient version $version\n";
     print "\n";
     return (1);
}

#__END__
