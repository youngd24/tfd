# =============================================================================
#
# ShipmentStatus
#
# Shipment Status object model and methods
#
# $Id: ShipmentStatus.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# =============================================================================

package XMLEDI::ShipmentStatus;

BEGIN {

     my $VERSION = '1.00';

     our(@ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

     use Exporter;

     my @EXPORT    = qw( );
     my @EXPORT_OK = qw( );

}

my $VERSION = '1.00';

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub new {

     XMLEDI::debugMsg(__PACKAGE__ . " => Entering new()");

     # Handles $ob = new ShipmentStatus or $ob = ShipmentStatus->new()
     my $this  = shift;
     my $file  = shift;
     my $class = ref($this) || $this;
     my $self  = {};

     bless $self, $class;

     $self->_initialize();

     return $self;

}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub _initialize {
     my $self = shift;

     XMLEDI::debugMsg(__PACKAGE__ . " => Entering _initialize()");

     $self{code}         = undef;
     $self{reason}       = undef;
     $self{city}         = undef;
     $self{state}        = undef;
     $self{equipment}    = undef;
     $self{equipmentnum} = undef;
     $self{date}         = undef;
     $self{time}         = undef;
     $self{timezone}     = undef;
     $self{notes}        = undef;

     return ($self);
}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub DESTROY {
     my $self = shift;

     XMLEDI::debugMsg(__PACKAGE__ . " => Entering DESTROY()");

     $self->_destroy();

     return (1);
}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub _destroy {
     my $self = shift;

     XMLEDI::debugMsg(__PACKAGE__ . " => Entering _destroy()");

     return (1);
}

1;
