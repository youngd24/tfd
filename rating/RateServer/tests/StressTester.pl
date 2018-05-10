# ===============================================================
#
# =head1 NAME
#
#    StressTester.pl
#
# =head1 HOMEPAGE
#
#    http://intranet.digiship.com/~youngd/perl/StressTester.pl
#
# =head1 ABSTRACT
#
#    RateServer Stress Testing Script
#
# =head1 COPYRIGHT
#
#    Contents Copyright (c) 2000, Digiship Corp.
#
# =head1 VERSION
#
#    $Id: StressTester.pl,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# =head1 AUTHOR
#
#    Darren Young
#    youngd@digiship.com
#
# ===============================================================
#
# =head1 DESCRIPTION
#
#    Description:
#
# ===============================================================
#
# =head1 USAGE
#
#    Usage:
#
# ===============================================================
#
# =head1 CHANGES
#
#    $Log: StressTester.pl,v $
#    Revision 1.1.1.1  2002/07/13 04:30:35  youngd
#    initial import
#
#    Revision 1.1.1.1  2001/12/15 18:17:56  youngd
#    new import
#
#    Revision 1.1.1.1  2001/12/12 19:00:28  youngd
#    initial import
#
#    Revision 1.4  2000/12/29 00:04:16  youngd
#    *** empty log message ***
#
#    Revision 1.3  2000/12/28 23:52:32  youngd
#    Removed "warn"
#
#    Revision 1.2  2000/12/28 23:46:11  youngd
#    Working version
#
#    Revision 1.1  2000/12/28 23:27:28  youngd
#    Initial version
#
#
# ===============================================================
#
# =head1 BUGS
#
#    Bugs:
#
# ===============================================================
#
# =head1 TODO
#
#    Todo:
#
# ===============================================================



my $cmd = "perl ../RateClient.pl";
my $i = 0;

print "Starting...\n";

while(true) {

	system($cmd);
	$i++;

}




