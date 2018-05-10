#!/usr/bin/perl

$total = 0;
$count = 0;
$line  = 0;

# Remove the old sorted zips file
if ( -f out.txt ) {
    unlink("out.txt") or die "Unable to remove out.txt\n";
}

open(ZIPSFILE, "<raw_zips.txt") || die "Unable to open zips file ($!)\n";
open(OUTFILE, ">zips.txt") || die "Unable to open output file ($!)\n";

while(<ZIPSFILE>) {
    chomp();
    if ( $count == 1 ) {
        print OUTFILE "'$_' ,'D');\n";
        $count = 0;
    } else {
        print OUTFILE "INSERT INTO zips VALUES ( 10019 , $_, " ;
        $count++;
        $total++;
    }

}


print "Processed $total lines\n";




close(ZIPSFILE) || die "Unable to close zips file ($!)\n";
close(OUTFILE) || die "Unable to close output file ($!)\n";

exit(0);
