# ==============================================================================
#
# PRODUCT.PM
#
# Digiship XMLEDI Product Module
#
# $Id: Product.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Product;

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    my ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &getNumProducts &getProductInfo );

    # Names to export on request
    @EXPORT_OK   = "";

}

# ====================================================================================
=head2 getNumProducts()

    Description : Retrieves the number of products in the file

    Needs       : Object Reference

    Returns     : String NumProducts

    Notes       : Also sets $self->{numproducts}

=cut
# ------------------------------------------------------------------------------------
sub getNumProducts {
    my $self = shift;
        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("product");
    my $n = $nodes->getLength();

    XMLEDI::debugMsg("Entering getNumProducts()");
    XMLEDI::debugMsg("Found $n matching elements");

    if ( $n ) {
        $self->{'numproducts'} = $n;
        return($n);
    } else {
        $self->{'numproducts'} = 0;
        return(0);
    }
} # END getNumProducts()


# ====================================================================================
=head2 getProductDetail()

    Description : Retrieves the details about the shipped product

    Needs       : Object Reference

    Returns     : Hash ProductInfo

    Notes       : Also sets:

                    $self->{prodDesc}

                    $self->{prodWeight}

                    $self->{prodClass}

                    $self->{prodUnits}

                    $self->{prodHazmat}

=cut
# ------------------------------------------------------------------------------------
sub getProductDetail {
        my $self = shift;
        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("product");
    my $n = $nodes->getLength();
    my %return;

    XMLEDI::debugMsg("getProductDetail => Entering getProductDetail()");
    XMLEDI::debugMsg("getProductDetail => Found $n matching elements");

    for (my $i = 0; $i < $n; $i++)
    {
        my $node = $nodes->item ($i);
        XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()} );

        for $kid ( $node->getChildNodes() ) {
            # Get the product description
            if ( $kid->getNodeName() =~ 'description' ) {
                $next = $kid->getFirstChild();
                $self->{'prodDesc'} = $next->getData();
                $return{prodDesc} = $next->getData();
            }
            # Get the product weight
            if ( $kid->getNodeName() =~ 'weight' ) {
                $next = $kid->getFirstChild();
                $self->{'prodWeight'} = $next->getData();
                $return{prodWeight} = $next->getData();
            }
            # Get the product class
            if ( $kid->getNodeName() =~ 'class' ) {
                $next = $kid->getFirstChild();
                $self->{'prodClass'} = $next->getData();
                $return{prodClass} = $next->getData();
            }
            # Get the product units
            if ( $kid->getNodeName() =~ 'units' ) {
                $next = $kid->getFirstChild();
                $self->{'prodUnits'} = $next->getData();
                $return{prodUnits} = $next->getData();
            }
            # Get the product hazmat
            if ( $kid->getNodeName() =~ 'hazmat' ) {
                $next = $kid->getFirstChild();
                $self->{'prodHazmat'} = $next->getData();
                $return{prodMazmat} = $next->getData();
            }
        }
    }
    return(%return);
} # END getProductDetail()


1;
