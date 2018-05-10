#
# Sample RateClient module usage
#

# Bring in the client
use RateClient;

# Turn debugging on
RateClient->Debug(1);

# Create a new client instance
$client = new RateClient('localhost');

# Connect to the remote or error out
$client->Connect() || die $client->lastError();



# Close if there's an open connection
if ( $client->isConnected() ) {
	$client->Shutdown();
}

