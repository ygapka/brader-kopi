# Panduan Implementasi Brader Cafe di Google Antigravity

Dokumen ini adalah cetak biru teknis (*Technical Execution Blueprint*) dan kumpulan prompt terstruktur untuk membangun sistem informasi Brader Cafe menggunakan agen AI di Google Antigravity.

---

## 1. Setup Awal Workspace Antigravity

Sebelum menjalankan prompt kode, buat file konfigurasi konteks di root direktori project Anda agar Antigravity selalu mematuhi batasan arsitektur.

1. Buka workspace baru di Google Antigravity.
2. Buat file bernama `PRD.md` dan tempelkan PRD lengkap dari proposal.
3. Buat file `.antigravity/instructions.md` (atau file context system) dengan isi instruksi berikut:

```markdown
# Antigravity System Directives
- Framework: Laravel 11 / PHP 8.2+
- Database: MySQL
- CSS Engine: Tailwind CSS
- Payment Gateway: Midtrans Snap & HTTP Notification Webhook
- Notification Service: WhatsApp API Gateway
- Semua kode wajib modular, menerapkan Model-View-Controller (MVC) standar Laravel, dan migration yang clean dengan foreign key constraints.
```

---

## 2. Rangkaian Prompt Eksekusi Tahap demi Tahap

Jalankan prompt berikut secara berurutan ke agen Antigravity:

### Tahap 1: Setup Proyek, Database, & Autentikasi
**Prompt untuk Antigravity:**
> Inisialisasi arsitektur dasar untuk aplikasi web Brader Cafe berbasis Laravel 11:
> 1. Buat sistem otentikasi menggunakan Laravel Breeze (Blade stack).
> 2. Buat migration, model, dan factory/seeder untuk entitas basis data berikut:
>    - `users` (id, name, email, password, role: `['admin', 'kasir', 'dapur', 'owner']`)
>    - `tables` (id, table_number, token: unique string/uuid, is_active: boolean)
>    - `categories` (id, name, slug)
>    - `menus` (id, category_id, name, description, price: decimal, image: nullable, is_available: boolean)
>    - `menu_variants` (id, menu_id, name, extra_price: decimal)
>    - `orders` (id, order_number: unique, table_id, customer_name, customer_whatsapp, total_amount: decimal, status: `['pending_payment', 'paid', 'processing', 'ready', 'completed', 'cancelled']`)
>    - `order_items` (id, order_id, menu_id, variant_name: nullable, quantity: integer, price: decimal, notes: nullable)
>    - `payments` (id, order_id, transaction_id: nullable, payment_type: nullable, gross_amount: decimal, status: `['pending', 'settlement', 'expire', 'cancel', 'deny']`, raw_response: json/nullable)
>    - `order_status_logs` (id, order_id, status, updated_by: nullable unsignedBigInteger, created_at)
>    - `notifications` (id, order_id, phone_number, status: `['queued', 'sent', 'failed']`, sent_at: nullable)
> 3. Tentukan relasi Eloquent yang lengkap antar model (belongsTo, hasMany).
> 4. Buat DatabaseSeeder dengan akun default untuk setiap role (Admin, Kasir, Dapur, Owner), beberapa meja contoh dengan token, serta kategori dan menu kafe.

---

### Tahap 2: Manajemen Meja & QR Code Generator
**Prompt untuk Antigravity:**
> Bangun modul Manajemen Meja untuk role Admin:
> 1. Pasang package QR code generator (`simplesoftwareio/simple-qrcode`).
> 2. Buat CRUD Meja (Controller, Request Validation, Blade View):
>    - Otomatis generate string token unik (menggunakan `Str::random` atau UUID) saat meja baru dibuat.
>    - Toggle status meja aktif/tidak aktif.
> 3. Buat tombol "Cetak QR Code" pada setiap baris meja:
>    - QR Code mengarah ke URL: `{APP_URL}/table/{token}`
>    - Tampilan print-friendly yang menampilkan logo Brader Cafe, Nomor Meja, instruksi scan, dan gambar QR Code SVG/PNG.
> 4. Pastikan middleware role membatasi akses ini hanya untuk Admin.

---

### Tahap 3: Halaman Self-Order Pelanggan (Front-End Mobile)
**Prompt untuk Antigravity:**
> Bangun modul pemesanan mandiri pelanggan tanpa login:
> 1. Route `/table/{token}`:
>    - Periksa apakah token valid dan meja `is_active == true`. Jika tidak, tampilkan halaman error informatif.
>    - Simpan session identitas meja.
> 2. Desain antarmuka mobile-first responsif:
>    - Header menampilkan identitas kafe dan nomor meja pemesan.
>    - Tab filter kategori menu (Makanan, Minuman, Snack, dll).
>    - Kartu menu dengan gambar, nama, deskripsi singkat, harga, dan badge ketersediaan stok.
>    - Modal pemilihan varian dan input catatan per item saat menu diklik.
> 3. State management keranjang (Cart) menggunakan Alpine.js atau LocalStorage/Session:
>    - Tambah, ubah jumlah, hapus item, dan hitung total harga otomatis.
>    - Form checkout: input Nama Pelanggan dan Nomor WhatsApp (validasi format nomor Indonesia, misal: 08xx / 628xx).
>    - Tombol checkout memicu request pembuatan order ke endpoint backend.

---

