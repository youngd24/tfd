# =============================================================================
#
# Makefile
#
# EDI Client Makefile
#
# $Id: Makefile,v 1.1 2002/10/20 20:24:22 youngd Exp $
#
# Contents Copyright (c) 2000-2002, Digiship Corp.
#
# Darren Young
# darren_young@yahoo.com
#
# =============================================================================

toplevel			= /digiship
include $(toplevel)/includes.mk

# -----------------------------------------------------------------------------
#                        G L O B A L   V A R I B L E S
# -----------------------------------------------------------------------------

client_source       = EDIClient.pl
client			= EDIClient



# -----------------------------------------------------------------------------
#                               T A R G E T S 
# -----------------------------------------------------------------------------

.PHONY : pre-compile post-compile clean
all : EDIClient

pre-compile :
	@echo "Making $@"

post-compile :
	@echo "Making $@"

clean :
	@echo "Making clean"
	@rm $(client_binary)

EDIClient : $(client_source)
	@echo "Making $@"
	@make pre-compile
	$(perl_compiler) $(perl_compiler_flags) EDIClient.pl
	@make post-compile
