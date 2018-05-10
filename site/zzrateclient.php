<?php
# =============================================================================
#
# zzrateclient.php
#
# CZAR Rating Page
#
# $Id: zzrateclient.php,v 1.10 2002/10/30 20:36:51 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: zzrateclient.php,v $
# Revision 1.10  2002/10/30 20:36:51  youngd
#   * Added more debug statements.
#
# Revision 1.9  2002/10/09 19:01:45  youngd
#   * Changed all the includes to be physical instead of logical.
#
# Revision 1.8  2002/10/03 22:10:21  youngd
#   * Added some debug lines.
#
# Revision 1.7  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("zzrateclient.php: entering after initial requires");

#echo $debug;
/* THIS IS THE RATE CLIENT FOR THE SOCKET SERVER RUNNING TO COMMUNICATE WITH THE CZAR COM OBJECT
YOU MUST PASS 4 VARIABLES - ORIGIN, DESTINATION, WEIGHT AND SHIPCLASS FOR THIS TO WORK
SET DEBUG = 10161979 FOR THE DEBUG DISPLAY.
COMMUNICATIONS PROTOCOL IS AS FOLLOWS:
WHEN WRITING A TO BASEPRICE FUNCTION, ALWAYS APPEND '&LEN=(STINGLENGTHBEFORE&LEN=)'
*/

// check for debug value
if ($debug == 10161979) {
	$debug = 1;
	echo "<b>NOW DEBUGGING</b><br><br>";
	if (!($origin) and !($destination) and !($weight) and !($shipclass) and !($thecarrier[3])) {
		echo "<b>NO VALUES PASSED, ASSIGNING FOLLOWING VALUES:</b><br>ORIGIN=60614, DESTINATION=48837, WEIGHT=10000, SHIPCLASS=100<br><br>";
		$origin=60614;
		$destination=48837;
		$weight=10000;
		$shipclass=100;
		$thecarrier[7]="RDWY";
	}
}
else {
	// define debug as 0 to make sure no one enters 1 at the command line
	$debug = 0;
}

// if no values where entered for origin destination weight and class
if (!($origin) and !($destination) and !($weight) and !($shipclass)) {
	die ("NO VALUES PASSED -- CANNOT RATE SHIPMENT");
	}

// assign rate variables from rating.php
$srczip = $origin;
$dstzip = $destination;
$weight = $weight;
$cls = $shipclass;
$scac = $thecarrier[7];
debug("zzrateclient.php: srczip=$srczip, dstzip=$dstzip, weight=$weight, cls=$cls, scac=$scac");

// assign communication strings for socket writes
$ratestr = "BASEPRICE?SRCZIP=$srczip&DSTZIP=$dstzip&WEIGHT=$weight&CLASS=$cls";
$transstr = "TRANSTIME?SCAC=$scac&TYPE=LTL&SRCZIP=$srczip&DSTZIP=$dstzip";
$quitstr = "QUITE\n";
$infostr = "INFOA\n";
$verstr = "VERSIONA\n";

// assign port, protocol, and ip address variables
$serviceport = 4979;
$address = "64.95.80.2";
$proto = getprotobyname('tcp');

// i'm creating a variable to make sure the initial response from the socket is correct
$socketready = "READY";
$socketdone = "BYE";

// good errors
$errors = "";

// socketopen opens a php socket handle and returns that handle
function socketopen($prototype) {
	// create tcp/ip socket
	$socket = socket_create (AF_INET, SOCK_STREAM, $prototype);
   	return $socket;
}

// socket connect connect the socket handle to a port on a machine and returns that result
function socketconnect($socket, $address, $serviceport) {
	$result = socket_connect ($socket, $address, $serviceport);
	return $result;
}

// closes a sockethandle
function socketclose($socket) {
	$shutdown = socket_shutdown($socket,2);
	$closer = socket_close ($socket);
	return $closer;
}

// reads to the socket and returns number of bytes read


