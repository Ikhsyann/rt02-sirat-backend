# SIRAT Backend (Admin & Core Database)

Sistem Informasi Rumah Tangga (SIRAT) — RT 02.
Repository ini berfungsi sebagai **Backend & Admin Panel** serta **Single Source of Truth** untuk skema dan migrasi database.

## Spesifikasi & Tech Stack
- **Framework**: Laravel 12
- **Admin Panel**: Filament v5 (`/admin`)
- **Database**: MySQL 8.0 (`rt02-db` di DBngin)
- **Port Default**: `8000` (`http://127.0.0.1:8000/admin`)

## Peran & Tanggung Jawab
1. Mengelola seluruh migrasi tabel domain dan data seeder (`php artisan migrate`, `php artisan db:seed`).
2. Menyediakan panel admin CRUD lengkap untuk Pengurus RT via Filament:
   - Data Kependudukan (Blok, Rumah, Kartu Keluarga, Anggota Keluarga)
   - Keuangan RT & Export Excel
   - Nomor Darurat
   - Kegiatan RT & Absensi Warga

## Cara Menjalankan
```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --port=8000
```
Login Admin: `admin@rt02.test` / `password`.

## Deploy

Push ke `main` memicu `.github/workflows/deploy.yml`, yang SSH ke VPS lalu menjalankan `./deploy.sh` di direktori aplikasi.

Secret GitHub yang dibutuhkan: `VPS_HOST`, `VPS_USER`, `VPS_SSH_KEY`, `VPS_APP_PATH`.

Backup database harian dijalankan `scripts/backup-db.sh` lewat cron VPS, 7 file terakhir disimpan.
