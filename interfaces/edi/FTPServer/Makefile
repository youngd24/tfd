# =============================================================================
#
# Makefile
#
# EDI-FTP Server Makefile
#
# $Id: Makefile,v 1.1 2002/10/20 20:26:27 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# =============================================================================

include ../../../includes.mk

# -----------------------------------------------------------------------------
#                        G L O B A L   V A R I B L E S
# -----------------------------------------------------------------------------

server_source       = EDIServer.pl
server_binary       = EDIServer



# -----------------------------------------------------------------------------
#                               T A R G E T S 
# -----------------------------------------------------------------------------

.PHONY : all clean pre-compile post-compile tags

tags :
	@echo "Updating tags"
	@ctags EDIServer.pl

all : EDIServer 

pre-compile :
	@echo "Making $@"

post-compile :
	@echo "Making $@"

clean :
	@echo "Making clean"
	@rm $(server_binary)

EDIServer : $(server_source) dbmod.pm
	@echo "Making $@"
	@make pre-compile
	$(perl_compiler) $(perl_compiler_flags) EDIServer.pl
	@make post-compile
