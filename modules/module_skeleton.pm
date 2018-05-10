# ====================================================================================
#
# MODULE_SKELETON.PM
#
# PERL MODULE SKELETON
#
# $Id: module_skeleton.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
# 
# ====================================================================================

# Our package declaration
package <package_name>;

# Module stars here
BEGIN {

    if ( $^O =~ /Win32/ || $^O =~ /cygwin/ ) {
        # Windows, most flavors
        use Win32;
        $os = "WinNT" if Win32::IsWinNT();
        $os = "Win9x" if Win32::IsWin95();
        
        # Digiship modules
        use lib '../../../modules';
    } else {
        # UNIX
        $os = "UNIX";
        
        # Digiship modules
        # use lib '/usr/local/digiship/modules';
    }

    # Bring in so we can export names into the global namespace
    use Exporter;
    
    # Initialize some variables
    our ( @EXPORT, @ISA, @EXPORT_OK, %EXPORT_TAGS);
    
    @ISA         = qw(Exporter); 
    
    # Default names to export
    @EXPORT      = qw( &functions);
    
    # Names to export on request
    @EXPORT_OK   = ""; 

}



# ====================================================================================
=head2 method()

    Description : 
	
    Needs       : 
    
    Returns     : 
    
    Notes       : 
                     
=cut
# ------------------------------------------------------------------------------------
sub method {
	my $self = shift;

    return(1);
} # END method()



1;
