<?php
# =============================================================================
#
# zzlogin.php
#
# The page that actually processes login information
#
# $Id: zzlogin.php,v 1.8 2002/11/15 19:23:48 webdev Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
#
# =============================================================================
#
# ChangeLog
#
# $Log: zzlogin.php,v $
# Revision 1.8  2002/11/15 19:23:48  webdev
#   * Added extended registration forms
#
# Revision 1.7  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.6.2.1  2002/11/05 15:15:12  webdev
#   * New registration in progress.
#
# =============================================================================

// Bring in our standard includes
require_once("config.php");
require_once("debug.php");
require_once("error.php");
require_once("event.php");
require_once("functions.php");
require_once("logging.php");

debug("zznewlogin.php: entering after initial requires");

if	($forgotepasswordyes) {
	require('zzmysql.php');
	
	// query db for password
	$getmail = mysql_query("Select name, password from customers where email = '$email'");
	if ($mailline = mysql_fetch_row($getmail)) {
		print("<script language='javascript'>alert('Your password has been e-mailed to you at $email');</script>");	
		//generate mail string
		$mailstr = "Hello $mailline[0],\nOur records show you recently requested your password for login at thefreightdepot.com.\n\nYour password is: $mailline[1]\n\nTo access the Freight Depot system, just point your web browser to www.thefreightdepot.com and enter your email address and the password above into the login form.\n\nSincerely,\n\nThe FD Team";
	
		//send mail
		mail($email, "FREIGHT DEPOT PASSWORD REQUEST -- DELETE WHEN FINISHED",$mailstr,"FROM: csr@thefreightdepot.com");
	}
}

?>

<title>The Freight Depot > Please Login</title>
<script language="JavaScript">
// form validation
function validate(frm) {
	var errors = "";
	if (frm.name.value == "") {
		errors += "Please enter your name.\n";
	}
	if (frm.company.value == "") {
		errors += "Please enter your company name.\n";
	}
	if (frm.email.value == "") {
		errors += "Please enter your email address.\n";
	}
	if (frm.pass1.value == "") {
		errors += "Please enter a password.\n";
	}
	if (frm.pass1.value != frm.pass2.value) {
		errors += "Your passwords do not match.\n";
	}
	if (errors != "") {
		alert(errors);
		return false;
	}
	else {
		return true;
	}
}

