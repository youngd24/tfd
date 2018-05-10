# ============================================================================
#
# default_data.sql
#
# Default Digiship Data
#
# $Id: default_data.sql,v 1.1 2002/08/23 20:10:59 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren_young@yahoo.com
#
# ============================================================================
#
# ChangeLog:
#
# $Log: default_data.sql,v $
# Revision 1.1  2002/08/23 20:10:59  youngd
# * Initial version
#
# ============================================================================

# -------------------------------------------------------------------
#                      D E F A U L T   D A T A
# -------------------------------------------------------------------

INSERT INTO edi VALUES (1, 'manual', '', '');

INSERT INTO sysconfig VALUES ('schema_revision', %SCHEMA_REVISION%);
INSERT INTO sysconfig VALUES ('schema_installed', SYSDATE());


INSERT INTO cancel_reasons VALUES (1,'Entry Error','An error occurred on entry of the shipment');
INSERT INTO cancel_reasons VALUES (2,'Order Cancelled','The order was cancelled by the customer');
INSERT INTO cancel_reasons VALUES (3,'Changed Transport Mode','The mode of transport was changed');
INSERT INTO cancel_reasons VALUES (4,'Carrier Cancelled','Carrier cancelled the shipment for some reason');
INSERT INTO cancel_reasons VALUES (5,'Administrative','Shipment was cancelled per Freight Depot administration');

