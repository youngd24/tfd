# =====================================================================
#
# Makefile
#
# $Id: Makefile,v 1.3 2003/01/09 19:24:05 youngd Exp $
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


install :
	echo "Installing"
	sudo cp EDIServer.pl /edi/bin
	sudo cp ../edi.cfg /edi
	sudo chown root:root /edi/bin/EDIServer.pl
	sudo chown root:root /edi/edi.cfg
	sudo chmod 755 /edi/bin/EDIServer.pl
	sudo chmod 644 /edi/edi.cfg

