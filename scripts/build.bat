@echo off
REM AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
REM Version: 1.0.0
REM Developer: ahadizapto (9hs@tuta.io)
REM License: MIT

REM Memastikan file .env ada
IF NOT EXIST .env (
    echo File .env tidak ditemukan. Menyalin dari .env.example...
    copy .env.example .env
    echo File .env telah dibuat. Silakan tinjau dan sesuaikan pengaturannya.
)

REM Mendapatkan nilai PHP_FRAMEWORK dari .env
FOR /F "tokens=1,2 delims==" %%G IN (.env) DO (
    IF "%%G"=="PHP_FRAMEWORK" SET PHP_FRAMEWORK=%%H
)

REM Jika PHP_FRAMEWORK tidak ditemukan, gunakan nilai default
IF "%PHP_FRAMEWORK%"=="" (
    SET PHP_FRAMEWORK=none
    echo PHP_FRAMEWORK tidak ditemukan di .env. Menggunakan nilai default: %PHP_FRAMEWORK%
)

echo Membangun AHZ-Stack-PHP dengan framework: %PHP_FRAMEWORK%

REM Menghentikan container yang sedang berjalan
echo Menghentikan container yang sedang berjalan...
docker-compose down

REM Membangun container dengan framework yang dipilih
echo Membangun container...
docker-compose build --build-arg PHP_FRAMEWORK=%PHP_FRAMEWORK%

REM Menjalankan container
echo Menjalankan container...
docker-compose up -d

echo AHZ-Stack-PHP telah berhasil dibangun dan dijalankan dengan framework: %PHP_FRAMEWORK%
echo Anda dapat mengakses aplikasi pada URL berikut:
echo - Dashboard: http://localhost
echo - phpMyAdmin: http://pma.localhost
echo - Redis Commander: http://redis.localhost

pause