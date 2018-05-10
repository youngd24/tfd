<?php
# =============================================================================
#
# zzaddress.php
#
# Address include page
#
# $Id: zzaddress.php,v 1.7 2002/10/09 19:01:45 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzaddress.php,v $
# Revision 1.7  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/15 07:27:53  webdev
#   * Added source header
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

// if origin company was entered, insert into db and get insert id
if ($origincompany and $originaddress1 and $origincity and $originstate) {
// first check to see if it's already there
	$checkifexists = mysql_query("SELECT addressid from address where company = '$origincompany' and address1 = '$originaddress1' and city = '$origincity' and zip = '$originzip' and custid = '$userarray[0]'") or die (mysql_error());
	// if it's not there, insert it
	if (!($check = mysql_fetch_row($checkifexists))) {
		$origininsert = mysql_query("INSERT INTO address (company, address1, address2, city, state, zip, custid, contact, phone) VALUES ('$origincompany','$originaddress1','$originaddress2','$origincity','$originstate','$originzip','$userarray[0]','$origincontact','$originphone')") or die (mysql_error());
		$shiporiginid = mysql_insert_id();
	}
	// if it is there, grab the id
	else {
		$shiporiginid = $check[0];
	}
}
// if origin was pulled from pulldown menu
elseif ($originselected and $originselected != "-1") {
	$gettheorigin = mysql_query("SELECT * from address where addressid = $originselected and custid = $userarray[0]") or die (mysql_error());
	$check = mysql_fetch_row($gettheorigin);
	$shiporiginid = $check[0];
}




// if destination company was entered, insert into db and get insert id
if ($destinationcompany and $destinationaddress1 and $destinationcity and $destinationstate) {

// first check to see if it's already there
	$checkifexists = mysql_query("SELECT addressid from address where company = '$destinationcompany' and address1 = '$destinationaddress1' and city = '$destinationcity' and zip = '$destinationzip' and custid = '$userarray[0]'") or die (mysql_error());
	// if it's not there, insert it
	if (!($check = mysql_fetch_row($checkifexists))) {
		$destinationinsert = mysql_query("INSERT INTO address (company, address1, address2, city, state, zip, custid, contact, phone) VALUES ('$destinationcompany','$destinationaddress1','$destinationaddress2','$destinationcity','$destinationstate','$destinationzip','$userarray[0]','$destinationcontact','$destinationphone')") or die (mysql_error());
		$shipdestid = mysql_insert_id();
	}
	// if it is there, grab the id
	else {
		$shipdestid = $check[0];
	}
}
// if destination was pulled from pulldown menu
elseif ($destselected and $destselected != "-1") {
	$getthedest = mysql_query("SELECT * from address where addressid = $destselected and custid = $userarray[0]") or die (mysql_error());
	$check = mysql_fetch_row($getthedest);
	$shipdestid = $check[0];
}
	

// if billing info was pulled
if ($billingcompany and $billingaddress1 and $billingcity and $billingstate) {

// first check to see if it's already there
	$checkifexists = mysql_query("SELECT addressid from address where company = '$billingcompany' and address1 = '$billingaddress1' and city = '$billingcity' and zip = '$billingzip' and custid = '$userarray[0]'") or die (mysql_error());
	//if it's not there, insert it
	if (!($check = mysql_fetch_row($checkifexists))) {
		$billinginsert = mysql_query("INSERT INTO address (company, address1, address2, city, state, zip, custid) VALUES ('$billingcompany', '$billingaddress1', '$billingaddress2', '$billingcity', '$billingstate', '$billingzip', '$userarray[0]')") or die (mysql_error());
		$shipbillid = mysql_insert_id();
	}
	//if it is there, grab the id
	else {
		$shipbillid = $check[0];
	}
}
?>