#
# Rating Makefile
#
# $Id: Makefile,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#

INCLUDE="IO::Socket;IO::Select"
.SUFFIXES : .exe .pl

RATESERVER_SRC = RateServer.pl
RATESERVER_OBJ = RateServer.exe

RATECLIENT_SRC = RateClient.pl
RATECLIENT_OBJ = RateClient.exe

all : RateServer RateClient

RateServer : $(RATESERVER_OBJ)

RateClient : $(RATECLIENT_OBJ)


clean : 
	@rm -f RateServer.exe
	@rm -f RateClient.exe


.pl.exe :
	@echo "Compiling $<"
	@perlapp -a $(INCLUDE) -f $<