// time functions for time display
function nowtime() {
	now = new Date();
	days = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	months = new Array("Januray", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	day = days[now.getDay()];
	month = months[now.getMonth()];
	date = now.getDate();
	year = now.getYear();
	hours = now.getHours();
	if (hours > 12) {
		hours -= 12;
		ampm = "pm";
	}
	else if (hours == 12) {
		ampm = "pm";
	}
	else if (hours == 0) {
		hours = "12";
		ampm = "am";
	}
	else {
		ampm = "am";
	}
	minutes = now.getMinutes();
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	nowstr = "" + day + ", " + " " + month + " " + date + " " + " - " + hours + ":" + minutes + " " + ampm;
	return nowstr;
}
function imageswitch(image, status) {
	if (status == 1) {
		eval("document.images." + image + ".src = " + image + "on.src");
	}
	else {
		eval("document.images." + image + ".src = " + image + "off.src");
	}
}


</script>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>
</head>

<?php
require('zzheader.php');
?>
<table align=center>
<tr valign=middle><td align=center>
	<table width=500 border=0>
	<tr><td colspan=2><font face=verdana size=2><b>
	<?php
	// use this message if user tried to register and used an email that was already registered but didn't have pass
	if ($alreadyregistered == 1) {
		debug("zznewlogin.php: alreadregistered is set, telling the user to try another");
		echo "<font face=verdana size=2 color=cb0000><b>I'M SORRY, THIS E-MAIL ADDRESS HAS ALREADY BEEN REGISTERED. IF YOU'VE REGISTERED BEFORE AND FORGOTTEN YOUR PASSWORD, PLEASE USE THE FORM ON BOTTOM LEFT OF THIS PAGE TO HAVE YOUR PASSWORD E-MAILED TO YOU.</FONT></B><br><br>";
	}
	elseif ($nouser == 1 and $email and $password) {
		echo "<font face=verdana size=2 color=cb0000><b>WE COULD NOT FIND YOUR LOGIN INFORMATION. IF YOU HAVEN'T REGISTERED YET, USE THE FORM ON THE RIGHT TO CREATE AN ACCOUNT. IF YOU ARE REGISTERED, PLEASE RE-ENTER YOUR LOGIN INFORMATION. IF YOU'VE FORGOTTEN YOUR PASSWORD, ENTER YOUR EMAIL IN THE FORM ON THE BOTTOM LEFT.</FONT></B><br><br>";
	}
	else {
		echo "You must be logged in to use this feature of thefreightdepot.com!</b><br>
	If you have already registered and created an account at The Freight Depot, just enter your e-mail address and password into the form on the left. If you haven't created an account, just register using the form on the right.<br><br>";
	}
	?>
	</td></tr>
	<tr valign=top><td width=250>
		
		<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>
		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=7390C0 align=center><font face=verdana size=1><b>REGISTERED? LOGIN NOW!</b></font></td></tr>
		<tr><td bgcolor=FAFAFA align=center>
		<form method="POST" action="mydigiship.php">
			<table width=160>
			<tr><td><font face=tahoma size=1>E-MAIL ADDRESS:</font></td></tr>
			<tr><td><input size=20 name="email"></td></tr>
			<tr><td><font face=tahoma size=1>PASSWORD:</font></td></tr>
			<tr><td><input size=20 name="password" type="password"></td></tr>
			<tr><td align=center><input type=image border=0 src="images/buttons/loginnow.gif" width=99 height=21></td></tr>		
			</table>
				
		</td></form></tr>
	
		</table>
		</td></tr>
		</table>
		<br>
			
		<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>
		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=7390C0 align=center><font face=verdana size=1><b>FORGOT YOUR PASSWORD?</b></font></td></tr>
		<tr><td bgcolor=FAFAFA align=center>
		<form method=post action="zzlogin.php"><input type=hidden name=forgotepasswordyes value=1>
			<table width=160>
			<tr><td><font face=tahoma size=1>WE'LL E-MAIL IT TO YOU!</TD></TR>
			<tr><td><font face=tahoma size=1>E-MAIL ADDRESS:</font></td></tr>
			<tr><td><input size=20 name="email"></td></tr>
			
			<tr><td align=center><input type=image border=0 src="images/buttons/loginnow.gif" width=99 height=21></td></tr>		
			</table>
				
		</td></form></tr>
	
		</table>
		</td></tr>
		</table>
		
		
	</td>

	<td width=250 align=right>
		
	<table cellspacing=0 cellpadding=0 border=0><tr><td bgcolor=000000>

		<table width=170 cellspacing=1 cellpadding=0>
		<tr height=20><td bgcolor=7390C0 align=center><font face=verdana size=1><b>CREATE AN ACCOUNT!</b></font></td></tr>
		<tr><td bgcolor=FAFAFA>
		<form method="POST" action="mydigiship.php">
			<table width=160>
			<tr><td><font face=tahoma size=1>YOUR NAME:</font></td></tr>
			<tr><td><input size=20 name="name"></td></tr>
			<tr><td><font face=tahoma size=1>ADDRESS 1:</font></td></tr>
			<tr><td><input size=20 name="address1"></td></tr>
			<tr><td><font face=tahoma size=1>ADDRESS 2:</font></td></tr>
			<tr><td><input size=20 name="address2"></td></tr>
			<tr><td><font face=tahoma size=1>CITY:</font></td></tr>
			<tr><td><input size=20 name="city"></td></tr>
			<tr><td><font face=tahoma size=1>STATE/PROVINCE:</font></td></tr>
			<tr><td><input size=10 name="state"></td></tr>
			<tr><td><font face=tahoma size=1>POSTAL CODE:</font></td></tr>
			<tr><td><input size=10 name="zip"></td></tr>
			<tr><td><font face=tahoma size=1>COUNTRY:</font></td></tr>
			<tr>
				<td>
					<select name="country" style="font-family:verdana; font-size:10pt">
						<?php
							
							// Build the country selection based on the db table country_codes
							// Display the name, the value is the ANSI 3 letter code
							$codequery = mysql_query("SELECT * FROM country_codes WHERE active=1 ORDER BY country");
							while($codelist = mysql_fetch_row($codequery) ) {
								print "<option value=$codelist[2]>$codelist[0]";
							}
						?>
					</select>
				</td>
			</tr>

			<tr><td><font face=tahoma size=1>COMPANY:</font></td></tr>
			<tr><td><input size=20 name="company"></td></tr>
			<tr><td><font face=tahoma size=1>E-MAIL ADDRESS:</font></td></tr>
			<tr><td><input size=20 name="email"></td></tr>
			<tr><td><font face=tahoma size=1>PHONE:</font></td></tr>
			<tr><td><input size=20 name="phone"></td></tr>
			<tr><td><font face=tahoma size=1>PASSWORD:</font></td></tr>
			<tr><td><input size=20 name="pass1" TYPE="PASSWORD"></td></tr>
			<tr><td><font face=tahoma size=1>PASSWORD AGAIN:</font></td></tr>
			<tr><td><input size=20 name="pass2" TYPE="PASSWORD"></td></tr>
			<tr><td align=center><input type=image border=0 src="images/buttons/registernow.gif" width=99 height=21></td></tr>
			</table>

				
		</td>
		
		</form></tr>
	
		</table>
	</td></tr>
	</table>

	</td></tr>
	</table>
</td></tr>
</table>
<?php

require("zzfooter.php");

?>