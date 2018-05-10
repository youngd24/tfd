/* ------------------------------------------------------------------------- */
/*                                                                           */
/* common.js                                                                 */
/*                                                                           */
/* Common JavaScript Functions                                               */
/*                                                                           */
/* $Id: common.js,v 1.18 2002/11/21 00:31:51 webdev Exp $
/*                                                                           */
/* Contents Copyrght (c) 2002, Transport Investments, Inc.                   */
/*                                                                           */
/* Darren Young [dyoung@thefreightdepot.com                                  */
/*                                                                           */
/* ------------------------------------------------------------------------- */
/*                                                                           */
/* Usage:                                                                    */
/*                                                                           */
/*    <script language="javaScript" src="/internal/common.js"                */
/*                                                                           */
/* ------------------------------------------------------------------------- */
/*                                                                           */
/* ChangeLog:                                                                */
/*                                                                           */
/* $Log: common.js,v $
/* Revision 1.18  2002/11/21 00:31:51  webdev
/*  * Added status bar and status messages.
/*
/* Revision 1.17  2002/11/21 00:09:42  webdev
/*   * Adjusted width and height of the customer lookup window
/*
/* Revision 1.16  2002/11/20 20:12:25  webdev
/*   * Added openCustomerLookupWindow function.
/*   * Added toolbar=yes to the BOL and INVOICE opening functions per Harry's request.
/*
/* Revision 1.15  2002/10/28 23:05:02  youngd
/*   * Changed the fax links
/*
/* Revision 1.14  2002/10/23 23:04:40  youngd
/*   * Changed carrier management to open in a new window.
/*
/* Revision 1.13  2002/10/23 21:44:19  youngd
/*   * Increase margin window sizes
/*
/* Revision 1.12  2002/10/15 21:18:38  youngd
/*   * Added more documentation headers.
/*
/* Revision 1.11  2002/10/15 21:07:35  youngd
/* smaller
/*
/* Revision 1.10  2002/10/15 21:06:18  youngd
/*   * Changed size
/*
/* Revision 1.9  2002/10/15 21:01:33  youngd
/*   * QuickRater in process.
/*
/* Revision 1.8  2002/10/15 20:31:15  youngd
/*   * Modified the print bol and print invoice quick links to open the associated
/*     document in a new window. Added JavaScript functions ot handle this.
/*
/* Revision 1.7  2002/10/15 18:31:17  youngd
/*   * Added openAdminManagementWindow method.
/*
/* Revision 1.6  2002/10/15 17:08:34  youngd
/*   * Added scrollbars to the bol.
/*
/* Revision 1.5  2002/10/15 06:24:58  youngd
/*   * Added rev to title
/*
/* Revision 1.4  2002/10/15 06:19:21  youngd
/*   * Moved all external functions into here.
/*
/* Revision 1.3  2002/10/14 23:12:26  youngd
/*   * Added resize and scroll options.
/*
/* Revision 1.2  2002/08/25 21:08:01  youngd
/* * updates
/*
/* Revision 1.1  2002/08/25 20:57:26  youngd
/* Updates
/*                                                                           */
/* ------------------------------------------------------------------------- */


