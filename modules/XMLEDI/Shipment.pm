# =============================================================================
#
# SHIPMENT.PM
#
# Digiship XMLEDI Shipment Module
#
# $Id: Shipment.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# =============================================================================

# Our package declaration
package XMLEDI::Shipment;

# Module stars here
BEGIN {

     # Bring in so we can export names into the global namespace
     use Exporter;
     use XMLEDI::ShipmentStatus;

     # Initialize some variables
     our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

     @ISA = qw(Exporter);

     # Default names to export
     @EXPORT = qw(  &getShipmentHeader
          &getPoNumber
          &getProNumber
          &getBolNumber
          &getNumStatuses
          &getAllStatuses );

     # Names to export on request
     @EXPORT_OK = "";

}

# =============================================================================

=head2 getShipmentHeader()

    Description : Gets the header information associated with a shipment

    Needs       : Object Reference

    Returns     : Hash ShipHeader ->

                    $HeaderInfo{shipmentProNum}

                    $HeaderInfo{shipmentBolNum}

                    $HeaderInfo{shipmentPoNum}

    Notes       : It also sets ->

                    $self->{shipmentProNum}

                    $self->{shipmentBolNum}

                    $self->{shipmentPoNum}

=cut
# -----------------------------------------------------------------------------
sub getShipmentHeader {
     my $self   = shift;
     my $parser = new XML::DOM::Parser;
     my $doc    = $parser->parsefile($self->{'file'});
     my $nodes  = $doc->getElementsByTagName("header");
     my $n      = $nodes->getLength();
     my %return;

     XMLEDI::debugMsg("Entering getHeaderInfo()");
     XMLEDI::debugMsg("Found $n matching elements");

     for (my $i = 0 ; $i < $n ; $i++) {
          my $node = $nodes->item($i);
          XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()});

          for $kid($node->getChildNodes()) {

               # Get the PRO Number
               if ($kid->getNodeName() =~ 'pronumber') {
                    $next = $kid->getFirstChild();
                    $self->{'shipmentProNum'} = $next->getData();
                    $return{shipmentProNum} = $next->getData();
               }

               # Get the BOL Number
               if ($kid->getNodeName() =~ 'bolnumber') {
                    $next = $kid->getFirstChild();
                    $self->{'shipmentBolNum'} = $next->getData();
                    $return{shipmentBolNum} = $next->getData();
               }

               # Get the PO Number
               if ($kid->getNodeName() =~ 'ponumber') {
                    $next = $kid->getFirstChild();
                    $self->{'shipmentPoNum'} = $next->getData();
                    $return{shipmentPoNum} = $next->getData();
               }
          }
     }
     return (%return);
}    # END getShipmentHeader()

# =============================================================================

=head2 getProNumber()

    Description : Retrieves the shipment PRO number

    Needs       : Object Reference

    Returns     : String ProNumber

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getProNumber {
     my $self = shift;

     XMLEDI::debugMsg("Entering getProNumber");

     if ($self->{'shipmentProNum'}) {
          return $self->{'shipmentProNum'};
          } else {
          errMsg("You have to do getShipmentHeader() first");
          return (0);
     }
}

# =============================================================================

=head2 getBolNumber()

    Description : Retrieves the shipment BOL number

    Needs       : Object Reference

    Returns     : String BolNumber

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getBolNumber {
     my $self = shift;

     XMLEDI::debugMsg("Entering getBolNumber");

     if ($self->{'shipmentBolNum'}) {
          return $self->{'shipmentBolNum'};
          } else {
          errMsg("You have to do getShipmentHeader() first");
          return (0);
     }
}

# =============================================================================

=head2 getPoNumber()

    Description : Retrieves the shipment PO number

    Needs       : Object Reference

    Returns     : String PoNumber

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getPoNumber {
     my $self = shift;

     XMLEDI::debugMsg("Entering getPoNumber");

     if ($self->{'shipmentPoNum'}) {
          return $self->{'shipmentPoNum'};
          } else {
          errMsg("You have to do getShipmentHeader() first");
          return (0);
     }
}

# =============================================================================

=head2 getNumStatuses()

    Description : Returns the number of status messages in the document

    Needs       : Object Reference

    Returns     : String NumStatuses

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getNumStatuses {
     my $self = shift;

     XMLEDI::debugMsg("Entering getNumStatuses");

     my $parser = new XML::DOM::Parser;
     my $doc    = $parser->parsefile($self->{'file'});
     my $nodes  = $doc->getElementsByTagName("shipment");
     my $n      = $nodes->getLength();
     my %return;

     XMLEDI::debugMsg("Found $n matching elements");

     return ($n);

}

# =============================================================================

=head2 getAllStatuses()

    Description :

    Needs       :

    Returns     :

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getAllStatuses {
     my $self   = shift;
     my $parser = new XML::DOM::Parser;
     my $doc    = $parser->parsefile($self->{'file'});
     my $nodes  = $doc->getElementsByTagName("status");
     my $n      = $nodes->getLength();
     our $status;

     XMLEDI::debugMsg("Entering getAllStatuses()");
     XMLEDI::debugMsg("Found $n matching elements");

     for (my $i = 0 ; $i < $n ; $i++) {
          my $node = $nodes->item($i);
          XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()});
          $status = XMLEDI::ShipmentStatus->new();

          for $kid($node->getChildNodes()) {
               if ($kid->getNodeName() eq 'code') {
                    $next = $kid->getFirstChild();
                    $status->{code} = $next->getData();
               }

               if ($kid->getNodeName() eq 'reason') {
                    $next = $kid->getFirstChild();
                    $status->{reason} = $next->getData();
               }
               if ($kid->getNodeName() eq 'city') {
                    $next = $kid->getFirstChild();
                    $status->{city} = $next->getData();
               }

               if ($kid->getNodeName() eq 'state') {
                    $next = $kid->getFirstChild();
                    $status->{state} = $next->getData();
               }
               if ($kid->getNodeName() eq 'equipment') {
                    $next = $kid->getFirstChild();
                    $status->{equipment} = $next->getData();
               }

               if ($kid->getNodeName() eq 'equipmentnum') {
                    $next = $kid->getFirstChild();
                    $status->{equipmentnum} = $next->getData();
               }
               if ($kid->getNodeName() eq 'date') {
                    $next = $kid->getFirstChild();
                    $status->{date} = $next->getData();
               }

               if ($kid->getNodeName() eq 'time') {
                    $next = $kid->getFirstChild();
                    $status->{time} = $next->getData();
               }
               if ($kid->getNodeName() eq 'timezone') {
                    $next = $kid->getFirstChild();
                    $status->{timezone} = $next->getData();
               }

               if ($kid->getNodeName() eq 'notes') {
                    $next = $kid->getFirstChild();
                    $status->{notes} = $next->getData();
               }
          }
     }

     # Return a reference to the status object
     return ($status);
}

# =============================================================================

=head2 getLastStatus()

    Description : Retrieves the last status message in the document

    Needs       :

    Returns     :

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getLastStatus {
     my $self   = shift;
     my $parser = new XML::DOM::Parser;
     my $doc    = $parser->parsefile($self->{'file'});
     my $nodes  = $doc->getElementsByTagName("status");
     my $n      = $nodes->getLength();
     my $last   = $n - 1;
     our $status;

     XMLEDI::debugMsg("Entering getLastStatus()");
     XMLEDI::debugMsg("Found $n matching elements");

}

1;
