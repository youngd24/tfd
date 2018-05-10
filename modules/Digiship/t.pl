#!/usr/bin/perl
# ============================================================================
#
# t.pl
#
# Test harness for Digiship::Debug and Digiship::Error
#
# $Id: t.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# ============================================================================

$| = 1;

$passed = 0;
$failed = 0;

print "\n";
print "**********************************\n";
print "** Starting Debug & Error Tests **\n";
print "**********************************\n";
print "\n";

# ----------------------------------------------------------------------------
# Add the standard module location to @INC
print "Adding /digiship/modules to \@INC\n";
use lib qw(/digiship/modules);


# ----------------------------------------------------------------------------
# Try to bring in the modules
print "Loading modules\n";
use Digiship::Debug;
use Digiship::Error;

# ----------------------------------------------------------------------------
print "Create new Digiship::Debug object ";
if ( $Debug = Digiship::Debug->new() ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


# ----------------------------------------------------------------------------
print "Create new Digiship::Error object ";
if ( $Error = Digiship::Error->new() ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


# ----------------------------------------------------------------------------
# Try to call the level setter methods
print "Calling Debug->setDebugLevel(1) ";
if ( $Debug->setDebugLevel(1) ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


# ----------------------------------------------------------------------------
print "Calling Error->setErrorLevel(1) ";
if ( $Error->setErrorLevel(1) ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


# ----------------------------------------------------------------------------
print "\n";
print "***************************\n";
print "** Digiship::Error Tests **\n";
print "***************************\n";

print "Calling Error->getName() ";
if ( $name = $Error->getName() ) {
	print "[OK] Got: \"$name\"\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}

print "Calling Error->getVersion() ";
if ( $version = $Error->getVersion() ) {
	print "[OK] Got: \"$version\"\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


print "Calling Error->printError(general): ";
if ( $Error->printError("general", " ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


print "Calling Error->printError(api): ";
if ( $Error->printError("api", " ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


print "Calling Error->printError(critical): ";
if ( $Error->printError("critical", " ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


print "Calling Error->printError(fatal): ";
if ( $Error->printError("fatal", " ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}



print "Calling Error->generalError(): ";
if ( $Error->generalError(" ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


print "Calling Error->apiError(): ";
if ( $Error->apiError(" ") ) { 
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}

print "Calling Error->criticalError(): ";
if ( $Error->criticalError(" ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}

print "Calling Error->fatalError(): ";
if ( $Error->fatalError(" ") ) { 
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


# ----------------------------------------------------------------------------
print "\n";
print "***************************\n";
print "** Digiship::Debug Tests **\n";
print "***************************\n";

print "Calling Debug->getName() ";
if ( $name = $Debug->getName() ) {
	print "[OK] Got: \"$name\"\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}

print "Calling Debug->getVersion() ";
if ( $version = $Debug->getVersion() ) {
	print "[OK] Got: \"$version\"\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}

print "Calling Debug->print(): ";
if ( $Debug->print("Debug->print() ") ) {
	print "[OK]\n";
	$passed++;
} else {
	print "[FAILED]\n";
	$failed++
}


print "\n";
print "All Done ($passed passed, $failed failed)\n";



exit(0);


__END__
