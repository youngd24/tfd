# ==============================================================================
#
# XMLEDI.PM
#
# Digiship XML->EDI Parsing & Communications Library
#
# $Id: XMLEDI.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package declaration
package XMLEDI;

my $name = "XML/EDI Parsing & Communications Library for LTL Shipping.";

BEGIN {

     sub croak {
          my $msg = shift;
          warn $msg . "\n";
          exit(1);
     }

     sub checkModule {
          my $module = shift;
          if (!eval "require $module") {
               croak("Unable to load module $module");
               } else {
               return (1);
          }
     }

     # Test to see if the required modules will even load
     use XML::DOM;

     use XMLEDI::Transmission;
     use XMLEDI::Document;
     use XMLEDI::Shipment;
     use XMLEDI::Location;
     use XMLEDI::Product;
     use XMLEDI::Party;
     use XMLEDI::Services;
     use XMLEDI::Status;

     # Enables debugging
     our $dbg = 0;

     # These are the DOM Node Types known to the XML::DOM module
     # Taken from the XML::DOM docs and verified against the DOM LEVEL 1 spec
     # The getNodeType method returns a numeric code, which is fine for
     # test statements, such as if ( $node->getNodeType == ELEMENT_NODE ) {}.
     # But for human consumption, it's better to display something other than
     # a numeric code, thus this hash. Say you have a node that is type 1, to
     # display that to the user:
     #       print $DOMNodeTypes{$node->getNodeType()} . "\n";
     # Which would display:
     #       ELEMENT_NODE
     #
     # XXX - Does this need to be exported? Shoved into $self somehow?
     #
     our %DOMNodeTypes = (
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
}


# ----------------------------------------------------------------------------
# NAME        : new
# DESCRIPTION : Creates a new instance of the XMLEDI object
# ARGUMENTS   : String(object), String(file)
# RETURNS     : Object(self)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub new {
     my $this  = shift;
     my $file  = shift;
     my $class = ref($this) || $this;
     my $self  = {};

     # Croak if they didn't hand us a file to work with
     die ("usage: XMLEDI->new(file) [file not supplied]\n") if not $file;

     debugMsg("Trying to open $file");

     # Try to open the file, die if we can't and give the called the error
     open(FILETEST, "<$file") || die "Unable to open $file ($!)\n";
     close(FILETEST);

     bless $self, $class;

     $self->{'file'} = $file;
     $self->initialize();

     return $self;
}    # END new()


# ----------------------------------------------------------------------------
# NAME        : initialize
# DESCRIPTION : Initialize the modules to a known state
# ARGUMENTS   : String(object)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub initialize {
     my $self = shift;

     debugMsg("Entering initialize()");

     # Error codes & messages
     $self->{'errcode'} = undef;
     $self->{'errmsg'}  = undef;

     # State variables
     $self->{gotDocInfo}        = 0;    # did getDocInfo()
     $self->{gotTransHeader}    = 0;    # did getTransInfo()
     $self->{gotShipmentHeader} = 0;    # did getHeaderInfo()

     # Transmittal variables
     $self->{transSender}   = undef;
     $self->{transReceiver} = undef;
     $self->{transDate}     = undef;
     $self->{transTime}     = undef;

     # Header variables
     $self->{shipmentProNum} = undef;
     $self->{shipmentBolNum} = undef;
     $self->{shipmentPoNum}  = undef;

     return (1);
}    # END initialize()


# ----------------------------------------------------------------------------
# NAME        : lastError
# DESCRIPTION : Returns the last known error
# ARGUMENTS   : String(object)
# RETURNS     : String(error_message)
# STATUS      : Deprecated
# NOTES       : None
# ----------------------------------------------------------------------------
sub lastError {
     my $self = shift;

     debugMsg("Entering lastError");

     return $self->{'errmsg'};

}    # END lastError


# ----------------------------------------------------------------------------
# NAME        : debugMsg
# DESCRIPTION : Prints a properly formatted debug message to STDOUT
# ARGUMENTS   : String(message)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub debugMsg {
     my $msg = shift;

     if ($msg eq "") {
          errMsg("usage: XMLEDI->debugMsg(message) [message not supplied]");
          return (0);
     }

     if ($dbg || $self->{"_DEBUG"}) {
          print "DEBUG: " . $msg . "\n";
          return (1);
          } else {
          return (1);
     }
}    # END debugMsg()


# ----------------------------------------------------------------------------
# NAME        : errMsg
# DESCRIPTION : Prints a properly formatted error message to STDOUT
# ARGUMENTS   : String(message)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub errMsg {
     my $msg = shift;

     if ($msg eq "") {
          print STDERR "usage: XMLEDI->errMsg(message) [message not supplied]";
          return (0);
     }

     if ($dbg || $self->{"_DEBUG"}) {
          print "ERROR: " . $msg . "\n";
          return (1);
          } else {
          return (1);
     }
}    # END errMsg()


# ----------------------------------------------------------------------------
# NAME        : getVersion
# DESCRIPTION : Retrieves and current version of the module
# ARGUMENTS   : String(object)
# RETURNS     : String(version)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub getVersion {
     my $self  = shift;
     my $cvsid = '$Id: XMLEDI.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $';

     debugMsg("Entering getVersion()");

     # The version is the third field of the string
     # There's probably a better way to do this with regular expressions
     my ($t, $t, $modVersion) = split (' ', $cvsid);

     $self->{modVersion};
     return ($modVersion);

}    # END getVersion()


# ----------------------------------------------------------------------------
# NAME        : debug
# DESCRIPTION : Sets the debug level of the module
# ARGUMENTS   : String(object)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub debug {
     my $self = shift;

     print "usage: EMLEDI->debug(level)" unless @_ == 1;

     my $level = shift;

     if (ref($self)) {
          $self->{"_DEBUG"} = $level;
          } else {
          $dbg = $level;
     }

     return (1);

}    # END debug()


# ----------------------------------------------------------------------------
# NAME        : DESTROY
# DESCRIPTION : Called by Perl when the object is done in
# ARGUMENTS   : String(object)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub DESTROY {
     my $self = shift;

     if ($dbg || $self->{"_DEBUG"}) {
          print "Destroying $self " . $self->name;
     }

     return (1);

}    # END DESTROY()


# ----------------------------------------------------------------------------
# NAME        : END
# DESCRIPTION : Called by Perl when the program ends
# ARGUMENTS   : None
# RETURNS     : Nothing
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub END {
     debugMsg("Ending...");
}


# ----------------------------------------------------------------------------
# NAME        : getFilename
# DESCRIPTION : Returns the name of the file in use by the object
# ARGUMENTS   : String(object)
# RETURNS     : String(filename) or 0 (false)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub getFileName {
     my $self = shift;

     debugMsg("Entering getFileName");

     if (defined($self->{'file'})) {
          debugMsg("getFileName returning: " . $self->{'file'});
          return $self->{'file'};
          } else {
          debugMsg("File is not defined");
          return (0);
     }
}

1;

__END__
