# ===================================================================
# 
# DLLEST.PL
#
# Perl :-) script to test the DigishipRate OLE object
#
# Contents Copyright (c) 2000, Digiship Corp.
#
# $Id: DllTest.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Darren Young
# youngd@digiship.com
#
# ===================================================================
#
# Change History:
#
# December 8, 2000 - 1.0
# 	* Initial working version
#
# ===================================================================
#
# Usage:
#
# Set the parameters to values that you know the results of
# Run the script and view the results. You can automate this testing
# by calling this script then examinine its exit status.
#
# This will only work on Windows, not a chance on UNIX 'cause of OLE
#
# ===================================================================
#
# Todo:
# 
# Add additional tests against the Win32::OLE API as we figure out
# how they actually work.
#
# ===================================================================
#
# Bugs:
#
# I'm sure there are some but haven't appeared yet :-)
#
# ===================================================================
#

# Gotta have 5.002 of the Perl interpreter
require 5.002;

# Try to pull in the entire OLE module
BEGIN {
	if ( ! eval "require Win32::OLE" ) {
		die "Unable to load Win32::OLE module\n";
	} else {
		use Win32::OLE;
	}
}

# Basic parameters, we know what these should produce
$srczip="01505";
$dstzip="60614";
$class="55";
$weight="10000";
$expected_rate = "1107";

# Create a new instance of the object and return a handle
$ex = Win32::OLE->new('DigishipRate.RateShipment') || die "Unable to create a new DigishipRate object\n";

# Rate a shipment with known parameters
# Remember the order of the method arguments!
$tested_rate = $ex->RateShipment($srczip,$dstzip,$class,$weight) || die Win32::OLE->LastError();

print $tested_rate . "\n";
exit(0);

# Test the rate
if ( $tested_rate == -1 ) {
	print "Error rating (-1 returned)\n";
	exit(1);
} elsif ( $tested_rate ne $expected_rate ) {
	print "Didn't receive the expected results.\n";
	print "We asked for: $expected_rate\n";
	print "We received: $tested_rate\n";
	undef $ex;
	exit(1);
} elsif ( $tested_rate eq $expected_rate ) {
	print "Basic rate test successful.\n";
	print "We asked for: $expected_rate\n";
	print "We received: $tested_rate\n";
	undef $ex;
	exit(0);
} else {
	print "Something really bad happened during the test.\n";
	undef $ex;
	exit(1);
}

