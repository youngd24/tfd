#!/usr/bin/perl
# =============================================================================
#
# mkversion.sh
#
# Script to create / update the version.php file
#
# $Id: mkversion.pl,v 1.8 2002/11/21 14:37:41 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren_young@yahoo.com]
#
# ============================================================================
#
# ChangeLog
#
# $Log: mkversion.pl,v $
# Revision 1.8  2002/11/21 14:37:41  webdev
#   * Changed my email address to be at the freight depot
#
# Revision 1.7  2002/09/19 14:38:21  youngd
#   * Fixed a typo
#
# Revision 1.6  2002/09/19 14:37:56  youngd
#   * Added some informational print statements
#   * Added cvs tag at the end of the process.
#
# Revision 1.5  2002/09/19 07:49:53  youngd
#   * Fixed cvs commit, works now.
#
# Revision 1.4  2002/09/19 07:48:38  youngd
#   * Added a cvs commit at the end of the process
#     The log message is the version committed
#
# Revision 1.3  2002/09/19 07:47:46  youngd
#   * Fixed command line check - works now
#   * Added the ability to specify the version on the command line
#
# Revision 1.2  2002/09/19 07:28:49  youngd
#   * Added exit if you don't enter the version
#
# Revision 1.1  2002/09/19 07:27:52  youngd
#   * Initial version
#
# ============================================================================

$version_file="version.php";

if ( "$ARGV[0]" eq "" ) {
    print "What is the version? ";
    $version = <STDIN>;
    chop($version);
} else {
    $version = "$ARGV[0]"
}

if ( "$version" eq "" ) {
    print "Fine, be that way...\n";
    exit(0);
}

open(FILE, ">$version_file") or die "Unable to open $version_file ($!)";

print FILE <<END;
<?php
# ==============================================================================
#
# version.php
#
# Version page
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# ==============================================================================

echo "<font face=tahoma, verdana size=2>";
echo "Version: $version";
echo "</font>";

?>
END

close(FILE);

print "Performing CVS commit on the version file\n";
system("cvs commit -m '$version' $version_file");

print "CVS tagging as $version\n";
system("cvs tag $version");

exit(0);
