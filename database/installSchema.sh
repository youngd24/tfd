#!/bin/sh
# =====================================================================
#
# installSchema.sh
#
# Script to install and load a new schema to a given database server.
# See the README file in this directory for a more detailed discussion
# on how to make changes and install them.
#
# $Id: installSchema.sh,v 1.4 2002/08/07 00:24:31 youngd Exp $
#
# Contents Copyright (c) 2002, The Freight Depot
#
# Darren Young <darren@younghome.com>
#
# =====================================================================
#
# vim: set noautoindent:
# vim: set nosmartindent:
#
# ===================================================================


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
		"h") HOST="$OPTARG" ;;
		"d") DATABASE="$OPTARG" ;;
		"u") USER="$OPTARG" ;;
		"p") PASSWD="$OPTARG" ;;
		[?]) print_usage ;;
	esac
done
shift `expr $OPTIND - 1`

if [ -z $HOST ]; then
	print_usage
	exit
fi


SCHEMA="tfd.schema"
TEMP_SCHEMA="$SCHEMA.tmp"


echo -n "Dropping database (if it's there already)... "
mysql -h $HOST -u$USER -p$PASSWD << EOF
	DROP DATABASE IF EXISTS $DATABASE;
EOF

RESULT=$?
if [ $RESULT != 0 ]; then
	echo "Database drop failed!"
	exit 1
else 
	echo "[DONE]"
fi


echo -n "Creating database (if it's not already there)... "
mysql -h $HOST -u$USER -p$PASSWD << EOF
	CREATE DATABASE IF NOT EXISTS $DATABASE;
	USE $DATABASE;
EOF

RESULT=$?
if [ $RESULT != 0 ]; then
	echo "Database create failed!"
	exit 1
else 
	echo "[DONE]"
fi


echo -n "Loading default data into database... "
SCHEMA_REVISION=`./schema_revision.sh $SCHEMA`
cat $SCHEMA | sed -e "s/%SCHEMA_REVISION%/$SCHEMA_REVISION/g" > $TEMP_SCHEMA

mysql -h $HOST -D $DATABASE -u$USER -p$PASSWD < $TEMP_SCHEMA

RESULT=$?
if [ $RESULT != 0 ]; then
	echo "Database load failed!"
	exit 1
else 
	echo "[DONE]"
fi

rm -f $TEMP_SCHEMA
echo "All done"
