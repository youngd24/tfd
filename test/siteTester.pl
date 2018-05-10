#!/usr/local/bin/perl
# =============================================================================
#
# siteTester.pl
#
# Script to perform QA test on the site
#
# $Id: siteTester.pl,v 1.3 2003/01/24 17:29:00 youngd Exp $
#
# Contents Copyright (c) 2002-2003, The Freight Depot
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# Description:
#
# =============================================================================
#
# ChangeLog
#
# $Log: siteTester.pl,v $
# Revision 1.3  2003/01/24 17:29:00  youngd
#   * Added code to start using the configuration file.
#   * Cookies are now stored and used correctly for all sessions.
#
# Revision 1.2  2003/01/21 21:28:27  youngd
#   * rateTest now checks the results.
#
# Revision 1.1  2003/01/20 20:36:47  youngd
#   * Initial version with only loginTest
#
# =============================================================================

$name = 'siteTester.pl';

$cvsid = '$Id: siteTester.pl,v 1.3 2003/01/24 17:29:00 youngd Exp $';
@cvsinfo = split(' ', $cvsid);
$version = $cvsinfo[2];


# ------------------------------------------------------------------------------
#                                 B E G I N
# ------------------------------------------------------------------------------
BEGIN {

    use LWP;
    #use LWP::Debug qw(+ -conns);
    use HTTP::Cookies;
    use Getopt::Long qw(GetOptions);
    use Config::Simple;
    use HTML::TokeParser;

    LWP::Debug::trace('send()');
    LWP::Debug::debug('url ok');
    LWP::Debug::conns("read $n bytes: $data");

    use lib '/tfd/modules';
    use logging;
    use err;

    use vars;
    use warnings;
    use strict;

}


# ------------------------------------------------------------------------------
#                              V A R I A B L E S
# ------------------------------------------------------------------------------
$configfile              = "tester.cfg";
$passed                  = 0;
$failed                  = 0;

$testhost                = undef;
$tempfile                = undef;
$clearcookies            = undef;

$loginTest_enabled       = undef;
$loginTest_username      = undef;
$loginTest_password      = undef;

$rateTest_enabled        = undef;
$rateTest_origin         = undef;
$rateTest_destination    = undef;
$rateTest_weight         = undef;
$rateTest_shipclass      = undef;
$rateTest_expectedresult = undef;


# ------------------------------------------------------------------------------
#                   C O M M A N D   L I N E   O P T I O N S
# ------------------------------------------------------------------------------
GetOptions ( "debug"             => \$debug,
             "help"              => sub { print_usage(); },
             "version"           => sub { print_version(); }
           );



# ------------------------------------------------------------------------------
#                          S C R I P T   M A I N 
# ------------------------------------------------------------------------------

# Read the config file which populates global variables
if ( ! readconfig() ) {
    print "Unable to read config file, puke\n";
    exit(1);
} else {
    debug("$name: Read config file");
}


# You have to have permission to read/write this file, hence the use of $HOME
print "Creating cookie jar to store cookies.\n";
$cookie_jar = HTTP::Cookies::Netscape->new (
    file => "$ENV{'HOME'}/lwp_cookies.dat",
    autosave => 1,
);


# Name it whatever you want
print "Creating User Agent.\n";
$ua = LWP::UserAgent->new;
$ua->agent("TFDTester/0.1 ");
$ua->cookie_jar($cookie_jar);


# The login test has to be run first so we get the cookies set properly.
# Additionally, that's the way it happens for real on the site anyways.
if ( $loginTest_enabled ) {
    print "Running login test\n";
    if (loginTest("$loginTest_username", "$loginTest_password")) {
        print "Login Test Passed\n";
        $passed++;
    } else {
        print "Login Test Failed\n";
        $failed++
    }
} else {
    print "NOT running login test\n";
}



if ( $rateTest_enabled ) {
    print "Running rate quote test(s)\n";
    if ( rateTest() ) {
        print "Rate Tests Passed\n";
        $passed++;
    } else {
        print "Rate Tests Failed\n";
        $failed++;
    }
} else {
    print "NOT running rate test\n";
}



# Clear out all the left over cookies
if ( $clearcookies ) {
    print "Clearing cookies\n";
    $cookie_jar->clear();
} else {
    print "NOT clearing cookies\n";
}


if ( $passed == 0 and $failed == 0 ) {
    print "Did you forget to enable some tests?\n";
    exit(1);
}

print "\n\n";
print "=========================\n";
print "TEST RESULTS\n";
print "=========================\n";
print "PASSED : $passed\n";
print "FAILED : $failed\n";
print "\n";


