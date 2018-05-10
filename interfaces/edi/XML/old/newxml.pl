# ====================================================================================
#
# XML PARSER
#
# $Id: newxml.pl,v 1.1 2003/01/07 20:47:04 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==================================================================================== 


BEGIN {
    if ( $^O =~ /Win32/ || $^O =~ /cygwin/ ) {
        # Windows, most flavors
        use Win32;
        $os = "WinNT" if Win32::IsWinNT();
        $os = "Win9x" if Win32::IsWin95();
        
        # Digiship modules
        use lib '../../../modules';
    } else {
        # UNIX
        $os = "UNIX";
        
        # Digiship modules
        # use lib '/usr/local/digiship/modules';
    }
}

print "Running on $os\n";

use XMLEDI;

print "Starting...\n";

# Module debugging, 0 = no, 1 = yes
XMLEDI->debug(0);

# Create a new XMLEDI object
$xmledi = XMLEDI->new('xmledi214.xml');

# Working with the transmission header
print "Retrieving transmission header\n";
$xmledi->getTransInfo();

print "Transmission date: "     . $xmledi->getTransDate() . "\n";
print "Transmission time: "     . $xmledi->getTransTime() . "\n";
print "Transmission sender: "   . $xmledi->getTransSender() . "\n";
print "Transmission receiver: " . $xmledi->getTransReceiver() . "\n";


# Working with the document header
print "Retrieving document header\n";
$xmledi->getDocInfo();

print "Document type: "     . $xmledi->getDocType() . "\n";
print "Document revision: " . $xmledi->getDocRev() . "\n";


# Shipment header information
print "Retrieving shipment header information\n";
$xmledi->getShipmentHeader();

print "PRO Number: "        . $xmledi->getProNumber() . "\n";
print "BOL Number: "        . $xmledi->getBolNumber() . "\n";
print "PO Number: "         . $xmledi->getPoNumber() . "\n";


# Working with parties
#
$numParties = $xmledi->getNumParties();
@parties = $xmledi->getPartyTypes();

print "There are $numParties parties in the document:\n";
$i = 1;
foreach $party ( @parties ) {
    print "  $i ->  " . $party . "\n";
    $i++;
}

# Party Details


# Working with shipment statuses
# Find out how many statuses are in the document
$numStatuses = $xmledi->getNumStatuses();
print "There are $numStatuses status message(s) in the document\n";

# Retrieve all the status messages
$stat = $xmledi->getAllStatuses();
print "Got code: " . $stat->{code} . "\n";
print "Got reason: " . $stat->{reason} . "\n";
print "Got city: " . $stat->{city} . "\n";
print "Got state: " . $stat->{state} . "\n";
print "Got equipment: " . $stat->{equipment} . "\n";
print "Got equipmentnum: " . $stat->{equipmentnum} . "\n";
print "Got date: " . $stat->{date} . "\n";
print "Got time: " . $stat->{time} . "\n";
print "Got timezone: " . $stat->{timezone} . "\n";
print "Got notes: " . $stat->{notes} . "\n";