<?php
/**
 * Alur Penerbitan & Ketentuan Naskah.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Alur Penerbitan — ' . $siteName;
$metaDesc  = 'Tahapan penerbitan buku di ' . $siteName . ': pengiriman naskah, telaah redaksi, penyuntingan, tata letak, produksi, hingga distribusi, beserta ketentuan naskah.';

$stages = [
    [
        'title' => 'Pengiriman Naskah',
        'desc'  => 'Penulis mengisi formulir daring: identitas, institusi, jenis naskah, dan deskripsi singkat karya, lalu melampirkan berkas naskah.',
        'note'  => 'Perkiraan waktu: 15 menit',
    ],
    [
        'title' => 'Pemeriksaan Kelengkapan',
        'desc'  => 'Redaksi memeriksa kelengkapan berkas dan data penulis. Bila ada yang kurang, kami menghubungi Anda lebih dahulu.',
        'note'  => 'Perkiraan waktu: 1×24 jam kerja',
    ],
    [
        'title' => 'Telaah Redaksi',
        'desc'  => 'Naskah dinilai dari sisi keaslian, kedalaman kajian, struktur, dan kesesuaian dengan lini terbitan. Hasil telaah disampaikan tertulis: diterima, diterima dengan revisi, atau belum layak terbit.',
        'note'  => 'Perkiraan waktu: 5–10 hari kerja',
    ],
    [
        'title' => 'Kesepakatan Penerbitan',
        'desc'  => 'Setelah naskah dinyatakan layak, lingkup pekerjaan, jadwal, dan biaya disusun dalam satu dokumen penawaran, kemudian dituangkan dalam perjanjian penerbitan.',
        'note'  => 'Perkiraan waktu: 2–3 hari kerja',
    ],
    [
        'title' => 'Penyuntingan',
        'desc'  => 'Penyuntingan substansi dikerjakan lebih dahulu, disusul penyuntingan bahasa. Catatan perubahan dikirim kepada penulis untuk ditanggapi pada dua putaran revisi.',
        'note'  => 'Perkiraan waktu: 2–4 minggu',
    ],
    [
        'title' => 'Tata Letak & Desain Sampul',
        'desc'  => 'Isi buku ditata sesuai standar terbitan akademik dan sampul dirancang bersama penulis. Contoh cetak (proof) dikirim untuk diperiksa.',
        'note'  => 'Perkiraan waktu: 1–2 minggu',
    ],
    [
        'title' => 'Persetujuan Akhir Penulis',
        'desc'  => 'Penulis menyatakan persetujuan tertulis atas berkas final. Tidak ada buku yang dicetak sebelum tahap ini terlampaui.',
        'note'  => 'Perkiraan waktu: 2–5 hari kerja',
    ],
    [
        'title' => 'Produksi & Distribusi',
        'desc'  => 'Buku dicetak sesuai spesifikasi, dicatat pada katalog penerbit lengkap dengan data bibliografinya, lalu disebarkan sesuai kanal yang disepakati.',
        'note'  => 'Perkiraan waktu: 2–3 minggu',
    ],
];

$requirements = [
    ['fa-file-word', 'Format berkas', 'Naskah dikirim dalam format DOC, DOCX, ODT, RTF, atau PDF. Bila berkas gambar terpisah, satukan dalam arsip ZIP/RAR.'],
    ['fa-list-ol', 'Kelengkapan naskah', 'Sertakan halaman judul, kata pengantar, daftar isi, isi bab, daftar pustaka, dan biodata penulis.'],
    ['fa-quote-right', 'Sitasi & rujukan', 'Gunakan satu gaya sitasi secara konsisten (APA, IEEE, atau lainnya) dan cantumkan seluruh sumber pada daftar pustaka.'],
    ['fa-image', 'Tabel & gambar', 'Tabel dan gambar diberi nomor serta judul, dan disertai sumber bila dikutip dari karya lain.'],
    ['fa-shield-halved', 'Keaslian karya', 'Naskah wajib merupakan karya asli penulis, belum pernah diterbitkan pihak lain, dan bebas dari sengketa hak cipta.'],
    ['fa-scale-balanced', 'Izin penggunaan', 'Materi milik pihak ketiga (gambar, tabel, kutipan panjang) harus disertai izin penggunaan dari pemiliknya.'],
];

$faqs = [
    ['Apakah setiap naskah pasti diterbitkan?',
     'Tidak. Naskah melewati telaah redaksi lebih dahulu. Naskah yang belum memenuhi standar akan dikembalikan disertai catatan perbaikan, dan penulis dipersilakan mengirimkannya kembali setelah diperbaiki.'],
    ['Berapa lama keseluruhan prosesnya?',
     'Untuk naskah yang sudah lengkap, umumnya 6–10 minggu terhitung sejak kesepakatan penerbitan sampai buku selesai dicetak. Naskah yang memerlukan penyuntingan berat membutuhkan waktu lebih panjang.'],
    ['Siapa pemilik hak cipta buku yang terbit?',
     'Hak cipta atas isi buku tetap berada pada penulis. Lingkup kerja sama, termasuk hak penerbitan dan distribusi, dituangkan dalam perjanjian penerbitan yang ditandatangani kedua pihak.'],
    ['Apakah naskah saya dijaga kerahasiaannya?',
     'Ya. Berkas naskah hanya diakses tim redaksi yang menangani, tidak dibagikan kepada pihak ketiga, dan tidak digunakan untuk keperluan di luar proses penerbitan.'],
    ['Bagaimana saya memantau posisi naskah?',
     'Setelah kesepakatan penerbitan, Anda menerima kode pelacakan. Masukkan kode tersebut pada halaman Lacak Naskah untuk melihat tahap yang sedang berjalan beserta tanggal penyelesaiannya.'],
    ['Bisakah saya merevisi naskah di tengah proses?',
     'Revisi kecil masih dapat diakomodasi sebelum tahap persetujuan akhir. Perubahan besar yang mengubah struktur buku akan dinilai ulang lingkup pekerjaannya dan disepakati kembali secara tertulis.'],
];
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Proses</li>
        </ol>
        <span class="eyebrow">Alur Kerja</span>
        <h1>Alur Penerbitan</h1>
        <p>Delapan tahap yang dilalui setiap naskah di meja redaksi kami, beserta perkiraan waktu pengerjaannya.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="reveal">
                    <span class="eyebrow">Tahapan</span>
                    <h2>Dari berkas naskah menjadi buku</h2>
                    <div class="rule-accent"></div>

                    <div class="rail mt-4">
                        <?php foreach ($stages as $i => $s): ?>
                        <div class="rail-item">
                            <span class="rail-dot"><?= $i + 1 ?></span>
                            <h3><?= $s['title'] ?></h3>
                            <p><?= $s['desc'] ?></p>
                            <span class="rail-note"><i class="fa-regular fa-clock"></i><?= $s['note'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="reveal" data-reveal-delay="0.08" style="position:sticky;top:110px;">
                    <div class="side-panel">
                        <h4>Ringkasan waktu</h4>
                        <ul class="check-list">
                            <li><i class="fa-solid fa-check"></i><span><strong>1×24 jam kerja</strong> — tanggapan awal atas naskah yang masuk.</span></li>
                            <li><i class="fa-solid fa-check"></i><span><strong>5–10 hari kerja</strong> — hasil telaah redaksi.</span></li>
                            <li><i class="fa-solid fa-check"></i><span><strong>6–10 minggu</strong> — total proses untuk naskah yang lengkap.</span></li>
                        </ul>
                        <p class="mb-0 text-muted" style="font-size:.86rem;">
                            Perkiraan di atas berlaku untuk naskah berukuran wajar dan dapat berubah sesuai kondisi naskah.
                        </p>
                    </div>

                    <div class="side-panel">
                        <h4>Sudah mengirim naskah?</h4>
                        <p style="font-size:.93rem;">Pantau tahap yang sedang berjalan menggunakan kode pelacakan yang Anda terima.</p>
                        <a href="<?= $siteUrl ?>/lacak" class="btn btn-outline-primary w-100">
                            <i class="fa-solid fa-location-crosshairs"></i>Lacak Naskah
                        </a>
                    </div>

                    <div class="side-panel">
                        <h4>Siap mengirim?</h4>
                        <p style="font-size:.93rem;">Lengkapi formulir daring dan lampirkan berkas naskah Anda.</p>
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary w-100">
                            <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ketentuan naskah -->
<section class="section paper-grain" id="ketentuan">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Ketentuan Naskah</span>
            <h2>Yang perlu disiapkan sebelum mengirim</h2>
            <p>Naskah yang lengkap mempercepat telaah dan mengurangi putaran revisi.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($requirements as $i => [$icon, $title, $desc]): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= ($i % 3) * 0.07 ?>">
                <div class="card-plain">
                    <div class="service-icon"><i class="fa-solid <?= $icon ?>"></i></div>
                    <h3 class="h6 mb-2"><?= $title ?></h3>
                    <p class="mb-0" style="font-size:.92rem;"><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="notice mt-4 reveal">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>
                Naskah yang terbukti memuat penjiplakan, data fiktif, atau melanggar hak cipta pihak lain
                akan dihentikan prosesnya. Tanggung jawab atas isi naskah sepenuhnya berada pada penulis.
            </span>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Pertanyaan Umum</span>
            <h2>Yang sering ditanyakan penulis</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion faq reveal" id="faqProses">
                    <?php foreach ($faqs as $i => [$q, $a]): ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#fq<?= $i ?>"
                                    aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                                <?= $q ?>
                            </button>
                        </h3>
                        <div id="fq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqProses">
                            <div class="accordion-body"><?= $a ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Naskah Anda sudah siap ditelaah?</h2>
                    <p>Isi formulir pengiriman naskah — redaksi menanggapi dalam 1×24 jam kerja.</p>
                </div>
                <div class="col-lg-4">
                    <div class="cta-actions justify-content-lg-end">
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-accent btn-lg"><i class="fa-regular fa-paper-plane"></i>Kirim Naskah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
