#!/usr/local/bin/perl
# =============================================================================
#
# RateClient.pm
#
# TCP Based Rate Client Module
#
# Contents Copyright (c) 2000-2002, Transport Investments, Inc..
#
# $Id: RateClient.pm,v 1.3 2003/01/03 18:48:21 youngd Exp $
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog"
#
# $Log: RateClient.pm,v $
# Revision 1.3  2003/01/03 18:48:21  youngd
# moved
#
# Revision 1.2  2002/10/16 22:56:35  youngd
#   * First working test versiom.
#
# =============================================================================


# -----------------------------------------------------------------------------
#                       G L O B A L   V A R I A B L E S  
# -----------------------------------------------------------------------------
package RateClient;
$debug = 0;


# -----------------------------------------------------------------------------
#                               B E G I N 
# -----------------------------------------------------------------------------
BEGIN {

    # Pragmas to use
    use strict;
    use warnings;

    # Standard Perl modules
    use IO::Socket;

    # Local Perl modules

}



# -----------------------------------------------------------------------------
# NAME        : getBasePrice
# DESCRIPTION : Return the baseprice of a shipmnent from CZAR
# ARGUMENTS   : srczip, dstzip, weight, class, carrier, server, port
# RETURNS     : baseprice
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub getBasePrice {
    
    my $srczip    = shift; 
    my $dstzip    = shift; 
    my $weight    = shift; 
    my $class     = shift;
    my $server    = shift;
    my $port      = shift;

    my $sock;

    my $READY_MSG = "READY\n";
    my $BASEPRICE_MSG="BASEPRICE?SRCZIP=$srczip&DSTZIP=$dstzip&WEIGHT=$weight&CLASS=$class";
    my $baselen = length($BASEPRICE_MSG) - 1;
    my $BASEPRICE_MSG = $BASEPRICE_MSG . "&LEN=$baselen" . "0\n";
    my $QUIT_MSG = "QUITT\n";
    my $BYE_MSG = "BYE\n";

    $sock = new IO::Socket::INET( PeerAddr => $server,
	    		    	          PeerPort => $port,
		    		              Proto    => 'tcp',
			    		        );

    die "Could not create socket to $server:$port ($!)\n" unless $sock;

    $sock->autoflush(1);

    debug("INFO: Wating for READY signal");

    # Read data and try to get READY
    while(sysread($sock, $buf,1)) {
        $msg = $msg . $buf;
        if ( ord($buf) == 10 ) {
            last;
        }
    }

    chop($msg);
    debug("RECV: Got raw message: $msg");

    # Make sure what we got is READY
    if ( $msg =~ "READY" ) { 
        debug("INFO: Good, got READY");
    } else {
        debug("ERROR: Bad, didn't get READY");
        close($sock);
        exit(1);
    }

    # Reset the message
    $msg = "";

    # Try and get a BASEPRICE back
    debug("SEND: Attempting to retrieve a BASEPRICE");
    print $sock $BASEPRICE_MSG;
    while(sysread($sock, $buf, 1)) {
        $msg = $msg . $buf;
        if ( ord($buf) == 10 ) {
            last;
        }
    }

    chop($msg);
    debug("RECV: Got raw message: $msg");

    # Parse the baseprice message and validate the checksum
    ($price, $right) = split(":", $msg);
    if ( length($price) != $right ) {
        debug("ERROR: Checksum bad");
        close($sock);
        exit(1);
    } else {
        debug("INFO: Checksum passsed");
    }

    $msg = "";

	# End the session
	debug("SEND: Sending QUIT");
	print $sock "$QUIT_MSG\n";

	while(sysread($sock, $buf,1)) {
		$msg = $msg . $buf;
		if ( ord($buf) == 10 ) {
			debug("RECV: Got message: $msg");
			last;
			$sock->close; 
		}
	}

	chop($msg);
    debug("RECV: Got raw message: $msg");

	if ( $msg == "BYE" ) { 
		debug("INFO: Server said BYE.");
	} else {
		debug("INFO: Server didn't say BYE, how rude!");
		return(0);
	}

    debug("INFO: Returning baseprice of $price");
    return($price);

}


# -----------------------------------------------------------------------------
# NAME        : debug
# DESCRIPTION : Prints a properly formatted debug message
# ARGUMENTS   : message
# RETURNS     : 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub debug {
    my $message = shift;

    # If debug is enabled globally, print the message
    if ( $debug ) {
        print "DEBUG: $message\n";
        return(1);
    } else {
        return(1);
    }
}

1;
