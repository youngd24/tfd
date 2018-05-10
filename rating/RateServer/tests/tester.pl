


use ConfigReader 1.0;
use strict;

my $config;

# Set the ConfigReader to verbose debug info
ConfigReader->debug(1) || warn "Unable to set ConfigReader->debug()\n";

# Create a new ConfigReader object
$config = ConfigReader->new() || die "Unable to create new ConfigReader\n";

# Read in the config file 
$config->readFile('rater.cfg') || die "Unable to read config file\n";

# Dump the contents of the file we read
# $config->dumpFile();

$config->getParam('hostname');
