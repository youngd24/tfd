# ====================================================================
#
# Makefile
#
# CGI Rater Makefile
#
# $Id: Makefile,v 1.8 2002/08/13 19:59:04 youngd Exp $
#
# Contents Copyright (c) 2000-2002 Transport Investments, Inc.
# Portions taken from Digiship build system
#
# Darren Young
# darren_young@yahoo.com
#
# ====================================================================
#
# You HAVE to have tabs in a makefile or it simply won't work. Now, I
# am a vi user and have to have this set. If you use emacs you're on
# your own. If you are using a Windows editor you probably won't have
# to worry about aby of this.
# 
# vim:set noexpandtab:
#
# ====================================================================
#
# ChangeLog:
#
#  $Log: Makefile,v $
#  Revision 1.8  2002/08/13 19:59:04  youngd
#  * Added install-qa and install-prod targets to IGNORE list
#    - Darren Young
#
#  Revision 1.7  2002/08/13 19:53:59  youngd
#  * Added test-sudo target that checks if sudo is installed
#    - Darren Young
#  * All installation targets now check for sudo
#    - Darren Young
#
#  Revision 1.6  2002/07/27 15:57:33  youngd
#  * Changed target location for production copy
#    - Darren Young <darren@younghome.com>
#
#  Revision 1.5  2002/07/27 15:55:39  youngd
#  * Cleaned up install-prod target
#    - Darren Young <darren@younghome.com>
#
#  Revision 1.4  2002/07/27 15:52:43  youngd
#  * Added perms settings on installation targets.
#    - Darren Young <darren@younghome.com>
#
#  Revision 1.3  2002/07/27 10:19:58  youngd
#  * First testable version
#    - Darren Young <darren@younghome.com>
#
#  Revision 1.2  2002/07/27 10:11:34  youngd
#  * First working version
#    - Darren Young <darren@younghome.com>
#
# ====================================================================

# Pull in the global build script. This is used to gather some global
# information common to all applications on all systems.
#
include	../../includes.mk

# These are considered "fake" targets.
.IGNORE : install install-laptop install-qa install-prod usage

# Don't echo back the commands that are being executed. This way you
# don't have to add the @ symbol in front of every command line.
.SILENT :

# Export all of the variables to any scripts that might call us. This
# way they pick up some globals from here as well as the master
# Makefile up a few levels
.EXPORT_ALL_VARIABLES :

# -------------------------------------------------------------------
#                             V A R I A B L E S
# -------------------------------------------------------------------

# Global variables
app_version               = "1.0.1"

# This is the rater script itself
rater_script  = rater.cgi

# Modules that the rater script needs
# These are local modules, not system ones. If you need any modules
# that this script uses and they're not in here, go to CPAN an
# download/install the required module. To use CPAN simply type
# perl -MCPAN -e shell


# This is the RedHat 7.2 standard location
standard_cgi  = /var/www/cgi-bin


# -------------------------------------------------------------------
#                     G L O B A L    T A R G E T S 
# -------------------------------------------------------------------

# Default to my laptop since that's where I work on this the most.
all : usage

usage :
	echo ""
	echo "Makefile to install CGI based rater on a given system"
	echo ""
	echo "Usage: make <target>"
	echo ""
	echo "Possible targets are:"
	echo "   install-laptop"
	echo "   install-qa"
	echo "   install-prod"

clean : 
	# Blast vi temp files
	rm -f *.~*

test-sudo :
	if test ! -f /usr/bin/sudo ; then \
		echo "sudo isn't installed"; \
		echo "Install it and try again"; \
		exit 1; fi\


# -------------------------------------------------------------------
#                 I N S T A L L A T I O N    T A R G E T S 
# -------------------------------------------------------------------

rater.cgi :
	echo "Cleaning up rater.cgi"



# This target installs the rater on my Compaq laptop named jango
# The location is standard for a stock 7.2 RedHat installation.
# /var/www/cgi-bin has become the RedHat standard.
#
# All this target does is copies the script to the target location
# as root via the sudo program.
#
# Make sure you are in teh sudoers file and allowed to execute this
# script. I wouldn't recommend installing any of that in a production
# environment, but it's fine for development.
#
install-laptop : test-sudo rater.cgi
	echo "Installing on laptop (jango)"
	sudo cp $(rater_script) $(standard_cgi)
	sudo chmod 755 $(standard_cgi)/$(rater_script)

install-qa : test-sudo rater.cgi
	echo "Installing in QA"
	echo "Copying rater.cgi to fett(home):$(standard_cgi) as root"
	sudo scp $(rater_script) root@fett.internal.younghome.com:/$(standard_cgi)/ > /dev/null 2>&1
	echo "Setting permissions on remote file"
	sudo ssh root@fett.internal.younghome.com chmod 755 $(standard_cgi)/$(rater_script) > /dev/null 2>&1
	echo "Done"

install-prod : test-sudo rater.cgi
	echo "Installing in PRODUCTION"
	echo "Copying rater.cgi to www.thefreightdepot.com:/usr/local/apache/cgi-bin as root"
	sudo scp $(rater_script) www.thefreightdepot.com:/usr/local/apache/cgi-bin > /dev/null 2>&1
	echo "Setting permissions on remote file"
	sudo ssh root@fett.internal.younghome.com chmod 755 /usr/local/apache/cgi-bin/$(rater_script) > /dev/null 2>&1
	echo "Done"
