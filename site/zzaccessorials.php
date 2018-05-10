<?php
# =============================================================================
#
# zzaccessorials.php
#
# Accessorials include page
#
# $Id: zzaccessorials.php,v 1.8 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzaccessorials.php,v $
# Revision 1.8  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.7  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.4  2002/09/13 08:34:54  webdev
#   * Many updates.
#   * Added services section in the rating page
#
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("zzaccessorials.php: entering after initial requires");

if ($accessorials) {
	$accessorialnames[] = "";
	$additionalcharges = 0;
	$additionalcost = 0;
	$asscount = count($accessorials) - 1;
	$asscontrol = "$accessorials[0] ";

	if (count($accessorials) > 1) {
		for($i = 1; $i < (count($accessorials) - 1); $i++) {
			$asscontrol = $asscontrol . "or assid = $accessorials[$i] ";
		}
		$asscontrol = $asscontrol . "or assid = $accessorials[$asscount]";
	}
$assstr = "SELECT * from accessorials where assid = " . $asscontrol;
$assquery = mysql_query($assstr) or die(mysql_error());
$x = 0;
while ($assline = mysql_fetch_row($assquery)) {
	$accessorialnames[$x] = $assline[1];
	$additionalcharges += $assline[2];
	$additionalcost += $assline[5];
	$x++;
	}
	
	// create string of accessorial ids, separated by commas, then put that string in a cookie
	
	foreach ($accessorials as $assa) {
		$asscookie .= "$assa,";
	}
	$asscookie = substr($asscookie, 0, (strlen($asscookie) - 1));
	
	setcookie("digishipaccessorials", $asscookie, 0, "/", "", 0);
}

debug("zzaccessorials.php: leaving");

?>