### Tahap 4: Integrasi Midtrans Snap & Webhook Handler
**Prompt untuk Antigravity:**
> Integrasikan Midtrans Payment Gateway untuk pembayaran QRIS:
> 1. Pasang library `midtrans/midtrans-php`.
> 2. Tambahkan konfigurasi di `config/services.php` dan `.env`:
>    - `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION` (false untuk sandbox).
> 3. Controller Checkout:
>    - Validasi keranjang belanja, hitung ulang total nominal di server (hindari manipulasi harga dari client).
>    - Buat record order dengan status `pending_payment`.
>    - Generate Snap Token dari Midtrans API dengan order_id unik: `"ORDER-{timestamp}-{order_id}"`.
>    - Kembalikan response JSON berisi Snap Token untuk diproses popup Midtrans Snap di browser pelanggan.
> 4. Webhook Notification Controller (`/api/midtrans/webhook`):
>    - Bypass proteksi CSRF untuk rute ini di `bootstrap/app.php` (Laravel 11).
>    - Validasi keamanan signature key: `SHA512(order_id + status_code + gross_amount + ServerKey)`.
>    - Implementasikan idempotensi: periksa apakah transaksi sudah berstatus settlement sebelumnya. Jangan update dua kali.
>    - Jika status `settlement` atau `capture`:
>      - Ubah status order menjadi `paid`.
>      - Simpan data payment.
>      - Catat di `order_status_logs`.
>      - Trigger event/job `SendWhatsAppInvoiceJob`.
>    - Jika status `expire`/`cancel`/`deny`, ubah status order menjadi `cancelled`.

---

### Tahap 5: Integrasi WhatsApp API Service
**Prompt untuk Antigravity:**
> Buat modul notifikasi WhatsApp setelah pembayaran berhasil:
> 1. Buat Service class `WhatsAppService` yang dapat mengirim pesan teks via API Gateway HTTP Client (misal: Fonnte / Wablas format generic).
> 2. Buat template pesan invoice yang rapi:
>    - Header: "*BRADER CAFE - BUKTI PEMBAYARAN*"
>    - Detail: No. Pesanan, Tanggal & Jam, No. Meja, Nama Pelanggan.
>    - Rincian: Daftar Menu x Qty (beserta varian/catatan jika ada) dan subtotal.
>    - Total Pembayaran, Metode: QRIS (Midtrans), Status: LUNAS.
>    - Footer: Pesan terima kasih ramah.
> 3. Buat Job antrean `SendWhatsAppInvoiceJob` yang dijalankan di background:
>    - Panggil `WhatsAppService`.
>    - Simpan status pengiriman ke tabel `notifications`.
>    - Pastikan ada proteksi agar struk hanya terkirim 1 kali per order.

---

### Tahap 6: Antarmuka Operasional (Kasir, Kitchen Display, & Owner)
**Prompt untuk Antigravity:**
> Bangun modul operasional internal:
> 1. Kitchen Display System (KDS) untuk Dapur/Barista:
>    - Tampilkan kartu pesanan aktif dengan status `paid`, `processing`, dan `ready`.
>    - Tampilkan nomor meja, nama pelanggan, waktu tunggu, rincian menu, dan catatan kustom.
>    - Tombol aksi cepat: "Mulai Proses" -> "Siap Diantar" -> "Selesaikan Pesanan".
>    - Pembaruan status otomatis mencatat log ke `order_status_logs`.
>    - Auto-refresh setiap 15 detik atau polling status sederhana.
> 2. Dashboard Kasir:
>    - Monitoring transaksi masuk real-time, pencarian pesanan, dan filter status pembayaran.
> 3. Dashboard Owner:
>    - Ringkasan metrik: Total Omzet, Total Transaksi Sukses, Rata-rata Nilai Transaksi.
>    - Filter rentang tanggal (Hari Ini, 7 Hari Terakhir, Bulan Ini, Kustom Periode).
>    - Tabel analisis Menu Terlaris (Top Selling Items).
>    - Tombol cetak laporan penjualan (Print Preview/PDF format).

---

## 3. Checklist Verifikasi & Testing (Black Box Sandbox)

Sebelum menyelesaikan implementasi, uji skenario berikut di Antigravity:

| No | Skenario Uji | Prosedur | Hasil yang Diharapkan |
| :---: | :--- | :--- | :--- |
| **1** | Validasi Token Meja | Akses `/table/invalid-token` dan `/table/token-nonaktif` | Sistem menampilkan halaman 404/Peringatan bahwa meja tidak valid |
| **2** | Manipulasi Harga | Ubah payload harga di browser console saat submit | Server menolak/menghitung ulang berdasarkan harga database |
| **3** | Pembayaran Sandbox | Lakukan scan QRIS di simulator Midtrans (SandBox) | Status webhook berubah ke `settlement`, order berubah ke `paid` |
| **4** | Idempotensi Webhook | Kirim payload webhook yang sama dua kali menggunakan curl/postman | Log pembayaran hanya diproses satu kali, tidak ada duplikasi data |
| **5** | Dispatch WhatsApp | Periksa tabel `notifications` setelah status `settlement` | Record notifikasi terbuat dengan status 'sent' |
| **6** | Kitchen Workflow | Klik tombol proses di dashboard Dapur | Status berpindah ke `processing` dan tercatat di `order_status_logs` |
