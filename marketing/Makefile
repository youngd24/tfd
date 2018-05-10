# ============================================================================
#
# Makefile
#
# Makefile for the email marketing system
#
# $Id: Makefile,v 1.17 2002/08/29 16:28:36 youngd Exp $
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
# Usage:
#
# ============================================================================
#
# ChangeLog
#
# $Log: Makefile,v $
# Revision 1.17  2002/08/29 16:28:36  youngd
# updates
#
# Revision 1.16  2002/08/22 01:10:43  youngd
# * Added schema target
#
# Revision 1.15  2002/08/21 20:11:10  youngd
# * Added sudo wrappers around commands that require root perms
#
# Revision 1.14  2002/08/21 15:24:29  youngd
# * Changed the default target to be install
#   via:
#      .DEFAULT : install
#      all : install
#
# Revision 1.13  2002/08/20 18:34:41  youngd
# * Added compelted messages
#
# Revision 1.12  2002/08/20 18:32:06  youngd
# * Fixed typo
#
# Revision 1.11  2002/08/20 18:31:35  youngd
# * Added clean-marketing and clean-db targets
#
# Revision 1.10  2002/08/20 18:28:18  youngd
# * Removed marketing.php page
#
# Revision 1.9  2002/08/20 17:12:33  youngd
# * Added viewshwag to list of pages to install
#
# Revision 1.8  2002/08/20 15:25:03  youngd
# * Added dbprops to be installed
#
# Revision 1.7  2002/08/20 14:36:11  youngd
# * Changed database host to be localhost not 127.0.0.1
#
# Revision 1.6  2002/08/20 14:33:03  youngd
# * Added test on dbclean
#
# Revision 1.5  2002/08/20 14:31:15  youngd
# * Added db-clean target
#
# Revision 1.4  2002/08/20 14:29:03  youngd
# Minor updates
#
# Revision 1.3  2002/08/20 03:14:46  youngd
# * Added usage target
# * The all target defaults to the usage target now
#
# Revision 1.2  2002/08/20 03:07:08  youngd
# * Added vim noexpandtab option
# * Added several variables
# * Added comment headers
#
# Revision 1.1  2002/08/20 02:56:48  youngd
# * Initial version here from the template
#
# ============================================================================

# ----------------------------------------------------------------------------
#                        I N C L U D E   F I L E S 
# ----------------------------------------------------------------------------
include ../includes.mk


# ----------------------------------------------------------------------------
#                         M A K E   O P T I O N S 
# ----------------------------------------------------------------------------
.SILENT :
.IGNORE : 
.EXPORT_ALL_VARIABLES : 
.DEFAULT : install


# ----------------------------------------------------------------------------
#                      G L O B A L   V A R I A B L E S 
# ----------------------------------------------------------------------------

topdir      = .
dbdir       = $(topdir)/database
htmldir		= /var/www/html
internaldir	= $(htmldir)/internal
marketdir	= $(internaldir)/marketing

dbname      = market
dbdump      = $(dbname).dmp
gzdbdump	= $(dbdump).gz
dbschema	= $(dbname).schema
dbuser		= root

webpages	= addshwag.php \
			  error_handler.php \
              emailer.php \
			  index.php \
			  sendshwag.php \
			  shwaglog.php \
			  dbprops.php \
			  viewshwag.php

# ----------------------------------------------------------------------------
#                      G E N E R A L   T A R G E T S 
# ----------------------------------------------------------------------------

all : install


usage : 
	echo ""
	echo "Usage: make <target>"
	echo ""
	echo "Available targets:"
	echo ""
	echo "   clean"
	echo "   clean-marketing"
	echo "   clean-db"
	echo ""
	echo "   install"
	echo "   install-all"
	echo "   install-dirs"
	echo "   install-web"
	echo ""
	echo "   decompress_dbdump"
	echo "   compress_dbdump"
	echo ""


# ----------------------------------------------------------------------------
#                    C L E A N I N G   T A R G E T S 
# ----------------------------------------------------------------------------

clean : clean-all

clean-all :
	echo "Making $@"
	echo "Done with $@"

clean-db :
	echo "Making $@"
	echo "Removing unnecessary files from $(dbdir)"
	if [ -f $(dbdir)/$(dbdump) ]; then \
		rm $(dbdir)/$(dbdump); \
	fi;\
	echo "Done with $@"

clean-marketing :
	echo "Making $@"
	echo "Removing all files from $(marketdir)"
	sudo rm -fr $(marketdir)/*.php
	echo "Done with $@"


# ----------------------------------------------------------------------------
#               I N S T A L L A T I O N   T A R G E T S 
# ----------------------------------------------------------------------------

install : install-all
	echo "Making $@"
	echo "Done with $@"


install-all : install-dirs
	echo "Making $@"
	make install-web
	echo "Done with $@"


install-dirs :
	echo "Making $@"
	if [ ! -d $(htmldir) ]; then \
		echo "Creating $(htmldir)"; \
		sudo mkdir $(htmldir); \
	fi; \

	if [ ! -d $(internaldir) ]; then \
		echo "Creating $(internaldir)"; \
		sudo mkdir $(internaldir); \
	fi; \

	if [ ! -d $(marketdir) ]; then \
		echo "Creating $(marketdir)"; \
		sudo mkdir $(marketdir); \
	fi; \
	echo "Done with $@"


install-web : install-dirs
	echo "Making $@"
	echo "Installing web content to $(marketdir)"
	for i in $(webpages); \
	do \
		echo "$$i -> $(marketdir)"; \
		sudo cp $$i $(marketdir) > /dev/null ; \
	done; \
	echo "Done with $@"


# ----------------------------------------------------------------------------
#                    D A T A B A S E   T A R G E T S 
# ----------------------------------------------------------------------------


# Decompress an existing compressed database dump file
decompress_dbdump :
	echo "Making $@"
	gzip -d $(dbdir)/$(gzdbdump)
	echo "Done with $@"


# Compress a database dump file
compress_dbdump :
	echo "Making $@"
	gzip $(dbdir)/$(dbdump)
	echo "Done with $@"


# Dump the current schema to a file on disk. This schema "dump" only includes
# the database structure, no data. If you want a dump of the current data use
# the dbdump target.
schema :
	echo "Making $@"
	echo "You must enter the $(dbuser) password for the $(dbname) database"
	echo "in order to create the schema dump"
	mysqldump --no-data --no-create-db -u root -p market > $(dbdir)/$(dbschema)
	echo "Done"
