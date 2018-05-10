# =====================================================================
#
# Makefile
#
# $Id: Makefile,v 1.2 2003/01/09 20:46:59 youngd Exp $
#
# Contents Copyright (c) 2000-2002, YoungHome.Com, Inc.
#
# Darren Young <darren_young@yahoo.com>
#
# =====================================================================
#
# vim:set noexpandtab:
#
# =====================================================================


# ---------------------------------------------------------------------
#                        V A R I A B L E S
# ---------------------------------------------------------------------


PREFIX    			= /var/www/cgi-bin


# ---------------------------------------------------------------------
#                           O P T I O N S
# ---------------------------------------------------------------------

.PHONY : install
.SILENT : 
.EXPORT_ALL_VARIABLES:
.IGNORE :

# ---------------------------------------------------------------------
#                     M I S C   T A R G E T S
# ---------------------------------------------------------------------

all : install
	sudo rm -f /edi/edi.cfg
	sudo cp edi.cfg /edi
	sudo chown root:root /edi/edi.cfg
	sudo chmod 644 /edi/edi.cfg
	cd EDIServer && make
	cd BOLSend && make
	cd Utilities && make
