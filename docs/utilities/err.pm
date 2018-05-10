# =============================================================================
#
# err.pm
#
# Error Methods Modules
#
# $Id: err.pm,v 1.1 2002/10/16 13:11:35 youngd Exp $
#
# Contents Copyright (c) 2002, Darren Young
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# Usage:
#
# use err;
#
# =============================================================================
#
# Notes:
#
# All of these return to the caller except for critical which exits.
#
# =============================================================================
#
# ChangeLog:
#
# $Log: err.pm,v $
# Revision 1.1  2002/10/16 13:11:35  youngd
#   * Copied from harvester.
#
# Revision 1.6  2002/09/29 08:37:21  youngd
#   * Removed Sys::Syslog.
#
# Revision 1.5  2002/09/27 02:45:15  youngd
#   * Cleaned up the function docs a bit.
#
# Revision 1.4  2002/09/27 02:43:26  youngd
#   * Changed critical to exit instead of returning.
#
# Revision 1.3  2002/09/27 02:41:24  youngd
#   * Changed all to use sigs.pm instead of inc.pm
#
# Revision 1.2  2002/09/22 21:14:00  youngd
#   * Moved functions out of the main program into this module. This is
#     the first working version of this module.
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
    use log;

    # Pragmas to use
    use strict;
    use warnings;
}


# -----------------------------------------------------------------------------
# NAME        : err::critical
# DESCRIPTION : Used to print a critical error message
# ARGUMENTS   : string (message)
# RETURN      : exit(1)
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub critical {
    my $message = shift;

    if (! $message) {
        syntax("param message not passed to err::critical()");
        return(0);
    } else {
        print localtime(time()) . " CRITICAL: $message\n";
        exit(1);
    }
}


# -----------------------------------------------------------------------------
# NAME        : err::syntax
# DESCRIPTION : Used to print a syntax error message
# ARGUMENTS   : string (message)
# RETURN      : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub syntax {
    my $message = shift;

    if (! $message) {
        syntax("param message not passed to err::syntax()");
        return(0);
    } else {
        print localtime(time()) . " SYNTAX: $message\n";
        return(1);
    }
}


# -----------------------------------------------------------------------------
# NAME        : err::error
# DESCRIPTION : Used to print a general error message
# ARGUMENTS   : string (message)
# RETURN      : 0 or 1
# STATUS      : Stable
# NOTES       : None
# -----------------------------------------------------------------------------
sub error {
    my $message = shift;

    if (! $message) {
        syntax("param message not passed to err::error()");
        return(0);
    } else {
        print localtime(time()) . " ERROR: $message\n";
        return(1);
    }
}


1;
