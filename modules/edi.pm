# =============================================================================
#
# edi.pm
#
# EDI Utility Module
#
# $Id: edi.pm,v 1.5 2003/01/09 22:08:58 youngd Exp $
#
# Contents Copyright (c) 2002-2003, The Freight Depot
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# Usage:
#
#   use edi;
#
# =============================================================================
#
# ChangeLog
#
# $Log: edi.pm,v $
# Revision 1.5  2003/01/09 22:08:58  youngd
#   * Removed the function I just added.
#
# Revision 1.4  2003/01/09 21:58:46  youngd
#   * Fixed a bug in the one I just commited.
#
# Revision 1.3  2003/01/09 21:57:49  youngd
#   * Added getCarrier204Status()
#
# Revision 1.2  2003/01/07 20:53:31  youngd
#   * Added getFilesInDir().
#   * Added more exports.
#
# Revision 1.1  2003/01/07 18:55:22  youngd
#   * Initial version
#
# =============================================================================


package edi;

my $cvsid = '$Id: edi.pm,v 1.5 2003/01/09 22:08:58 youngd Exp $';
my @cvsinfo = split(' ', $cvsid);
my $VERSION = @cvsinfo[2];


BEGIN {

    use strict;
    use warnings;
    use vars qw(@ISA @EXPORT @EXPORT_OK);
    use Exporter();
    use DBI;
    use Net::FTP;
    use POSIX qw(strftime setsid);
    use Config::Simple;
    use XML::DOM;

    use lib '/tfd/modules';
    use logging;
    use err;

    # Initialize some variables
    our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA = qw(Exporter);

    # Default names to export
    @EXPORT = qw(checkForFailed getEdiDocType getFilesInDir);

    # Names to export on request
    @EXPORT_OK = qw(checkForFailed getEdiDocType getFilesInDir);

}


# -------------------------------------------------------------------
#               G L O B A L   V A R I A B L E S
# -------------------------------------------------------------------






























# -----------------------------------------------------------------------------
# NAME        : checkForFailed
# DESCRIPTION : Checks the failed dir for old BOL's
# ARGUMENTS   : string(dir)
# RETURNS     : 0 or array(files)
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub checkForFailed {
    my $dir = shift;
    my @files;
    my @return;
    
    unless($dir) {
        print "You have to give me a directory to check!\n";
        return(0);
    }
    
    # Open the directory for reads
    if ( ! opendir(FAILQ, $dir) ) {
        print "Unable to open dir $dir ($!)\n";
        return(0);
    }
    
    # Grab the file list, remove the . and .. entries
    @files = grep !/^\.\.?$/, readdir(FAILQ);
    debug("checkForFailed: file list is @files");
    
    closedir(FAILQ);
    
    # If there are 0 entries, might as well go back
    if ( scalar(@files) == 0 ) {
        print "No failed transactions\n";
        return(0);
    }

    # Iterate through the file list and pop them onto the stack    
    foreach $file ( @files ) {
        push(@return, $file);
    }

    # Return the list of names of files that are failed    
    return(@return);
}


# -----------------------------------------------------------------------------
# NAME        : getFilesInDir
# DESCRIPTION : Returns the names of files in a given directory
# ARGUMENTS   : string(dir)
# RETURNS     : 0 or array(files)
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub getFilesInDir {
    my $dir = shift;
    my @files;
    my @return;
    
    unless($dir) {
        syntax("getFilesInDir(): you have to give me a directory to check!");
        return(0);
    }
    
    # Open the directory for reads
    if ( ! opendir(DIR, $dir) ) {
        print "Unable to open dir $dir ($!)\n";
        return(0);
    }
    
    # Grab the file list, remove the . and .. entries
    @files = grep !/^\.\.?$/, readdir(DIR);
    debug("getFilesInDir(): file list is @files");
    
    closedir(DIR);
    
    # If there are 0 entries, might as well go back
    if ( scalar(@files) == 0 ) {
        debug("getFilesInDir(): no files found in $dir, returning an empty array");
        @files = "";
        return(@files);
    }

    # Iterate through the file list and pop them onto the stack    
    foreach $file ( @files ) {
        push(@return, $file);
    }

    # Return the list of names of files that are failed    
    return(@return);
}



sub getEdiDocType {
    my $file = shift;

    # Because we don't want to pollute our environment :-)
    my $parser;
    my $doc;
    my $edidocs;
    my $numedidocs;
    my $edielem;
    my $editype;

    unless($file) {
        syntax("getEdiDocType(): you have to give me a file to check on!");
        return(0);
    }



    # Create a new XML parser
    $parser = new XML::DOM::Parser;

    # Parse the file
    debug("getEdiDocType(): parsing file $file");
    $doc = $parser->parsefile($file);

    # Find the xmledi element (should be JUST 1)
    $edidocs = $doc->getElementsByTagName("xmledi");
    $numedidocs = $edidocs->getLength();

    # There should be only 1 xmledi element in the document
    if ( $numedidocs == 1 ) {

        # Zero in on the first element (0)
        $edielem = $edidocs->item(0);

        # Get the doctype attribute of this element
        debug("getEdiDocType(): checking for the doctype attribute");
        $editype = $edielem->getAttribute("doctype");

        # The doctype attribute of the xmledi element HAS to be set to something
        if ( $editype ) {
            debug("getEdiDocType(): returning edi doctype of $editype");
            $doc->dispose();
            return($editype);
        } else {
            debug("getEdiDocType(): $file is an xmledi file but the doctype attribute is not set, puke.");
            $doc->dispose();
            return(0);
        }

    } else {
        debug("getEdiDocType(): There were more than 1 doctype attributes, puke.");
        $doc->dispose();
        return(0);
    }
}



1;