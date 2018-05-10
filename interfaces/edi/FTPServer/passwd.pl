#!/usr/bin/perl
# ============================================================================
#
# PASSWD.PL
#
# EDI/FTP password getter/setter program
#
# $Id: passwd.pl,v 1.1 2002/10/20 20:26:27 youngd Exp $
#
# Contents Copyright (c) 2000,2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ============================================================================ 
#
# Basic algorithm:
#  Supply the username on the command line
#  Check to see if the user is in the users file
#      (default users file or command line option)
#  If it's there, change the password
#  If it's not there, add it and set the password
#
# ----------------------------------------------------------------------------
#
# Doesn't challenge for the current password since this is considered to be
# an administrative tool.
#
require 5.6.1;

use lib '/digiship/modules';

# Command line parsing module
use Getopt::Std;

# Our password library
use Passlib;

# Global variables
my $dbg;                # Debugging
my $autoadd;            # Auto add user?
my $autopass;           # Auto ass password?
my $username;           # Username to work with
my $passfile;           # Users file to act upon
my $choice;             # User's response to questions

# Get the command line options (Getopt::Std)
getopts('adhf:p:s');

# Enable debugging if they added -d on the command line
$dbg = 1 if $opt_d;

# Enable auto-addition if they said so on the command line
$autoadd = 1 if $opt_a;

# Prints program usage if they added -h on the command line
printUsage() if $opt_h;

# Do STDOUT password if they asked for it
doStdOut() if $opt_s;

print "\n";

# If they set the password on the command line
# Don't bother prompting for it
if ( $opt_p ) {
    $password = $opt_p;
    print "Password set from command line\n" if $dbg;
}

# Sets the location of the users file if they added -f <file> on the command line
if ( $opt_f ) {
    $passfile = $opt_f;
    print "Password file: $passfile set from command line\n" if $dbg;
} else {
    $passfile = 'users';
    print "Using default password file: $passfile\n" if $dbg;
}

# They have to give us a username to change
if ( $ARGV[0] eq "" ) {
    print "Didn't give me a username to work with\n" if $dbg;
    printUsage();
} else {
    $username = $ARGV[0];
    print "Username set to $username\n" if $dbg;
}

# Start working here

# If the user is already there, change the password
# Unless they added the -a option, then dump out
if ( isUser($passfile, $username) ) {
    if ( $autoadd ) {
        print "Can't autoadd if the user is already there\n";
        exit(1);
    }
    print "Username found\n" if $dbg;
    exit(0);

	# If they're not there, add them
	# If they passed the -a on the command line, don't ask to add it
	# Of they set the password with the -p, just do it
	} else {
	    print "Username not found, do you want to add it [y/n] ? " if not $password;
    	if ( ! $autoadd ) {
        	$choice = <STDIN>;
	        chop($choice);
    	    if ( $choice eq "y" ) {
        	    print "Adding user\n";
            	while( ! $password ) {
	               $password = askPass();
		       	    if ( addUser($passfile, $username, $password) ) {
    		       	    print "User added\n";
        		       	exit(0);
            		} else {
	   	        	    print "User add failed\n";
    	   	        	exit(1);
					}
				}  
	    	} elsif ( $choice eq "n" ) {
    	    	print "Fine then.\n";
        	    exit(1);
			} else {
    			print "Invalid answer $choice\n";
        		exit(1);
	       	}
    	} else {
        	print "\nAdding user automagically\n" if not $password;
	        while(! $password ) {
    	        $password = askPass();
        	}
	        if ( addUser($passfile, $username, $password) ) {
    	        print "User added\n";
        	    exit(0);
			} else {
				print "User add failed\n";
				exit(1);
			}
      }
}


# Fall through just in case
exit(1);


# Prints out how to use us
sub printUsage {
    print "\n";
    print "Digiship EDI/FTP Password Program\n";
    print "\n";
    print "passwd usage: passwd <options> username\n";
    print "  Where options are:\n";
    print "    -a            Add user if not found\n";
    print "    -d            Enable debugging\n";
    print "    -h            Prints help screen (this one)\n";
    print "    -f <file>     Location of users file\n";
    print "    -p <password> Password\n";
    print "    -s            Dump password to STDOUT (not the file)\n";
    print "\n";
    exit(0);
}


# Just dump the password to STDOUT
sub doStdOut {

    while ( ! $password ) {
        $password = askPass();
    }
    
    print STDOUT "\n" . genPass($password) . "\n";
    exit(0);
}
