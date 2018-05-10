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
# $Id: ConfigReader.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
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
package ConfigReader;

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
# Status: Stable
#
sub new {
	my $this = shift;
	my $class = ref($this) || $this;
	my $self = {};

	if ( $dbg ) { carp "New object created" }

	# We go ahead an initialize the values that we use
	$self->{NAME}		= undef;		# Our name
	$self->{CONFIGFILE}	= undef;		# Config file
	$self->{FILEHANDLE} = undef;		# File read handle
	$self->{PARAM}		= undef;		# Retrieved parameter
	$self->{VALUE}		= undef;		# Retrieved value
	$self->{DATA_ARRAY}	= ();		# Array file is read to
	$self->{CONFIGHASH}	= ();
	$self->{CONFIGKKEY}	= "";
	$self->{CONFIGVAL}	= "";

	bless($self, $class);
	return $self;
}



sub getAllParams {
	my $self = shift;
	my $key;
	
	foreach $key (keys %{$self->{CONFIGHASH}}) {
	}

	return $TRUE;
}

sub getParam {
	my $self = shift;
	my $param = shift;

	return $TRUE;
}


# Name: DESTROY
# Description: Descructor for the object (Perl mandated)
# Needs: Nothing (it's called by the Perl garbage collector)
# Returns: Nothing
# Status: Stable
#
sub DESTROY {
	my $self = shift;

	if ( $dbg ) { carp "Destroying self" }
	return $TRUE;
}



# Name: setFileName
# Description: Sets the file to read values from
# Needs: String filename
# Returns: Result code
# Status: Deprecated
#
sub setFileName {
	my $self = shift;

	$self->{CONFIGFILE} = shift;
	if ( $dbg ) { carp "Set filename to " . $self->{CONFIGFILE} }
	return $TRUE;
}



# Name: getFileName
# Description: Gets the name of the file we are working with
# Needs: Nothing
# Returns: String filename
# Status: Stable
#
sub getFileName {
	my $self = shift;

	return $self->{CONFIGFILE};
}



# Name: readFile
# Description: Read the contents of the config file into an array
# Needs: String filename
# Returns: Result code
# Status: Stable
#
sub readFile {
	my $self = shift;
	my $configFile = shift;
	my (%configHash, $configKey, $configVal);

	keys(%configHash) = 128;

	$self->{CONFIGFILE} = $configFile;
	if ( $dbg ) { carp "readFile() called" }
	if ( $configFile eq "" ) { 
		carp "You have to give me a filename";
		return $FALSE;
	} else {
		open(FILEHANDLE, "<$self->{CONFIGFILE}") || die "Unable to open file\n";
		while(<FILEHANDLE>) {
			# Skip if it's a comment
			if ( substr($_, 0, 1) =~ '#' ) {
				next;
			# Skip if it's a blank newline
			} elsif ( substr($_, 0, 1) =~ '\n' ) {
				next;
			} else {
			# If it hasn't been thrown away, add it to the hash
				chop($_);
				($configKey, $configVal) = split('=', $_);
				$configHash{$configKey} = $configVal;
			}
		}
		close(FILEHANDLE);
		return %configHash;
	}
}



# Name: dumpFile
# Description: Dumps the array we got from the file
# Needs: Nothing
# Returns: Result code
# Status: Stable
#
sub dumpFile {
	my $self = shift;

	print @{$self->{DATA_ARRAY}};
	return $TRUE;
}



# Name: debug
# Description: Turns on debugging
# Needs: Debug value (0 or 1)
# Returns: Result code
# Status: Stable
#
sub debug {
	my $self = shift;

	if (ref($self)) {
		warn "Class method called as object method";
		return $FALSE;
	}
	unless (@_ == 1) {
		warn "usage: CLASSNAME->debug(level)";
		return $FALSE;
	}
	carp "Setting debug to on";
	$dbg = shift;
	return $TRUE;
}


1;
# The 1; has to appear at the end for the module to work
