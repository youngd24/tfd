@echo off
c:\windows\system\regsvr32 /s /c /u DigiCarrier.dll
if %ERRORLEVEL% == 1 echo 1
if %ERRORLEVEL% == 2 echo 2
if %ERRORLEVEL% == 3 echo 3
