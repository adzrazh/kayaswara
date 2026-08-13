<?php
/**
 * Kebijakan Privasi.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$legalName = html_entity_decode(getSetting('legal_name', 'CV. Kayaswara'), ENT_QUOTES, 'UTF-8');
$email     = getSetting('email_contact', '');
$pageTitle = 'Kebijakan Privasi — ' . $siteName;
$metaDesc  = 'Bagaimana ' . $legalName . ' mengumpulkan, menggunakan, dan menjaga data pribadi serta naskah yang dikirim penulis.';

$sections = [
    [
        'Data yang kami kumpulkan',
        '<p>Melalui formulir pengiriman naskah dan komunikasi selanjutnya, kami mengumpulkan:</p>
         <ul>
            <li><strong>Identitas penulis</strong> — nama dan gelar, alamat surel, nomor telepon/WhatsApp, serta institusi atau afiliasi.</li>
            <li><strong>Data naskah</strong> — berkas naskah yang Anda unggah, jenis naskah, deskripsi karya, dan perkiraan anggaran bila Anda mengisinya.</li>
            <li><strong>Data teknis</strong> — catatan akses server standar (alamat IP, jenis peramban, waktu akses) yang tercipta otomatis saat situs diakses.</li>
         </ul>',
    ],
    [
        'Tujuan penggunaan data',
        '<p>Data digunakan semata-mata untuk:</p>
         <ul>
            <li>Menelaah kelayakan naskah dan menyusun penawaran penerbitan.</li>
            <li>Menghubungi Anda mengenai status naskah dan proses penerbitan.</li>
            <li>Menerbitkan tagihan serta menyimpan catatan transaksi sesuai kebutuhan administrasi.</li>
            <li>Mencantumkan nama penulis pada terbitan dan katalog penerbit, sesuai perjanjian penerbitan.</li>
         </ul>
         <p>Kami tidak menggunakan data Anda untuk pemasaran pihak ketiga dan tidak memperjualbelikannya.</p>',
    ],
    [
        'Kerahasiaan naskah',
        '<p>Berkas naskah diperlakukan sebagai dokumen rahasia. Akses dibatasi pada anggota tim redaksi yang menangani naskah tersebut. Isi naskah tidak dibagikan kepada pihak luar, tidak dipublikasikan sebagian maupun seluruhnya tanpa persetujuan tertulis penulis, dan tidak digunakan untuk keperluan di luar proses telaah dan penerbitan.</p>',
    ],
    [
        'Penyimpanan & keamanan',
        '<p>Data disimpan pada server penyedia layanan hosting kami dengan pembatasan akses berbasis akun. Naskah yang tidak dilanjutkan ke tahap penerbitan disimpan paling lama 12 bulan sejak keputusan telaah, lalu dihapus, kecuali Anda meminta penghapusan lebih awal.</p>
         <p>Meski kami mengambil langkah pengamanan yang wajar, tidak ada sistem yang sepenuhnya kebal. Kami akan memberitahukan Anda bila terjadi insiden yang berdampak pada data Anda.</p>',
    ],
    [
        'Pembagian data kepada pihak lain',
        '<p>Data hanya dibagikan kepada pihak ketiga dalam keadaan berikut:</p>
         <ul>
            <li>Penyedia layanan yang menunjang operasional (hosting, pengiriman surel, layanan pesan) sebatas yang diperlukan.</li>
            <li>Mitra produksi cetak, terbatas pada berkas yang memang dibutuhkan untuk mencetak.</li>
            <li>Instansi berwenang, apabila diwajibkan oleh peraturan perundang-undangan.</li>
         </ul>',
    ],
    [
        'Hak Anda atas data',
        '<p>Anda berhak meminta akses, perbaikan, atau penghapusan data pribadi Anda, serta menarik naskah dari proses telaah. Permintaan dapat disampaikan melalui surel resmi kami dan akan ditanggapi dalam 7 hari kerja.</p>',
    ],
    [
        'Cookie',
        '<p>Situs ini menggunakan cookie sesi seperlunya untuk menjaga keamanan formulir (perlindungan CSRF) dan sesi panel admin. Kami tidak memasang cookie pelacak untuk iklan.</p>',
    ],
    [
        'Perubahan kebijakan',
        '<p>Kebijakan ini dapat diperbarui bila terjadi perubahan proses kerja atau ketentuan hukum. Versi terbaru selalu tersedia pada halaman ini beserta tanggal pembaruannya.</p>',
    ],
];
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Kebijakan Privasi</li>
        </ol>
        <span class="eyebrow">Dokumen Kebijakan</span>
        <h1>Kebijakan Privasi</h1>
        <p>Bagaimana <?= htmlspecialchars($legalName) ?> memperlakukan data pribadi dan naskah yang Anda percayakan kepada kami.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5 justify-content-center">
            <div class="col-lg-8 reveal">
                <p class="text-muted" style="font-size:.88rem;">Terakhir diperbarui: <?= date('d F Y') ?></p>

                <div class="prose">
                    <?php foreach ($sections as $i => [$title, $body]): ?>
                        <h2><?= ($i + 1) . '. ' . $title ?></h2>
                        <?= $body ?>
                    <?php endforeach; ?>
                </div>

                <?php if ($email): ?>
                <div class="notice mt-4">
                    <i class="fa-regular fa-envelope"></i>
                    <span>
                        Pertanyaan mengenai kebijakan ini dapat dikirim ke
                        <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>.
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="side-panel reveal" data-reveal-delay="0.08" style="position:sticky;top:110px;">
                    <h4>Dokumen Terkait</h4>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-solid fa-file-contract"></i></span>
                        <div>
                            <div class="lb">Kebijakan</div>
                            <div class="vl"><a href="<?= $siteUrl ?>/kebijakan-refund" style="color:var(--ink);">Kebijakan Pembatalan &amp; Pengembalian</a></div>
                        </div>
                    </div>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-solid fa-list-check"></i></span>
                        <div>
                            <div class="lb">Panduan</div>
                            <div class="vl"><a href="<?= $siteUrl ?>/proses#ketentuan" style="color:var(--ink);">Ketentuan Naskah</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
