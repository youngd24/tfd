#!/usr/local/bin/perl
# =============================================================================
#
# edi-dispatch.pl
#
# Script to determine inbound edi document types and dispatch them to 
# an appropriate sub-processor.
#
# $Id: edi-dispatch.pl,v 1.10 2003/01/07 19:27:45 youngd Exp $
#
# Contents Copyright (c) 2002, 2003 The Freight Depot
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# Usage:
#
#      edi-dispatch.pl <file> --debug
#
# =============================================================================
#
# ChangeLog:
#
# $Log: edi-dispatch.pl,v $
# Revision 1.10  2003/01/07 19:27:45  youngd
#   * Changed use of log to logging.
#   * Removed use sigs.
#
# Revision 1.9  2003/01/03 19:02:42  youngd
#   * Added section header documentation.
#   * Reworked config file usage. Now uses the standard one in /edi/.
#   * Moved all variables to their own location like most of the other
#     edi scripts have.
#   * Added use of local logging and error modules from /tfd/modules.
#   * Started changing debug prints to use the debug() function from log.pm
#
# Revision 1.8  2003/01/03 18:32:59  youngd
#   * Added --test command line option and test on it before the system call
#   * Added use Config::Simple for the config file parsing.
#
# Revision 1.7  2003/01/02 21:00:54  youngd
#   * Added a hash to store the various processors in.
#   * Changed all references to processors to use the newly added hash.
#   * Added some additional debug statements to dump processor information.
#
# Revision 1.6  2003/01/02 20:26:44  youngd
#   * Added the name of the script to the debug output.
#
# Revision 1.5  2003/01/02 20:13:30  youngd
#   * Moved the --debug flag to the end of the line (after the file name)
#   * Changed the path of the 214 processor to be /edi/bin/edi-214.pl
#
# Revision 1.4  2003/01/02 19:22:07  youngd
#   * Added $doc->dispose() statements to reduce memory consumption
#     during background operation.
#
# Revision 1.3  2003/01/02 19:19:51  youngd
#   * Added additional comments.
#
# =============================================================================

$name = "edi-dispatch.pl";

$cvsid = '$Id: edi-dispatch.pl,v 1.10 2003/01/07 19:27:45 youngd Exp $';
@cvsinfo = split(' ', $cvsid);
$version = $cvsinfo[2];



# -----------------------------------------------------------------------------
#                               B E G I N 
# -----------------------------------------------------------------------------

BEGIN {

    use lib '/tfd/modules';

    use warnings;
    use strict;

    use Getopt::Long;
    use Config::Simple;
    use XML::DOM;

    use logging;
    use err;

}


# -----------------------------------------------------------------------------
#                              V A R I A B L E S 
# -----------------------------------------------------------------------------
$configfile = "/edi/edi.cfg";
$debug      = 0;
$test       = 0;

# Change this to be $ARGV[0] in production
# Set it to test.xml in development
$file  = $ARGV[0];

# Specific edi processors for each doctype
%processors = (
                '214' => 'edi-214.pl',
                '210' => 'edi-210.pl',
                '990' => 'edi-990.pl',
                '997' => 'edi-997.pl',
               );



# ------------------------------------------------------------------------------
#                   C O M M A N D   L I N E   O P T I O N S
# ------------------------------------------------------------------------------

# Get the command line options
GetOptions ( "debug" => \$debug,
             "test"  => \$test,

             "config-file" => \$configfile,
           );


# ------------------------------------------------------------------------------
#                          C O N F I G   F I L E
# ------------------------------------------------------------------------------

if ( defined($configfile) ) {

    if ( -f $configfile ) {
        debug("$name: Processing config file $configfile");
        $cfg = new Config::Simple(filename=>$configfile) or die "Unable to open config file $configfile($!)\n";
    } else {
        error("$name: Unable to open config file $configfile");
        exit(0);
    }

}


# Config params
$edibinpath = $cfg->param("edi.bin");




# ------------------------------------------------------------------------------
#                          V A R I O U S   C H E C K S
# ------------------------------------------------------------------------------

# Make sure they gave us a file to process
if ( ! $file ) {
    print "$name: You have to tell me what file to process\n";
    print "$name: Usage: edi-process.pl --debug <file>\n";
    exit(0);
}

if ( ! -f $file ) {
    error("$name: unable to open xml file $file for processing");
    exit(0);
}

if ( $debug ) {
    print "$name: Debug enabled\n";
}

if ( $test ) {
    print "$name: Running in test mode\n";
}


# ------------------------------------------------------------------------------
#                                   M A I N 
# ------------------------------------------------------------------------------

# Create a new XML parser
$parser = new XML::DOM::Parser;

# Parse the file
$doc = $parser->parsefile($file);

# Find the xmledi element (should be JUST 1)
$edidocs = $doc->getElementsByTagName("xmledi");
$numedidocs = $edidocs->getLength();

# There should be only 1 xmledi element in the document
if ( $numedidocs == 1 ) {

    # Zero in on the first element (0)
    $edielem = $edidocs->item(0);

    # Get the doctype attribute of this element
    $editype = $edielem->getAttribute("doctype");

    # The doctype attribute of the xmledi element HAS to be set to something
    if ( $editype ) {

        # Dispatch the appropriate sub-processor based on the edi doctype
        SWITCH: for ($editype) {

            # The 214 status message processor
            /214/ && do {

                $processor = $processors{214};
                print "$name: Running 214 processor ($processor) on file $file from $edibinpath\n" if $debug;

                # Build up the command to run on the xml file
                if ( $debug ) {
                    @args = ("$edibinpath/$processor", "$file", "--debug");
                } else {
                    @args = ("$edibinpath/$processor", "$file");
                }

                unless ( $test ) {
                    system(@args);
                }

                # Remember to divide the return val ($?) by 256 to get the "real" exit value
                # You could always shift the bits as well (<< 8) if you really want to
                $exit_value  = $? / 256;

                # The exit should be 0, if not the system call failed.
                if ( $exit_value == 0 ) {

                    print "$name: 214 processor ($processor) ran fine on file $file\n" if $debug;
                    $doc->dispose();
                    exit(0);

                } else {

                    print "$name: 214 processor ($processor) ran badly with exit of $exit_value\n" if $debug;
                    print "$name: args were: @args\n" if $debug;
                    $doc->dispose();
                    exit(1);

                }


            }; # End 214 processor


            # Add other inbound processors here


            # Catch all statement to cover unknown types
            /^*/ && do {
                print "$name: Unknown document type ($editype) encountered\n";
                $doc->dispose();
                exit(1);
            };

        } # End SWITCH statement

    } else {

        print "$name: The doctype attribute of the xmledi element was not set. Puke";
        $doc->dispose();
        exit(1);

    }

} else {

    print "$name: There were more than 1 xmledi elements, malformed document. Puke\n";
    $doc->dispose();
    exit(0);

}

exit(0);
