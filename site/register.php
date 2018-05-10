<?php
# =============================================================================
#
# register.php
#
# User registration page
#
# $Id: register.php,v 1.7 2002/11/14 21:32:26 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: register.php,v $
# Revision 1.7  2002/11/14 21:32:26  youngd
#   * Conflicts resolved and merged with BRANCH_2002-11-04
#
# Revision 1.6.2.2  2002/11/07 22:21:45  webdev
#    * Added country field and automatic drop-down selector from database.
#
# Revision 1.6.2.1  2002/11/05 15:15:12  webdev
#   * New registration in progress.
#
# Revision 1.6  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.5  2002/09/15 07:26:53  webdev
#   * Added source header
#
# =============================================================================
?>

<html>
<head>
	<title>The Freight Depot > Register</title>
	
<link rel="stylesheet" type="text/css" href="css/main.css">
<script language="JavaScript" src="js/main.js"></script>

<script language="JavaScript">
	function validate(frm) {
		var errors = "";
		
		if (frm.name.value == "") {
			errors += "Please enter your name.\n"
		}
		if (frm.address1.value == "") {
			errors += "Please enter your address.\n"
		}
		if (frm.city.value == "") {
			errors += "Please enter your city.\n"
		}
		if (frm.state.value == "") {
			errors += "Please enter your state.\n"
		}
		if (frm.zip.value == "") {
			errors += "Please enter your zip.\n"
		}

		if (frm.email.value == "") {
			errors += "Please enter your email.\n"
		}
		if (frm.phone.value == "") {
			errors += "Please enter your phone.\n"
		}

		if (frm.pass1.value == "") {
			errors += "Please enter your password.\n"
		}
		if (frm.pass2.value == "") {
			errors += "Please confirm your password.\n"
		}

		if (errors != "") {
			alert(errors);
			return false;
		}
		else {
			return true;
		}
	}


</script>

</head>

<?php

require("zzheader.php");

?>
<br><br>
<blockquote>
		<form name="frmRegister" method="POST" action="mydigiship.php" onSubmit="return validate(this);"><b>
		<font face="'Trebuchet MS', tahoma" size=2>Fill out all the following form fields to create an account with The Freight Depot.<br><br>
		<blockquote>

		<table cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>YOUR NAME:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="name" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>ADDRESS 1:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="address1" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>ADDRESS 2:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="address2" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>CITY:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="city" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>STATE/PROVINCE:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=3 name="state" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>POSTAL CODE:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=10 name="zip" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>COUNTRY:</b>&nbsp;</font></td>
				<td align=left valign=middle>
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
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>COMPANY:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="company" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>EMAIL:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="email" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>PHONE:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 name="phone" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>PASSWORD:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 type=password name="pass1" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1><b>PASSWORD AGAIN:</b>&nbsp;</font></td>
				<td align=left valign=middle><input size=20 type=password name="pass2" style="font-family:verdana; font-size:10pt"></td>
			</tr>
			<tr>
				<td align=right valign=middle><font face="verdana" size=1>&nbsp;</font></td>
				<td align=left valign=middle><input type=image border=0 src="images/buttons/registernow.gif" width=99 height=21></td>
			</tr>
		</table>
		</blockquote>
	</form>
</blockquote>

<?php

require("zzfooter.php");

?>