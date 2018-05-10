# =============================================================================
#
# Makefile
#
# Digiship Top Level Makefile
#
# $Id: Makefile,v 1.1.1.1 2002/07/13 04:30:34 youngd Exp $
#
# Contents Copyright (c) 2000-2002 Digiship corp.
#
# Darren Young
# darren_young@yahoo.com
#
# =============================================================================

include	includes.mk
.IGNORE : 

# -----------------------------------------------------------------------------
#                             V A R I A B L E S 
# -----------------------------------------------------------------------------

# Global variables
app_version               = "1.0.1"


# Directories
prefix                    = /digiship
site_dir                  = $(prefix)/site
modules_dir               = $(prefix)/modules
temp_dir                  = $(prefix)/temp
dist_dir                  = $(prefix)/distfiles

# Distribution Tar Files
site_tarfile              = digiship_site_$(app_version).tgz
modules_tarfile           = digiship_modules_$(app_version).tgz




# -----------------------------------------------------------------------------
#                     G L O B A L    T A R G E T S 
# -----------------------------------------------------------------------------

all : pre-prepare site-dist modules-dist post-prepare

clean : site-clean modules-clean


# -----------------------------------------------------------------------------
#                     G E N E R A L   T A R G E T S 
# -----------------------------------------------------------------------------

pre-prepare :
	@echo ""
	@echo "-------------------------------"
	@echo "Building Digiship version $(app_version)"
	@echo "-------------------------------"
	@echo ""

post-prepare:
	@echo ""
	@echo "Build complete."
	@echo ""




# -----------------------------------------------------------------------------
#                       S I T E   T A R G E T S 
# -----------------------------------------------------------------------------

$(site_tarfile) : 
	@echo " ** Making $@"
	@mkdir $(temp_dir)
	@echo " ** Copying"
	@cp -R $(site_dir)/* $(temp_dir)
	@echo " ** Cleaning out CVS files"
	@find $(temp_dir) -type d -name CVS > list
	@for i in `cat list`; \
	 do                   \
		rm -fr $$i;      \
	 done
	@echo " ** Creating tar file"
	@cd $(temp_dir) && tar -zcf $(dist_dir)/$(site_tarfile) .
	@echo " ** Cleaning up"
	@rm -fr $(temp_dir)
	@rm list

site-dist : $(site_tarfile)

site-clean :
	@echo " ** Making $@"
	@rm -f $(site_tarfile)
	@rm -fr $(temp_dir)




# -----------------------------------------------------------------------------
#                     M O D U L E S   T A R G E T S 
# -----------------------------------------------------------------------------

$(modules_tarfile) : 
	@echo " ** Making $@"
	@mkdir $(temp_dir)
	@echo " ** Copying"
	@cp -R $(modules_dir)/* $(temp_dir)
	@echo " ** Cleaning out CVS files"
	@find $(temp_dir) -type d -name CVS > list
	@for i in `cat list`; \
	 do                   \
		rm -fr $$i;      \
	 done
	@echo " ** Creating tar file"
	@cd $(temp_dir) && tar -zcf $(dist_dir)/$(modules_tarfile) .
	@echo " ** Cleaning up"
	@rm -fr $(temp_dir)
	@rm list

modules-dist : $(modules_tarfile)

modules-clean :
	@echo " ** Making $@"
	@rm -f $(modules_tarfile)
	@rm -fr $(temp_dir)
