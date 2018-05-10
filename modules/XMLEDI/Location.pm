# ==============================================================================
#
# LOCATION.PM
#
# Digiship XMLEDI Location Module
#
# $Id: Location.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Location;

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &getNumLocations );

    # Names to export on request
    @EXPORT_OK   = "";

}


# ====================================================================================
=head2 getNumLocations()

    Description : Retrieves the number of locations in the file

    Needs       : Object Reference

    Returns     : String NumLocations

    Notes       : Also sets $self->{numlocations}

=cut
# ------------------------------------------------------------------------------------
sub getNumLocations {
    my $self = shift;
        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("location");
    my $n = $nodes->getLength();

    XMLEDI::debugMsg("Entering getNumLocations()");
    XMLEDI::debugMsg("Found $n matching elements");

    if ( $n ) {
        $self->{'numlocations'} = $n;
        return($n);
    } else {
        $self->{'numlocations'} = 0;
        return(0);
    }
} # END getNumLocations()




1;
