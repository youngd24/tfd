Rate Server Monitor (rsmon.pl) README

Design and Operations

$Id: README.TXT,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $

Contents Copyright (c) 2000, Digiship Corp.

Darren Young
youngd@digiship.com


Background

Digiship currently has a component known as the RateServer. This module runs on an NT server machine and listens for requests on tcp port 4979. Upon receipt of a connection, it services conversations from clients for shipment rating. Upon successfully calling the OLE DLL to rate the shipment, it returns the results to the client that requested it via the opened socket. This service is critical to operations and therefore has to be available 24x7.

Operation

While the rate server has the inherent ability to watch for certain errors and react accordingly, there are times that conditions will arise that have not been accounted for. These particular conditions may arise at times when personnel are not on-site to notice something has gone astray. While operations will maintain due vigilence to manually test and monitor the application, the need for automated testing and monitoring is needed. The package documented herein is designed for just this purpose. It will run on a machine somewhere on the network in the background and attempt to establish connections to the rating server. If the server is unavailable, it will retry based on certain rules and notify operations personnel if necessary.

Goals

   * Key values stored in a configuration file
   * TCP enabled.
   * Email notification.
   * Ability to run as a daemon on UNIX or as a service on NT.
   * Optional debug flags.
   * Log file ability.
   * Will contain "intelligent" rate client.
   * Small and simple to avoid bugs.

Process

   * Connect to a specified rate server and attempt to retrieve a rate.
   * Will perform this operation at a set interval, starting at 120 seconds.
   * If successful, will go back to "sleep".
   * If unsuccessful, will retry the operation an additional time to ensure that the failure was
     legitimate.
   * If unsuccessful the second time, will perform some type of notification to an administrator. Email 
     will be the first type supported.
   * After notification, it will reduce the sleep interval by a set increment upon every successive
     failure. This is to prevent excess notification to the site administrator (more later).
   * Upon a successful connection indicating that the server has become available again, it will retry
     the operation a second time to make sure the server is truly operational again.
   * After the second successful connection it will notify the administrator that the server has
     become available.
   * All operations will be logged to a log file on disk.
   * If run on UNIX, it will operate as a "daemon", if run on Windows NT, it will operate as an 
     NT "Service".

Design

The program itself will be written as a Perl script for a few reasons. First, the Rate Server itself was created in Perl, so it make sense to create the monitor in Perl (we already have Perl rate client code). Secondly, it is platform independent. The components that are plaftorm specific (UNIX daemon & NT service) may be isolated in functions or not even implemented. Finally, it is simple to implement and maintain.

Many monitoring programs suffer from a common design problem, they notify too much. For notifications that may be received on a rather expensive medium such as a cell phone, this will become troublesome. To deal with this problem, this program will increase the time value after unsuccessful connections. The end result is an elongating wait timer upon every bad connect. The reasoning behind this is that the first time the administrator has been notified about the bad connect, he will start working on the problem. There is no reason to notify that person every 60 seconds. All that needs to be sent are "reminder" messages that the service is still down. An of course, "service becomes available again" messages. Good messages are not sent at any time, there is no reason for the admin to know it's up and running. The assumption is that if you have not received any bad messages, it's up and running.

The actual "connection" will be a tcp socket pipe opened up to the rate server. The monitoring program will actually implement a simple rate client. The client portion of the code will send and receive all rate server messages in the correct protocol format. After the program client negotiates a conversation with the server, it will attempt to retrieve a base price. The parameters for the base price function will be set so the return result is known. The client can compare this return result from the server to the result it expects to get so it may determine that the server is functioning properly.

There are 4 basic error conditions that the client monitor may detect including:
   1. Unable to connect
   2. Connect but timeout
   3. Connect but unable to retrieve proper base price
   4. Connect, able to retrieve proper base price, but unable to shutdown connection

Before real coding will start, "macro" code will be produced so that the ideas may be mapped and refined in order to maximize time and ensure the program will perform as required. The macro code produced below is not in  any formal syntax, it is simply meant to represent, in English, how the program will operate. This example may be directly implemented in code to acieve the desired results. Here it goes:

PROGRAM START
READ IN CONFIGURATION FILE
   IF GOOD:
      SET KEY DYNAMIC VARIABLES
         SLEEP_TIME(SECONDS[60])
         SLEEP_INCREMENT(VALUE[*1.5])
         STATUS(VALUE[TRUE|FALSE])
         ADMIN(VALUE[user@domain.com])
   IF BAD:
      DIE
