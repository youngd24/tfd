# ============================================================================
#
# Makefile
#
# Makefile for the internal (Intranet) pages
#
# $Id: Makefile,v 1.11 2002/08/20 20:25:57 youngd Exp $
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
# Revision 1.11  2002/08/20 20:25:57  youngd
# working
#
# Revision 1.10  2002/08/20 20:23:20  youngd
# updates
#
# Revision 1.9  2002/08/20 18:50:28  youngd
# * Added more to the usage
#
# Revision 1.8  2002/08/20 18:49:21  youngd
# * Fixed a tab typo
#
# Revision 1.7  2002/08/20 18:49:05  youngd
# * Added more to th about / usage sections
#
# Revision 1.6  2002/08/20 18:44:41  youngd
# * Changed order of installation
#
# Revision 1.5  2002/08/20 18:40:05  youngd
# * Added install-index targets and logic
#
# Revision 1.4  2002/08/20 18:36:01  youngd
# * Added additional ignore targets
#
# Revision 1.3  2002/08/20 17:40:15  youngd
# * Modified include path to reflect production system
# * Added cancelshipment targets
#
# Revision 1.2  2002/08/20 17:36:46  youngd
# * Added list of web pages
#
# Revision 1.1  2002/08/20 17:34:18  youngd
# * Initial version
#
# ============================================================================

# ----------------------------------------------------------------------------
#                        I N C L U D E   F I L E S 
# ----------------------------------------------------------------------------
include ../../includes.mk


# ----------------------------------------------------------------------------
#                         M A K E   O P T I O N S 
# ----------------------------------------------------------------------------
.SILENT :
.IGNORE : all usage about
.EXPORT_ALL_VARIABLES : 
.DEFAULT : usage


# ----------------------------------------------------------------------------
#                      G L O B A L   V A R I A B L E S 
# ----------------------------------------------------------------------------

topdir      = .
htmldir		= /var/www/html
internaldir	= $(htmldir)/internal
marketdir	= $(internaldir)/marketing

cancelshipment	= cancelshipment.php
index		= index.php
qacss       = qa.css

pages       = accountassign.php \
			  accountmgr.php \
			  billupdate.php \
			  bol.php \
			  cancelshipment.php \
			  custs.php \
			  emailer.php \
			  index.php \
			  invoicepd.php \
			  invoice.php \
			  mikesheet.php \
			  nonltl.php \
			  notes.php \
			  pastdue.php \
			  quotes.php \
			  ships.php \
			  shwaglog.php \
			  tomreport.php \
			  update.php 


# ----------------------------------------------------------------------------
#                      G E N E R A L   T A R G E T S 
# ----------------------------------------------------------------------------

all : usage


about :


usage : 
	echo ""
	echo "Usage: make <target>"
	echo ""
	echo "Available targets:"
	echo ""
	echo "   all"
	echo "   about"
	echo "   usage"
	echo ""
	echo "   clean"
	echo "   clean-cancelshipment"
	echo "   clean-index"
	echo ""
	echo "   install"
	echo "   install-cancelshipment"
	echo "   install-index"
	echo ""
	echo "   cancelshipment.php"
	echo "   index.php"
	echo ""


# ----------------------------------------------------------------------------
#                    C L E A N I N G   T A R G E T S 
# ----------------------------------------------------------------------------

clean :
	echo "Making $@"
	echo "Done with $@"

clean-cancelshipment :
	echo "Making $@"
	echo "Done with $@"

clean-index :
	echo "Making $@"
	echo "Done with $@"


# ----------------------------------------------------------------------------
#                      P A G E   T A R G E T S 
# ----------------------------------------------------------------------------

cancelshipment.php :
	echo "Making $@"
	echo "Done with $@"

index.php :
	echo "Making $@"
	echo "Done with $@"

qa.css :
	echo "Making $@"
	echo "Done with $@"


# ----------------------------------------------------------------------------
#               I N S T A L L A T I O N   T A R G E T S 
# ----------------------------------------------------------------------------

install : install-cancelshipment \
	      install-index \
		  install-qacss

install-cancelshipment : cancelshipment.php
	echo "Making $@"
	echo "$(cancelshipment) -> /usr/local/apache/htdocs/internal"
	cp $(cancelshipment) /usr/local/apache/htdocs/internal
	echo "Done with $@"

install-index : index.php
	echo "Making $@"
	echo "$(index) /usr/local/apache/htdocs/internal"
	cp $(index) /usr/local/apache/htdocs/internal
	echo "Done with $@"

install-qacss : qa.css
	echo "Making $@"
	echo "$(qacss) /usr/local/apache/htdocs/internal"
	cp $(qacss) /usr/local/apache/htdocs/internal
	echo "Done with $@"
