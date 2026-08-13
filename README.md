# Kayaswara — Situs Penerbit Buku Akademik

Situs resmi **CV. Kayaswara**, penerbit buku ajar, buku referensi, monograf, dan bunga rampai.
Dibangun dengan PHP native + MySQL agar berjalan langsung di shared hosting cPanel tanpa Composer,
Node, atau proses build apa pun.

---

## Struktur Situs

### Halaman Publik

| Rute | Berkas | Isi |
|------|--------|-----|
| `/` | `pages/home.php` | Profil singkat penerbit, lini terbitan, katalog terbaru, alur penerbitan, wawasan |
| `/publikasi` | `pages/publikasi.php` | **Katalog publikasi** — pencarian, saring kategori & tahun, urutkan, paginasi |
| `/publikasi/{slug}` | `pages/publikasi-detail.php` | Detail buku + tabel data bibliografi + judul terkait |
| `/layanan` | `pages/layanan.php` | Lingkup pekerjaan redaksi & produksi |
| `/proses` | `pages/proses.php` | Delapan tahap penerbitan, ketentuan naskah (`#ketentuan`), FAQ |
| `/biaya` | `pages/harga.php` | Paket layanan, tabel perbandingan, FAQ biaya |
| `/kirim-naskah` | `pages/konsultasi.php` | Formulir pengajuan naskah + unggah berkas (AJAX) |
| `/lacak` | `pages/tracking.php` | Pelacakan progres naskah berdasarkan kode (AJAX) |
| `/wawasan`, `/wawasan/{slug}` | `pages/blog.php`, `blog-detail.php` | Catatan redaksi untuk penulis |
| `/portofolio`, `/portofolio/{slug}` | `pages/portofolio.php`, `portofolio-detail.php` | Kerja sama institusi |
| `/tentang` | `pages/tentang.php` | Identitas & legalitas penerbit, prinsip redaksi, struktur kerja |
| `/kebijakan-privasi`, `/kebijakan-refund` | — | Dokumen kebijakan |
| `/invoice/{token}` | `pages/invoice.php` | Unduhan PDF tagihan (tanpa kerangka situs) |

Rute lama (`/harga`, `/blog`, `/konsultasi`, `/tracking`, `/katalog`) tetap dilayani agar tautan
dan penanda buku yang sudah tersebar tidak putus.

### Panel Admin (`/admin`)

- **Dashboard** — ringkasan katalog, pengajuan naskah, pesanan, dan pendapatan
- **Pengajuan Naskah** — naskah masuk dari formulir publik, lengkap dengan berkas lampiran
- **Pesanan & Progres** — pekerjaan berjalan, tahapan (milestone), notifikasi WhatsApp/surel, tagihan PDF
- **Katalog Publikasi** — CRUD buku terbitan: sampul, data bibliografi, sinopsis, status tayang
- **Kerja Sama** — dokumentasi kolaborasi institusi
- **Wawasan** — tulisan redaksi
- **Pengaturan** — identitas & legalitas penerbit, kontak, media sosial, warna, logo, SMTP, WhatsApp API
- **Ekspor Data** — unduh rekap dalam CSV

---

## Ketentuan Perpustakaan Nasional

Situs ini disusun mengikuti catatan penilaian Perpusnas:

1. **Nama penerbit** ditulis `Kayaswara` sesuai legalitas (bukan "Kayaswara Publisher").
   Badan usaha `CV. Kayaswara` ditampilkan pada halaman Tentang dan footer.
2. **Menu Publikasi** tersedia sebagai katalog buku ber-basis data, bukan halaman statis.
3. **Tidak ada penawaran jasa/fasilitas pencantuman ISBN** di seluruh halaman. Nomor ISBN hanya
   muncul sebagai data bibliografi pada halaman detail buku yang memang sudah memilikinya.
4. Data legalitas (NIB, akta pendirian, NPWP, tahun berdiri) diisi lewat
   **Admin → Pengaturan → Umum**; kolom yang dikosongkan tidak ditampilkan di situs.

---

## Teknologi

- PHP 7.4+ (native, tanpa framework) — PDO MySQL, prepared statement di seluruh kueri
- MySQL 5.7+ / MariaDB
- Bootstrap 5.3 (grid & komponen dasar) + CSS kustom `assets/css/style.css`
- Font Awesome 6.5, Google Fonts: Source Serif 4 + Inter
- FPDF (disertakan) untuk tagihan PDF