exit;








# =============================================================================
#                             F U N C T I O N S
# =============================================================================



sub readconfig {

    if ( defined($main::configfile) ) {

        if ( -f $main::configfile ) {
            $cfg = new Config::Simple(filename=>$main::configfile) or die "Unable to open config file $main::configfile($!)\n";
        } else {
            error("$name: Failed to open config file $main::configfile");
            exit(0);
        }
    }

    # Populate the global variables with what we found.
    $main::testhost = $cfg->param("main.testhost");
    $main::tempfile = $cfg->param("main.tempfile");
    $main::clearcookies = $cfg->param("main.clearcookies");

    $main::loginTest_enabled = $cfg->param("loginTest.enabled");
    $main::loginTest_username = $cfg->param("loginTest.username");
    $main::loginTest_password = $cfg->param("loginTest.password");

    $main::rateTest_enabled = $cfg->param("rateTest.enabled");
    $main::rateTest_origin = $cfg->param("rateTest.origin");
    $main::rateTest_destination = $cfg->param("rateTest.destination");
    $main::rateTest_weight = $cfg->param("rateTest.weight");
    $main::rateTest_shipclass= $cfg->param("rateTest.shipclass");
    $main::rateTest_expectedresult = $cfg->param("rateTest.expectedresult");

    return(1);

}




sub loginTest {

    my $email = shift;
    my $password = shift;


    # Create a request
    my $req = HTTP::Request->new(POST => "http://$main::testhost/mydigiship.php");
    $req->content_type('application/x-www-form-urlencoded');
    $req->content("email=$email&password=$password");

    # Pass request to the user agent and get a response back
    my $res = $ua->request($req);

    # Check the outcome of the response
    if ($res->is_success) {
        if ( $main::debug ) {
            print "HEADERS:\n";
            print "-------------\n";
            print $res->headers->as_string;
            print "-------------\n\n";
        }

        $cookie_jar->scan (\&parse_cookies );

        $_ = $res->content;

        if ( /is logged in/ ) {
            return(1);
        } else {
            return(0);
        }

    } else {

        return(0);
    }
}




sub rateTest {
    
    my $origin = "$main::rateTest_origin";
    my $destination = "$main::rateTest_destination";
    my $weight = "$main::rateTest_weight";
    my $shipclass = "$main::rateTest_shipclass";

    # Create a request
    my $req = HTTP::Request->new(POST => "http://$main::testhost/rating.php");
    $req->content_type('application/x-www-form-urlencoded');
    $req->content("origin=$origin&destination=$destination&weight=$weight&shipclass=$shipclass");

    # Pass request to the user agent and get a response back
    my $res = $ua->request($req);

    if ( $res->is_success ) {
        if ( $main::debug ) {
            print "HEADERS:\n";
            print "-------------\n";
            print $res->headers->as_string;
            print "-------------\n\n";
        }

        # Open the temp file for write
        open(TMPFILE, ">$main::tempfile") or die "Unable to open tempfile $main::tempfile ($!)\n";

        # dump the content to the file
        print TMPFILE $res->content;

        # Close the tempfile
        close(TMPFILE);

        debug("creating new HTML::TokeParser object");
        $p = HTML::TokeParser->new("$main::tempfile") ||
                die "Unable to create new HTML::TokeParser object ($!)\n";

        while ( $token = $p->get_tag("input") ) {
                if ( $token->[1]{name} =~ "garbonzo" ) {
                    debug("GOT RESULT: $token->[1]{value}\n");
                    $gotresult = $token->[1]{value};
                }
        }

        if ( $gotresult == $main::rateTest_expectedresult ) {
            return(1);
        } else {
            return(0);
        }

    } else {
        return(0);
    }
}




sub parse_cookies {
    my ($version,$key,$val,$path,$domain,$port,$path_spec,$secure,$expires,$discard,$hash) = @_;

    # We save these off for use in other tests since it's a session cookie.
    if ( $key eq "digishipcookie1" ) {
        print "  ** Saving digishipcookie1 ** \n";
        $cookie_jar->clear($domain,$path,$key);
        $cookie_jar->set_cookie($version,$key,$val,$path,$domain,$port,0,0,60,0);
    }

    if ( $key eq "digishipcookie2" ) {
        print "  ** Saving digishipcookie2 **\n";
        $cookie_jar->clear($domain,$path,$key);
        $cookie_jar->set_cookie($version,$key,$val,$path,$domain,$port,0,0,60,0);
    }

}
