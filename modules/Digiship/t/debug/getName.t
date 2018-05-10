#!/usr/bin/perl
# ============================================================================
#
# getName.t
#
# Test script for Digiship::Debug::getName
#
# $Id: getName.t,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000-2002 Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# ============================================================================

use lib qw(/digiship/modules);
use Digiship::Debug qw(getName);

if ( $Debug = Digiship::Debug->new() ) {
} else {
	print "not ok\n";
	exit(1);
}

if ( $Debug->Digiship::Debug::getName() ) {
	print "ok\n";
	exit(0);
} else { 
	print "not ok\n";
	exit(1);
}
