#!/usr/bin/perl
# ==============================================================================
#
# rater.cgi
#
# Web based CGI interface to the rating system
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# $Id: rater.cgi,v 1.8 2002/10/21 21:12:31 youngd Exp $
#
# Darren Young <darren@younghome.com>
#
# ==============================================================================
#
# Usage:
#
#    rater.cgi?carrier=xxxx&srczip=xxxxx&dstzip=xxxxx&debug=xxxx
#
# ==============================================================================
#
# ChangeLog:
#
#    $Log: rater.cgi,v $
#    Revision 1.8  2002/10/21 21:12:31  youngd
#      * Added additional fuel surcharge field. Still need to calculate it.
#
#    Revision 1.7  2002/08/12 22:14:13  youngd
#    * First working version
#
#    Revision 1.6  2002/08/02 23:48:02  youngd
#    update again
#
#    Revision 1.5  2002/08/01 17:50:54  youngd
#    no message
#
#    Revision 1.4  2002/07/27 15:53:35  youngd
#    * Timestamp is now dynamic
#      - Darren Young <darren@younghome.com>
#    * Added a form to input the data within a table
#      - Darren Young <darren@younghome.com>
#
#    Revision 1.3  2002/07/27 13:54:24  youngd
#    * Started to add documentation to subroutines
#      - Darren Young <darren@younghome.com>
#
#    Revision 1.2  2002/07/27 09:58:34  youngd
#    * First test version
#
#    Revision 1.1  2002/07/27 04:54:19  youngd
#    * Initial version from a template
#
# ==============================================================================

package main;

$name = "rater.cgi";

$cvsid   = '$Id: rater.cgi,v 1.8 2002/10/21 21:12:31 youngd Exp $';
@cvsinfo = split (' ', $cvsid);
$version = $cvsinfo[2];

BEGIN {

    # Pragmas to use
    use strict;
    use warnings;

    # System modules to use
    use IO::Socket;
    use CGI qw/:standard/;
    use CGI::Carp 'fatalsToBrowser';
    use POSIX qw(strftime);
    use DBI;

}

# -------------------------------------------------------------------
#                   G L O B A L   V A R I B L E S
# -------------------------------------------------------------------
$debug  = 0;
$proto  = "tcp";
$server = "64.95.80.2";
$port   = "4979";
$timeo  = 30;
$msg    = "";
$sock   = "";

$db_host = "www.thefreightdepot.com";
$db_name = "digiship";
$db_user = "php";
$db_pass = "password";
$db_port = "3306";

# -------------------------------------------------------------------
#                       S C R I P T   M A I N
# -------------------------------------------------------------------

# Set if you want extended debugging messages
if ($main::extended_debug) {
    use Carp;
}

$q = new CGI;

