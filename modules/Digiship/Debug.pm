# ==============================================================================
#
# Debug.pm
#
# Digiship Debugging Module
#
# $Id: Debug.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# ==============================================================================

# Our package declaration
package Digiship::Debug;

# Our name as it appears to other modules
my $name = 'Digiship Debugging Module';

# Our version, grab it from the CVS ID
my $cvsid   = '$Id: Debug.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $';
my @cvsinfo = split (' ', $cvsid);
my $version = $cvsinfo[2];

BEGIN {

     use strict;
     use warnings;
     use vars qw(@ISA @EXPORT @EXPORT_OK);
     use Exporter();
     use Sys::Hostname;

     use lib '/digiship/modules';

     # Initialize some variables
     our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

     @ISA = qw(Exporter);

     # Default names to export
     @EXPORT = qw(getName getVersion print);

     # Names to export on request
     @EXPORT_OK = qw(getName getVersion print);

}


# ----------------------------------------------------------------------------
# NAME        : new
# DESCRIPTION : Creates a new instance of the XMLEDI object
# ARGUMENTS   : String(object), String(file)
# RETURNS     : Object(self)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub Digiship::Debug::new {
     my $this  = shift;
     my $class = ref($this) || $this;
     my $self  = {};

     bless $self, $class;

     #$self->{'state'} = 'new';
     $self->initialize();

     return $self;
}


# ----------------------------------------------------------------------------
# NAME        : initialize
# DESCRIPTION : Initialize the modules to a known state
# ARGUMENTS   : String(object)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub Digiship::Debug::initialize {
     my $self = shift;

     unless ($self) {
          print "initialize->APIError: Class called without a reference\n";
          return (0);
     }

     $self->{'debugLevel'} = 0;
     $self->{'version'}    = $version;
     $self->{'name'}       = $name;
     $self->{'hostname'}   = hostname();
     $self->{'state'}      = 'initialized';

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : getName
# DESCRIPTION : Returns the name of the module
# ARGUMENTS   : String(object)
# RETURNS     : String(Name)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub Digiship::Debug::getName {
     my $self = shift;

     unless ($self) {
          print "getName->APIError: Class called without a reference\n";
          return (0);
     }

     return $self->{'name'};
}


# ----------------------------------------------------------------------------
# NAME        : getVersion
# DESCRIPTION : Returns the version of the module
# ARGUMENTS   : String(object)
# RETURNS     : String(Version)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub Digiship::Debug::getVersion {
     my $self = shift;

     unless ($self) {
          print "getVersion->APIError: Class called without a reference\n";
          return (0);
     }

     return($self->{'version'})
}


# ----------------------------------------------------------------------------
# NAME        : setDebugLevel
# DESCRIPTION : Sets the debug level mask for future operations
# ARGUMENTS   : String(object), String(level)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub Digiship::Debug::setDebugLevel {
     my $self  = shift;
     my $level = shift;

     unless ($self) {
          print "setDebugLevel->APIError: Class called without a reference\n";
          return (0);
     }

     if ($level == "") {
          $self->{'debugLevel'} = 0;
          return (1);
          } else {
          $self->{'debugLevel'} = $level;
          return (1);
     }
}


# ----------------------------------------------------------------------------
# NAME        : print
# DESCRIPTION : Prints a properly formatted debug message to file handle
#             : If no handle is given we default to STDERR
# ARGUMENTS   : String(object), String(Message)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub Digiship::Debug::print {
     my $self    = shift;
     my $message = shift;

     unless ($self) {
          print "print->APIError: Class called without a reference\n";
          return (0);
     }

     unless ($message) {
          print "print->APIError: Message not given\n";
          return (0);
     }

     my $time = localtime(time());

     if ($self->{'debugLevel'} == 0) {
          return (1);
     }

     if ($self->{'debugLevel'} == 1) {
          print "<" . $time . "> " . "<Debug> " . $message;
          return (1);
     }

     if ($self->{'debugLevel'} == 2) {
          return (1);
     }

     return (1);

}

1;

__END__

