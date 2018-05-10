# ==============================================================================
#
# dbmod.pm
#
# XMLEDI Database Module
#
# $Id: dbmod.pm,v 1.1 2002/10/20 20:24:22 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

$debug = 0;

# Our package name
package dbmod;

# Our name as it appears to other modules
$name = "EDI FTP Database Module";

# Module version information
$cvsid   = '$Id: dbmod.pm,v 1.1 2002/10/20 20:24:22 youngd Exp $';
@cvsinfo = split (' ', $cvsid);
$version = $cvsinfo[2];

# Our configuration file
$configFile = "db.conf";

BEGIN {

     # Modules to bring in
     use DBI;
     use Exporter;
     use Carp;
     use Config::General;

     # Pragma's to enable
     #use warnings;
     use vars qw($VERSION $NAME);

     # Module level variables
     my $VERSION     = "$version";
     my $NAME        = "$name";
     my @ISA         = qw(Exporter);
     my @EXPORT      = qw(dbConnect dbClose getStatuses getAllStatusCodes isValidShipment);
     my @EXPORT_OK   = qw(dbConnect dbClose getStatuses getAllStatusCodes isValidShipment);
     my %EXPORT_TAGS = {};

}
# Try and open the config file to see if we can
open(CFGFILE, "<$configFile") || die "Unable to open $configFile: $!\n";
close(CFGFILE);

# Parse the config file
my $conf   = new Config::General($configFile);
my %config = $conf->getall();

# Database table mappings
$dbTable                       = {};
$dbTable{carrier}              = $config{carrier_table};
$dbTable{customers}            = $config{customer_table};
$dbTable{shipment}             = $config{shipment_table};
$dbTable{shipmentaccessorials} = $config{shipmentaccessorial_table};
$dbTable{shipmentstatus}       = $config{shipmentstatus_table};
$dbTable{shipmentstatuscodes}  = $config{shipmentstatuscode_table};

# Debug?
$debug = $config{debug};

# Database settings
$driver   = $config{db_driver};
$host     = $config{db_host};
$port     = $config{db_port};
$database = $config{db_database};
$username = $config{db_username};
$password = $config{db_password};


# ----------------------------------------------------------------------------
# NAME        : new
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub new {
     my $this  = shift;
     my $class = ref($this) || $this;
     my $self  = {};

     bless $self, $class;

     $self->initialize();

     return $self;

}


# ----------------------------------------------------------------------------
# NAME        : initialize
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub initialize {
     my $self = shift;

     return (1);

}


# ----------------------------------------------------------------------------
# NAME        : DESTROY
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub DESTROY {
     my $self = shift;

     $self->_destroy();

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : destroy
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub _destroy {
     my $self = shift;

     return (1);

}


# ----------------------------------------------------------------------------
# NAME        : dbConnect
# DESCRIPTION : Establish the connection to a given database
# ARGUMENTS   : String(object)
# RETURNS     : Object(DBHandle)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub dbConnect {

     my $self = shift;

     # These need to be "local" variables
     # my ($driver, $host, $port, $database, $username, $password, $dbh, $dsn);

#     $driver   = "mysql";
#     $host     = "localhost";
#     $port     = undef;
#     $database = "digiship";
#     $username = "root";
#     $password = "password";

     print "Attempting connection to database\n" if $dbg;
     $dsn = "DBI:$driver:database=$database;host=$host;port=$port";
     $dbh = DBI->connect($dsn, $username, $password);

     unless ($dbh) {
          $self->{connected} = 0;
          $dbh = undef;
          return ($dbh);
     }

     $self->{connected} = 1;
     return ($dbh);

}


# ----------------------------------------------------------------------------
# NAME        : dbClose
# DESCRIPTION : Disconnect from a database handle
# ARGUMENTS   : String(object), String(DBHandle)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub dbClose {
     my $self = shift;
     my $dbh  = shift;

     if ($dbh->disconnect()) {
          return (1);
     } else {
          return (0);
     }
}


