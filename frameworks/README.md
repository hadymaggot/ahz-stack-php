# Frameworks Directory

Direktori ini digunakan untuk menyimpan framework PHP yang diinstal secara dinamis berdasarkan konfigurasi di file `.env`.

## Cara Menggunakan

1. Buka file `.env` di root proyek
2. Cari variabel `PHP_FRAMEWORK`
3. Atur nilai sesuai dengan framework yang ingin digunakan:
   - `none` - Menggunakan aplikasi default AHZ-Stack-PHP
   - `laravel` - Menggunakan Laravel framework
   - `symfony` - Menggunakan Symfony framework
   - `codeigniter` - Menggunakan CodeIgniter framework

## Contoh

```
# PHP Framework Settings
# Available options: none, laravel, symfony, codeigniter
PHP_FRAMEWORK=laravel
```

## Catatan

- Setelah mengubah nilai `PHP_FRAMEWORK`, Anda perlu me-rebuild container dengan perintah:
  ```
  docker-compose down
  docker-compose build
  docker-compose up -d
  ```
- Framework akan diinstal secara otomatis saat container di-build
- Setiap framework memiliki struktur direktori dan konfigurasi yang berbeda
- Pastikan Anda memahami cara menggunakan framework yang dipilih