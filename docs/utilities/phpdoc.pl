#!/usr/bin/perl
# =============================================================================
#
# phpdoc.pl
#
# PHP Documentation Extration / Generation Utility
#
# $Id: phpdoc.pl,v 1.2 2002/10/16 13:37:12 youngd Exp $
#
# Contents Copyright (c) 2001-2002, YoungHome.Com, Inc.
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# Usage:
#
# =============================================================================
#
# ChangeLog:
#
# $Log: phpdoc.pl,v $
# Revision 1.2  2002/10/16 13:37:12  youngd
#   * First version.
#
# Revision 1.1  2002/10/16 13:02:13  youngd
#   * Initial version for development.
#
# =============================================================================

$name = "$0";

$cvsid = '$Id: phpdoc.pl,v 1.2 2002/10/16 13:37:12 youngd Exp $';
@cvsinfo = split(' ', $cvsid);
$version = $cvsinfo[2];


# -----------------------------------------------------------------------------
#                                 B E G I N 
# -----------------------------------------------------------------------------
BEGIN {

	# Pragmas to use
	use warnings;
	use strict;


	# Standard modules to use
	use Getopt::Long;
	use Getopt::Std;
	use Config::Simple;


	# Local modules to use
	use err;
	use log;
	use sigs;

}


# -----------------------------------------------------------------------------
#                       G L O B A L   V A R I A B L E S
# -----------------------------------------------------------------------------
$debug			= 0;
$verbose		= 0;
$config_file	= "phpdoc.cfg";


# -----------------------------------------------------------------------------
#                       P A R S E   C O M M A N D   L I N E
# -----------------------------------------------------------------------------
GetOptions ( "debug"             => \$debug,
             "verbose"           => \$verbose,

             "config-file=s"     => \$config_file,
             "logfile=s"         => \$logfile,

             "about"             => sub { print_about(); exit },
             "help"              => sub { print_usage(); exit },
             "version"           => sub { print_version(); exit },

           );



# -----------------------------------------------------------------------------
#                         P A R S E  C O N F I G   F I L E 
# -----------------------------------------------------------------------------
# If it's defined, it means they gave it to us on the command line
if ( defined($config_file) ) {

	$config_file = $config_file;

    if ( -f $config_file ) {
		debug("Processing config file $config_file per command line");
		$cfg = new Config::Simple(filename=>$config_file)
			or die "Unable to open config file $config_file($!)\n";
    }

} else {

	# Take the default
	$config_file = "harvest.cfg";

	if ( -f $config_file ) {
		debug("Processing default config file $config_file");
		$cfg = new Config::Simple(filename=>$config_file)
			or die "Unable to open config file $config_file ($!)\n";
	}

}




# -----------------------------------------------------------------------------
#                            S C R I P T   M A I N 
# -----------------------------------------------------------------------------
$numfiles = $#ARGV + 1;

# Make sure they gave us some file names to process
debug("Checking to see if they gave us file names to process");
if ( $numfiles == 0 ) {
	print "\n";
	print "ERROR: No file specified on the command line!\n";
	print "\n";
	print "Try --help for more information if you're having problems\n";
	print "\n";
	exit(1);
} else {
	debug("Processing $numfiles files");
}


# Iterate through each file and process it
for ($i = 0; $i < $numfiles ; $i++) {
	debug("Entering main file process iterator");
	print "Processing: $ARGV[$i]\n";

	if ( ! -f $ARGV[$i] ) {
		print "File $ARGV[$i] does not exist\n";
	} else {
		docify($ARGV[$i]);
	}
}


exit(0);






























# -----------------------------------------------------------------------------
#                              F U N C T I O N S 
# -----------------------------------------------------------------------------

# -----------------------------------------------------------------------------
# NAME        : print_version
# DESCRIPTION : Display the program's version
# ARGUMENTS   : None
# RETURN      : 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub print_version {
    debug("print_version(): entering print_version()");
    print "$name version $version\n";

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : print_about
# DESCRIPTION : Display the program's about information
# ARGUMENTS   : None
# RETURN      : 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub print_about {
    debug("print_about(): entering print_about()");
    print "$name version $version\n";
    print "Darren Young [darren_young\@yahoo.com]\n";

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : print_usage
# DESCRIPTION : Display the program's usage
# ARGUMENTS   : None
# RETURN      : 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub print_usage {
    debug("print_usage(): entering print_usage()");
    print "\n";
    print "Usage: $name <options> <php_file>\n";
    print "\n";
    print "Options:\n";
    print "   --help                 Display help (this screen).\n";
    print "   --version              Display program version.\n";
    print "   --about                Display more information about the program.\n";
	print "\n";
    print "   --debug                Enable debugging.\n";
    print "   --verbose              Enable verbose debugging.\n";
    print "   --config               Path to the configuration file.\n";
    print "   --logfile              Path to the log file.\n";
	print "\n";

    return(1);
}


# -----------------------------------------------------------------------------
# NAME        : docify
# DESCRIPTION : The actual PHP doc creator function
# ARGUMENTS   : String filesname
# RETURN      : 0 or 1
# STATUS      : Development
# NOTES       : None
# -----------------------------------------------------------------------------
sub docify {

	my $file = shift;

	debug("docify(): entering with file $file");

	unless ($file) {
		syntax("Param file not passed to docify");
	}

	debug("docify(): opening file $file");
	open(FILE, "< $file") || die "Unable to open file $file ($!)\n";


	debug("docify(): iterating through file");
	while(<FILE>) {
		chop();

		next if ! /^#/;

		if ($_ =~ "NAME") {
			my ($left, $right) = split(':', $_);

			print "NAME: $right\n";
		}
	}


	debug("docify(): closing file $file");
	close(FILE) || die "Unable to close file $file ($!)\n";
	
	return(1);

}