# ==============================================================================
#
# DOCUMENT.PM
#
# Digiship XMLEDI Document Module
#
# $Id: Document.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Document;

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &getDocInfo &getDocType &getDocRev );

    # Names to export on request
    @EXPORT_OK   = "";

}


# ====================================================================================
=head2 getDocInfo()

    Description : Gets the basic information associated with a transmittal

    Needs       : Object Reference

    Returns     : Hash Docinfo

                : $Docinfo{doctype}     -> Document type (204, 214)

                : $Docinfo{revid}       -> Revision ID (1.00, 1.01)

    Notes       : None

=cut
# ------------------------------------------------------------------------------------
sub getDocInfo {
        my $self = shift;
        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("xmledi");
    my $n = $nodes->getLength();
    my %return;

    XMLEDI::debugMsg("getDocInfo => Entering getDocInfo()");
    XMLEDI::debugMsg("getDocInfo => Found $n matching elements");

    # Get all the xmledi elements
    for (my $i = 0; $i < $n; $i++) {
        my $node = $nodes->item ($i);
        XMLEDI::debugMsg("getDocInfo => Node name: " . $node->getNodeName());
        XMLEDI::debugMsg("getDocinfo => Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()});

        $attrs = $node->getAttributes();
        $attrLength = $attrs->getLength();

        XMLEDI::debugMsg("getDocInfo => Found $attrLength attributes");

        # Get the attributes for the xmledi element
        for ( my $x = 0; $x < $attrLength; $x++ ) {
            my $attr = $attrs->item ($x);
            XMLEDI::debugMsg("getDocInfo => Node name: " . $attr->getNodeName());
            XMLEDI::debugMsg("getDocInfo => Node type: " . $XMLEDI::DOMNodeTypes{$attr->getNodeType()});

            # The doctype
            if ( $attr->getNodeName() eq "document" ) {
                XMLEDI::debugMsg("getDocInfo => doctype -> " . $attr->getValue());
                $self->{'doctype'} = $attr->getValue();
                $return{doctype} = $attr->getValue();
            }

            # The revid
            if ( $attr->getNodeName() eq "revid" ) {
                XMLEDI::debugMsg("getDocInfo => revid -> " . $attr->getValue());
                $self->{'revid'} = $attr->getValue();
                $return{revid} = $attr->getValue();
            }
        }
    }
        return(%return);
} # END getDocInfo()



# ====================================================================================
=head2 getDocType()

    Description : Retrieves the type of document. (204, 214, etc).

    Needs       : Object Reference

    Returns     : String Doctype

    Notes       : None

=cut
# ------------------------------------------------------------------------------------
sub getDocType {
        my $self = shift;

    XMLEDI::debugMsg("getDocType => Entering getDocType()");

    if ( $self->{'doctype'} ) {
        return $self->{'doctype'};
    } else {
        $self->{'errmsg'} = "You have to do getDocInfo() first";
        return(0);
    }

} # END getDocType()



# ====================================================================================
=head2 getDocRev()

    Description : Retrieves the revid of the document.

    Needs       : Object Reference

    Returns     : String Redid

    Notes       : None

=cut
# ------------------------------------------------------------------------------------
sub getDocRev {
        my $self = shift;

    XMLEDI::debugMsg("getDocRev => Entering getDocRev()");

    if ( $self->{'revid'} ) {
        return $self->{'revid'};
    } else {
        $self->{'errmsg'} = "You have to do getDocInfo() first";
        return(0);
    }

} # END getDocRev()


1;
