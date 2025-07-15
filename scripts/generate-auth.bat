@echo off
REM Script batch untuk menggenerate kredensial auth dari .env ke Caddyfile
REM Usage: generate-auth.bat

echo.
echo ========================================
echo  AHZ-Stack-PHP Auth Generator (Windows)
echo ========================================
echo.

REM Jalankan script PHP
php src\generate-auth.php

REM Pause untuk melihat hasil
echo.
echo Tekan tombol apa saja untuk melanjutkan...
pause >nul