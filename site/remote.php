<?php
# =============================================================================
#
# remote.php
#
# Remote control page
#
# $Id: remote.php,v 1.3 2002/10/02 20:01:42 youngd Exp $
#
# Contents Copyright (c) 2002, Transport Investments, Inc.
#
# Darren Young [dyoung@thefreightdepot.com]
# 
# =============================================================================
#
# ChangeLog:
# 
# $Log: remote.php,v $
# Revision 1.3  2002/10/02 20:01:42  youngd
#   * Adjusted comments to be 80 characters
#   * Added source headers where necessary.
#   * Added requires to the remaining pages.
#
# Revision 1.2  2002/09/19 20:47:57  youngd
#   * Changed harry's email to be the new one at aol
#
# Revision 1.1  2002/09/19 19:10:58  youngd
#   * New file addition
#
# =============================================================================

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html>
	<head>
		<title>The Freight Depot: Depot Floater</title>
		<style type="text/css">
			<!--
			.newshead1 {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 16px;
					font-weight     : 900;
					color           : #FFFFFF;
					text-decoration : none;
					}

			.newshead2 {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       :16px;
					line-height     :20px;
					color           :#FFFFFF;
					text-decoration :none;
					letter-spacing  :1pt;
					}

			.newsitem {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 12px;
					line-height     : 14px;
					color           : #FFFFFF;
					text-decoration : none;
					}

			.newsitemlink {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 10px;
					font-weight     : bold;
					color           : #FFFFFF;
					text-decoration : underline;
					}

			.customertools {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 12px;
					font-weight     : normal;
					color           : #FFFFFF;
					text-decoration : none;
					}

			.customertools:hover {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 12px;
					font-weight     : normal;
					color           : #FFFFFF;
					text-decoration : underline;
					}

			.footer {
					font-family : Verdana, Arial, Helvetica, sans-serif;
					font-size   : 10px;
					line-height : 13px;
					color       : #FFFFFF
					}

			.toolbox {
					font-family : Verdana, Arial, Helvetica, sans-serif;
					font-size   : 10px;
					}

			.customertools2 {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 12px;
					color           : #FFFFFF;
					text-decoration : underline;
					}

			.customertoolssmall {
					font-family     : Verdana, Arial, Helvetica, sans-serif;
					font-size       : 10px;
					color           : #FFCC33;
					text-decoration : none;}
			 -->
		</style>
		
		<script language="javascript">

				function dateConfirm(){
					error_message="Please enter the date in this format mm/dd/yyyy";
					n = prompt("Please enter shipment delivery date: (mm/dd/yyyy +/- 30 days)","");
					document.shippingdocs.date.value=n;
					if (n.length != 10){
						alert(error_message);
						return false;
					}
					mPart = n.split('/');
					if (mPart[1] == undefined || mPart[2] == undefined){
						alert(error_message);
						return false;
					}
					if (mPart[0] < 1 || mPart[0] > 12){
						alert(error_message);
						return false;
					}
					if (mPart[0] == 4 || mPart[0] == 6 || mPart[0] == 9 || mPart[0] == 11){
						maxDay = 30;
					} else if (mPart[0] == 2) {
						if (mPart[2] % 4 > 0) maxDay =28;
							else if (mPart[2] % 100 == 0 && mPart[2] % 400 > 0) maxDay = 28;
						else maxDay = 29;
					} else {
						maxDay=31;
					}
					if (mPart[1] < 1 || mPart[1] > maxDay){
						alert(error_message);
						return false;
					}
				}

		</script>
	</head>

<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#444285" vlink="#5C1E24" alink="#5C1E24">



<table width="165" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td valign="top" background="/elements/clr-blue.gif">
   <img src="/elements/home/2000-08-01/customertools.gif" width="160" height="32">
   <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr valign="top">
     <td width="100%" align="right" class="customertools" nowrap><a href="/tools/quiktrak.cgi" class="customertools2" target="main">TRACKING</a></td>
     <td width="14">&nbsp;</td>
    </tr>

