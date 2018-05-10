#!/usr/bin/perl
# ============================================================================
#
# new.t
#
# Test script for Digiship::Debug::new
#
# $Id: new.t,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000-2002 Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# ============================================================================

use lib qw(/digiship/modules);
use Digiship::Debug;

if ( $Debug = Digiship::Debug->new() ) {
	print "ok\n";
	exit(0);
} else {
	print "not ok\n";
	exit(1);
}

