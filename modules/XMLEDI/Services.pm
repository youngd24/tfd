# ==============================================================================
#
# SERVICES.PM
#
# Digiship XMLEDI Services Module
#
# $Id: Services.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Services;

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &getNumServices );

    # Names to export on request
    @EXPORT_OK   = "";

}



# ====================================================================================
=head2 getNumServices()

    Description : Retrieves the total number of service entries in the file

    Needs       : String Object

    Returns     : String NumServices

    Notes       : None

=cut
# ------------------------------------------------------------------------------------
sub getNumServices {
        my $self = shift;

    return(1);
} # END getNumServices()



1;
