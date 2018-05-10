# =============================================================================
#
# err.pm
#
# Harvester Error Module
#
# $Id: err.pm,v 1.4 2003/01/07 20:54:04 youngd Exp $
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
# Revision 1.4  2003/01/07 20:54:04  youngd
#   * Removed the use of the sigs module.
#
# Revision 1.3  2003/01/07 19:20:40  youngd
#   * Added package declaration.
#
# Revision 1.2  2003/01/07 19:06:56  youngd
#   * Added the use of the Exporter module.
#
# Revision 1.1  2003/01/03 18:48:21  youngd
# moved
#
# Revision 1.1  2002/10/21 20:26:44  youngd
# new
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

package err;

my $cvsid = '$Id: err.pm,v 1.4 2003/01/07 20:54:04 youngd Exp $';
my @cvsinfo = split(' ', $cvsid);
my $VERSION = @cvsinfo[2];

# -----------------------------------------------------------------------------
#                                    B E G I N
# -----------------------------------------------------------------------------
BEGIN {

    # System modules we need
    use Sys::Hostname;
    use Exporter();

    # Local modules we need
    use lib "/tfd/modules";
    use logging;

    # Pragmas to use
    use strict;
    use warnings;

    # Initialize some variables
    our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA = qw(Exporter);

    # Default names to export
    @EXPORT = qw(critical syntax error);

    # Names to export on request
    @EXPORT_OK = qw(critical syntax error);
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
