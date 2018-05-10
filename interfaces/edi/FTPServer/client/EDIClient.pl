#!/usr/bin/perl
# ==============================================================================
#
# EDIClient.pl
#
# Perl based EDI client
#
# $Id: EDIClient.pl,v 1.1 2002/10/20 20:28:48 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# ==============================================================================

$debug = 0;

# Our package name
package EDIClient;

# Our name
$name = "Digiship EDI FTP Client";

# Version information
$cvsid   = '$Id: EDIClient.pl,v 1.1 2002/10/20 20:28:48 youngd Exp $';
@cvsinfo = split (' ', $cvsid);
$version = $cvsinfo[2];

BEGIN {

     # Standard modules
     use Net::FTP;
     use Getopt::Long;
     use Config::General;
     use Carp;
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
$debug     = "0";
$passive   = "0";
$hash      = "0";
$doctype   = "214";

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
$debug     = $config{debug};
$passive   = $config{passive};
$hash      = $config{hash};
$doctype   = $config{doctype};

# Process the command line
# these take over the config file options
GetOptions(
     "debug"         => \$debug,
     "help"          => \$opt_h,
     "config-file=s" => \$cfgfile,
     "doctype=s"     => \$doctype,
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
debug("DocType   : $doctype");

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

$ftp->site("EDI") || die "Unable to set EDI mode\n";

$ftp->site("DOCTYPE 214") || die "Unable to set DOCTYPE\n";

$ftp->put("$xmldoc") || die "Unable to transfer document\n";

$ftp->quit || die "Quit failed: $!\n";

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
sub debug {
     my $msg = shift;

     if ($debug) {
          print "DEBUG: $msg\n";
          return (1);
          } else {
          return (1);
     }
}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub print_usage {
     print "Usage: EDIClient <options> <transaction type>\n";
     print "Where <options> are:\n";
     print "   --help        Help (this screen)\n";
     print "   --version     Displays the program version\n";
     print "   --about       Prints version, copyright and author info.\n";
     print "   --docytpe     Set the document type for the transfer\n";
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
     print "Copyright (c) 2000-2002, Digiship Corp.\n";
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
