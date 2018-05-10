#
# XMLPARSER.PL
#
# A test XML parser for the EDI/FTP server
#


# The Perl XML DOM parser
# Built on top of XML::Parser which is built on top of XML::Parser::Expat
# Which uses the Expat library.
use XML::DOM;

# These are all the DOM element types, the getNodeType returns a numeric code
# that has to be mapped to text for display. From the XML::DOM module and 
# verified from the DOM spec.
#
#    As an example:
#    print "Node type: " . $DOMNodeTypes{$node->getNodeType()} . "\n";
#       (Assuming $node points to a valid XML node)
#
%DOMNodeTypes = (
                    0  => "UNKNOWN_NODE",
                    1  => "ELEMENT_NODE",
                    2  => "ATTRIBUTE_NODE",
                    3  => "TEXT_NODE",
                    4  => "CDATA_SECTION_NODE",
                    5  => "ENTITY_REFERENCE_NODE",
                    6  => "ENTITY_NODE",
                    7  => "PROCESSING_INSTRUCTION_NODE",
                    8  => "COMMENT_NODE",
                    9  => "DOCUMENT_NODE",
                    10 => "DOCUMENT_TYPE_NODE",
                    11 => "DOCUMENT_FRAGMENT_NODE",
                    12 => "NOTATION_NODE",
                    13 => "ELEMENT_DECL_NODE",
                    14 => "ATT_DEF_NODE",
                    15 => "XML_DECL_NODE",
                    16 => "ATTLIST_DECL_NODE",
);

$dbg = 1;

$parser = new XML::DOM::Parser;
$doc = $parser->parsefile("xmledi_2.xml");

# Get the document information, such as doctype and revid
%docinfo = getDocInfo();

#%transdata = getTransmissionInfo();

# foreach $key ( sort ( keys ( %transdata ) ) ) {
#    print "$key -> $transdata{$key}\n";
#}


sub getDocInfo {
    my $nodes = $doc->getElementsByTagName("xmledi");    
    my $n = $nodes->getLength();
    my %return;

    print "Entering getDocInfo()\n" if $dbg;
    print "Found $n matching elements\n" if $dbg;
    
    # Get all the xmledi elements
    for (my $i = 0; $i < $n; $i++) {
        my $node = $nodes->item ($i);
        print "Node name: " . $node->getNodeName() . "\n" if $dbg;
        print "Node type: " . $DOMNodeTypes{$node->getNodeType()} . "\n" if $dbg;

        $attrs = $node->getAttributes();
        $attrLength = $attrs->getLength();
        
        # Get the attributes for the xmledi element
        for ( my $x = 0; $x < $attrLength; $x++ ) {
            my $attr = $attrs->item ($x);
            print "Node name: " . $attr->getNodeName() . "\n" if $dbg;
            print "Node type: " . $DOMNodeTypes{$attr->getNodeType()} . "\n" if $dbg;

            # The doctype
            if ( $attr->getNodeName() eq "doctype" ) {
                print "Print doctype -> " . $attr->getValue() . "\n" if $dbg;
                $return{doctype} = $attr->getValue();
            }
         
            # The revid
            if ( $attr->getNodeName() eq "revid" ) {
                print "Print revid -> " . $attr->getValue() . "\n" if $dbg;
                $return{revid} = $attr->getValue();
            }            
        }
        
    }
}


sub getTransmissionInfo {
    my $nodes = $doc->getElementsByTagName("transmission");
    my $n = $nodes->getLength();
    my %return;

    print "Entering getTransmissionInfo()\n" if $dbg;
    
    for (my $i = 0; $i < $n; $i++)
    {
        my $node = $nodes->item ($i);
        print "Node type: " . $DOMNodeTypes{$node->getNodeType()} . "\n" if $dbg;
    
        for $kid ( $node->getChildNodes() ) {
            # Get the transmission sender
            if ( $kid->getNodeName() =~ 'sender' ) {
                $next = $kid->getFirstChild();
                $return{sender} = $next->getData();
            }
            # Get the transmissionreceiver
            if ( $kid->getNodeName() =~ 'receiver' ) {
                $next = $kid->getFirstChild();
                $return{receiver} = $next->getData();
            }
            # Get the transmission date
            if ( $kid->getNodeName() =~ 'date' ) {
                $next = $kid->getFirstChild();
                $return{date} = $next->getData();
            }
            # Get the transmission time
            if ( $kid->getNodeName() =~ 'time' ) {
                $next = $kid->getFirstChild();
                $return{time} = $next->getData();
            }
        }        
    }
    return(%return);
}


exit(0);

                     