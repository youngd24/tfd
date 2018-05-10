# =========================================================================
#
# =head1 NAME
#
# ConfigReader.pl
#
# =head1 HOMEPAGE
#
# http://intranet.digiship.com/~youngd/perl/ConfigReader.html
#
# =head1 ABSTRACT
#
# Config file reader module
#
# =head1 COPYRIGHT
#
# Contents Copyright (c) 2000, Digiship Corp.
#
# =head1 VERSION
#
# $Id: RateClient.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
# 
# =head1 AUTHOR
#
# Darren Young
# youngd@digiship.com
#
# =========================================================================
# 
# =head1 DESCRIPTION
#
# Description:
# 
#    ConfigReader is an Object Oriented (OO) interface to read, parse and 
#    retrieve values from configuration files. Multiple files may be acted 
#    upon by simply creating new objects that reference the physical file.
#    To conserve disk read hits, the initial method used to access the file
#    reads its contents into an array in memory. From there all the values
#    are read from that array. While this will increase the overhead of the 
#    process in memory, it will conserve disk hits over time. Some people
#    hate memory usage, some hate disk usage. This script was designed 
#    with the idea that it will be running on machines with large amounts
#    of memory and very active disks.
#
# =========================================================================
#
# = head1 USAGE
#
# Usage:
#
#    use ConfigReader
#
#    String (result)    = ConfigReader->new()
#    String (result)    = ConfigReader->debug(String level)
#    String (result)    = ConfigReader->setFileName(String filename)
#    String (filename)  = ConfigReader->getFileName()
#    String (result)    = ConfigReader->readFile(String filename)
#    String (result)    = ConfigReader->dumpFile();
#
# =========================================================================
#
# =head1 EXAMPLES
#
# Examples:
#
#
# =========================================================================
#
# =head1 CHANGELOG
#
# Change History:
#
#    December 9, 2000 - 0.1 - DAY
#        Initial version
#
# =========================================================================
# 
# =head1 BUGS
#
# Bugs:
#
#   Has not been tested yet
#
# =========================================================================
#
# =head1 TODO
#
# Todo:
#
#   Allow 2 different modes of operation, first is the native, in memory
#   configFile read. The second is to open the configFile when the object
#   is created and close it on the DESTROTY() method. Not sure what the 
#   advantages and disadvantages are yet. This needs to be added then
#   tested for performance and system utilization.
#
# =========================================================================
#
# Our package declaration
# Children will be known as ConfigReader::<child>
package RateClient;

# Version declaration
# To require an explicit version in the calling program, code it as
# use ConfigReader <version>
our $VERSION = '1.0';

# Strict is a _good_ thing, it'll prevent us from making stupid mistakes
# This in addition to the run-time -w switch makes it almost impossible
# to miss syntax and declaration errors
use strict;

# Carp lets us spit out useful debug information like line number and 
# the location in the caller where the user had a problem
use Carp;

use IO::Socket;
use IO::Select;

# Debug initializer, don't adjust this, use CLASSNAME->debug(level) 
# See the example above for more info
my $dbg = 0;

# Sometimes it is useful to override TRUE and FALSE
my $TRUE = 1;
my $FALSE = 0;


# Name: new
# Description: Constructor for object creation
# Needs: String Object, String name
# Returns: Object reference
#
sub new {
	my $this = shift;
	my $remote = shift;
	my $class = ref($this) || $this;
	my $self = {};

	if ( $dbg ) { carp "New object created" }

	# We go ahead an initialize the values that we use
	$self->{REMOTE}	= $remote;
	$self->{CONNECTED}	= $FALSE;

	if ( $remote eq "" ) { 
		die "Usage: CLASSNAME->new(RemoteServer)\n";
		return($FALSE);
	} else {
		bless($self, $class);
		return $self;
	}
}


# Name: DESTROY
# Description: Descructor for the object (Perl mandated)
# Needs: Nothing (it's called by the Perl garbage collector)
# Returns: Nothing
#
sub DESTROY {
	my $self = shift;

	if ( $dbg ) { carp "Destroying self" }
	return $TRUE;
}


# Name: Debug
# Description: Turns on debugging
# Needs: Debug value (0 or 1)
# Returns: Result code
#
sub Debug {
	my $self = shift;

	if (ref($self)) {
		warn "Class method called as object method";
		return $FALSE;
	}
	unless (@_ == 1) {
		warn "Usage: CLASSNAME->debug(level)";
		return $FALSE;
	}
	carp "Setting debug to on";
	$dbg = shift;
	return $TRUE;
}


# Name: Connect
# Description: Connects to a remote server
# Needs: Nothing
# Returns: Result code
#
sub Connect {
	my $self = shift;

	if ( $dbg ) { carp "Starting Connect() method" };
	$self->{SOCK} = new IO::Socket::INET(PeerAddr => $self->{REMOTE},
				                      PeerPort => '4979',
				                         Proto => 'tcp',);

	if ( $self->{SOCK} ) {
		$self->{ERROR} = "Connect OK";
		$self->{CONNECTED} = $TRUE;
		return($TRUE)
	} else {
		$self->{ERROR} = "Couldn't connect to $self->{REMOTE}";
		$self->{CONNECTED} = $FALSE;
		return($FALSE)
	}
}

sub lastError {
	my $self = shift;

	print $self->{ERROR} . "\n";
	return($TRUE);
}


# Name: isConnected
# Description: Determines if a remote is connected
# Needs: Nothing
# Returns: Result code
#
sub isConnected {
	my $self = shift;

	if ( $dbg ) { carp "Starting isConnected() method" }

	if ( $self->{CONNECTED} == $TRUE ) {
		return($TRUE);
	} elsif ( $self->{CONNECTED} == $FALSE ) {
		return($FALSE);
	} else {
		return($FALSE);
	}
}


sub Shutdown {
	my $self = shift;

	if ( $dbg ) { carp "Starting Shutdown() method" }

	if ( $self->isConnected() ) {
		if ( $dbg ) { carp "Telling remote we're done" }

		if ( $dbg ) { carp "Shutting down socket" }
		$self->{SOCK}->shutdown(2);
	}
}






1;
# The 1; has to appear at the end for the module to work
