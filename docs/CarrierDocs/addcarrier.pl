#!/usr/bin/perl
# =============================================================================
#
# addcarrier.pl
#
# Script to add a carrier to the database
#
# $Id: addcarrier.pl,v 1.1 2002/09/27 04:44:42 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: addcarrier.pl,v $
# Revision 1.1  2002/09/27 04:44:42  youngd
# initial version
#
# =============================================================================

use DBI;


print "Carrier Name [text]: ";
$carrier_name = <STDIN>;
chop($carrier_name);

print "Carrier Discount [integer]: ";
$carrier_discount = <STDIN>;
chop($carrier_discount);

print "Carrier SCAC [4 char text]: ";
$carrier_scac = <STDIN>;
chop($carrier_scac);

print "Carrier Type [1|2|3]: ";
$carrier_type = <STDIN>;
chop($carrier_type);

print "Carrier Description [text]: ";
$carrier_description = <STDIN>;
chop($carrier_description);

print "Carrier Minimum [xx.xx]: ";
$carrier_minimum = <STDIN>;
chop($carrier_minimum);

print "Carrier CCSCAC [4 char text]: ";
$carrier_ccscac = <STDIN>;
chop($carrier_ccscac);

print "Carrier ORMargin [integer]: ";
$carrier_ormargin = <STDIN>;
chop($carrier_ormargin);


print "----------------------------------------\n";
print "CARRIER INFORMATION:\n";
print "\n";
print "NAME: $carrier_name\n";
print "DISCOUNT: $carrier_discount\n";
print "SCAC: $carrier_scac\n";
print "TYPE: $carrier_type\n";
print "DESCRIPTION: $carrier_description\n";
print "MINIMUM: $carrier_minimum\n";
print "CCSCAC: $carrier_ccscac\n";
print "ORMARGIN: $carrier_ormargin\n";
print "----------------------------------------\n";

exit(0);