function socketread($socket) {
	$sockstring = "";
	$sockstring = socket_read($socket, 2048);
	/*while(read($socket, $buf, 1) and $buf != '\n') {
		$sockstring += $buf;
	}*/

	return $sockstring;
}

// writes to the socket and returns number of bytes written
function socketwrite($socket, $writestr) {
	$thewrite = socket_write($socket, $writestr, (strlen ($writestr)));
	return $thewrite;
}

// parses a rate string for rate and checksum
function parserate($ratestringgood) {
	// parse ratestringgood, structure = RESULT:CHECKSUM
	$ratearray = explode(":", $ratestringgood);
	return $ratearray;
}

// processes errors and then mails them
function onerror($heystring, $sendflag) {
	global $srczip, $dstzip, $weight, $cls, $REMOTE_ADDR, $errors, $baseprice;
	if ($heystring) {
		$errors = $errors . $heystring . "\n";
	}
	if ($sendflag and $errors != "") {
		$errors = $errors . "\n\nSOURCE = $srczip\nDEST = $dstzip\nWEIGHT = $weight\nCLASS = $cls\n\nIP = $REMOTE_ADDR";
		mail("darren_young@yahoo.com", "CZAR CLIENT ERROR", $errors);
		$baseprice = "ERROR";
		
	}
}

// gets the server info from the socket client
function getserverinfo($socket) {
	global $infostr, $verstr;
	$write = socketwrite($socket, $infostr);
	$read = socketread($socket) . "<br>" . socketread($socket) . "<br>" . socketread($socket) . "<br>" . socketread($socket) . "<br>" . socketread($socket) . "<br>" . socketread($socket) . "<br>" . socketread($socket) . "<br>" .  socketread($socket);
	$write = socketwrite($socket, $verstr);
	$read = $read . "<Br>" . socketread($socket);
	return $read;
}

// function calls begin here


// open socket
$currentsocket = socketopen($proto);
debug("zzrateclient.php: SOCKET OPEN = $currentsocket");
if ($debug == 1) {
	echo "<B>SOCKET OPEN = </B>$currentsocket<br><br>";
}

if ($currentsocket < 0) {
	onerror("CANNOT OPEN SOCKET HANDLE",0);
}


// connect sockets
$socketconnected = socketconnect($currentsocket, $address, $serviceport);
debug("zzrateclient.php: SOCKET CONNECT ON $address AT $serviceport = $socketconnected");
if ($debug == 1) {
	echo "<B>SOCKET CONNECT ON $address AT $serviceport = </B>$socketconnected<br>";
	echo "<B>SOCKET STRERROR() MESSAGE: </B>" . socket_strerror($socketconnected) ."<BR><BR>";
}

IF ($socketconnected < 0) {
	onerror("CANNOT CONNECT SOCKET TO $address ON $serviceport",0);
}



// read until value read equals the socketready message
$read = "";
// for ($i = 0; ($read != $socketready && $i < 25); $i++) {
	$read = socketread($currentsocket);
// }



debug("zzrateclient.php: INITIAL READ = $read ($i turns through loop)");
if ($debug == 1) {
	echo "<b>INITITAL READ = </b> <i>$read</i> ($i turns through loop)<br><br>";
}

//if ($read != $socketready) {
//	onerror("INITIAL READ NOT EQUAL TO SOCKET READY MESSAGE. VALUE READ = $read. EXPECTED $socketready. $i loops",0);
//}


// write ratestr
$writestr = $ratestr . "&LEN=" . strlen($ratestr) . "a \n";
$write = socketwrite($currentsocket, $writestr);
debug("zzrateclient.php: PRICE WRITE $write, $writestr");
if ($debug == 1) {
	echo "<b>PRICE WRITE</b> $write<br><b>WROTE </b> <i>$writestr</i><br><br>";
}

// read rate from socket
$read = "";
// for ($i = 0; ($read == "" && $i < 25); $i++) {
	$read = socketread($currentsocket);
// }

