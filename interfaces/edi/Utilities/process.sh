#!/bin/sh
# ===================================================================
#
# process.sh
#
# Script to process all of the XML EDI files in /edi
#
# $Id: process.sh,v 1.8 2003/01/02 20:12:28 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# ===================================================================
#
# ChangeLog
#
# $Log: process.sh,v $
# Revision 1.8  2003/01/02 20:12:28  youngd
#   * Changed program paths to reflect new names (214 processor)
#
# Revision 1.7  2002/08/02 18:16:21  youngd
#    * Added sending of logfiles via email after processing
#
# Revision 1.6  2002/07/31 21:10:55  youngd
#    * added failed move
#
# Revision 1.5  2002/07/31 21:09:36  youngd
#    * redirect errors
#
# Revision 1.4  2002/07/31 21:06:52  youngd
#    * moved bin
#
# Revision 1.3  2002/07/31 21:02:44  youngd
#    * ready for production
#
# Revision 1.2  2002/07/31 21:02:27  youngd
#    * Added path
#
# Revision 1.1  2002/07/31 20:59:29  youngd
#    * Initial version
#
# ===================================================================


for i in `ls /edi/incoming`
do
    echo "Processing $i"
    /edi/bin/edi-dispatch.pl /edi/incoming/$i > /edi/logs/$i.log 2>&1
    RES=$?

    if [ "$RES" != "0" ]; then
        echo "Processing of $i failed"
        mv /edi/incoming/$i /edi/failed
    else
        cat /edi/logs/$i.log | mail -s $i.log darren_young@yahoo.com
        cat /edi/logs/$i.log | mail -s $i.log hpavlos@thefreightdepot.com
        echo "Completed successfully"
    fi
done


exit
