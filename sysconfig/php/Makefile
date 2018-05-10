# ============================================================================
#
# Makefile
#
# Makefile for php system configuration files
#
# $Id: Makefile,v 1.15 2002/10/18 13:05:23 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# ============================================================================
#
# vim:set noexpandtab:
#
# ============================================================================
#
# ChangeLog
#
# $Log: Makefile,v $
# Revision 1.15  2002/10/18 13:05:23  youngd
#  * Changed dev location to be /usr/local/lib instead of /etc.
#
# Revision 1.14  2002/09/19 13:56:24  youngd
#   * Changed the directory where jango's file was copied to
#
# Revision 1.13  2002/09/13 19:48:35  youngd
#   * Added c:\php to the extensions_dir
#
# Revision 1.12  2002/09/13 19:23:02  youngd
#   * Added new targets for the machine lavender
#
# Revision 1.11  2002/09/13 13:56:30  webdev
#   * Added additional help text to teh about target.
#
# Revision 1.10  2002/09/13 13:55:28  webdev
#   * Added sudo wrapper around all commands
#
# Revision 1.9  2002/09/13 13:52:03  webdev
#   * Added jango installation target
#
# Revision 1.8  2002/08/20 17:51:00  youngd
#   * Added .DEFAULT target that points to the usage target
#
# Revision 1.7  2002/08/20 17:49:30  youngd
#   * Added more comments
#
# Revision 1.6  2002/08/20 17:46:51  youngd
#   * Added more to the about section
#
# Revision 1.5  2002/08/20 17:43:50  youngd
#   * Added production php.ini installation target
#
# Revision 1.4  2002/08/20 15:21:07  youngd
#   * Added additional help output
#
# Revision 1.3  2002/08/20 15:18:53  youngd
#   * Added additional ignore targets
#
# Revision 1.2  2002/08/20 15:17:51  youngd
#   * First working version
#
# Revision 1.1  2002/08/20 14:50:55  youngd
#   * Initial version from template
#
# ============================================================================

# ----------------------------------------------------------------------------
#                        I N C L U D E   F I L E S 
# ----------------------------------------------------------------------------
include ../../includes.mk


# ----------------------------------------------------------------------------
#                         M A K E   O P T I O N S 
# ----------------------------------------------------------------------------
.IGNORE : all usage about clean \
          install install-php-ini install-php-ini-dev
.SILENT : 
.EXPORT_ALL_VARIABLES : 
.DEFAULT : usage

SHELL	= sh


# ----------------------------------------------------------------------------
#                      G L O B A L   V A R I A B L E S 
# ----------------------------------------------------------------------------

topdir      	= .

devsys		= maul
qasys		= mailer
prodsys		= fett
jango		= jango
lavender	= lavender

thishost	= $(shell hostname)

# ----------------------------------------------------------------------------
#                      G E N E R A L   T A R G E T S 
# ----------------------------------------------------------------------------

# If no target is given, tell the caller what targets are available
all : usage


usage : about
	echo ""
	echo "Usage: make <target>"
	echo ""
	echo "Available targets:"
	echo ""
	echo "   all"
	echo ""
	echo "   about"
	echo "   usage"
	echo ""
	echo "   clean"
	echo ""
	echo "   install"
	echo "   install-php-ini-dev"
	echo "   install-php-ini-qa"
	echo "   install-php-ini-prod"
	echo "   install-php-ini-jango"
	echo "   install-php-ini-lavender"
	echo ""
	echo "The 'install' target is (hopefully) intelligent enough to figure"
	echo "out where you are and run the correct target"


# This is just to dump out some info about the environment
about :
	echo -n "You are on the machine $(thishost) "
	if [ "$$thishost" == "maul" ]; then \
		echo "(DEV)" ; \
	fi
	if [ "$$thishost" == "mailer" ]; then \
		echo "(QA)" ; \
	fi
	if [ "$$thishost" == "fett" ]; then \
		echo "(PROD)" ; \
	fi
	if [ "$$thishost" == "jango" ]; then \
		echo "(DEV-LAPTOP)" ; \
	fi
	if [ "$$thishost" == "lavender" ]; then \
		echo "(DEV-WIN2K)" ; \
	fi


# ----------------------------------------------------------------------------
#                    C L E A N I N G   T A R G E T S 
# ----------------------------------------------------------------------------

clean :
	echo "Making $@"
	rm -f *.??~


# ----------------------------------------------------------------------------
#                I N S T A L L A T I O N   T A R G E T S 
# ----------------------------------------------------------------------------

# A general installation target that calls a 'dispatcher'
install : about install-php-ini

# The installation dispatcher target. This target determines the machine
# being execure on and calls the appropriate target for that environment.
install-php-ini : 
	if [ "$$thishost" == "maul" ]; then \
		make install-php-ini-dev ; \
	fi
	if [ "$$thishost" == "mailer" ]; then \
		make install-php-ini-qa ; \
	fi
	if [ "$$thishost" == "fett" ]; then \
		make install-php-ini-prod ; \
	fi
	if [ "$$thishost" == "jango" ]; then \
		make install-php-ini-jango ; \
	fi
	if [ "$$thishost" == "lavender" ]; then \
		make install-php-ini-lavender ; \
	fi


# Installs the dev php.ini file
install-php-ini-dev : 
	echo "Making $@"
	echo "Backing up current file"
	sudo cp /usr/local/lib/php.ini /usr/local/lib/php.ini.bak
	echo "Copying new file"
	sudo cp php.ini.dev /usr/local/lib/php.ini
	echo "Done"


# Installs the prod php.ini file
install-php-ini-prod : 
	echo "Making $@"
	echo "Backing up current file"
	sudo cp /usr/local/lib/php.ini /usr/local/lib/php.ini.bak
	echo "Copying new file"
	sudo cp php.ini.prod /usr/local/lib/php.ini
	echo "Done"


# Installs the jango php.ini file
install-php-ini-jango : 
	echo "Making $@"
	echo "Backing up current file"
	sudo cp /usr/local/lib/php.ini /usr/local/lib/php.ini.bak
	echo "Copying new file"
	sudo cp php.ini.jango /usr/local/lib/php.ini
	echo "Done"

# Installs the lavender php.ini file
# It's a Windows machine so the paths are a bit different
install-php-ini-lavender : 
	echo "Making $@"
	echo "Backing up current file"
	copy c:\winnt\php.ini c:\winnt\php.ini.bak
	echo "Copying new file"
	copy php.ini.lavender c:\winnt\php.ini
	echo "Done"
