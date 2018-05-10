#!/bin/sh
# ============================================================================
#
# Script to start RateServer monitors
#
# $Id: doit.sh,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# ============================================================================
#
# $Log: doit.sh,v $
# Revision 1.1.1.1  2002/07/13 04:30:35  youngd
# initial import
#
# Revision 1.1.1.1  2001/12/15 18:17:56  youngd
# new import
#
# Revision 1.1.1.1  2001/12/12 19:00:28  youngd
# initial import
#
# Revision 1.4  2001/02/23 20:24:11  deploy
# Working version
#
# Revision 1.3  2001/02/23 19:57:13  deploy
# Adjusted to take into account the new logfile parameter
#
# Revision 1.2  2001/02/23 19:31:38  deploy
# Added Log header
#
#
# ============================================================================

# Which ones to monitor, results in loading <server>.cfg per monitor
MONS="dev prod"

if [ "$LOGNAME" != "root" ]
then
    echo "You must be root to run this script!"
    exit 1
fi

if [ ! -d /var/log/RSMon ]
then
    echo "Creating log directory /var/log/RSMon"
    mkdir /var/log/RSMon
fi

if [ ! -x ./rsmon.pl ]
then
    echo "rsmon is not executable, setting it."
    RETVAL=`chmod +x ./rsmon.pl`
    if [ $? != 0 ]
    then
        echo "Setting mode +x on rsmon.pl failed"
        exit 1
    fi
fi

for i in $MONS
do
    echo "Starting monitor for $i"

    if [ -f /var/log/RSMon/$i.log ]
    then
        echo "Removing $i.log"
        rm -f /var/log/RSMon/$i.log
        echo "Creating empty $i.log"
        touch /var/log/RSMon/$i.log
    else
        echo "Creating empty $i.log"
        touch /var/log/RSMon/$i.log
    fi
    ./rsmon.pl --config-file=$i.cfg &
done
