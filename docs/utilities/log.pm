# =============================================================================
#
# log.pm
#
# Logging Methods Module
#
# $Id: log.pm,v 1.1 2002/10/16 13:11:35 youngd Exp $
#
# Contents Copyright (c) 2002, Darren Young
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: log.pm,v $
# Revision 1.1  2002/10/16 13:11:35  youngd
#   * Copied from harvester.
#
# Revision 1.5  2002/09/29 08:37:12  youngd
#   * Removed Sys::Syslog.
#
# Revision 1.4  2002/09/27 02:41:24  youngd
#   * Changed all to use sigs.pm instead of inc.pm
#
# Revision 1.3  2002/09/27 02:31:03  youngd
#   * Changed err::syntax to be syntax.
#
# Revision 1.2  2002/09/22 21:13:31  youngd
#   * Moved functions out of the main script and into here. This is the
#     first working version of this module.
#
# Revision 1.1  2002/09/22 18:13:42  youngd
#   * Initial version.
#
# =============================================================================


# -----------------------------------------------------------------------------
#                                    B E G I N
# -----------------------------------------------------------------------------
BEGIN {

    # System modules we need
    use Sys::Hostname;

    # Local modules we need
    use lib ".";
    use sigs;
    use err;

    # Pragmas to use
    use strict;
    use warnings;
}


# -----------------------------------------------------------------------------
# NAME        : log::log
# DESCRIPTION : Used to print a normal log message
# ARGUMENTS   : string (message)
# RETURN      : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------

sub log {
    my $message = shift;

    if (! $message) {
        syntax("param message not passed to log::log");
        return(0);
    } else {
        print localtime(time()) . " $message\n";
        return(1);
    }
}


# -----------------------------------------------------------------------------
# NAME        : log::debug
# DESCRIPTION : Used to print a debug message
# ARGUMENTS   : string (message)
# RETURN      : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub debug {
    my $message = shift;

    if (! $message) {
        syntax("param debug not passed to log::debug");
        return(0);
    } else {
        if ( $main::debug ) {
            print localtime(time()) . " DEBUG: $message\n";
            return(1);
        }
    }
}


# -----------------------------------------------------------------------------
# NAME        : log::verbose
# DESCRIPTION : Used to print a verbose message
# ARGUMENTS   : string (message)
# RETURN      : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub verbose {
    my $message = shift;

    if (! $message) {
        syntax("param verbose not passed to log::verbose");
        return(0);
    } else {
        if ( $main::verbose ) {
            print localtime(time()) . " VERBOSE: $message\n";
            return(1);
        }
    }
}

1;
