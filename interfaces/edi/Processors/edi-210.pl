#!/usr/local/bin/perl
# =============================================================================
#
# edi-210.pl
#
# EDI 210 Processor
#
# $Id: edi-210.pl,v 1.2 2003/02/05 19:00:21 youngd Exp $
#
# Contents Copyright (c) 2002-2003, The Freight Depot
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# Usage:
#
# =============================================================================
#
# ChangeLog
#
# $Log: edi-210.pl,v $
# Revision 1.2  2003/02/05 19:00:21  youngd
#   * Added database section.
#
# Revision 1.1  2003/02/05 18:57:03  youngd
#  * Initial version.
#
# =============================================================================

$name = "edi-210.pl";

$cvsid = '$Id: edi-210.pl,v 1.2 2003/02/05 19:00:21 youngd Exp $';
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
    use Config::Simple;
    use Log::Log4perl;
    use Getopt::Long qw(GetOptions);
    use POSIX qw(strftime setsid);
    use Carp;

    # Local modules to pull in
    use edi;
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