## Sistem Desain

Palet "kertas daur ulang + tinta hijau tua", diringkas di `design_guidelines.json`:

| Token | Nilai | Pemakaian |
|-------|-------|-----------|
| `--primary` | `#1F4B3F` | Warna utama, tombol, pita CTA |
| `--secondary` | `#2F6B57` | Ikon, aksen tenang |
| `--accent` | `#A9752F` | Penekanan, sorotan, garis eyebrow |
| `--paper` | `#FBFAF6` | Latar halaman |
| `--ink` | `#16211C` | Teks judul |

Ketiga warna merek dapat diubah dari **Admin → Pengaturan → Tampilan** dan disuntikkan sebagai
variabel CSS pada `includes/header.php`.

---

## Pemasangan Baru

1. Unggah seluruh berkas ke `public_html`.
2. Buka situs di peramban → otomatis diarahkan ke `install.php`.
3. Isi kredensial database, URL situs, dan akun administrator.
4. Selesai. Masuk ke `/admin`, lengkapi **Pengaturan → Umum**, lalu isi **Katalog Publikasi**.
5. Hapus `install.php` dan `migrate.php` dari server.

## Data Contoh (opsional)

`seed.php` mengisi basis data dengan **5 judul katalog** dan **5 tulisan wawasan**
supaya tampilan situs dapat dinilai memakai data nyata dari database, bukan contoh
yang ditanam di dalam template.

```
php seed.php            # memasukkan data contoh (melewati yang sudah ada)
php seed.php --remove    # menghapus kembali seluruh data contoh
```

Kolom ISBN pada data contoh sengaja dikosongkan — ISBN adalah nomor resmi dan tidak
boleh diisi angka karangan.

> **Sebelum mengajukan situs ke Perpustakaan Nasional:** jalankan `php seed.php --remove`,
> isi katalog dengan terbitan yang benar-benar sudah diterbitkan, lalu hapus `seed.php`
> dari server.

## Memperbarui Instalasi Lama

1. Timpa berkas lama dengan versi ini (jangan hapus `config.php`).
2. Jalankan `migrate.php` dari localhost atau CLI:
   ```
   php migrate.php
   ```
   Skrip ini membuat tabel `publications`, melengkapi tabel/kolom yang belum ada, menambah
   pengaturan identitas & media sosial, memperbarui palet warna bila masih memakai nilai lama,
   dan merapikan nama penerbit.
3. Hapus `migrate.php` setelah selesai.

---

## Struktur Direktori

```
├── admin/                  Panel admin (router index.php + halaman)
│   └── includes/           Kerangka admin: header, sidebar, footer
├── assets/
│   ├── css/style.css       Sistem desain situs publik
│   ├── js/main.js          Perilaku front-end (reveal, dropzone, dll.)
│   └── uploads/            publications · portfolio · blog · site · consultations
├── includes/
│   ├── db.php              Pemuat konfigurasi
│   ├── functions.php       Helper inti (DB, pengaturan, kosakata, notifikasi, PDF)
│   ├── header.php          Kerangka atas situs publik
│   ├── footer.php          Kerangka bawah situs publik
│   └── fpdf/               Pustaka PDF
├── pages/                  Halaman publik
├── invoices/               PDF tagihan yang dihasilkan
├── index.php               Front controller & perutean
├── install.php             Pemasang berbasis web
├── migrate.php             Migrasi basis data (hapus setelah dipakai)
├── seed.php                Data contoh katalog & wawasan (hapus setelah dipakai)
└── .htaccess               Perutean bersih, header keamanan, kompresi
```

## Catatan Keamanan

- Seluruh kueri memakai prepared statement PDO.
- Formulir publik dilindungi token CSRF.
- Unggahan naskah divalidasi ekstensi & ukuran, disimpan dengan nama acak di direktori
  yang tidak dapat dieksekusi (`assets/uploads/consultations/.htaccess`).
- Konten kaya pada tulisan disaring `sanitizeHtml()` sebelum ditampilkan.
- Header keamanan (CSP, X-Frame-Options, nosniff, HSTS saat HTTPS) dipasang di
  `index.php` dan `admin/index.php`.
- `config.php` diblokir dari akses langsung lewat `.htaccess` dan diabaikan Git.

---

© CV. Kayaswara. Seluruh hak cipta dilindungi.
