@echo off
if "%1" == "" goto usage
if "%1" == "--version" goto version
if "%1" == "local" goto doLocalRate
if "%1" == "dev" goto doDevRate
if "%1" == "prod" goto doProdRate
goto usage

:doLocalRate
RateClient --carrier=RDWY --srczip=60601 --dstzip=45345 --server=127.0.0.1 --weight=15000 --class=55 
goto end

:doDevRate
RateClient --carrier=RDWY --srczip=60601 --dstzip=45345 --server=192.168.4.20 --weight=15000 --class=55 
goto end

:doProdRate
RateClient --carrier=RDWY --srczip=60601 --dstzip=45345 --server=216.80.68.206 --weight=15000 --class=55 
goto end

:version
echo $Id: rate.bat,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
goto end

:usage
echo Usage: rate [environment]
echo   where [environment] is:
echo        local - 127.0.0.1
echo        dev   - 192.168.4.20
echo        prod  - 216.80.68.206
goto :end



:end