<form name="tracking" action="http://www.quiktrak.roadway.com/cgi-bin/quiktrak" method="post" target="main">
<input type="hidden" name="type" value="0">
    <tr valign="top">
     <td align="right"><input type="text" size="15" name="pro0" class="toolbox"></td>
     <td class="customertoolssmall" valign="middle"><input type="image" border="0" name="go" src="/elements/func_buttons/go_text.gif" width="14" height="9"></td>
    </tr>
    <tr>
     <td align="right" class="customertoolssmall">enter PRO #&nbsp;</td>
     <td class="customertoolssmall">&nbsp;</td>
    </tr>
</form>

    <tr valign="top"> 
     <td width="100%" align="right" class="customertools"><a href="/tools/pod.cgi" class="customertools2" target="main">SHIPPING DOCUMENTS</a></td>
     <td width="14">&nbsp;</td>
    </tr>

<form name="shippingdocs" method="post" action="http://www.quiktrak.roadway.com/cgi-bin/pod" onSubmit="return dateConfirm()" target="main">
<input type="hidden" name="info" value="1">
<input type="hidden" name="date" value=''>
    <tr valign="top"> 
     <td width="100%" align="right"><input type="text" size="15" name="pro" class="toolbox"></td>
     <td class="customertoolssmall" valign="middle"><input type="image" border="0" name="go" src="/elements/func_buttons/go_text.gif" width="14" height="9"></td>
    </tr>
    <tr>
     <td align="right" class="customertoolssmall">enter PRO # for POD&nbsp;</td>
     <td class="customertoolssmall">&nbsp;</td>
    </tr>
</form>

    <tr valign="top"> 
     <td width="100%" align="right" class="customertools"><a href="/tools/pod.cgi" class="customertools2" target="main">FIND A<br>SERVICE CENTER</a></td>
     <td width="14">&nbsp;</td>
    </tr>

<form name="findservcent" method=POST action="http://my.roadway.com/public/cgi-bin/pub_redir" target="main">
<input type=hidden name=redir value="/RGN710">
<input type=hidden name=LSTSVC value="">

    <tr valign="top"> 
     <td width="100%" align="right"><input type="text" size="15" name="OZIP" class="toolbox"></td>
     <td class="customertoolssmall" valign="middle"><input type="image" border="0" name="go" src="/elements/func_buttons/go_text.gif" width="14" height="9"></td>
    </tr>
    <tr>
     <td align="right" class="customertoolssmall">enter zip code&nbsp;</td>
     <td class="customertoolssmall">&nbsp;</td>
    </tr>
</form>

    <tr valign="top">
     <td width="100%" align="right">
      <img src="/elements/subpages/btn-new-box.gif"> <a href="/tools/etracking.cgi" class="customertools2" target="main">E-MAIL TRACKING</a><br>
      <img src="/elements/spacer.gif" width="2" height="2"><br>
      <a href="/tools/ezrate.cgi" class="customertools2" target="main">RATING &amp; ROUTING</a><br>
      <img src="/elements/spacer.gif" width="2" height="2"><br>
      <a href="http://www.quiktrak.roadway.com/cgi-bin/claims" class="customertools2" target="main">CARGO CLAIMS</a><br>
      <img src="/elements/spacer.gif" width="2" height="2"><br>
      <a href="http://my.roadway.com/public/cgi-bin/pub_redir?redir=/rcs510" class="customertools2" target="main">OVERCHARGE CLAIMS</a><br>
      <img src="/elements/spacer.gif" width="2" height="2"><br>
      <a href="/tools/pu.html" class="customertools2" target="main">PICKUP REQUEST</a><br>
      <img src="/elements/spacer.gif" width="2" height="2"><br>
      <a href="/shippers/shipformlib.html" class="customertools2" target="main">FORMS LIBRARY</a><br>

      <p class="customertoolssmall"><a href="mailto:webmaster@roadway.com" class="customertoolssmall">email</a><br>
      <a href="/" class="customertoolssmall" target="main">home </a><br>
      <img src="/elements/spacer.gif" width="10" height="4"></p>

      <img src="/elements/spacer.gif" width="2" height="2">
     </td>
     <td width="14">&nbsp;</td>
    </tr>
   </table>
  </td>
  <td valign="top" background="/elements/home/2000-08-01/middlefade.gif"><img src="/elements/spacer.gif" width="9" height="20"></td>
 </tr>
</table>
</BODY>
</HTML>


