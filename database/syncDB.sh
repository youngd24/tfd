#!/bin/sh
# =============================================================================
#
# syncDB.sh
#
# Script to sync the production db's to dev
#
# $Id: syncDB.sh,v 1.1 2002/10/28 17:17:12 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: syncDB.sh,v $
# Revision 1.1  2002/10/28 17:17:12  youngd
#   * First working version
#
# =============================================================================

echo "*******************************************"
echo "**       SYNCING DIGISHIP DATABASE       **"
echo "*******************************************"
sh ./loadProdData.sh digiship


echo "*******************************************"
echo "**        SYNCING MARKET DATABASE        **"
echo "*******************************************"
sh ./loadProdData.sh market


echo "*******************************************"
echo "**        SYNCING DEPOT DATABASE         **"
echo "*******************************************"
sh ./loadPostgresData.sh depot
