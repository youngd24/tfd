# =====================================================================
#
# Makefile
#
# $Id: Makefile,v 1.9 2003/01/09 19:25:37 youngd Exp $
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
	sudo rm -f /edi/bin/edi-dispatch.pl
	sudo rm -f /edi/bin/edi-214.pl
	sudo rm -f /edi/bin/process.sh
	sudo cp edi-dispatch.pl /edi/bin/
	sudo cp edi-214.pl /edi/bin
	sudo cp process.sh /edi/bin/
	sudo chown root:root /edi/bin/edi-dispatch.pl
	sudo chown root:root /edi/bin/edi-214.pl
	sudo chown root:root /edi/bin/process.sh
	sudo chmod 755 /edi/bin/edi-dispatch.pl
	sudo chmod 755 /edi/bin/edi-214.pl
	sudo chmod 755 /edi/bin/process.sh
