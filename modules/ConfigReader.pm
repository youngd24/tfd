# =========================================================================
#
# ConfigReader.pm
#
# Config file reader module
#
# $Id: ConfigReader.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
# 
# Darren Young
# youngd@digiship.com
#
# =========================================================================

# Our package declaration
package ConfigReader;

# Our version, grab it from the CVS ID
my $cvsid   = '$Id: ConfigReader.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $';
my @cvsinfo = split (' ', $cvsid);
my $version = $cvsinfo[2];


use strict;
use Carp;

my $dbg = 0;

# Sometimes it is useful to override TRUE and FALSE
my $TRUE = 1;
my $FALSE = 0;



# ----------------------------------------------------------------------------
# NAME        : new
# DESCRIPTION : Constructor for object creation
# ARGUMENTS   : String Object, String name
# RETURNS     : Object reference
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub new {
	my $this = shift;
	my $class = ref($this) || $this;
	my $self = {};

	if ( $dbg ) { carp localtime(time()) . " ConfigReaderDebug: New object created" }

	# We go ahead an initialize the values that we use
	$self->{NAME}		= undef;     # Our name
	$self->{CONFIGFILE}	= undef;     # Config file
	$self->{FILEHANDLE} 	= undef;     # File read handle
	$self->{PARAM}		= undef;     # Retrieved parameter
	$self->{VALUE}		= undef;     # Retrieved value
	$self->{DATA_ARRAY}	= ();	     # Array file is read to
	$self->{CONFIGHASH}	= ();        # Hash used to hold the keys and values
	$self->{CONFIGKKEY}	= "";        # Scalar that holds the looked up key
	$self->{CONFIGVAL}	= "";        # The value of the previous key

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

	if ( $dbg ) { carp localtime(time()) . " ConfigReaderDebug: Destroying self" }
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
	if ( $dbg ) { carp localtime(time()) . " ConfigReaderDebug: Set filename to " . $self->{CONFIGFILE} }
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
	if ( $dbg ) { carp localtime(time()) . " ConfigReaderDebug: readFile() called" }
	if ( $configFile eq "" ) { 
		carp "You have to give me a filename";
		return $FALSE;
	} else {
		open(FILEHANDLE, "<$self->{CONFIGFILE}") || die "Unable to open configuration file $configFile ($!)\n";
		while(<FILEHANDLE>) {

			# ignore blank lines and comments
 			next if /^\s*$/ || /^#/;

			chop($_);
			($configKey, $configVal) = split('=', $_);
			$configVal =~ s/^\s*//;
			$configVal =~ s/\s*$//;
			if ( $dbg ) { carp "configVal = $configVal\n"; }
			$configHash{$configKey} = $configVal;
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
	carp localtime(time()) . " ConfigReader: Setting debug to on";
	$dbg = shift;
	return $TRUE;
}


1;

__END__


=head1 NAME

ConfigReadger - Configuration File Reader

=head1 SYNOPSIS

use ConfigReader;

ConfigReader is an Object Oriented (OO) interface to read, parse and 
retrieve values from configuration files. Multiple files may be acted 
upon by simply creating new objects that reference the physical file.
To conserve disk read hits, the initial method used to access the file
reads its contents into an array in memory. From there all the values
are read from that array. While this will increase the overhead of the 
process in memory, it will conserve disk hits over time. Some people
hate memory usage, some hate disk usage. This script was designed 
with the idea that it will be running on machines with large amounts
of memory and very active disks.

=head1 DESCRIPTION

Description

=head1 USAGE

=over 4

=item ConfigReader->new(filename)

Creates a new instance of the module and assign the filename as the config
file to read and parse.

=item ConfigReader->debug(level)

Use this to set the debug level of the current instance. Valid values are
0 or 1.

=item ConfigReader->getFileName()

Retruns the name of the configuration file that is currently tied to the 
instance.

=item ConfigReader->readFile();

Reads and parses the contents of the configuration file and stores the result
in the calling array.

=item ConfigReader->dumpFile();

Returns a raw dump of the configuration file currently in use.

=back

=head1 CONFIGURATION FILE FORMAT

The files that ConfigReader will read and parse should be in simple
name = value pairs. Comments may be inserted with pound (#) signs.
After parsing the configuration file, the name portions of the file
will be assigned to global variables.

=head1 EXAMPLES

use ConfigReader;

# Create a new instance
$reader = ConfigReader->new();

# Turn debugging on
$reader->debug(1);

# Read and parse the file
%config = $reader->readFile('myapp.conf');

# Grab the values and assign them to variables
foreach $key ( sort( keys %config ) ) {
    SWITCH: for ( $key ) {
        /^name/ && do  {
                            $name = $config{$key};
                            last SWITCH;
		};
	}
}

=cut
