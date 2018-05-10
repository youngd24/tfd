#!/usr/bin/perl
# =============================================================================
#
# parse_terms.pl
#
# Script to parse Parker's terminal file
#
# $Id: parse_terms.pl,v 1.1 2002/10/28 20:19:59 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: parse_terms.pl,v $
# Revision 1.1  2002/10/28 20:19:59  youngd
#   * Updates
#
# =============================================================================


$infile = "raw_terminals.txt";
$outfile = "terminals.txt";



open(INFILE, "<$infile") || die "Unable to open infile $infile ($!)\n";
open(OUTFILE, ">$outfile") || die "Unable to open outfile $outfile ($!)\n";


while(<INFILE>) {

    chomp();

    # Skip comments
    next if /^#/;

    next if /^\n/;

    print OUTFILE  $_ . ",";



}





close(INFILE);
close(OUTFILE);
