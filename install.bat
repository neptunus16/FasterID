@echo off
set "PATH=C:\Windows\System32;C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\git\cmd;%PATH%"
cd /d "%~dp0"
"C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe" "C:\laragon\bin\composer\composer.phar" install --no-interaction

