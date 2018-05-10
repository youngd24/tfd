# ===================================================================
#
# dbfunctions.sh
#
# Common database functions
#
# $Id: dbfunctions.sh,v 1.3 2002/08/07 00:24:31 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# ===================================================================
#
# vim: set noautoindent:
# vim: set nosmartindent:
#
# ===================================================================
#
# Usage:
#
# To use these functions in a script include the following line in
# your script:
#
#     . dbfunctions.sh
#
# Additionally you should have 4 variables set inside the calling
# script that will be used by the functions in here. They are:
# 
# HOSTNAME : The host where the database resides
# DATABASE : The database to perform operations on
# USERNAME : The username to authenticate with
# PASSWORD : The password for the username
#
# ===================================================================
#
# ChangeLog:
#
# $Log: dbfunctions.sh,v $
# Revision 1.3  2002/08/07 00:24:31  youngd
# * Test versions
#
# Revision 1.2  2002/08/06 23:42:12  youngd
# * Added vim header
#   - Darren Young
#
# Revision 1.1  2002/08/06 23:40:56  youngd
# * Initial version
#   - Darren Young
#
# ===================================================================


# -------------------------------------------------------------------
#                  G L O B A L   V A R I B L E S
# -------------------------------------------------------------------
MYSQL="/usr/bin/mysql"
MYSQLDUMP="/usr/bin/mysqldump"


# -------------------------------------------------------------------
#                       F U N C T I O N S 
# -------------------------------------------------------------------
typeset -f getSchemaRevision
typeset -f dumpDbData
typeset -f dumpTableData


# -------------------------------------------------------------------
# NAME        : getSchemaRevision
# DESCRIPTION : Returns the revision of a database
# ARGUMENTS   : String(hostname)
#             : String(database)
#             : String(username)
#             : String(password)
# RETURNS     : String(version)
# NOTES       : None
# -------------------------------------------------------------------
function getSchemaRevision {
    local HOSTNAME="$1"
    local DATABASE="$2"
    local USERNAME="$3"
    local PASSWORD="$4"

    VER=`mysql -h $HOSTNAME -D $DATABASE -u$USERNAME -p$PASSWORD < \
         sql/getSchemaRevision.sql`

    echo $VER | awk '{print $2}'
    
    return
}


# -------------------------------------------------------------------
# NAME        : dumpDbData
# DESCRIPTION : Dumps the contents of a database to a text file
# ARGUMENTS   : String(database), String(file)
# RETURNS     : True or False
# NOTES       : None
# -------------------------------------------------------------------
function dumpDbData {
    local DB="$1"
    local FILE="$2"
    return
}


# -------------------------------------------------------------------
# NAME        : dumpTaleData
# DESCRIPTION : Dumps the contents of a table to a text file
# ARGUMENTS   : String(database), String(table), String(file)
# RETURNS     : True or False
# NOTES       : None
# -------------------------------------------------------------------
function dumpTableData {
    local DB="$1"
    local TABLE="$2"
    local FILE="$3"
    return
}
