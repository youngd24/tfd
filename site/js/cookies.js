/******************************************************************************/
/*                                                                            */
/* cookies.js                                                                 */
/*                                                                            */
/* JavaScript cookie read/write functions                                     */
/*                                                                            */
/* $Id: cookies.js,v 1.5 2002/11/21 08:15:08 webdev Exp $                     */
/*                                                                            */
/* Contents Copyright (c) 2002, Transport Investments, Inc.                   */
/*                                                                            */
/* Darren Young [dyoung@thefreightdepot.com]                                  */
/*                                                                            */
/******************************************************************************/
/*                                                                            */
/* ChangeLog:                                                                 */
/*                                                                            */
/* $Log: cookies.js,v $
/* Revision 1.5  2002/11/21 08:15:08  webdev
/*   * Added function document headers.
/*   * Cleaned up spaces and tabs in functions.
/*
/* Revision 1.4  2002/11/21 08:06:42  webdev
/*   * Added coments.
/*                                                                            */
/* Revision 1.3  2002/11/21 08:06:02  webdev                                  */
/*   * Adjusted headers.                                                      */
/*                                                                            */
/******************************************************************************/




/******************************************************************************/
/* NAME        : Cookie                                                       */
/* DESCRIPTION : Create a new cookie                                          */
/* ARGUMENTS   : document,name,hours,path,domain,secure                       */
/* RETURNS     : None                                                         */
/* NOTES       : None                                                         */
/* STATUS      : Stable                                                       */
/******************************************************************************/
function Cookie(document,name,hours,path,domain,secure) {
    // any VAR in "this" that does not start with a "$" will
    // be written into the cookie (read from also)
    this.$doc  = document
    this.$name = name
    if (hours)  this.$expiration=new Date((new Date()).getTime()+hours*3600000); else this.$expiration = null
    if (path)   this.$path   = path;                                             else this.$path       = null
    if (domain) this.$domain = domain;                                           else this.$domain     = null
    if (secure) this.$secure = true;                                             else this.$secure     = false
}



/******************************************************************************/
/* NAME        : CookieWrite                                                  */
/* DESCRIPTION : Write a cookie                                               */
/* ARGUMENTS   : None                                                         */
/* RETURNS     : None                                                         */
/* NOTES       : None                                                         */
/* STATUS      : Stable                                                       */
/******************************************************************************/
function CookieWrite() {
    var cookieval=""

    for (var prop in this) {
        if ((prop.charAt(0) == '$') || ((typeof this[prop]) == 'function') || prop == '') continue
        if (cookieval != "") cookieval += '&'
        cookieval+=prop+":"+escape(this[prop])
    }
    
    var cookie=this.$name+"="+cookieval
    if (this.$expiration) cookie+='; expires=' + this.$expiration.toGMTString()
    if (this.$path)       cookie+='; path='    + this.$path
    if (this.$domain)     cookie+='; domain='  + this.$domain
    if (this.$secure)     cookie+='; secure'
    this.$doc.cookie=cookie
}



/******************************************************************************/
/* NAME        : CookieRead                                                   */
/* DESCRIPTION : Read a cookie                                                */
/* ARGUMENTS   : None                                                         */
/* RETURNS     : None                                                         */
/* NOTES       : None                                                         */
/* STATUS      : Stable                                                       */
/******************************************************************************/
function CookieRead() {
    var allcookies=this.$doc.cookie

    if (allcookies=="") {
        return false
    }

    var start= allcookies.indexOf(this.$name+'=')

    if (start== -1) {
        return false
    }

    start += this.$name.length+1
    var end=allcookies.indexOf(';',start)
    if (end == -1) end=allcookies.length
    var cookieval = allcookies.substring(start,end)
    var a = cookieval.split('&')
    for (var i=0;i < a.length;i++) a[i]=a[i].split(':')
    for (var i=0;i < a.length;i++) this[a[i][0]]=unescape(a[i][1])
    return true
}



/******************************************************************************/
/* NAME        : CookieDelete                                                 */
/* DESCRIPTION : Delete a cookie                                              */
/* ARGUMENTS   : None                                                         */
/* RETURNS     : None                                                         */
/* NOTES       : None                                                         */
/* STATUS      : Stable                                                       */
/******************************************************************************/
function CookieDelete() {
    var cookie = this.$name+'='
    if (this.$path)   cookie+='; path='+this.$path
    if (this.$domain) cookie+='; domain='+this.$domain
    cookie+='; expires=Fri, 02-Jan-1970 00:00:00 GMT'  // MAKE IT EXPIRE!
    this.$doc.cookie=cookie
}

new Cookie()
Cookie.prototype.write = CookieWrite
Cookie.prototype.del   = CookieDelete
Cookie.prototype.read  = CookieRead