SET ADDITIONAL STATIC VARIABLES
ATTEMPT AN INITIAL CONNECTION
   IF GOOD:
      MOVE ON
   IF BAD:
      DIE
ENTER LOOP (WHILE(TRUE)):
   SLEEP (SLEEP_TIME):
      WAKEUP:
         IF LAST STATUS WAS GOOD:  --> Server was up 
            ATTEMPT CONNECTION
               IF GOOD:
                   RETURN
               IF BAD:
                  ATTEMPT CONNECTION
                     IF GOOD:
                        NOTIFY OF FALSE BAD
                        RETURN
                     IF BAD:
                        NOTIIFY OF SERVER DOWN
                        SET STATUS TO BAD
                        INCREMENT SLEEP_TIME VALUE(SLEEP_INCREMENT)
                        RETURN
                     ENDIF:
                ENDIF:
         IF LAST STATUS WAS BAD:  --> Server was down
            ATTEMPT CONNECTION
               IF GOOD:
                  ATTEMPT CONNECTION
                     IF GOOD:
                        NOTIFY OF SERVER UP
                        SET STATUS TO GOOD
                        RESET SLEEP_TIME TO DEFAULT
                        RETURN
                     IF BAD:
                        NOTIFY OF SERVER DOWN
                        SET STATUS TO BAD
                        INCREMENT SLEEP_TIME VALUE(SLEEP_INCREMENT)
                        RETURN
                     ENDIF:
               IF BAD:
                  ATTEMPT CONNECTION
                     IF GOOD:
                        NOTIFY OF SERVER UP
                        SET STATUS TO GOOD
                        RESET SLEEP_TIME TO DEFAULT
                        RETURN
                     IF BAD:
                        NOTIFY OF SERVER DOWN
                        SET STATUS TO BAD
                        INCREMENT SLEEP_TIME VALUE(SLEEP_INCREMENT)
                        RETURN
                     ENDIF:
               ENDIF:
         ENDIF:
   END SLEEP:
END LOOP:
          

Based on the basic design as well as the macro code, there are several methods that may be "rolled" up into functions. The ones identified are:
   * ATTEMPT CONNECTION
   * NOTIFY
   * SET STATUS
   * GET STATUS
   * SET SLEEP

ATTEMPT CONNECTION (SERVER):
   STRING SERVER = VALUE(SERVERNAME)
   RETURN = INTEGER STATUS([11|21|31|41|51])
   Open a connection to a rate server SERVER, attempt to retrieve a baserate and return a result code
   Return result codes are currently:
      11 -> Unable to connnect
      21 -> Connect, but timeout
      31 -> Connect, but unable to retrieve base price
      41 -> Connect, able to retrieve base price, but unable to shutdown connection
      51 -> Connect, able to retrieve base price and shutdown properly

NOTIFY (TYPE, RCPT, MSG):
   STRING TYPE = VALUE([EMAIL|PAGER])
   STRING RCPT = VALUE(RCPT)
   STRING MSG = VALUE(MSG)
   RETURN = INTEGER STATUS([0|1])
   Send a notification MSG to a given RCPT on network of TYPE

SET STATUS (STATUS):
   STRING STATUS = VALUE([GOOD|BAD])
   RETURN = INTEGER([0|1])
   Set the global variable status

GET STATUS ():
   RETURN = STRING(STATUS)
   Returns the current value of the global variable status

SET SLEEP (SECONDS):
   STRING SECONDS = VALUE(SECONDS)
   RETURN = INTEGER([0|1])
   Sets the sleep time for the application. Checks the global status, if the status is good, sets the
   default value. If the status is bad, checks the last value of the sleep and increments it by the
   value desired in the configuration file. For example, if the SLEEP_INCREMENT value is 1.5, it 
   increment the value time 1.5, so if the previous was 60 seconds, the new value would be (60*1.5) or
   90 seconds. The next time around, it does exactly the same process. If the value was bad, the new 
   value will be (90*1.5) or 135 seconds.


Documentation

This document represents pre-design information. It is meant as a medium to exchange ideas and to refine what will become the final product. The final documentation will be in whatever format the language uses for "self documentation". In this case, it will be in PerlDoc embedded within the program itself. This PerlDoc may be stripped out and converted to any number of other formats including text and html.

With PerlDoc, documenting the interface and the program actually occurs as the program develops. The information that is to be presented outside the program is embedded within the code. The perldoc tool is then used to strip this documentation out and convert it to human readable form.

This document as well as the program and the resulting PerlDoc will be added to the version control system. This allows the author to revert back in time whenever necessary.
