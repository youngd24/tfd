#!/usr/bin/perl
# =============================================================================
#
# process.pl
#
# Script to process the zip code file for RoadWay
#
# $Id: process.pl,v 1.2 2002/12/12 18:58:19 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: process.pl,v $
# Revision 1.2  2002/12/12 18:58:19  youngd
#   * Processed new file from Roadway that included indirect points.
#
# Revision 1.1  2002/10/28 21:53:50  youngd
# updates
#
# Revision 1.2  2002/10/22 23:42:01  youngd
#   * Ready for testing.
#
# =============================================================================

$i = 1;

# The name of the file that contans the zips / terminals from RoadWay
open(INFILE, "<US-DirectAndIndirectPoints.csv") || die "Unable to open America.txt\n";


while(<INFILE>) {


    ( $first, $last, $term, $di ) = split(",", $_);


    $diff = $last - $first;

    if ( $diff == 0 ) {
        print "$first, $term, $di";
        $i++;
        next;
    } 

    if ( $diff == 1 ) {
        print "$first, $term, $di";
        print "$last, $term, $di";
        $i++;
        next;
    }

    for  ( scalar($first)..scalar($last) ) {
        if ( length($_) == 5 ) {
            print "$_, $term, $di";
        }
        if ( length($_) == 4 ) {
            print "0$_, $term, $di";
        }
        if ( length($_) == 3 ) {
            print "00$_, $term, $di";
        }
        if ( length($_) == 2 ) {
            print "000$_, $term, $di";
        }
        if ( length($_) == 1 ) {
            print "0000$_, $term, $di";
        }
    }


    $i++;
}




close(INFILE);
