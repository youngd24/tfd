# =====================================================================
#
# Makefile
#
# $Id: Makefile,v 1.3 2003/01/07 20:50:37 youngd Exp $
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


install :
	echo "Installing"
	sudo cp sender.cfg /edi
	sudo cp ../XML/204.tmpl /edi
	sudo cp BOLSender.pl /edi/bin
	sudo chown root:root /edi/sender.cfg
	sudo chown root:root /edi/204.tmpl
	sudo chown root:root /edi/bin/BOLSender.pl
	sudo chmod 644 /edi/sender.cfg
	sudo chmod 644 /edi/204.tmpl
	sudo chmod 755 /edi/bin/BOLSender.pl

