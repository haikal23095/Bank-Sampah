# 🏦 EcoBank - Sistem Manajemen Bank Sampah

EcoBank adalah aplikasi berbasis web yang dibangun menggunakan **Laravel 12** dan **Tailwind CSS**. Aplikasi ini dirancang untuk mendigitalisasi operasional bank sampah, mulai dari pengelolaan nasabah, pencatatan setoran sampah otomatis, hingga penarikan saldo nasabah.

## 🚀 Fitur Utama

- **Dashboard Admin**: Ringkasan statistik (Total sampah, Saldo, Nasabah aktif).
- **Setoran Sampah**: Input penimbangan sampah dengan kalkulasi otomatis dan pencatatan saldo real-time.
- **Kelola Nasabah**: Manajemen akun nasabah dengan validasi email dan nomor telepon.
- **Katalog Harga**: Pengelolaan kategori dan jenis sampah beserta harga per kilogram/satuan.
- **Penarikan Saldo**: Sistem pengajuan dan persetujuan penarikan saldo (Tunai & Bank Transfer).
- **Riwayat Transaksi**: Log aktivitas lengkap untuk audit dan transparansi.
- **Desain Responsif**: Antarmuka modern yang mendukung perangkat desktop dan mobile (Android/iOS).

## 🛠️ Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / MariaDB

## ⚙️ Cara Instalasi

1. **Clone Repository**

   ```bash
   git clone https://github.com/username/Bank-Sampah.git
   cd Bank-Sampah/bank-sampah
   ```

2. **Install Dependencies**

   ```bash
   composer install
   npm install && npm run build
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` ke `.env` dan sesuaikan pengaturan database Anda.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seeding**

   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```

## 📂 Struktur Folder Penting

- `app/Models/`: Definisi database (Transaction, User, Wallet, WasteType, dll).
- `app/Http/Controllers/`: Logika bisnis aplikasi.
- `resources/views/admin/`: File antarmuka untuk panel admin.
- `resources/views/layouts/`: Template utama aplikasi (Navigation, Sidebar).
- `database/migrations/`: Skema database.
- `routes/web.php`: Definisi jalur URL aplikasi.

## 📱 Tampilan Mobile

Aplikasi ini telah dioptimalkan untuk penggunaan mobile dengan fitur:

- Sidebar slide-out dengan menu hamburger.
- Tabel responsif yang dapat digeser (horizontal scroll).
- Form input yang fleksibel untuk layar kecil.

---

Dibangun untuk mendukung gerakan lingkungan yang lebih bersih dan berkelanjutan. 🌍
