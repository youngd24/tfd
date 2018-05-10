# 
# DLL INSTALLER
#

# Our package declaration
package installer;

# Place all startup actions in here
BEGIN {
	
	# Must be defined here as it's used in the BEGIN section
	sub croak {
		my ( $msg ) = @_;
		print "FATAL: " . $msg;
		exit(1);
	}

	# Test for the modules
	eval 'require carp' || croak("carp.pm must be installed\n");
#	eval 'require English' || croak("English.pm must be installed\n");
	
	# Bring in modules
	use carp;
	
}

# Global values
$dbg = 0;
$oldTransitDll = "";
$oldPricingDll = "";
$newTransitDll = "";
$newPricingDll = "";
$regsvr = "c:\windows\system\regsvr32.exe";


print "Starting...\n";
parseCmdLine() || die "Unable to parse command line\n";




# ---------
# FUNCTIONS
# ---------


# Returns an array of file information
sub getFileInfo {
	my ( $file ) = @_;
	my %fileInfo;
	debugMsg("Entering getFileInfo()\n");
		
	return %fileInfo;
}


# Register / unregister a windows OLE program
sub RegSvr {
	my ( $mode, $dll ) = @_;
	debugMsg("Entering RegSvr()\n");
	
	return(1);
}


# Determines is file1 is newer than file2
sub isNewer {
	my ( $file1, $file2 ) = @_;
	debugMsg("Entering isNewer()\n");
	
	return(1);
}


# Determines if file1 is the same as file2
sub isSame {
	my ( $file1, $file2 ) = @_;
	debugMsg("Entering isSame()\n");

	return(1);
}


# Deletes a file from the local filesystem
sub deleteFile {
	my ( $file ) = @_;
	debugMsg("Entering deleteFile()\n");

	return(1);
}


# Copies a file from source to dest
sub copyFile {
	my ( $src, $dst ) = @_;
	debugMsg("Entering copyFile\n");

	return(1);
}


# Displays a properly formatted debug message
sub debugMsg {
	my ( $msg ) = @_;

	if ( $dbg ) {
		print "DEBUG: " . $msg;
	}
	return(1);
}


# Displays the version obtained from the cvs id string
sub printVersion {
	debugMsg("Entering printVersion\n");
	
	print "Installer version $cvsid\n";
	return(1);
}


# Display how to use this program
sub printUsage {
	debugMsg("Entering printUsage()\n");

	print "\n";
	print "Usage: Installer <options>\n";
	print "  Where options are:\n";
	print "    --debug     - Enabled verbose debugging\n";
	print "    --version   - Displays version and exits\n";
	print "\n";
}


# Parses the command line
sub parseCmdLine {
	debugMsg("Entering parseCmdLine\n");
		
	# Display version and exit
	if ( $ARGV[0] eq "--version" ) {
		printVersion();
		exit(0);
	}
	
	# Enable debugging
	if ( $ARGV[0] eq "--debug" ) {
		$dbg = 1;
		debugMsg("Debugging enabled\n");
	}
	
	return(1);
}


# Checks to see if a file exists
sub checkFile {
	my ( $file ) = @_;
	debugMsg("Entering checkFile()\n");
	
	# If it's there, return good, otherwise go bad
	if ( -f $file ) {
		return(1);
	} else {
		return(0);
	}
}
