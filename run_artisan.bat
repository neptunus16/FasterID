@echo off
set "PATH=C:\Windows\System32;C:\Windows\System32\WindowsPowerShell\v1.0;C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64;C:\laragon\bin\composer;%PATH%"
cd /d "%~dp0"
"C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe" artisan %*

