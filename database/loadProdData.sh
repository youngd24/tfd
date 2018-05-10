#!/bin/sh
# ===================================================================
#
# loadProdData.sh
#
# Script to load current data from production to the local machine
#
# $Id: loadProdData.sh,v 1.8 2002/10/23 20:21:20 youngd Exp $
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
# ChangeLog:
#
# $Log: loadProdData.sh,v $
# Revision 1.8  2002/10/23 20:21:20  youngd
#   * Added the ability to load any database down.
#
# Revision 1.7  2002/10/18 13:36:45  youngd
#   * First working version
#
# Revision 1.6  2002/10/18 13:10:32  youngd
#   * Added test for dbfunctions.sh script. If it's not there, die.
#
# Revision 1.5  2002/08/07 00:24:31  youngd
# * Test versions
#
# Revision 1.4  2002/08/05 23:45:09  youngd
# * Ready for testing
#
# Revision 1.3  2002/08/05 23:39:46  youngd
# * Correctly loads schema now
#   - Darren Young
#
# Revision 1.2  2002/08/05 23:00:25  youngd
# * Still going
#   - Darren Young
#
# Revision 1.1  2002/08/05 22:55:40  youngd
# * Initial skeleton version
#   - Darren Young
#
# ===================================================================

CVSID="$Id: loadProdData.sh,v 1.8 2002/10/23 20:21:20 youngd Exp $"
VERSION=`echo $CVSID | awk '{print $3}'`


# We have to get the dbfunctions.sh stuff or we die
if [ -f dbfunctions.sh ]; then
    echo "Loading standard database functions"
    . dbfunctions.sh
else
    echo "Database functions file dbfunctions.sh not found in ."
    echo "Fatal. Bye."
    exit
fi


 
# -------------------------------------------------------------------
#                          F U N C T I O N S
# -------------------------------------------------------------------

typeset -f print_usage
typeset -f print_version

function print_usage {
    echo ""
    echo "Usage: `basename $0` <options> database"
    echo ""
    echo "Options:"
    echo "    -d   Enable debug"
    echo "    -h   Prints help"
    echo "    -v   Version"
    echo ""
    return
}


function print_version {
    echo "`basename $0` version $VERSION"
    echo "Contents Copyright (c) 2002, Transport Investments, Inc."
    echo "Darren Young [dyoung@thefreightdepot.com]"
    return
}
 

# -------------------------------------------------------------------
#                C O M M A N D   L I N E   O P T I O N S       
# -------------------------------------------------------------------
while getopts dhv args
do
    case "$args" in
        "d") echo "Debug enabled"
             DEBUG=1
             ;;
        "h") print_usage
             exit ;;
        "v") print_version 
             exit ;;
        [?]) print_usage
             exit ;;
    esac
done
shift `expr $OPTIND - 1`

if [ -z "$1" ]; then
    print_usage
    exit
fi
 

# -------------------------------------------------------------------
#                  G L O B A L   V A R I A B L E S
# -------------------------------------------------------------------

LOCAL_HOST="localhost"
LOCAL_DB="$1"
LOCAL_USERNAME="root"
LOCAL_PASSWORD="password"

PROD_DB_HOST="www.thefreightdepot.com"
PROD_DB_DB="$1"
PROD_DB_USERNAME="php"
PROD_DB_PASSWORD="password"

REAL_SCHEMA="tfd.schema"
TEMP_SCHEMA="tfd.schema.temp"


# -------------------------------------------------------------------
#                       S C R I P T   M A I N 
# -------------------------------------------------------------------


#
# Drop the database on the local machine
#
echo -n "Dropping database $LOCAL_DB on $LOCAL_HOST... "
mysql -h $LOCAL_HOST -u$LOCAL_USERNAME -p$LOCAL_PASSWORD << EOF > /dev/null 2>&1
   DROP DATABASE IF EXISTS $LOCAL_DB;
EOF

RESULT=$?
if [ $RESULT != 0 ]; then
        echo "[FAILED]"
        exit 1
else
        echo "[DONE]"
fi




#
# Create a new fresh database on the local machine
#
echo -n "Creating database (if it's not already there)... " 
mysql -h $LOCAL_HOST -u$LOCAL_USERNAME -p$LOCAL_PASSWORD << EOF > /dev/null 2>&1
    CREATE DATABASE $LOCAL_DB; 
EOF
         
RESULT=$? 
if [ $RESULT != 0 ]; then 
    echo "[FAILED]" 
    exit 1   
else  
    echo "[DONE]" 
fi 



#
# Connect to production and remove any old dump file(s)
#
echo -n "Connecting to production ($PROD_DB_HOST) and removing any old dump file(s)..." 
ssh root@www.thefreightdepot.com "if [ -f /tmp/$1.dmp ]; then rm -f /tmp/$1.dmp; fi" > /dev/null 2>&1
ssh root@www.thefreightdepot.com "if [ -f /tmp/$1.dmp.gz ]; then rm -f /tmp/$1.dmp.gz; fi" > /dev/null 2>&1

RESULT=$? 
if [ $RESULT != 0 ]; then 
    echo "[FAILED]" 
    exit 1   
else  
    echo "[DONE]" 
fi 




#
# Connect to the production machine and create a dump file
#
echo -n "Connecting to production and creating a compressed dump file..."
ssh root@www.thefreightdepot.com "mysqldump -u $PROD_DB_USERNAME -p$PROD_DB_PASSWORD $PROD_DB_DB > /tmp/$PROD_DB_DB.dmp && gzip /tmp/$PROD_DB_DB.dmp" > /dev/null 2>&1

RESULT=$? 
if [ $RESULT != 0 ]; then 
    echo "[FAILED]" 
    exit 1   
else  
    echo "[DONE]" 
fi 



#
# Copy the dump file down locally
#
echo -n "Copying dump file(s) from $PROD_DB_HOST to /tmp..."
sudo rm -fr /tmp/$1.dmp
sudo rm -fr /tmp/$1.dmp.gz
scp root@www.thefreightdepot.com:/tmp/$1.dmp.gz /tmp > /dev/null 2>&1
sudo gzip -d /tmp/$1.dmp.gz

RESULT=$? 
if [ $RESULT != 0 ]; then 
    echo "[FAILED]" 
    exit 1   
else  
    echo "[DONE]" 
fi 



# 
# Load the data into the local database
#

echo -n "Loading data into local database..."
mysql -u root -ppassword $1 < /tmp/$1.dmp

RESULT=$? 
if [ $RESULT != 0 ]; then 
    echo "[FAILED]" 
    exit 1   
else  
    echo "[DONE]" 
fi 


echo "All done"
exit
