<!-----------------------------------------------------------------------------

source.php

Source code highlight / display page. Portions taken from the PHP manual.

$Id: source.php,v 1.3 2002/09/08 18:47:23 webdev Exp $

Contents Copyright (c) 2002, YoungHome.Com, Inc.

Darren Young [darren_young@yahoo.com]

-------------------------------------------------------------------------------

Usage:

    Install this script in the root of your Apache web server docroot.

    Call it with the URL:

        http://server_name>/source/<path_to_script>.php|.inc

    The path to the script is relative to the web server document root.

    Add the following lines to the Apache config file:

        <Location /source>
            ForceType application/x-httpd-php
        </Location>

-------------------------------------------------------------------------------

ChangeLog

$Log: source.php,v $
Revision 1.3  2002/09/08 18:47:23  webdev
* Copied fresh from production

Revision 1.1  2002/08/25 00:10:26  youngd
no message

------------------------------------------------------------------------------>

<HTML>

    <HEAD>

        <!-- Meta Tags -->
        <META NAME="Revision" CONTENT="$Revision: 1.3 $">
        <META NAME="Author"   CONTENT="$Author: webdev $">

        <!-- Set the page to expire immediately -->
        <?php header("Expires: " . date("D, d M Y H:i:s") . " GMT"); ?>

        <TITLE>Source Display</TITLE>

        <!-- CSS Styles -->
        <STYLE>

            <!-- Big text formatting (Verdana, 20 pixels) -->
            .big {
                font-family : Verdana;
                font-size   : 20 px;
            }

            <!-- Used for error messages (Verdana, Bold, 16 pixels) -->
            .error {
                font-family : Verdana;
                font-size   : 16 px;
                font-weight : Bold;
            }

            <!-- Used for the page footer (Verdana, 12 pixels) -->
            .footer {
                font-family : verdana;
                font-size   : 12 px;
            }

        </STYLE>

    </HEAD>

    <BODY BGCOLOR=white>

    <!-- Start the display script -->
    <?php

        // PATH_TRANSLATED will take the supplied URL and convert it to the name of the file
        // on the local disk
        $script = getenv ("PATH_TRANSLATED");
        
        // If they gave us a script name, either physical or relative, dump it to the 
        // screen in PHP syntax highlighted format.
        if ( !$script ) {

            // oops, the didn't give us a script name
            echo "<DIV CLASS=error>ERROR: Script Name needed</DIV><BR>";
        
        } else {
        
            // Only allow php related pages to be dumped
            if ( ereg("(\.php|\.inc)$", $script ) ) {

                // Give it a header
                echo "<DIV CLASS=big>Source of: $script</DIV>\n<HR>\n";
                
                // This dumps the contents of the file to the screen with pretty color
                // highlighting.
                highlight_file($script);
            
            } else {
            
                // They requested something other than *.php or *.inc
                echo "<DIV CLASS=error>ERROR: Only PHP or include script names are allowed</DIV>"; 
            
            }
        
        }
    
        // Just so they know when it was generated in case they want to print the 
        // page and come back to it later.
        echo "<HR>";
        echo "<DIV CLASS=footer>Processed: " . date("Y/M/d H:i:s",time()) . "</DIV>";

    ?>

    </BODY>

</HTML>