# =============================================================================
#
# XMLWORK.PL
#
# File used to work out XML problems, this is not guaranteed to be good code.
#
# I use this file to work things out before they make it into the module
# for testing.
#
# $Id: xmlwork.pl,v 1.1 2003/01/07 20:47:04 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# =============================================================================
#

use lib '../../../modules';
use XMLEDI;

$file = 'xmledi.xml';

$xmledi = XMLEDI->new($file);
$xmledi->debug(1);

if ( ! -f $file ) {
    die "File $file doesn't exist\n";
}

# Get the document information
$xmledi->getDocInfo();

$numloc = $xmledi->getNumLocations();
$numparty = $xmledi->getNumParties();
$numprod = $xmledi->getNumProducts();
$cvsid = $xmledi->getVersion();
$doctype = $xmledi->getDocType();
$docrev = $xmledi->getDocRev();

print "You are using version $cvsid of this program\n";

print "The document type is: $doctype\n";

print "The version of the XML file in use is: $docrev\n";

print "There are $numloc locations in the file $file\n";

print "There are $numparty parties in the file $file\n";

print "There are $numprod products in the file $file\n";






# Overloaded here for testing purposes (we're not _actually_ using the module)
sub debugMsg {
    my $msg = shift;
    
    print STDERR $msg . "\n";
    return(1);
}



