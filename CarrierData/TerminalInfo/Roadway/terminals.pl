#!/usr/bin/perl
# =============================================================================
#
# terminals.pl
#
# Script to process the TermProfile.txt file
#
# $Id: terminals.pl,v 1.4 2002/10/28 21:13:12 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: terminals.pl,v $
# Revision 1.4  2002/10/28 21:13:12  youngd
# updates
#
# Revision 1.3  2002/10/28 21:02:39  youngd
#   * Updates
#
# Revision 1.2  2002/10/22 23:27:53  youngd
#   * Modifications
#
# Revision 1.1  2002/10/22 22:51:32  youngd
#   * Initial version.
#
# =============================================================================

$file = "TermProfile.txt";

open(TERMFILE, "<$file") || die "Unable to open $file ($!)\n";


while(<TERMFILE>) {

    # Get these values from TermFileLayout.doc in this directory
    $scac     = substr($_, 0, 4);
    $termname = substr($_, 4, 50);
    $termcode = substr($_, 54,5);
    $addr1    = substr($_, 59, 50);
    $addr2    = substr($_, 109, 50);
    $city     = substr($_, 159, 30);
    $state    = substr($_, 189, 2);
    $zip      = substr($_, 191, 10);
    $phone    = substr($_, 201, 12);
    $tollfree = substr($_, 213, 12);
    $fax      = substr($_, 225, 12);
    $email    = substr($_, 237, 100);
    $contname = substr($_, 337, 50);
    $conttit  = substr($_, 387, 50);

    # Remove any trailing whitespace and/or newlines
    chomp($scac);
    chomp($termname);
    chomp($termcode);
    chomp($addr1);
    chomp($addr2);
    chomp($city);
    chomp($state);
    chomp($zip);
    chomp($phone);
    chomp($tollfree);
    chomp($fax);
    chomp($email);
    chomp($contname);
    chomp($conttit);


#    print "SCAC: $scac\n";
#    print "TERMNAME: $termname\n";
#    print "TERMCODE: $termcode\n";
#    print "ADDR1: $addr1\n";
#    print "ADDR2: $addr2\n";
#    print "CITY: $city\n";
#    print "STATE: $state\n";
#    print "ZIP: $zip\n";
#    print "PHONE: $phone\n";
#    print "TOLLFREE: $tollfree\n";
#    print "FAX: $fax\n";
#    print "EMAIL: $email\n";
#    print "CONTACT NAME: $contname\n";
#    print "CONTACT TITLE: $conttit\n";

#   print "\n";


    #$csv = "10000," . printf("%03s", $termcode) . ",$city,$state,$zip,$phone,$tollfree,$fax\n";

    chop($termcode);
    chop($termcode);

    chop($zip);
    chop($zip);
    chop($zip);
    chop($zip);
    chop($zip);

    print "10000,$termcode,$city,$state,$zip,$phone,$tollfree,$fax\n";



}


close(TERMFILE);
