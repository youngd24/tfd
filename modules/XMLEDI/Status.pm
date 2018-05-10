# ==============================================================================
#
# STATUS.PM
#
# Digiship XMLEDI Status Module
#
# $Id: Status.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Status;

# Module stars here
BEGIN {

     # Bring in so we can export names into the global namespace
     use Exporter;

     # Initialize some variables
     our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

     @ISA = qw(Exporter);

     # Default names to export
     @EXPORT = qw( &getNumStatus);

     # Names to export on request
     @EXPORT_OK = "";

}

# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub getNumStatus {
     my $self = shift;

     return (1);
}    # END getNumStatus()

1;
