#!/bin/sh
# =====================================================================
#
# getDbSchemaRevision.sh
#
# Returns the revision number of a given database schema
#
# $Id: getSchemaRevision.sh,v 1.2 2002/08/07 00:24:31 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# =====================================================================
#
# vim: set noautoindent:
# vim: set nosmartindent:
#
# ===================================================================
#
# Usage:
#
# getDbSchemaRevision.sh <options>
#
# ===================================================================
#
# Options:
#
# -h hostname
# -d database
# -u username
# -p password
#
# ===================================================================
#
# ChangeLog:
#
# $Log: getSchemaRevision.sh,v $
# Revision 1.2  2002/08/07 00:24:31  youngd
# * Test versions
#
# Revision 1.1  2002/08/07 00:22:36  youngd
# * getSchemaRevision.sh works
#
# Revision 1.2  2002/08/07 00:09:35  youngd
# * First working version
#
# Revision 1.1  2002/08/07 00:05:30  youngd
# * Initial version
#
# ===================================================================

. dbfunctions.sh


function print_usage {
	echo ""
	echo "Usage: `basename $0` <options>"
	echo ""
	echo "Options:"
	echo "     -h Hostname"
	echo "     -d Database"
	echo "     -u Username"
	echo "     -p Password"
	echo ""
	exit 1
}

while getopts h:d:u:p: args
do
	case "$args" in
		"h") HOSTNAME="$OPTARG" ;;
		"d") DATABASE="$OPTARG" ;;
		"u") USERNAME="$OPTARG" ;;
		"p") PASSWORD="$OPTARG" ;;
		[?]) print_usage ;;
	esac
done
shift `expr $OPTIND - 1`

if [ -z $HOSTNAME ]; then
	print_usage
	exit
fi

getSchemaRevision $HOSTNAME $DATABASE $USERNAME $PASSWORD
