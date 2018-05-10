# ==============================================================================
#
# Debugger.pm
#
# Freight Depot Debugging Module
#
# $Id: Error.pm,v 1.2 2002/10/20 20:17:50 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# ==============================================================================

# Our package declaration
package TFD::Error;

# Our name as it appears to other modules
my $name = 'Freight Depot Error Module';

# Our version, grab it from the CVS ID
my $cvsid   = '$Id: Error.pm,v 1.2 2002/10/20 20:17:50 youngd Exp $';
my @cvsinfo = split (' ', $cvsid);
my $version = $cvsinfo[2];

BEGIN {

     use lib '../';
     use strict;
     use warnings;
     use TFD::Debug;
     use Exporter;
     use Sys::Hostname;

     # Initialize some variables
     our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

     @ISA = qw(Exporter);

     # Default names to export
     @EXPORT = qw( &setErrorLevel
          &printError
          &generalError
          &apiError
          &criticalError
          &fatalError
          &getVersion
          &getName );

     # Names to export on request
     @EXPORT_OK = "";

}


# ----------------------------------------------------------------------------
# NAME        : new
# DESCRIPTION : Creates a new instance of the XMLEDI object
# ARGUMENTS   : String(object), String(file)
# RETURNS     : Object(self)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub new {
     my $this  = shift;
     my $class = ref($this) || $this;
     my $self  = {};

     bless $self, $class;

     $self->{'state'} = 'new';
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
sub initialize {
     my $self = shift;

     unless ($self) {
          print "initialize->APIError: Class called without a reference\n";
          return (0);
     }

     $self->{'errorLevel'} = 0;
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
sub getName {
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
sub getVersion {
     my $self = shift;

     unless ($self) {
          print "getVersion->APIError: Class called without a reference\n";
          return (0);
     }

     return $self->{'version'};
}


# ----------------------------------------------------------------------------
# NAME        : setErrorLevel
# DESCRIPTION : Sets the error level mask for future operations
# ARGUMENTS   : String(object), String(level)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub setErrorLevel {
     my $self  = shift;
     my $level = shift;

     unless ($self) {
          print "setErrorLevel->APIError: Class called without a reference\n";
          return (0);
     }

     if ($level == "") {
          $self->{'errorLevel'} = 0;
          return (1);
          } else {
          $self->{'errorLevel'} = $level;
          return (1);
     }

}


# ----------------------------------------------------------------------------
# NAME        : print
# DESCRIPTION : Prints a properly formatted debug message to STDERR
# ARGUMENTS   : String(object), String(type), String(Message)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub printError {
     my $self    = shift;
     my $type    = shift;
     my $message = shift;

     unless ($self) {
          print "printError->APIError: Class called without a reference\n";
          return (0);
     }

     unless ($type) {
          print "printError->APIError: Type not given\n";
          return (0);
     }

     unless ($message) {
          print "printError->APIError: Message not given\n";
          return (0);
     }

     TYPE: for ($type) {
          /general/ && do {
               if ($self->{'errorLevel'} == 1) {
                    print STDERR "<" . localtime(time()) . ">" . " <GeneralError> " . $message;
               }
               last TYPE;
          };

          /api/ && do {
               if ($self->{'errorLevel'} == 1) {
                    print STDERR "<" . localtime(time()) . ">" . " <APIError> " . $message;
               }
               last TYPE;
          };

          /critical/ && do {
               if ($self->{'errorLevel'} == 1) {
                    print STDERR "<" . localtime(time()) . ">" . " <CriticalError> " . $message;
               }
               last TYPE;
          };

          /fatal/ && do {
               if ($self->{'errorLevel'} == 1) {
                    print STDERR "<" . localtime(time()) . ">" . " <FatalError> " . $message;
               }
               last TYPE;
          };
     }
     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : generalError
# DESCRIPTION : Prints a general error message to STDERR
# ARGUMENTS   : String(object), String(message)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Development
# NOTES       : None
# ----------------------------------------------------------------------------
sub generalError {
     my $self    = shift;
     my $message = shift;

     unless ($self) {
          print "generalError->APIError: Class called without a reference\n";
          return (0);
     }

     unless ($message) {
          print "generalError->APIError: Message not given\n";
          return (0);
     }

     if ($self->{'errorLevel'} == 1) {
          print STDERR "<" . localtime(time()) . ">" . " <GeneralError> " . $message;
     }

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : apiError
# DESCRIPTION : Prints an API error message to STDERR
# ARGUMENTS   : String(object), String(message)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Development
# NOTES       : None
# ----------------------------------------------------------------------------
sub apiError {
     my $self    = shift;
     my $message = shift;

     unless ($self) {
          print "apiError->APIError: Class called without a reference\n";
          return (0);
     }

     unless ($message) {
          print "apiError->APIError: Message not given\n";
          return (0);
     }

     if ($self->{'errorLevel'} == 1) {
          print STDERR "<" . localtime(time()) . ">" . " <APIError> " . $message;
     }

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : criticalError
# DESCRIPTION : Prints a critical error message to STDERR
# ARGUMENTS   : String(object), String(message)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Development
# NOTES       : None
# ----------------------------------------------------------------------------
sub criticalError {
     my $self    = shift;
     my $message = shift;

     unless ($self) {
          print "criticalError->APIError: Class called without a reference\n";
          return (0);
     }

     unless ($message) {
          print "criticalError->APIError: Message not given\n";
          return (0);
     }

     if ($self->{'errorLevel'} == 1) {
          print STDERR "<" . localtime(time()) . ">" . " <CriticalError> " . $message;
     }

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : fatalError
# DESCRIPTION : Prints a fatal error message to STDERR
# ARGUMENTS   : String(object), String(message)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Development
# NOTES       : None
# ----------------------------------------------------------------------------
sub fatalError {
     my $self    = shift;
     my $message = shift;

     unless ($self) {
          print "fatalError->APIError: Class called without a reference\n";
          return (0);
     }

     unless ($message) {
          print "fatalError->APIError: Message not given\n";
          return (0);
     }

     if ($self->{'errorLevel'} == 1) {
          print STDERR "<" . localtime(time()) . ">" . " <FatalError> " . $message;
     }
     return (1);
}

1;

__END__
