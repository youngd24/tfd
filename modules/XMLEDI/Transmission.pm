# ==============================================================================
#
# TRANSMISSION.PM
#
# Digiship XMLEDI Transmission Module
#
# $Id: Transmission.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI::Transmission;

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &getTransInfo &getTransSender &getTransReceiver &getTransDate &getTransTime );

    # Names to export on request
    @EXPORT_OK   = "";

}



# =============================================================================
=head2 getTransInfo()

    Description : Retrieves information about the transmittal

    Needs       : Object Reference

    Returns     : Hash TransInfo ->

                     $TransInfo{transSender}

                     $TransInfo{transReceiver}

                     $TransInfo{transDate}

                     $TransInfo{transTime}

    Notes       : It also sets ->

                     $self->{transSender}

                     $self->{transReceiver}

                     $self->{transDate}

                     $self->{transTime}

=cut
# -----------------------------------------------------------------------------
sub getTransInfo {
        my $self = shift;
        my $parser = new XML::DOM::Parser;
        my $doc = $parser->parsefile($self->{'file'});
    my $nodes = $doc->getElementsByTagName("transmission");
    my $n = $nodes->getLength();
    my %return;

    XMLEDI::debugMsg("getTransInfo => Entering getTransInfo()");
    XMLEDI::debugMsg("getTransInfo => Found $n matching elements");

    for (my $i = 0; $i < $n; $i++)
    {
        my $node = $nodes->item ($i);
        XMLEDI::debugMsg("Node type: " . $XMLEDI::DOMNodeTypes{$node->getNodeType()} );

        for $kid ( $node->getChildNodes() ) {
            # Get the transmission sender
            if ( $kid->getNodeName() =~ 'sender' ) {
                $next = $kid->getFirstChild();
                $self->{'transSender'} = $next->getData();
                $return{transSender} = $next->getData();
            }
            # Get the transmission receiver
            if ( $kid->getNodeName() =~ 'receiver' ) {
                $next = $kid->getFirstChild();
                $self->{'transReceiver'} = $next->getData();
                $return{transReceiver} = $next->getData();
            }
            # Get the transmission date
            if ( $kid->getNodeName() =~ 'date' ) {
                $next = $kid->getFirstChild();
                $self->{'transDate'} = $next->getData();
                $return{transDate} = $next->getData();
            }
            # Get the transmission time
            if ( $kid->getNodeName() =~ 'time' ) {
                $next = $kid->getFirstChild();
                $self->{'transTime'} = $next->getData();
                $return{transTime} = $next->getData();
            }
        }
    }
    return(%return);
} # END getTransInfo()



# =============================================================================
=head2 getTransSender()

    Description : Retrieves the sender of the document

    Needs       : Object Reference

    Returns     : String Sender

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getTransSender {
        my $self = shift;

    XMLEDI::debugMsg("getTransSender => Entering getTransSender");

    if ( $self->{'transSender'} ) {
        return $self->{'transSender'};
    } else {
        $self->{'errmsg'} = "You have to do getTransInfo() first";
        return(0);
    }

} # END getTransSender()



# =============================================================================
=head2 getTransReceiver()

    Description : Retrieves the receiver of the document

    Needs       : Object Reference

    Returns     : String Receiver

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getTransReceiver {
        my $self = shift;

        XMLEDI::debugMsg("getTransReceiver => Entering getTransReceiver");

    if ( $self->{'transReceiver'} ) {
        return $self->{'transReceiver'};
    } else {
        $self->{'errmsg'} = "You have to do getTransInfo() first";
        return(0);
    }
} # END getTransReceiver()



# =============================================================================
=head2 getTransDate()

    Description : Retrieves the date the document was transmitted

    Needs       : Object Reference

    Returns     : String Time (YYYY-MM-DD)

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getTransDate {
        my $self = shift;

        XMLEDI::debugMsg("getTransDate => Entering getTransDate");

    if ( $self->{'transDate'} ) {
        return $self->{'transDate'};
    } else {
        errMsg("You have to do getTransInfo() first");
        return(0);
    }
} # END getTransDate()



# =============================================================================
=head2 getTransTime()

    Description : Retrieves the time the document was transmitted

    Needs       : Object Reference

    Returns     : String Time (HH:MM:SS)

    Notes       : None

=cut
# -----------------------------------------------------------------------------
sub getTransTime {
        my $self = shift;

        XMLEDI::debugMsg("getTransTime => Entering getTransTime");

    if ( $self->{'transTime'} ) {
        return $self->{'transTime'};
    } else {
        errMsg("You have to do getTransInfo() first");
        return(0);
    }

} # END getTransTime()


1;