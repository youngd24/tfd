
keys(%configHash) = 128 ;

open(FILE, 'rater.cfg');

while(<FILE>) {
	# Skip if it's a comment
	if ( substr($_, 0, 1) =~ '#' ) {
		next;
	# Skip if it's a blank newline
	} elsif ( substr($_, 0, 1) =~ '\n' ) {
		next;
	} else {
	# If it hasn't been thrown away, add it to the hash
		chop($_);
		($configKey, $configVal) = split('=', $_);
		$configHash{$configKey} = $configVal;
	}
}

foreach $key (sort(keys %configHash)) {
	print $key, '=', $configHash{$key}, "\n";
}

close(FILE);
