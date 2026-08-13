# Kayaswara Publishing - Website Jasa Penerbitan Buku Akademik

Website profesional untuk jasa penerbitan buku akademik (buku ajar, buku referensi, monograf, konversi KTI). Dibangun dengan PHP native, MySQL, Bootstrap 5, dan kompatibel dengan shared hosting / cPanel.

## Fitur Utama

### Frontend (Public)
- **Halaman Beranda** — Hero section, statistik, layanan unggulan, portofolio, testimoni
- **Halaman Layanan** — Detail layanan penerbitan: Penerbitan Buku, Konversi KTI, Editing & Layout, Desain Cover, Distribusi & Pemasaran, Konsultasi
- **Halaman Harga** — Perbandingan paket layanan (Basic, Professional, Premium, Custom) dengan tabel perbandingan
- **Halaman Tentang** — Profil perusahaan, visi misi, tim, testimoni
- **Portofolio** — Galeri buku yang telah diterbitkan dengan filter kategori
- **Blog** — Artikel tips penulisan & penerbitan dengan kategori dan pencarian
- **Konsultasi** — Form konsultasi AJAX dengan validasi client & server
- **Tracking Pesanan** — Lacak progres penerbitan buku real-time
- **Invoice** — Generate PDF invoice otomatis

### Admin Panel
- Dashboard statistik
- Manajemen pesanan & milestone
- Manajemen portofolio & blog
- Sistem invoice dengan PDF
- Notifikasi WhatsApp (Fonnte/Wablas)
- Pengaturan website (warna, logo, kontak, dll)

## Teknologi
- **Backend**: PHP 7.4+ (native, tanpa framework)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: Bootstrap 5.3, Font Awesome 6.5, JavaScript vanilla
- **Fonts**: Playfair Display + Outfit (Google Fonts)
- **PDF**: FPDF library (built-in)

## Instalasi

1. Upload semua file ke `public_html` di hosting Anda
2. Buka website di browser → otomatis redirect ke `install.php`
3. Ikuti wizard instalasi:
   - Masukkan kredensial database MySQL
   - Tentukan URL website
   - Buat akun admin
4. Selesai! Website siap digunakan

## Struktur Folder

```
├── admin/              # Panel admin
├── assets/
│   ├── css/style.css   # Stylesheet utama
│   ├── js/main.js      # JavaScript utama
│   └── uploads/        # File upload (portfolio, blog, site)
├── includes/
│   ├── db.php          # Database connection
│   ├── functions.php   # Core functions
│   ├── header.php      # Global header
│   ├── footer.php      # Global footer
│   └── fpdf/           # PDF library
├── pages/              # Halaman frontend
├── invoices/           # Generated PDF invoices
├── config.sample.php   # Template konfigurasi
├── index.php           # Front controller & router
├── install.php         # Web installer
└── .htaccess           # URL rewriting
```

## Konfigurasi Warna

Warna website dapat diubah melalui Admin Panel → Pengaturan:
- **Primary**: #1E3A5F (Deep Blue)
- **Secondary**: #0B7A6E (Teal Green)
- **Accent**: #C4880C (Warm Gold)

## Lisensi

Hak cipta Kayaswara. Semua hak dilindungi.
