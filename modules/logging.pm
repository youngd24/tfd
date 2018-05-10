# =============================================================================
#
# log.pm
#
# Harvester Logging Module
#
# $Id: logging.pm,v 1.3 2003/01/07 20:54:17 youngd Exp $
#
# Contents Copyright (c) 2002, Darren Young
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: logging.pm,v $
# Revision 1.3  2003/01/07 20:54:17  youngd
#   * Removed the use of the sigs module.
#
# Revision 1.2  2003/01/07 19:21:01  youngd
#   * Renamed to logging from log as it conflicted with the internal log
#   * Added package declaration.
#
# Revision 1.1  2003/01/07 19:13:23  youngd
#   * Rename
#
# Revision 1.2  2003/01/07 19:05:30  youngd
#   * Added use of the Exporter module so we can call these methods as though
#     they are in the caller's namespace.
#
# Revision 1.1  2003/01/03 18:48:21  youngd
#   * moved
#
# Revision 1.1  2002/10/21 20:26:44  youngd
#   * new
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

package logging;

my $cvsid = '$Id: logging.pm,v 1.3 2003/01/07 20:54:17 youngd Exp $';
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
    use err;

    # Pragmas to use
    use strict;
    use warnings;

    # Initialize some variables
    our(@EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA = qw(Exporter);

    # Default names to export
    @EXPORT = qw(debug verbose);

    # Names to export on request
    @EXPORT_OK = qw(debug verbose);

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
