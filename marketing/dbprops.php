<!--
==============================================================================

dbprops.php

File that sets various database properties

$Id: dbprops.php,v 1.2 2002/08/20 15:36:26 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [darren@younghome.com]

==============================================================================

ChangeLog:

$Log: dbprops.php,v $
Revision 1.2  2002/08/20 15:36:26  youngd
* Cleaned html tags
* Added digiship_database variable

Revision 1.1  2002/08/20 14:39:36  youngd
* Initial version from template with basic settings

Revision 1.1  2002/08/20 02:30:51  youngd
* Initial version

==============================================================================
-->

<?php

    # The host that the database resides on
    $host = "localhost";

    # The user to authenticate with into the database
    $user = "php";

    # The password to use for that user
    $pass = "password";

    # The name of the database
    $database = "market";
    $market_database = "market";

    # The name of the digiship database
    $digiship_database = "digiship";

?>
