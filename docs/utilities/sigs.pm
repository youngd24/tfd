# =============================================================================
#
# sigs.pm
#
# Signal Methods Module
#
# $Id: sigs.pm,v 1.1 2002/10/16 13:11:35 youngd Exp $
#
# Contents Copyright (c) 2002, Darren Young
#
# Darren Young [darren_young@yahoo.com]
#
# =============================================================================
#
# ChangeLog:
#
# $Log: sigs.pm,v $
# Revision 1.1  2002/10/16 13:11:35  youngd
#   * Copied from harvester.
#
# Revision 1.1  2002/09/27 02:39:39  youngd
#   * Renamed to sigs.pm from inc.pm
#
# Revision 1.2  2002/09/22 09:55:44  youngd
#   * Still working on it, routine commit.
#
# Revision 1.1  2002/09/22 08:54:26  youngd
#   * Initial version.
#
# =============================================================================
sub sig_hup_handler {
    debug("sig_hup_handler(): Entering sig_hup_handler()");
    return(1);
}


sub sig_int_handler {
    debug("sig_int_handler(): Entering sig_int_handler()");
    print "Caught interrupt signal, exiting...\n";
    cleanup();
    exit(0);
}


sub sig_quit_handler {
    debug("sig_quit_handler(): Entering sig_quit_handler()");
    return(1);
}


sub sig_kill_handler {
    debug("sig_kill_handler(): Entering sig_kill_handler()");
    return(1);
}


sub sig_usr1_handler {
    debug("sig_usr1_handler(): Entering sig_usr1_handler()");
    return(1);
}


sub sig_usr2_handler {
    debug("sig_usr2_handler(): Entering sig_usr2_handler()");
    return(1);
}


1;
