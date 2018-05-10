#!/bin/sh
# ===================================================================
#
# schema_revision.sh
#
# Script to return the revision of a given schema file.
#
# $Id: schema_revision.sh,v 1.1 2002/07/13 07:55:13 youngd Exp $
#
# Contents Copyright (c) 2000-2002, YoungHome.Com, Inc.
#
# Darren Young <darren@younghome.com>
#
# ===================================================================
#
# vim: set noautoindent:
# vim: set nosmartindent:
#
# ===================================================================

if [ -z "$1" ]; then
    echo "Usage: `basename $0` <schema_file>"
    exit 1
else
    SCHEMA="$1"
    REVISION=`grep Id $SCHEMA | awk '{print $4}'`
    if [ ! -z "$REVISION" ]; then
        echo "$REVISION"
        exit 0
     else
        echo "Revision not found in file $SCHEMA"
        exit 1
    fi
fi