# ----------------------------------------------------------------------------
# NAME        : getNumStatusesByID
# DESCRIPTION : Returns the number of rows in the database that match the
#             : shipmentid
# ARGUMENTS   : String(object), String(DBHandle), String(id)
# RETURNS     : String(NumID)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub getNumStatusesByID {
     my $self = shift;
     my $dbh  = shift;
     my $id   = shift;

     my $sql       = undef;
     my $sth       = undef;
     my $names     = undef;
     my $numRows   = undef;
     my $numFields = undef;
     my $ref       = undef;

     # Select statement to retrieve all shipment statuses
     $sql = "SELECT * from $dbTable{shipmentstatus} WHERE shipmentid = $id";

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     $names     = $sth->{'NAME'};
     $numFields = $sth->{'NUM_OF_FIELDS'};
     $numRows   = $sth->rows;

     print "NUM ROWS: $numRows\n" if $dbg;
     print "NUM FIELDS: $numFields\n" if $dbg;

     $sth->finish();

     return ($numRows);

}


# ----------------------------------------------------------------------------
# NAME        : getStatusByID
# DESCRIPTION : Retrieves status data keyed by the shipment id
# ARGUMENTS   : String(object), String(DBHandle), String(id)
# RETURNS     : String(status)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub getStatusByID {
     my $self      = shift;
     my $dbh       = shift;
     my $id        = shift;

     my $sql       = undef;
     my $sth       = undef;
     my $names     = undef;
     my $numRows   = undef;
     my $numFields = undef;
     my $ref       = undef;

     # Select statement to retrieve all shipment statuses
     $sql = "SELECT * from $dbTable{shipmentstatus} WHERE shipmentid = $id";

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     $names     = $sth->{'NAME'};
     $numFields = $sth->{'NUM_OF_FIELDS'};
     $numRows   = $sth->rows;

     print "NUM ROWS: $numRows\n" if $dbg;
     print "NUM FIELDS: $numFields\n" if $dbg;

     #$status = shipmentStatus->new();
     #$status->{shipmentid} = $id;
     while ($ref = $sth->fetchrow_hashref()) {

          #$status->{statusid} = $ref->{'statusid'};
          #$status->{statusdetails} = $ref->{'statusdetails'};
          print "Found a row: statusid = $ref->{'statusid'}, details = $ref->{'statusdetails'}\n";
     }

     $sth->finish();

     return ($status);

}


# ----------------------------------------------------------------------------
# NAME        : isValidShipment
# DESCRIPTION : Routine to make sure that a given shipment is valid (booked)
# ARGUMENTS   : String(object), String(DBHandle), String(shipid)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub isValidShipment {
     my $self       = shift;
     my $dbh        = shift;
     my $shipmentid = shift;
     my $sth;

     # Make sure they give us everything we need
     unless ($self && $dbh && $shipmentid) {
          print "API error: usage: isValidShipment(object, dbhandle, shipmentid)\n";
          return (0);
     }

     # Make sure we're connected to the database
     unless ($self->{'connected'}) {
          print "Database not connected\n";
          return (0);
     }

     # Build up the query, result shoule be >0
     $sql = "SELECT count(shipmentid) FROM $dbTable{shipment} WHERE shipmentid=$shipmentid";

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     $numRows = $sth->rows;

     print "ROWS: $numRows\n";

     if ($numRows > 0) {
          $sth->finish();
          return (1);
          } else {
          $sth->finish();
          return (0);
     }

}


# ----------------------------------------------------------------------------
# NAME        : doesExist
# DESCRIPTION : Determines if a given record exists in the database based on
#             : status id and text
# ARGUMENTS   : String(object), String(DBHandle), String(id), String(detail)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub doesExist {
     my $self         = shift;
     my $dbh          = shift;
     my $statusid     = shift;
     my $statusdetail = shift;
     my $sth;

     unless ($statusid) {
          print "Give me a status id!\n";
          return (0);
     }

     unless ($statusdetail) {
          print "Give me status detail!\n";
          return (0);
     }

     # Make sure we're connected to the database
     unless ($self->{'connected'}) {
          print "Database not connected\n";
          return (0);
     }

     # Build up the query
     $query = "SELECT count(statusid) FROM shipmentstatus WHERE statusid=$statusid and \
                    statusdetail=$statusdetail";

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     while ($ref = $sth->fetchrow_hashref()) {
          print "Got: \n";
     }

     $sth->finish();

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : getAllShipments
# DESCRIPTION : None
# ARGUMENTS   : String(object), String(DBHandle)
# RETURNS     : 1
# STATUS      : Development
# NOTES       : None
# ----------------------------------------------------------------------------
sub getAllShipments {
     my $self = shift;
     my $dbh  = shift;
     my $sth;

     return (1);
}


