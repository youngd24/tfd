<?php
# =============================================================================
#
# zzheader.php
#
# Standard site header page
#
# $Id: zzheader.php,v 1.13 2003/02/14 20:02:02 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzheader.php,v $
# Revision 1.13  2003/02/14 20:02:02  youngd
#   * Messing with background image.
#
# Revision 1.12  2003/01/14 18:48:49  webdev
#   * Raised accessorial rates.
#
# Revision 1.11  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.10  2002/10/04 16:22:25  youngd
# done for darren.
#
# Revision 1.9  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.8  2002/09/15 07:25:23  webdev
#   * Added logout link
#
# Revision 1.7  2002/09/14 06:56:50  webdev
# * Converted to UNIX format
#
# Revision 1.6  2002/09/13 14:52:47  webdev
#   * Updates from home, work in progress.
#
# Revision 1.5  2002/09/13 08:34:31  webdev
#   * Many updates
#
# Revision 1.4  2002/09/13 01:37:43  webdev
#   * Addd source header
#
# =============================================================================

require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("zzheader.php: entering after initial requires");

?>

<!--<body bgcolor=ffffff background="images/backgrounds/bg.gif" topmargin=20 leftmargin=0 marginwidth=0 marginheight=0>-->
<body bgcolor=ffffff background="images/backgrounds/bg.gif" topmargin=20 leftmargin=0 marginwidth=0 marginheight=0>

<table width="100%" height="100%" border=0 cellpadding=0 cellspacing=0>

<tr>
    
    <td height=10>
        
        <table width="100%" cellspacing=0 cellpadding=0 border=0>
            
            <tr>
                <td width=300>
                    <a href="index.php">
                    <img src="images/logos/mainfd.gif" height=28 width=300 border=0>
                    </a>
                </td>
                <td align=right>
                <?php
                    if ($userarray) {
                        echo '<font face="tahoma, verdana" size=1><b>WELCOME!</b> ' . $userarray[1] . ' is logged in.</font>
                              <font face=tahoma, verdana size=1><a href=logout.php class=links_general>(Log out)</a></font>
                              &nbsp; &nbsp; &nbsp;';

                    } else {
                        echo '<font face="tahoma, verdana" size=1>
                              <b>WELCOME!</b> 
                              <a href="login.php" class="links_inactive">LOGIN</a> 
                              or 
                              <a href="register.php" class="links_inactive">REGISTER</a>
                              </font>&nbsp; &nbsp; &nbsp;';
                    }
    
                ?>
                </td>
            </tr>
            
            <tr>
                <td bgcolor=000000 colspan=3 height=2>
                    <!-- <img src="images/pixels/blackpixel.gif" width="100%" height=1> -->
                </td>
            </tr>

            <tr height=10>
                <td bgcolor=A20E07 colspan=3>
		            <table width="750" cellspacing=0 cellpadding=0 border=0>
                        <tr>
                            <td width=8>&nbsp;</td>
                            <td align=center valign=middle width=25>
                                <a href="mydigiship.php">
                                <img src="images/icons/mydigi.gif" width=20 height=20 border=0>
                                </a>
                            </td>
                            <td valign=middle width=148>
                                <a href="mydigiship.php" class="toolbarlinks"> MY FREIGHT DEPOT
                                </a>
                            </td>
                            <td align=center valign=middle width=25>
                                <a href="rating.php">
                                <img src="images/icons/rate.gif" width=20 height=20 border=0>
                                </a>
                            </td>
                            <td valign=middle width=148>
                                <a href="rating.php" class="toolbarlinks"> RATING & SCHEDULING
                                </a>
                            </td>
                            <td align=center valign=middle width=25>
                                <a href="tracking.php">
                                <img src="images/icons/track.gif" width=20 height=20 border=0>
                                </a>
                            </td>
                            <td valign=middle width=148>
                                <a href="tracking.php" class="toolbarlinks"> SHIPMENT TRACKING
                                </a>
                            </td>
                            <td align=center valign=middle width=25>
                                <a href="reports.php">
                                <img src="images/icons/report.gif" width=20 height=20 border=0>
                                </a>
                            </td>
                            <td valign=middle width=148>
                                <a href="reports.php" class="toolbarlinks">SHIPMENT REPORTS
                                </a>
                            </td>
                            <td align=center valign=middle width=25>
                                <a href="reports.php">
                                <!--<img src="images/icons/help.gif" width=20 height=20 border=0>-->
                                </a>
                            </td>
							<?php
								if (getConfigValue("enableHelp") == 1) {
									echo "<td valign=middle width=148>";
									echo "<a href='help.php' class='toolbarlinks'>HELP</a>";
									echo "</td>";
								}
							?>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr height=1>
                <td bgcolor=000000 colspan=3 height=2>
                    <!-- <img src="images/pixels/blackpixel.gif" width="100%" height=2> -->
                </td>
            </tr>

        </table>
    </td>
</tr>
    <tr>
        <td valign=top>

        <?php
            debug("zzheader.php: leaving");
        ?>