if ($q->param()) {

    $debug    = $q->param(debug);
    $username = $q->param(username);
    $password = $q->param(password);
    $srczip   = $q->param(srczip);
    $dstzip   = $q->param(dstzip);
    $weight   = $q->param(weight);
    $class    = $q->param(class);

    # Check to make sure we have all the required
    # parameters. Make sure the status of the resulting document
    # is 500 (error) and not 200 (ok)

    # Username
    if (!$username) {
        $errors = 1;
        print $q->header(
         -type    => 'text/html',
         -expires => 'now',
         -status  => '500 Username Required'
        );
        print $q->start_html;
        print $q->h2("ERROR: Username number not supplied");
    }

    # Password
    if (!$password) {
        if (!$errors) {
            print $q->header(
             -type    => 'text/html',
             -expires => 'now',
             -status  => '500 Password Required'
            );
         } else {
            $errors = 1;
        }
        print $q->start_html;
        print $q->h2("ERROR: Password not supplied");
    }

    # Source Zip Code

    if (!$srczip) {
        if (!$errors) {
            print $q->header(
             -type    => 'text/html',
             -expires => 'now',
             -status  => '500 Source Zip Required'
            );
         } else {
            $errors = 1;
        }
        print $q->start_html;
        print $q->h2("ERROR: Source Zip not supplied");
    }

    # Destination Zip Code
    if (!$dstzip) {
        if (!$errors) {
            print $q->header(
             -type    => 'text/html',
             -expires => 'now',
             -status  => '500 Dest Zip Required'
            );
         } else {
            $errors = 1;
        }
        print $q->start_html;
        print $q->h2("ERROR: Dest Zip not supplied");
    }

    if ($errors) {
        exit(1);
    }

    if ($debug) {
        print $q->header;
        print $q->start_html;
        print "Debug Enabled";
        print $q->br;
        print "Param Dump:";
        print $q->Dump;
        $hprint = 1;
    }

    # Connect to the database
    $dsn = "DBI:mysql:database=$db_name:host=$db_host:$port=$db_port";
    $dbh = DBI->connect($dsn, $db_user, $db_pass);

    unless ($dbh) {
        print "Failed to connect to database";
        exit(0);
    }

    # Get the current password stored for the username

    # Connect a socket to the rate server
    debug("Creating new socket");
    $sock = new IO::Socket::INET(
     PeerAddr => $server,
     PeerPort => $port,
     Proto    => $proto
    );

    if (!$sock) {
        $sockerr = $!;

        if (!$hprint) {
            print $q->header(
             -type    => 'text/html',
             -expires => 'now',
             -status  => '500 Network Error'
            );
            print $q->start_html;
        }

        print $q->h2("FATAL ERROR");
        print "Could not create socket to $server:$port ($sockerr)\n";
        print $q->end_html;
        exit(1);
    }

    $sock->autoflush(1);

    if (!$hprint) {
        if ($debug) {
            print $q->header;
            print $q->start_html;
        }
    }

    # Read data and try to get READY
    while (sysread($sock, $buf, 1)) {
        $msg = $msg . $buf;
        if (ord($buf) == 10) {
            last;
        }
    }

    debug("RECV: Got raw message: $msg");

    if ($msg =~ "READY") {
        debug("Got ready message");
     } else {
        print "Didn't get ready";
        close($sock);
        exit(1);
    }

    $msg = "";

    debug("Attempting to retrieve VERSION");
    print $sock "VERSION\r\n";

    while (sysread($sock, $buf, 1)) {
        $msg = $msg . $buf;
        if (ord($buf) == 10) {
            last;
        }
    }

    debug("RECV: Got raw message: $msg");

    # Grab the fields produced by the VERSION command separated by white
    # space
    # i.e. PROTOCOL VERSION 1.1 PROGRAM VERSION 1.23
    #      ^^1      ^^2     ^^3 ^^4     ^^5     ^^6
    #
    ($field1, $field2, $srvrProtVer, $field4, $field5, $srvrProgVer) =
     split (' ', $msg);

    if ($srvrProtVer eq "1.1") {
        debug("INFO: Good, server supports version 1.1");
     } else {
        print "ERROR: Server doesn't support version 1.1";
        close($sock);
        print $q->end_html;
        exit(1);
    }

    $msg = "";

    # Try and get a BASEPRICE back
    $BASEPRICE_MSG =
     "BASEPRICE?SRCZIP=$srczip&DSTZIP=$dstzip&WEIGHT=$weight&CLASS=$class";

    $msg_len = length($BASEPRICE_MSG);

    # For some reason we have to trim 1 from the length for the
    # thing to work correctly.
    $msg_len = $msg_len - 1;

    # Have to pad the end with a 0 to complete the message
    # XXX - What the hell was I thinking when I added that?
    $BASEPRICE_MSG .= "&LEN=$msg_len" . "0" . "\n";
    debug($BASEPRICE_MSG);
    debug("Message length: $msg_len");

    debug("SEND: Attempting to retrieve a BASEPRICE");
    print $sock $BASEPRICE_MSG;

    while (sysread($sock, $buf, 1)) {
        $msg = $msg . $buf;
        if (ord($buf) == 10) {
            last;
        }
    }

    debug("RECV: Got raw message: $msg");

    # Parse the baseprice message and validate the checksum
    ($price, $right) = split (":", $msg);
    if (length($price) != $right) {
        print "ERROR: Checksum bad\n";
        close($sock);
        print $q->end_html;
        exit(1);
     } else {
        debug("INFO: Checksum passsed");
        debug("Price is \$$price");
    }

    $msg = "";

    # Get the current time to use for the id
    $timestamp = strftime "%m-%d-%Y %k:%M:%S", gmtime;
    debug("Timestamp: $timestamp");

    # --------------------------------------------------------------------------
    # Connect to the database
    $dsn = "DBI:mysql:database=$db_name;host=$db_host;port=$db_port";
    $dbh = DBI->connect($dsn, $db_user, $db_pass);
    debug("DBH: $dbh");

    unless ($dbh) {
        $dbh = undef;
        die "Unable to connect to database";
    }

    # --------------------------------------------------------------------------
    # Grab the customer ID from the database based on the supplied username
    $sql = "SELECT custid FROM customers WHERE email='$username'";
    debug("SQL: $sql");

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if (!$sth) {
        die "Error: " . $dbh->errstr . "\n";
    }

    # Execute the statement, print the error and return false if it fails
    if (!$sth->execute()) {
        die "Error: " . $dbh->errstr . "\n";
    }

    # Grab the customer ID from the query
    while ($ref = $sth->fetchrow_hashref()) {
        $custid = $ref->{'custid'};
        debug("CUSTID: $custid");
    }

    if (!$custid) {
        if (!$errors) {
            print $q->header(
             -type    => 'text/html',
             -expires => 'now',
             -status  => '500 Invalid Username'
            );
         } else {
            $errors = 1;
        }
        print $q->start_html;
        print $q->h2("ERROR: Invalid Username ($username)");
        exit(0);
    }

    # Finish that query
    $sth->finish();

# --------------------------------------------------------------------------
    # Insert this request into the quotes table
    $sql = "INSERT INTO quotes VALUES ('',
                                       '$custid',
                                       '',
                                       '$srczip',
                                       '$dstzip',
                                       '$weight',
                                       '$class',
                                       '$price',
                                       '0.00',
                                       '0.00',
                                       '$timestamp',
                                       '1',
                                       '3',
                                        '0','');";
    debug("SQL: $sql");

    # Prepare the statement, print the error and return false if it fails
    $sth = $dbh->prepare($sql);
    if (!$sth) {
        die "Error: " . $dbh->errstr . "\n";
    }

    # Execute the statement, print the error and return false if it fails
    if (!$sth->execute()) {
        die "Error: " . $dbh->errstr . "\n";
    }

    # Get the inserted ID
    $quoteid = $dbh->{'mysql_insertid'};
    debug("QUOTEID: $quoteid");

    # Finish that query
    $sth->finish();

    # Close the database connection
    $dbh->disconnect();

    # -------------------------------------------------------------------------
    # Print the final XML document
    print $q->header(
     -type    => 'text/xml',
     -expires => 'now'
    );

    print "<quote customerid=\"$custid\" quoteid=\"$quoteid\">";
    print "   <timestamp>$timestamp</timestamp>";
    print "   <srczip>$srczip</srczip>";
    print "   <dstzip>$dstzip</dstzip>";
    print "   <weight>$weight</weight>";
    print "   <class>$class</class>";
    print "   <price>$price</price>";

    if ($price =~ "-1.00") {
        print "<status>ERROR</status>";
     } else {
        print "<status>OK</status>";
    }
    print "</quote>";

 } else {

    # We didn't get any params so we must need to paint the screen
    # with a help message.
    #
    print $q->header;
    print $q->start_html;

    print $q->h2("HTTP GET USAGE:");
    print "rater.cgi?carrier=xxxx&srczip=xxxxx&dstzip=xxxxx";

    print $q->br;
    print $q->br;
    print $q->h2("OR USE THIS FORM");

    print "<form name=\"rater\" method=\"GET\" action=\"rater.cgi\">";
    print "<table>";

    print "<tr>";
    print "<td>USERNAME:</td>";
    print
     "<td><input type=\"text\" name=\"username\" id=\"username\" size=25></td>";
    print "</tr>";

    print "<tr>";
    print "<td>PASSWORD:</td>";
    print
"<td><input type=\"password\" name=\"password\" id=\"password\" size=16></td>";
    print "</tr>";

    print "<tr>";
    print "<td>SRCZIP:</td>";
    print "<td><input type=\"text\" name=\"srczip\" id=\"srczip\" size=5></td>";
    print "</tr>";

    print "<tr>";
    print "<td>DSTZIP</td>";
    print "<td><input type=\"text\" name=\"dstzip\" id=\"dstzip\" size=5></td>";
    print "</tr>";

    print "<tr>";
    print "<td>WEIGHT:</td>";
    print "<td><input type=\"text\" name=\"weight\" id=\"weight\" size=5></td>";
    print "</tr>";

    print "<tr>";
    print "<td>CLASS</td>";
    print "<td><input type=\"text\" name=\"class\" id=\"class\" size=5></td>";
    print "</tr>";

    print "<tr>";
    print "<td>DEBUG:</td>";
    print "<td>";
    print $q->checkbox(
     -name    => 'debug',
     -checked => 0,
     -value   => '1',
     -label   => 'debug'
    );
    print "</td>";
    print "</tr>";

    print "<tr>";
    print "<td><input type=\"submit\" name=\"submit\" id=\"submit\"></td>";
    print "<td><input type=\"reset\" name=\"reset\" id=\"reset\"></td>";

    print "</table>";
    print "</form>";

    print $q->end_html;
    exit(0);

}

# -------------------------------------------------------------------
# NAME        : debug
# DESCRIPTION : Used to print a debug message only when $debug is set
# ARGUMENTS   : String(message)
# RETURNS     : 1 (true)
# STATUS      : Stable
# NOTES       : None
# -------------------------------------------------------------------
sub debug {
    my $message = shift;

    unless ($message) {
        print $q->header;
        print $q->start_html;
        print $q->h2("SYNTAX ERROR");
        print "No message passed to debug()";
    }

    if ($main::debug) {
        print "DEBUG: $message\n";
        print $q->br;
    }
    return (1);
}
