# ==============================================================================
#
# tools.pm
#
# Tools Module
#
# $Id: tools.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ==============================================================================

# Our package name
package tools;

# The name we are know to other modules with
$name = "Tools Module";

# Module stars here
BEGIN {

    # Bring in so we can export names into the global namespace
    use Exporter;

    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);

    @ISA         = qw(Exporter);

    # Default names to export
    @EXPORT      = qw( &debugMsg
                       &errorMsg
                       &setDebugLevel );

    # Names to export on request
    @EXPORT_OK   = "";

}


# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub setDebugLevel {
     my $level = shift;

}


# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub debugMsg {
     return(1);
}


# ----------------------------------------------------------------------------
# NAME        :
# DESCRIPTION :
# ARGUMENTS   :
# RETURNS     :
# STATUS      :
# NOTES       :
# ----------------------------------------------------------------------------
sub errorMsg {
     return(1);
}