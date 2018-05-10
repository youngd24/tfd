#!/bin/sh
# ==================================================================
#
# MKDIRS.SH
#
# Makes EDI Server Operation Directories
#
# $Id: mkdirs.sh,v 1.1 2002/10/20 20:26:27 youngd Exp $
#
# Contents Copyright (c) 2000, 2001 Digiship Corp
#
# Darren Young
# youngd@digiship.com
#
# ==================================================================
# 
echo "Creating directories..."
mkdir /ediroot
mkdir /ediroot/{etc,bin,lib,usr,tmp,pub}

mkdir /ediroot/usr/{lib,local}

mkdir /ediroot/pub/{upload,misc}

mkdir /ediroot/edi
mkdir /ediroot/edi/{inq,outq,work,data}
mkdir /ediroot/edi/data/{RDWY,CARG,CSXP}


echo "Creating users file"
cp users /ediroot/etc/