debug("zzrateclient.php: PRICE READ = $read ($i turns through loop)");
if ($debug == 1) {
	echo "<b>PRICE READ = </b> <i>$read</i> ($i turns through loop)<br><br>";
}


// parse the rate
$finalrate = parserate($read);
debug("zzrateclient.php: PARSING RATE: $finalrate[0] / $finalrate[1]");
if ($debug == 1) {
	echo "<b>PARSING RATE: </b>$finalrate[0] / $finalrate[1]</b><br><br>";
}

// checking final rate. first, did we get one? then does it pass the checksum? then, is it greater than 0?

if ($finalrate[0]) {

	// make sure rate response length mataches checksum
	if (strlen ($finalrate[0]) != $finalrate[1]) {
		$baseprice = "ERROR";
		onerror("RATE CHECKSUM ERROR. RATE RECIEVED = $finalrate[0]. CHECKSUM = $finalrate[1]",0);
	}
	else {
		// check to see if server returned -1
		if ($finalrate[0] < 0) {
			$baseprice = "ERROR";
			onerror("RECIEVED CZAR ERROR. RATE RECIEVED = $finalrate[0].",0);
		}
		else {
			$baseprice = $finalrate[0];
			debug("zzrateclient.php: the final rate (baseprice) is $finalrate[0]");
		}
	}
}
else {
	$baseprice = "ERROR";
	onerror("RATE ERROR. DID NOT RECEIVE RATE. RECEIVED '$finalrate[0]'. We went $i times through the loop.",0);
}

// write transstr for transit time
$writestr = $transstr . "&LEN=" . strlen($transstr) . "a \n";
$write = socketwrite($currentsocket, $writestr);
debug("zzrateclient.php: TRANSIT WRITE: $write, $writestr");
if ($debug == 1) {
	echo "<b>TRANSIT WRITE</b> $write<br><b>WROTE </b> <i>$writestr</i><br><br>";
}

// read transit time
$tread = "";
// for ($i = 0; ($tread == "" && $i < 25); $i++) {
	$tread = socketread($currentsocket);
// }

debug("zzrateclient.php: TRANSIT READ = $tread ($i turns through the loop)");
if ($debug == 1) {
	echo "<b>TRANSIT READ = </b> <i>$tread</i> ($i turns through the loop)<br><br>";
}

// parse the transit time
$transitar = parserate($tread);
$transit = $transitar[0];
debug("zzrateclient.php: PARSING TRANSIT: $transitar[0] / $transitar[1]");
if ($debug == 1) {
	echo "<b>PARSING TRANSIT: </b>$transitar[0] / $transitar[1]</b><br><br>";
}

// do some error checking on transit time
if ($transit < 1) {
	$transit = 0;
}

// write the info string if debug = 1
//if ($debug == 1) {
	//$infostring = getserverinfo($currentsocket);
//	echo "<b>SERVER INFO</b><br>$infostring<br><br>";
//}


// write the quit string
$write = socketwrite($currentsocket, $quitstr);
debug("zzrateclient.php: WRITING QUIT STRING = $write, $quitestr");
if ($debug == 1) {
	echo "<b>WRITING QUIT STRING =</b> $write<br><b>WROTE</b> <i>$quitstr</i><br><br>";
}

// read the final goodbye message from the socket
$read = socketread($currentsocket);
if ($debug == 1) {
	echo "<b>DISCONNECT READ = </b> <i>$read</i><br><br>";
}

/* comment this out for now
if ($read != $socketdone) {
	onerror("FINAL READ NOT EQUAL TO SOCKET DONE MESSAGE. VALUE READ = $read. EXPECTED $socketdone",0);
}
*/

//close the socket
$socketclosed = socketclose($currentsocket);
debug("zzrateclient.php: SOCKET CLOSED, RETURN CODE = $socketclosed");
if ($debug == 1) {
	echo "<B>SOCKET CLOSED RETURN CODE = </B>$socketclosed<br><br>";
}

//if ($socketclosed != 1) {
//	onerror("COULD NOT CLOSE SOCKET",0);
//}

onerror("",1);

?>

