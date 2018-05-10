# ==============================================================================
#
# PARTY.PM
#
# Digiship XMLEDI Party Module
#
# $Id: Party.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Party;

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &getNumParties &getPartyByType &getPartyTypes );

    # Names to export on request
    @EXPORT_OK   = "";

}


# ====================================================================================
=head2 getNumParties()

    Description : Retrieves the number of parties in the file

    Needs       : Object Reference

    Returns     : String NumParties

    Notes       : Also sets $self->{numparties}

=cut
# ------------------------------------------------------------------------------------
sub getNumParties {
    my $self = shift;
        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("party");
    my $n = $nodes->getLength();

    XMLEDI::debugMsg("Entering getNumParties()");
    XMLEDI::debugMsg("Found $n matching elements");

    if ( $n ) {
        $self->{'numparties'} = $n;
        return($n);
    } else {
        $self->{'numparties'} = 0;
        return(0);
    }
} # END getNumParties()


# ====================================================================================
=head2 getPartyByType()

    Description : Retrieves the information for a party of type x

    Needs       : Object Reference, String Party Type (origin, destination, etc.)

    Returns     : Hash PartyInfo

    Notes       : Also sets $self->{%PartyInfo)

=cut
# ------------------------------------------------------------------------------------
sub getPartyByType {
    my $self = shift;
    my $type = shift;

    XMLEDI::debugMsg("Entering getPartyByType()");

    if ( $type eq "" ) {
        errMsg("getPartyByName usage: getParty(object, type) [party not supplied]");
        return(0);
    }

          my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("party");
    my $n = $nodes->getLength();
    my %return;

    # First level of iteration, step through the elements of type 'party'
    for (my $i = 0; $i < $n; $i++) {
        my $node = $nodes->item ($i);

        XMLEDI::debugMsg("Node name: " . $node->getNodeName());
        XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()});

        $attrs = $node->getAttributes();
        $attrLength = $attrs->getLength();

        XMLEDI::debugMsg("Found $attrLength attributes");

        # Get the attributes for the party element
        # Second level of iteration, step through the 'party' elements and
        # find the type
        for ( my $x = 0; $x < $attrLength; $x++ ) {
            my $attr = $attrs->item ($x);

            XMLEDI::debugMsg("Node name: " . $attr->getNodeName());
            XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$attr->getNodeType()});

            # We just hit an attibute "type"
            if ( $attr->getNodeName() eq "type" ) {
                XMLEDI::debugMsg("partyType -> " . $attr->getValue());

                # Get the type of the party, if it's the type that the caller
                # requested, go to the next level and get the data.
                if ( $attr->getValue() eq $type ) {

                }
            }
        }
    }
} # END getPartyByType()


# ====================================================================================
=head2 getPartyTypes()

    Description : Retrieves the types of all the parties in the file

    Needs       : Object Reference

    Returns     : Array PartyTypes

                  Example: @array = ('origin','destination','billing')

    Notes       : None

=cut
# ------------------------------------------------------------------------------------
sub getPartyTypes {
    my $self = shift;

    XMLEDI::debugMsg("Entering getPartyTypes()");

        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("party");
    my $n = $nodes->getLength();
    my @return;

    for (my $i = 0; $i < $n; $i++) {
        my $node = $nodes->item ($i);
        XMLEDI::debugMsg("Node name: " . $node->getNodeName());
        XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()});

        $attrs = $node->getAttributes();
        $attrLength = $attrs->getLength();

        XMLEDI::debugMsg("Found $attrLength attributes");

        # Get the attributes for the party element
        for ( my $x = 0; $x < $attrLength; $x++ ) {
            my $attr = $attrs->item ($x);
            XMLEDI::debugMsg("Node name: " . $attr->getNodeName());
            XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$attr->getNodeType()});

            # Pull the type attribute out and put it in @return
            if ( $attr->getNodeName() eq "type" ) {
                XMLEDI::debugMsg("partyType -> " . $attr->getValue());
                push(@return, $attr->getValue());
            }
        }
    }
    XMLEDI::debugMsg("Returning -> @return");
    return @return;
} # END getPartyTypes()


1;
