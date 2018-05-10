# ===================================================================
#
# Makefile
#
# Web Site Makfile
#
# $Id: Makefile,v 1.4 2002/08/21 18:00:59 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# ===================================================================
#
# vim:set noexpandtab:
#
# ===================================================================
#
# Usage:
#
#
# ===================================================================
#
# ChangeLog:
#
# $Log: Makefile,v $
# Revision 1.4  2002/08/21 18:00:59  youngd
# * Added main site installer
#
# Revision 1.3  2002/08/21 17:53:17  youngd
# * Added install and clean targets
#
# Revision 1.2  2002/08/15 16:13:55  youngd
# * Initial version
#
# ===================================================================


include ../includes.mk

.SILENT :
.IGNORE : install clean
.EXPORT_ALL_VARIABLES :
.DEFAULT : install


all : install


clean : 
	echo "Making $@"
	rm -fr /var/www/html/*
	echo "Done"


install : clean
	echo "Making $@"
	if [ `hostname` == "maul" ]; \
	then \
		echo "Installing on maul" ;\
		cp -dpR * /var/www/html ;\
	fi
	cd ../marketing && make install
	echo "Done"
