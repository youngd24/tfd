<!--
==============================================================================

error_handler.php

Page to handle application errors

$Id: error_handler.php,v 1.1 2002/08/29 16:30:42 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren_young@yahoo.com]

==============================================================================

ChangeLog:

$Log: error_handler.php,v $
Revision 1.1  2002/08/29 16:30:42  youngd
initial version

==============================================================================
-->

<?php

# ----------------------------------------------------------------------------
# NAME        : localErrorHandler
# DESCRIPTION : Function to deal with application errors
# ARGUMENTS   : string errorno
#             : string errmsg
#             : string filename
#             : string linenum
#             : string vard
# RETURN      : None
# NOTES       : None
# ----------------------------------------------------------------------------
function localErrorHandler ( $errorno, $errmsg, $filename, $linenum, $vars ) {

	$dt = date("Y-m-d H:i:s (T)");

	$errortype = array (
			1	   =>	"Error",
			2	   =>	"Warning",
			4	   =>	"Parsing Error",
			8	   =>	"Notice",
			16	   =>	"Core Error",
			32	   =>	"Core Warning",
			64	   =>	"Compile Error",
			128	   =>	"Compile Warning",
			256	   =>	"User Error",
			512	   =>	"User Warning",
			1024   =>	"User Notice"
		);

		$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

		print "Error: $err";
		print "<br>";
		print "Message: $errmsg";
		print "<br>";

	}

?>