
use Strict;				# Keep strict
use Carp;					# Dumps line numbers for debug

my $gbg;					# Garbage, useless data
our @AllowList;			# Global array for the allow list
our @DenyList;				# Global array for the deny list
our @ProcOrder;			# Global array for the order to process
our $tmp;					# Global temp variable
our $start;				# Global variable to represent the start

$ACLFile = 'access.conf';

open(ACLFILE, "<$ACLFile") || die "Unable to open acl file: $ACLFile\n";

# Load the variables from the file
while(<ACLFILE>) {
	chop($_);
	# Skip comments
	if ( substr($_, 0, 1) =~ '#' ) {
		next;
	# Skip lines where the first character is a newline (empty)
	} elsif ( substr($_, 0, 1) =~ '\n' ) {
		next;
	# Parse everything else
	} else {
		if ( substr($_, 0, 5) =~ 'Allow') {
			($gbg, $tmp) = split('=', $_);
			(@AllowList) = split(',', $tmp);
		} elsif ( substr($_, 0, 4) =~ 'Deny') {
			($gbg, $tmp) = split('=', $_);
			(@DenyList) = split(',', $tmp);
		} elsif ( substr($_, 0, 5) =~ 'Order') {
			($gbg, $tmp) = split('=', $_);
			(@ProcOrder) = split(',', $tmp);
		} else {
			print "Hit unknown directive ($_) in $ACLFile at line $.\n";
		}
	} # END PARSE
} # END WHILE LOOP

# Close the ACL file
close(ACLFILE);

# Example of an IP address check
my $clientIP = "192.168.1.1";
if ( isAllowed($clientIP) ) {
	print "IP Address 192.168.1.1 allowed\n";
}

# Name: isAllowed
# Description: Checks an IP address against the ACL list
# Needs: String IP Address (dotted notation)
# Returns: Result code
# Status: Stable
#
sub isAllowed {
	my ($IPAddress) = @_;
	my ($octet1, $octet2, $octet3, $octet4);
	my ($toctet1, $toctet2, $toctet3, $toctet4);
	my $match = 0;

	# Chop the octets from the address passed to us
	($octet1, $octet2, $octet3, $octet4) = split("\\.", $IPAddress);

	# How many entries are in the loaded array()s
	my $AllowLength = scalar(@AllowList);
	my $DenyLength = scalar(@DenyList);
	my $ProcOrderLength = scalar(@ProcOrder);

	# Figure out what order we'll process the ACL
	if ( $ProcOrder[0] eq "Allow" ) {
		$start = "ALLOW";
	} elsif ( $ProcOrder[0] eq "Deny" ) {
		$start = "DENY";
	}

	# Process the entries in the AllowList keyed by number ($i)
	for ( $i = 1; $i < $AllowLength; $i++ ) {
		# Grab the octets from the @AllowList
		($toctet1, $toctet2, $toctet3, $toctet4) = split("\\.", $AllowList[$i]);
		# Test each octet, only when all 4 are the same do we set $match
		if ( $octet1 == $toctet1 ) {
			if ( $dbg ) { print "OCTET-1 matched\n" }
			if ( $octet2 == $toctet2 ) {
				if ( $dbg ) { print "OCTET-2 matched\n" }
				if ( $octet3 == $toctet3 ) {
					if ( $dbg ) { print "OCTET-3 matched\n" }
					if ( $octet4 == $toctet4 ) {
						if ( $dbg ) { print "OCTET-4 matched\n" }
						$match = 1;
					} # END OCTET 4
				} # END OCTET 3
			} # END OCTET 2
		} # END OCTET 1
	} # END FOR LOOP

	# If we have a hit, return yes, otherwise, no
	if ( $match ) {
		return 1;
	} else {
		return 0;
	} # END MATCH RETURN
} # END SUB

