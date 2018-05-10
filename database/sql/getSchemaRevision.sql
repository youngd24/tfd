# ===================================================================
#
# getSchemaRevision.sql
#
# $Id: getSchemaRevision.sql,v 1.1 2002/08/07 00:20:35 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [darren@younghome.com]
#
# ===================================================================

select val from sysconfig where var='schema_revision';
