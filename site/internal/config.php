<!--
===============================================================================

config.php

Site configuration file

$Id: config.php,v 1.2 2002/08/29 12:37:06 youngd Exp $

Contents Copyright (c) 2002, Transport Investments, Inc.

Darren Young [dyoung@thefreightdepot.com]

===============================================================================

Usage:

===============================================================================

ChangeLog:

$Log: config.php,v $
Revision 1.2  2002/08/29 12:37:06  youngd
no message

===============================================================================
-->

<?php

    # -------------------------------------------------------------------------
    # DATABASE SETTINGS
    #
    # Adjust these as necessary for the various environments.
    # -------------------------------------------------------------------------
    $database_host      = "www.thefreightdepot.com";
    $database_user      = "php";
    $database_pass      = "password";
    $database_db        = "test";


    # -------------------------------------------------------------------------
    # DATABASE TABLE MAPPINGS
    #
    # These are used to map logical (application) data table names to 
    # physical (database) table names.
    # -------------------------------------------------------------------------
    $database_tables = array ( 'accessorials'         => 'accessorials',
                               'acctmanagers'         => 'acctmanagers',
                               'acctmgrs'             => 'acctmgrs',
                               'address'              => 'address',
                               'browser'              => 'browser',
                               'cancel_reasons'       => 'cancel_reasons',
                               'carriers'             => 'carriers',
                               'customers'            => 'customers',
                               'digiship'             => 'digiship',
                               'quotenotes'           => 'quotenotes',
                               'quotes'               => 'quotes',
                               'shipment'             => 'shipment',
                               'shipment_types'       => 'shipment_types',
                               'shipmentaccessorials' => 'shipmentaccessorials',
                               'shipmentstatus'       => 'shipmentstatus',
                               'shipmentstatuscodes'  => 'shipmentstatuscodes',
                               'sysconfig'            => 'sysconfig',
                               'userprofiles'         => 'userprofiles',
                               'zipcitystate'         => 'zipcitystate',
                               'zips'                 => 'zips',
                               'zips2'                => 'zips2');


    # -------------------------------------------------------------------------
    # WEB SETTINGS
    #
    # These are used to set the logical url locations
    # -------------------------------------------------------------------------
    $secure_protocol    = "http";
    $use_ssl            = 0;                        // Flip to 1 if SSL is used
    $internal_url       = "/internal";
    $marketing_url      = "$internal_url/marketing";


    # -------------------------------------------------------------------------
    # EMAIL SETTINGS
    # -------------------------------------------------------------------------

    # -------------------------------------------------------------------------
    # ENVIRONMENT SPECIFIC SETTINGS
    # -------------------------------------------------------------------------
    $this_hostname = $_SERVER['SERVER_NAME'];

?>