# ----------------------------------------------------------------------------
# NAME        : getStatuses
# DESCRIPTION : Returns an array of all statuses
# ARGUMENTS   : String(DBHandle)
# RETURNS     : Hash(Statuses)
# STATUS      : Development
# NOTES       : None
# ----------------------------------------------------------------------------
sub getStatuses {
     my $dbh       = shift;

     my $sql       = undef;
     my $sth       = undef;
     my $names     = undef;
     my $numRows   = undef;
     my $numFields = undef;
     my $ref       = undef;

     # Select statement to retrieve all shipment statuses
     $sql = "SELECT * from $dbTable{shipmentstatus}";

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     $names     = $sth->{'NAME'};
     $numFields = $sth->{'NUM_OF_FIELDS'};
     $numRows   = $sth->rows;

     print "NUM ROWS: $numRows\n" if $dbg;
     print "NUM FIELDS: $numFields\n" if $dbg;

     while ($ref = $sth->fetchrow_hashref()) {
          print "Found a row: statusid = $ref->{'statusid'}, details = $ref->{'statusdetails'}\n" if $dbg;
     }

     $sth->finish();

     return (%return);

}


# ----------------------------------------------------------------------------
# NAME        : writeStatusToDB
# DESCRIPTION : Write and commit a shipment status to a DB handle
# ARGUMENTS   : String(object), String(DBHandle)
# RETURNS     : 0 (false) or 1 (true)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub writeStatusToDB {
     my $self = shift;
     my $dbh  = shift;

     # Fields to store
     my $shipmentid    = shift;
     my $statusdetails = shift;
     my $statuscode    = shift;
     my $xmlstore      = shift;
     my $statustime    = shift;

     $sql =
     "insert into shipmentstatus values (\'\', \'$shipmentid\', \'$statusdetails\', $statuscode, \'$xmlstore\', \'$statustime\')";
     print $sql;

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     $sth->finish();

     return (1);

}


# ----------------------------------------------------------------------------
# NAME        : getAllStatusCodes
# DESCRIPTION : Returns all the know status codes
# ARGUMENTS   : String(object), String(DBHandle)
# RETURNS     : Hash(codes)
# STATUS      : Stable
# NOTES       : None
# ----------------------------------------------------------------------------
sub getAllStatusCodes {
     my $self = shift;
     my $dbh  = shift;

     my $sql;
     my $sth;
     my $names;
     my $numRows;
     my $numFields;
     my %return;
     my $ref;

     # Select statement to retrieve all shipment statuses
     $sql = "SELECT * from $dbTable{shipmentstatuscodes}";

     # Prepare the statement, print the error and return false if it fails
     $sth = $dbh->prepare($sql);
     if (!$sth) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     # Execute the statement, print the error and return false if it fails
     if (!$sth->execute()) {
          print "Error: " . $dbh->errstr . "\n";
          return (0);
     }

     $names     = $sth->{'NAME'};
     $numFields = $sth->{'NUM_OF_FIELDS'};
     $numRows   = $sth->rows;

     print "NUM ROWS: $numRows\n" if $dbg;
     print "NUM FIELDS: $numFields\n" if $dbg;

     while ($ref = $sth->fetchrow_hashref()) {
          print "Found a row: statusid = $ref->{'statuscode'}, details = $ref->{'statusdetails'}\n" if $dbg;
     }

     $sth->finish();

     return (%return);

}

1;