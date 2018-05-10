
#
#
# Database test script
#

$dbg = 1;

use dbmod qw(dbConnect);

$dbo = dbmod->new();
$dbh = $dbo->dbConnect();

unless($dbh) {
    die "Failed to connect to the database\n";
} else {
    print "Connected to database\n";
}

$ref = $dbo->getNumStatusesByID($dbh, '32');
print $ref . "\n";






if ( $dbo->dbClose($dbh) ) {
    print "Closed database connection\n";
} else {
    die "Failed to close database connection\n";
}