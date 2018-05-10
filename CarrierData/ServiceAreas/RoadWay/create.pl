#!/usr/bin/perl


open(INFILE, "<all.txt");


while(<INFILE>) {

    chop();
    chop();
    ($zip, $term, $route) = split(",", $_);

    print "INSERT INTO zips VALUES ( 10000, $zip, '$term', '$route');\n";
}



close(INFILE);
