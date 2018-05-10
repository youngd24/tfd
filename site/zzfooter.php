<?php
# =============================================================================
#
# zzfooter.php
#
# Standard site footer page
#
# $Id: zzfooter.php,v 1.9 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzfooter.php,v $
# Revision 1.9  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.8  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.7  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.6  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.5  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.4  2002/09/13 08:34:54  webdev
#   * Many updates.
#   * Added services section in the rating page
#
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("zzfooter.php: entering after initial requires");

?>

<br>
</td></tr>
<tr><td height=40>
<table width="100%" cellspacing=0 cellpadding=0 border=0>
<tr height=1><td bgcolor=000000 colspan=3 height=1><img src="images/pixels/graypixel.gif" width="100%" height=2></td></tr>
<tr height=1><td bgcolor=ffffff colspan=3 height=1></td></tr>
<tr height=20><td bgcolor=D9E1EE>&nbsp; &nbsp;<font face="tahoma, verdana" size=1>Need Help? Call us at <B>866-445-1212</b></font></td>
<td bgcolor=D9E1EE align=right><font face=tahoma size=1>&copy; Copyright 2002 - THE FREIGHT DEPOT &nbsp; &nbsp; &nbsp;</td></tr>
<tr height=1><td bgcolor=ffffff colspan=3 height=1></td></tr>
<tr height=1><td bgcolor=000000 colspan=3 height=1><img src="images/pixels/graypixel.gif" width="100%" height=2></td></tr>
</table>
</td></tr></table>
</body>
</html>


<?php
    debug("zzfooter.php: leaving");
?>