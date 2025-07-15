#!/bin/bash

# AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
# Version: 1.0.0
# Developer: ahadizapto (9hs@tuta.io)
# License: MIT

# Memastikan file .env ada
if [ ! -f .env ]; then
    echo "File .env tidak ditemukan. Menyalin dari .env.example..."
    cp .env.example .env
    echo "File .env telah dibuat. Silakan tinjau dan sesuaikan pengaturannya."
fi

# Mendapatkan nilai PHP_FRAMEWORK dari .env
PHP_FRAMEWORK=$(grep -E "^PHP_FRAMEWORK=" .env | cut -d '=' -f2)

# Jika PHP_FRAMEWORK tidak ditemukan, gunakan nilai default
if [ -z "$PHP_FRAMEWORK" ]; then
    PHP_FRAMEWORK="none"
    echo "PHP_FRAMEWORK tidak ditemukan di .env. Menggunakan nilai default: $PHP_FRAMEWORK"
fi

echo "Membangun AHZ-Stack-PHP dengan framework: $PHP_FRAMEWORK"

# Menghentikan container yang sedang berjalan
echo "Menghentikan container yang sedang berjalan..."
docker-compose down

# Membangun container dengan framework yang dipilih
echo "Membangun container..."
docker-compose build --build-arg PHP_FRAMEWORK=$PHP_FRAMEWORK

# Menjalankan container
echo "Menjalankan container..."
docker-compose up -d

echo "AHZ-Stack-PHP telah berhasil dibangun dan dijalankan dengan framework: $PHP_FRAMEWORK"
echo "Anda dapat mengakses aplikasi pada URL berikut:"
echo "- Dashboard: http://localhost"
echo "- phpMyAdmin: http://pma.localhost"
echo "- Redis Commander: http://redis.localhost"