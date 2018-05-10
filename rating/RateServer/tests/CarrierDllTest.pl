# ===================================================================
# 
# DLLEST.PL
#
# Perl :-) script to test the DigishipRate OLE object
#
# Contents Copyright (c) 2000, Digiship Corp.
#
# $Id: CarrierDllTest.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
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

use Carp;

# Basic parameters, we know what these should produce

# Create a new instance of the object and return a handle
$ex = Win32::OLE->new('DigiCarrier.CarrierTransit') || die "Unable to create a new DigiCarrier object (error: $!)\n";

# Format for GetTransitDays
# SCAC code, Type, orgzip, dstzip
$days = $ex->GetTransitDays('RDWY', 'LTL', '60601', '45345') || die $!;

print $days;
