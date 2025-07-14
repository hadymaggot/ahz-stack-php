# AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools

![Dashboard AHZ-Stack-PHP](Screenshot-AHZ-Stack-PHP.png)

AHZ-Stack-PHP adalah lingkungan pengembangan PHP yang lengkap dan siap pakai dengan dashboard monitoring terintegrasi. Proyek ini menyediakan:

- **Web Server**: Caddy dengan FrankenPHP untuk eksekusi PHP berkinerja tinggi.
- **PHP**: PHP 8.3 dengan ekstensi OPcache dan Redis.
- **Database**: MySQL 8.4.
- **In-Memory Cache**: Redis.
- **Admin Tools**: phpMyAdmin dan Redis Commander untuk manajemen yang mudah.
- **Dashboard Monitoring**: Dashboard interaktif untuk memantau status sistem, OpCache, MySQL, dan Redis.

## Fitur

- **Siap Produksi**: Dikonfigurasi dengan mempertimbangkan praktik terbaik keamanan dan kinerja.
- **Data Persisten**: Menggunakan bind mount lokal untuk data MySQL, Redis, dan Caddy, memastikan data tetap ada setelah container di-restart.
- **Konfigurasi Mudah**: Semua pengaturan spesifik lingkungan dikelola melalui file `.env`.
- **Aman secara Default**: Menyertakan peringatan dan panduan untuk mengamankan lingkungan produksi.
- **Dashboard Monitoring**: Dashboard interaktif yang menampilkan:
  - **Informasi Sistem**: Versi PHP, Server, Hostname, OS, dan Timezone.
  - **Status OpCache**: Penggunaan memori, hit rate, dan status JIT dengan detail diagnostik lengkap.
  - **Status MySQL**: Versi, uptime, dan koneksi dengan akses cepat ke phpMyAdmin.
  - **Status Redis**: Versi, penggunaan memori, hit rate, dan jumlah keys dengan akses cepat ke Redis Commander.
- **Auto-Refresh**: Semua informasi status diperbarui secara otomatis setiap 5 detik.

## Prasyarat

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

Untuk pengembangan lokal tanpa Docker, Anda memerlukan:

- PHP 8.3+ dengan ekstensi OpCache dan Redis
- MySQL 8.4+
- Redis Server

## Instalasi dan Pengaturan

