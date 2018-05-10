# =============================================================================
#
# includes.mk
#
# Standard rules to include in makefiles
#
# $Id: includes.mk,v 1.2 2002/08/21 05:55:49 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# =============================================================================
#
# vim:set noexpandtab:
#
# =============================================================================
#
# Usage:
#
# include includes.mk
#
# =============================================================================
#
# ChangeLog:
#
# $Log: includes.mk,v $
# Revision 1.2  2002/08/21 05:55:49  youngd
# * Initial version with new variables
#
# =============================================================================

# -----------------------------------------------------------------------------
#                          M A K E   O P T I O N S 
# -----------------------------------------------------------------------------
.SILENT :
.IGNORE :
.EXPORT_ALL_VARIABLES :
.DEFAULT : usage

# -----------------------------------------------------------------------------
#                         G L O B A L   V A R I B L E S
# -----------------------------------------------------------------------------
basedir					= /tfd
modulesdir				= $(basedir)/modules
interfacedir			= $(basedir)/interfaces

edidir					= /edi
edibindir               = $(edidir)/bin
ediserverdir			= $(edidir)/EDIServer

devsys					= maul
qasys					= mailer
prodsys					= fett


# -----------------------------------------------------------------------------
#                          P E R L   S E T T I N G S 
# -----------------------------------------------------------------------------
perl_compiler				= /usr/local/bin/perlapp
perl_libraries				= $(modules_dir)
perl_compiler_flags			= --force --lib .:$(perl_libraries)


# -----------------------------------------------------------------------------
#                              W E B R O O T
# -----------------------------------------------------------------------------
dev_webroot					= /var/www/html
qa_webroot					= /var/www/html
prod_webroot				= /usr/local/apache/htdocs




