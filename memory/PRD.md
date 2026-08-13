# Kayaswara — PRD

## Masalah

CV. Kayaswara mengajukan diri sebagai penerbit ke Perpustakaan Nasional RI. Pengajuan pertama
ditolak dengan empat catatan; tiga di antaranya menyangkut situs web:

1. Nama akun harus sesuai legalitas: **"Kayaswara"**, bukan "Kayaswara Publisher".
2. Untuk CV: legalitas akta pendirian digabung satu berkas dengan NIB. *(dokumen, di luar situs)*
3. Situs **wajib memiliki menu Publikasi** sebagai tempat katalog buku.
4. Situs penerbit **dilarang mempromosikan jasa/fasilitas pencantuman ISBN**.

Situs versi 2 dirombak total dari sisi desain, informasi, dan alur — logika bisnisnya
(pengajuan naskah, pesanan + tahapan, tagihan, notifikasi, CMS) dipertahankan.

## Merek

- **Nama penerbit**: Kayaswara
- **Badan usaha**: CV. Kayaswara
- **Alamat**: Jln. Sunan Kalijaga Timur 10, Kec. Larangan, Kota Tangerang, Banten
- **Telepon**: 0812-1316-9703
- **Surel**: kayaswara.jurnal@gmail.com
- **Kode pelacakan**: `KYSWR-DDMMYYYY-NNN`

## Sistem Desain — Academic Press, ramah lingkungan

| Token | Nilai |
|-------|-------|
| Primary | `#1F4B3F` (hijau hutan) |
| Secondary | `#2F6B57` |
| Accent | `#A9752F` (kuning tanah) |
| Paper | `#FBFAF6` (kertas daur ulang) |
| Ink | `#16211C` |
| Fonts | Source Serif 4 (judul) + Inter (isi) |
| Radius | 6–16px, garis tipis, bayangan sangat halus |

Rinciannya di `design_guidelines.json`.

## Arsitektur Informasi

Beranda · Publikasi · Layanan · Proses · Biaya · Wawasan · Tentang
+ utilitas **Lacak Naskah** dan CTA tunggal **Kirim Naskah**.

Rute lama tetap dilayani (`/harga`, `/blog`, `/konsultasi`, `/tracking`, `/katalog`).

## Yang Sudah Dikerjakan

- [x] Sistem desain baru (`assets/css/style.css`) + perilaku front-end (`assets/js/main.js`)
- [x] Kerangka situs baru: topbar utilitas, masthead lengket, laci mobile, footer empat kolom
- [x] **Tabel `publications`** + katalog publik (cari, saring kategori/tahun, urutkan, paginasi)
- [x] Halaman detail buku dengan tabel data bibliografi
- [x] CRUD katalog di panel admin (sampul, sinopsis, bibliografi, status tayang, unggulan)
- [x] Halaman **Proses** baru: 8 tahap + ketentuan naskah + FAQ
- [x] Formulir **Kirim Naskah** tiga langkah (logika unggah & simpan tidak diubah)
- [x] **Lacak Naskah** dengan lini masa tahapan
- [x] Halaman Tentang memuat blok identitas & legalitas yang diisi dari Pengaturan
- [x] Panel admin dirombak tampilannya; kontrak kelas CSS dipertahankan
- [x] Pengaturan: `legal_name`, `founded_year`, `legal_nib`, `legal_akta`, `legal_npwp`, media sosial
- [x] Kosakata layanan/paket/kategori dipusatkan sebagai helper di `includes/functions.php`
- [x] Milestone bawaan diselaraskan dengan alur di halaman Proses
- [x] `install.php` & `migrate.php` diperbarui; berkas migrasi ad-hoc lama dihapus
- [x] Sapuan kepatuhan: nol penawaran ISBN di seluruh halaman

## Yang Harus Diisi Pemilik Situs

- [ ] Isi Katalog Publikasi dengan judul yang benar-benar sudah terbit (sampul + bibliografi)
- [ ] Lengkapi Pengaturan → Umum: nama badan usaha, tahun berdiri, NIB, akta, NPWP
- [ ] Unggah logo & favicon
- [ ] Isi tautan media sosial yang aktif saja
- [ ] Tulis 2–3 artikel Wawasan agar halaman tidak kosong
- [ ] Ganti nomor rekening pembayaran pada `getInvoiceBankAccounts()` di `includes/functions.php`