1. **Clone repository:**

   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```
2. **Buat file environment:**

   Salin file environment contoh untuk membuat konfigurasi lokal Anda sendiri.

   ```bash
   cp .env.example .env
   ```
3. **Tinjau dan sesuaikan `.env`:**

   Buka file `.env` dan tinjau pengaturannya. **Sangat penting untuk mengubah password default untuk lingkungan produksi.**

   - `MYSQL_ROOT_PASSWORD`: Password root untuk MySQL.
   - `MYSQL_PASSWORD`: Password untuk pengguna MySQL aplikasi.
   - `REDIS_PASSWORD`: Password untuk Redis.
   - `REDIS_COMMANDER_USER` / `REDIS_COMMANDER_PASSWORD`: Kredensial untuk Redis Commander.
4. **Build dan jalankan container:**

   ```bash
   docker-compose up -d --build
   ```

   This command will build the PHP image, pull the other necessary images, and start all services in detached mode.

## Penggunaan

Setelah container berjalan, Anda dapat mengakses layanan pada URL berikut:

- **Dashboard**: [http://localhost](http://localhost)
- **phpMyAdmin**: [http://pma.localhost](http://localhost:8080)
- **Redis Commander**: [http://redis.localhost](http://localhost:8081)

Kode aplikasi PHP Anda harus ditempatkan di direktori `www/`.

### Dashboard Monitoring

![Dashboard AHZ-Stack-PHP](dashboard-screenshot.svg)

Dashboard monitoring menyediakan tampilan real-time dari:

- **System Information**: Menampilkan versi PHP, informasi server, hostname, OS, dan timezone dengan ikon yang intuitif.
- **OpCache**: Memantau status OpCache, penggunaan memori, hit rate, dan status JIT. Tombol "Details" membuka modal dengan informasi diagnostik lengkap.
- **MySQL**: Menampilkan versi MySQL, uptime, dan penggunaan koneksi dengan tombol akses cepat ke phpMyAdmin.
- **Redis**: Menampilkan versi Redis, penggunaan memori, hit rate, dan jumlah keys dengan tombol akses cepat ke Redis Commander.

Semua informasi pada dashboard diperbarui secara otomatis setiap 5 detik.

## Checklist Keamanan Produksi

**PENTING**: Sebelum men-deploy stack ini ke lingkungan produksi, harap selesaikan pemeriksaan keamanan berikut:

1. **Ubah Semua Password Default**: Pastikan semua password di file `.env` Anda kuat dan unik.
2. **Hapus atau Amankan Admin Tools**:
   - **phpMyAdmin** dan **Redis Commander** adalah risiko keamanan utama. Untuk produksi, sangat disarankan untuk **menghapusnya** dari file `compose.yaml`.
   - Jika Anda benar-benar harus mempertahankannya, amankan dengan:
     - Menempatkannya di belakang firewall atau VPN.
     - Menggunakan reverse proxy dengan autentikasi kuat (misalnya, OAuth2, basic auth dengan password kuat).
     - Membatasi akses hanya dari alamat IP tepercaya.
3. **Konfigurasi Firewall**: Siapkan firewall di mesin host Anda untuk hanya mengizinkan lalu lintas pada port yang diperlukan (misalnya, 80, 443).
4. **Tinjau Caddyfile**: Sesuaikan `Caddyfile` untuk domain Anda dan aktifkan HTTPS. Caddy akan secara otomatis menyediakan dan memperbarui sertifikat SSL untuk Anda.
5. **Backup Rutin**: Implementasikan strategi untuk backup rutin data MySQL Anda yang terletak di direktori `./mysql/data`.

## Mengelola Layanan

- **Menghentikan semua layanan**:

  ```bash
  docker-compose down
  ```
- **Menghentikan dan menghapus volume data** (gunakan dengan hati-hati):

  ```bash
  docker-compose down -v
  ```
- **Melihat log untuk layanan tertentu**:

  ```bash
  docker-compose logs -f php
  ```

## Struktur Proyek

```
├── .env                  # File konfigurasi lingkungan
├── Caddyfile             # Konfigurasi server web Caddy
├── Dockerfile.frankenphp # Dockerfile untuk FrankenPHP
├── compose.yaml          # Konfigurasi Docker Compose
├── php/                  # Konfigurasi PHP
├── src/                  # Kode sumber PHP untuk dashboard
│   ├── Cache/            # Kelas untuk OpCache dan Redis
│   ├── Database/         # Kelas untuk MySQL
│   └── System/           # Kelas untuk informasi sistem
└── www/                  # Direktori web publik
    ├── api.php           # API endpoint untuk dashboard
    ├── index.php         # File entri utama
    └── templates/        # Template dashboard
```

## Pengembangan

Dashboard monitoring dirancang untuk memudahkan pemantauan lingkungan pengembangan PHP Anda. Anda dapat memperluas fungsionalitasnya dengan menambahkan lebih banyak metrik atau integrasi dengan layanan lain.

## Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail.

## Kredit

**Versi:** 1.0

**Pengembang:** ahadizapto
**Email:** 9hs@tuta.io

**Kontak:**

- **Website:** [https://hadymaggot.github.io](https://hadymaggot.github.io)
- **GitHub:** [https://github.com/hadymaggot](https://github.com/hadymaggot)
- **LinkedIn:** [https://www.linkedin.com/in/saptohadi/](https://www.linkedin.com/in/saptohadi/)

Lihat file [CREDITS.md](CREDITS.md) untuk informasi lebih lanjut.
