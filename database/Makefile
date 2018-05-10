# ===================================================================
#
# Makefile
#
# Makefile for the tfd database schema. See the file README in this
# directory for a more thorough discussion of how to manage the
# database schema.
#
# $Id: Makefile,v 1.2 2002/07/13 07:55:13 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young <darren@younghome.com>
#
# ===================================================================
#
# vim:set noexpandtab:
#
# =====================================================================


.PHONY  : 
.SILENT :
.IGNORE :
.EXPORT_ALL_VARIABLES : SCHEMA_REVISION

SCHEMA_REVISION  = $(shell ./schema_revision.sh tfd.schema)


all : update-version

update-version :
	echo "Making $@"
	echo "Version $(SCHEMA_REVISION)"
