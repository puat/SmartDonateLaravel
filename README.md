# SmartDonate

Platform crowdfunding dan monitoring donasi berbasis Laravel, Livewire, milestone, verifikasi bukti SHA-256, dan audit blockchain simulasi. Donasi selalu menggunakan Rupiah; blockchain hanya menjadi lapisan integritas dan keterlacakan.

## Modul utama

- Landing page, pencarian/filter program, detail transparansi, dan explorer blockchain.
- Autentikasi lengkap: daftar, masuk, keluar, lupa dan reset kata sandi.
- Dasbor terpisah untuk Donatur, Pengelola Program, Verifier, dan Administrator.
- Donasi Rupiah dengan simulasi QRIS, transfer bank, atau virtual account.
- Milestone wajib berjumlah 100%, unggah bukti PDF/JPG/PNG, dan hash SHA-256.
- Verifikasi program/milestone dan pencairan satu kali dengan transaksi database serta row lock.
- Smart contract contoh di `contracts/SmartDonate.sol`; kontrak tidak mentransfer Rupiah.
- Seeder berisi 6 program Indonesia, kategori, metode pembayaran, milestone, dan akun demo.

## Instalasi lokal

Prasyarat: PHP 8.3+, Composer, Node.js/npm, MySQL atau MariaDB.

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

Di Windows PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti `cp` bila diperlukan.

1. Buka phpMyAdmin.
2. Buat database baru bernama `smartdonate` dengan collation `utf8mb4_unicode_ci`.
3. Pastikan `.env` berisi konfigurasi berikut:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartdonate
DB_USERNAME=root
DB_PASSWORD=
BLOCKCHAIN_MODE=simulation
```

4. Siapkan database dan storage, lalu jalankan aplikasi:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun demo

Semua akun menggunakan kata sandi `password`.

| Peran | Email |
|---|---|
| Donatur | `donatur@smartdonate.id` |
| Pengelola Program | `pengelola@smartdonate.id` |
| Verifier | `verifier@smartdonate.id` |
| Administrator | `admin@smartdonate.id` |

## Pengujian

```bash
php artisan test
```

Suite fitur mencakup TC-01 sampai TC-10: akses per peran, pembuatan program, donasi Rupiah, simulasi pembayaran, pencatatan blockchain, pengajuan milestone, SHA-256, persetujuan verifier, pencairan, dan pencegahan pencairan ganda.

## Catatan akademik

`BLOCKCHAIN_MODE=simulation` menghasilkan transaction hash heksadesimal realistis dan menandainya sebagai `SIMULATION`. Tidak ada transaksi bank atau jaringan blockchain nyata. Mode `ethereum` disiapkan sebagai konfigurasi pengembangan berikutnya melalui `BLOCKCHAIN_RPC_URL` dan `BLOCKCHAIN_CONTRACT_ADDRESS`.
