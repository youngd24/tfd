#!/usr/bin/perl
#
use lib qw(/digiship/modules);

use Digiship::Debug;

$Debug = Digiship::Debug->new();

STDOUT = "/dev/null";
STDERR = "/dev/null";

$Debug->setDebugLevel(1);
if ( $Debug->print("Test\n") ) {
	STDOUT = "";
	STDERR = "";
	print "ok";
} else {
	STDOUT = "";
	STDERR = "";
	print "not ok";
}

exit;
