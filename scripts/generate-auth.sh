#!/bin/bash
# Script untuk menggenerate kredensial auth dari .env ke Caddyfile
# Usage: ./generate-auth.sh

echo
echo "========================================"
echo "  AHZ-Stack-PHP Auth Generator (Unix)"
echo "========================================"
echo

# Jalankan script PHP
php src/generate-auth.php

echo
echo "Script selesai dijalankan."