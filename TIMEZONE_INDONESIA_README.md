# Panduan Perbaikan Timezone Indonesia - Laravel Barokah

## Daftar Perubahan yang Telah Diterapkan

### 1. **Konfigurasi Sistem**
- ✅ Timezone aplikasi diubah dari `UTC` ke `Asia/Jakarta` di `config/app.php`
- ✅ Locale aplikasi diubah ke `id` (Indonesia) 
- ✅ Carbon locale diatur ke bahasa Indonesia di `AppServiceProvider`

### 2. **Perbaikan Model & Logika Backend**
- ✅ **PersonalDiscount Model**: Enhanced dengan timezone handling yang lebih robust
- ✅ **Customer Model**: Ditambahkan trait `IndonesianDateFormat` dan timezone handling
- ✅ **Order Model**: Ditambahkan trait untuk formatting tanggal Indonesia
- ✅ **CrmController**: Semua operasi waktu menggunakan timezone Indonesia
- ✅ **CustomerController**: Perbaikan timezone untuk operasi customer

### 3. **Perbaikan Frontend & Formatting**
- ✅ **Admin Dashboard**: Format tanggal menggunakan `translatedFormat()` dengan WIB
- ✅ **Customer Home**: Notifikasi diskon dengan format tanggal Indonesia
- ✅ **Admin CRM**: Format bulan dan tanggal dalam bahasa Indonesia
- ✅ **Customer Management**: Format tanggal lahir dan registrasi
- ✅ **Order Management**: Format tanggal pesanan dengan timezone WIB
- ✅ **Product Management**: Format tanggal dibuat produk

### 4. **Tools & Helper**
- ✅ **IndonesianDateFormat Trait**: Helper untuk formatting tanggal konsisten
- ✅ **ClearAllCache Command**: Command untuk membersihkan semua cache

## Instruksi Penerapan

### Langkah 1: Update Environment File
```bash
# Tambahkan di file .env
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FAKER_LOCALE=id_ID
```

### Langkah 2: Pembersihan Cache (WAJIB)
```bash
# Jalankan command berikut untuk membersihkan semua cache:
php artisan cache:clear-all

# Atau jalankan satu per satu:
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear
```

### Langkah 3: Restart Web Server
```bash
# Jika menggunakan Apache/Nginx dengan PHP-FPM:
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx

# Jika menggunakan Laravel development server:
php artisan serve
```

## Fitur Baru yang Tersedia

### 1. **Formatting Tanggal Otomatis**
```php
// Contoh penggunaan di Blade:
{{ $order->formatted_order_date }}        // "5 September 2025, 15:30 WIB"
{{ $customer->formatted_birthday }}       // "15 Agustus 1990"
{{ $discount->formatted_expiry }}         // "10 Oktober 2025, 23:59 WIB"
{{ $discount->formatted_expiry_short }}   // "10 Okt 2025"
```

### 2. **Validasi Diskon yang Lebih Akurat**
- Diskon sekarang menggunakan timezone Indonesia untuk pengecekan kedaluwarsa
- Triple validation: database scope, model method, dan aplikasi logic
- Logging yang lebih detail untuk debugging

### 3. **Pesan Otomatis dengan Format Indonesia**
- Pesan CRM menggunakan format tanggal Indonesia
- Semua notifikasi menggunakan zona waktu WIB

## Pengujian

### Test Timezone
```php
// Test di Tinker untuk memastikan timezone benar:
php artisan tinker

// Test 1: Cek timezone aplikasi
echo config('app.timezone'); // Harus: Asia/Jakarta

// Test 2: Cek waktu sekarang
echo now()->format('Y-m-d H:i:s T'); // Harus menampilkan WIB

// Test 3: Cek Carbon locale
echo now()->translatedFormat('d F Y'); // Harus dalam bahasa Indonesia
```

### Test Diskon
```php
// Test validasi diskon dengan timezone
$discount = PersonalDiscount::first();
echo $discount->isValid(); // true/false
echo $discount->formatted_expiry; // Format Indonesia
```

## Catatan Penting

1. **Database**: Waktu di database tetap disimpan dalam UTC, hanya ditampilkan dalam WIB
2. **JavaScript**: Frontend JavaScript sudah disesuaikan untuk timezone Indonesia (+7 UTC)
3. **Cache**: Selalu jalankan `cache:clear-all` setelah perubahan konfigurasi
4. **Production**: Gunakan `--force` flag untuk clear cache di production

## Troubleshooting

### Jika Tanggal Masih UTC:
1. Pastikan `config:clear` sudah dijalankan
2. Restart web server
3. Cek file `.env` sudah benar

### Jika Locale Belum Indonesia:
1. Pastikan locale system mendukung `id_ID.UTF-8`
2. Jalankan `locale -a` untuk cek available locales
3. Install Indonesia locale jika belum ada:
   ```bash
   sudo locale-gen id_ID.UTF-8
   sudo update-locale
   ```

### Jika Diskon Masih Tidak Expired:
1. Cek data di database: `SELECT expires_at FROM personal_discounts WHERE id=X;`
2. Bandingkan dengan waktu sekarang: `SELECT datetime('now', '+7 hours');`
3. Jalankan debugging di CrmController dengan Log

## Format Tanggal yang Digunakan

- **Full**: `5 September 2025, 15:30 WIB`
- **Short**: `5 Sep 2025`
- **Date Only**: `5 September 2025`
- **Time Only**: `15:30 WIB`

Semua aspek waktu (konfigurasi, logika backend, dan tampilan frontend) telah diselaraskan dengan zona waktu dan format Indonesia (WIB).