/* ------------------------------------------------------------------------- */
/* NAME        : openCancelShipmentWindow                                    */
/* DESCRIPTION : Opens the shipment cancellation window                      */
/* ARGUMENTS   : None                                                        */
/* RETURN      : True                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openCancelShipmentWindow() {
    url = "/internal/cancelshipment.php";
    var cancelShipmentWindow =
        window.open(url,
                  "cancelShipmentWindow",
                  "resizable=no,height=200,width=375");
}
    


/* ------------------------------------------------------------------------- */
/* NAME        : openAdminRemoteWindow                                       */
/* DESCRIPTION : Opens the remote admin tools window                         */
/* ARGUMENTS   : None                                                        */
/* RETURN      : True                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openAdminRemoteWindow() {
	url = "/internal/adminremote.php";
    var adminRemoteWindow =
        window.open(url,
                    "adminRemoteWindow",
                    "resizable=no,height=410,width=300");
}    



/* ------------------------------------------------------------------------- */
/* NAME        : openReasonEditWindow                                        */
/* DESCRIPTION : Opens the cancellation reason edit window                   */
/* ARGUMENTS   : None                                                        */
/* RETURN      : True                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openReasonEditWindow() {
    var reasonEditWindow =
    window.open("/internal/reasonEdit.php?mode=view",
                "reasonEditWindow",
                "resizable=yes,scrolling=yes,height=350,width=450");
    return true;
}



/* ------------------------------------------------------------------------- */
/* NAME        : openMarginManagementWindow                                  */
/* DESCRIPTION : Opens the cancellation reason edit window                   */
/* ARGUMENTS   : None                                                        */
/* RETURN      : True                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openMarginManagementWindow() {
    url = "/internal/marginmaint.php";
    var marginManagementWindow = 
        window.open(url,
                    "marginManagementWindow",
                    "resizable=yes,scrollbars=yes,height=500,width=700");
}



/* ------------------------------------------------------------------------- */
/* NAME        : openAdminManagementWindow                                   */
/* DESCRIPTION : Opens the cancellation reason edit window                   */
/* ARGUMENTS   : None                                                        */
/* RETURN      : None                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openAdminManagementWindow() {
    url = "/internal/adminmaint.php";
    var adminManagementWindow = 
        window.open(url,
                    "adminManagementWindow",
                    "resizable=no,scrollbars=yes,height=350,width=385");
}



/* ------------------------------------------------------------------------- */
/* NAME        : openAdminManagementWindow                                   */
/* DESCRIPTION : Opens the cancellation reason edit window                   */
/* ARGUMENTS   : None                                                        */
/* RETURN      : None                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openCarrierManagementWindow() {
    url = "/internal/carriermaint.php";
    var carrierManagementWindow = 
        window.open(url,
                    "carrierManagementWindow",
                    "resizable=yes,scrollbars=yes,height=700,width=800");
}



/* ------------------------------------------------------------------------- */
/* NAME        : displayBillOfLading                                         */
/* DESCRIPTION : Opens a window and displays a bill of lading                */
/* ARGUMENTS   : shipmentid                                                  */
/* RETURN      : None                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function displayBillOfLading(shipmentid) {

	if ( shipmentid.value == "" ) {
		alert("You have to enter a shipment id (BOL) first");
		shipmentid.value = "shipmentid";
		return;
	}

	url = "/internal/csrbol.php?shipmentid=" + shipmentid.value;
	var billOfLadingWindow = 
		window.open(url,
		            "billOfLadingWindow",
                    "resizable=yes,scrollbars=yes,toolbar=yes,height=720,width=725");

}



/* ------------------------------------------------------------------------- */
/* NAME        : displayInvoice                                              */
/* DESCRIPTION : Opens a window and displays an invoice                      */
/* ARGUMENTS   : ordnumber (shipmentid)                                      */
/* RETURN      : None                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function displayInvoice(ordnumber) {

	url = "/internal/invoice.php?ordnumber=" + ordnumber.value;
	var invoiceWindow = 
		window.open(url,
		            "invoiceWindow",
                    "resizable=yes,scrollbars=yes,toolbar=yes,height=720,width=725");
}



/* ------------------------------------------------------------------------- */
/* NAME        : openCustomerLookupWindow                                    */
/* DESCRIPTION : Opens a window and displays the customer search             */
/* ARGUMENTS   : None                                                        */
/* RETURN      : None                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function openCustomerLookupWindow() {

    url = "/internal/custlookup.php";
    var customerLookupWindow = 
        window.open(url,
                    "customerLookupWindow",
                    "resizable=yes,scrollbars=yes,status=yes,height=400,width=800");

}



/* ------------------------------------------------------------------------- */
/* NAME        : faxBillOfLading                                             */
/* DESCRIPTION : Display a window with a BOL and fax cover                   */
/* ARGUMENTS   : ordnumber (shipmentid)                                      */
/* RETURN      : None                                                        */
/* NOTES       : None                                                        */
/* ------------------------------------------------------------------------- */
function faxBillOfLading(shipmentid) {

	url = "/internal/bolfax.php?shipmentid=" + faxshipmentid.value;
	var faxWindow = 
		window.open(url,
		            "faxWindow",
                    "resizable=yes,scrollbars=yes,height=720,width=725");
}


// SCRIPT END