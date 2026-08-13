<?php
/**
 * Kebijakan Pembatalan & Pengembalian Biaya.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$legalName = html_entity_decode(getSetting('legal_name', 'CV. Kayaswara'), ENT_QUOTES, 'UTF-8');
$email     = getSetting('email_contact', '');
$pageTitle = 'Kebijakan Pembatalan & Pengembalian — ' . $siteName;
$metaDesc  = 'Ketentuan pembatalan pekerjaan penerbitan dan pengembalian biaya di ' . $legalName . '.';

$stages = [
    ['Sebelum pekerjaan dimulai', '100% dikembalikan', 'Pembatalan sebelum penyuntingan dimulai. Uang muka dikembalikan penuh setelah dipotong biaya transfer.', 'ok'],
    ['Tahap penyuntingan berjalan', 'Sisa proporsional', 'Biaya untuk pekerjaan yang telah dikerjakan diperhitungkan; sisanya dikembalikan. Berkas hasil suntingan sampai titik pembatalan diserahkan kepada penulis.', 'warn'],
    ['Tata letak & desain selesai', 'Tidak dikembalikan', 'Seluruh berkas hasil kerja (naskah tertata dan rancangan sampul) diserahkan kepada penulis dalam format final.', 'no'],
    ['Buku telah dicetak', 'Tidak dikembalikan', 'Biaya produksi telah dikeluarkan. Penggantian hanya berlaku untuk cacat produksi sesuai ketentuan di bawah.', 'no'],
];

$sections = [
    [
        'Ruang lingkup',
        '<p>Kebijakan ini mengatur pembatalan pekerjaan penerbitan dan pengembalian biaya antara penulis dan ' . htmlspecialchars($legalName) . '. Ketentuan pada perjanjian penerbitan yang telah ditandatangani kedua pihak berlaku lebih dahulu bila terdapat perbedaan pengaturan.</p>',
    ],
    [
        'Tahap telaah naskah',
        '<p>Proses telaah naskah tidak dipungut biaya. Apabila naskah dinyatakan belum layak terbit, tidak ada kewajiban pembayaran apa pun dari pihak penulis.</p>',
    ],
    [
        'Cacat produksi',
        '<p>Buku dengan cacat produksi — halaman kosong, urutan halaman tertukar, jilid lepas, atau cetakan tidak terbaca — dapat diajukan penggantian dalam <strong>14 hari kalender</strong> sejak buku diterima, disertai foto dan keterangan. Penggantian dilakukan dengan mencetak ulang eksemplar yang cacat tanpa biaya tambahan.</p>
         <p>Perbedaan warna cetak yang masih dalam batas toleransi percetakan tidak dikategorikan sebagai cacat produksi.</p>',
    ],
    [
        'Perubahan atas permintaan penulis',
        '<p>Perubahan isi naskah setelah tahap persetujuan akhir, atau perubahan yang mengubah struktur buku secara mendasar, dinilai sebagai pekerjaan tambahan dan dihitung terpisah dari biaya awal.</p>',
    ],
    [
        'Pembatalan oleh penerbit',
        '<p>Kami dapat menghentikan pekerjaan apabila ditemukan penjiplakan, pemalsuan data, pelanggaran hak cipta pihak lain, atau pelanggaran ketentuan perjanjian. Dalam hal ini biaya untuk pekerjaan yang telah dikerjakan tidak dikembalikan.</p>',
    ],
    [
        'Cara mengajukan',
        '<p>Pengajuan pembatalan atau pengembalian biaya disampaikan tertulis melalui surel resmi kami, memuat kode pelacakan, alasan pengajuan, dan bukti pendukung bila ada. Pengajuan ditanggapi dalam <strong>7 hari kerja</strong>, dan dana yang disetujui dikembalikan dalam <strong>14 hari kerja</strong> ke rekening asal pembayaran.</p>',
    ],
];

$badge = ['ok' => 'chip', 'warn' => 'chip chip-gold', 'no' => 'chip chip-plain'];
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Kebijakan Pembatalan</li>
        </ol>
        <span class="eyebrow">Dokumen Kebijakan</span>
        <h1>Kebijakan Pembatalan &amp; Pengembalian</h1>
        <p>Ketentuan yang berlaku bila pekerjaan penerbitan dihentikan di tengah jalan, beserta hak masing-masing pihak.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5 justify-content-center">
            <div class="col-lg-8 reveal">
                <p class="text-muted" style="font-size:.88rem;">Terakhir diperbarui: <?= date('d F Y') ?></p>

                <h2 class="h4">Pengembalian menurut tahap pekerjaan</h2>
                <div class="rule-accent"></div>

                <div class="table-responsive mb-4">
                    <table class="compare-table">
                        <thead>
                            <tr>
                                <th style="min-width:190px;">Tahap saat pembatalan</th>
                                <th style="min-width:150px;">Pengembalian</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stages as [$stage, $amount, $note, $tone]): ?>
                            <tr>
                                <td class="fw-600"><?= $stage ?></td>
                                <td><span class="<?= $badge[$tone] ?>"><?= $amount ?></span></td>
                                <td style="font-size:.88rem;"><?= $note ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

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
                        Pengajuan dan pertanyaan mengenai kebijakan ini dikirim ke
                        <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>.
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="side-panel reveal" data-reveal-delay="0.08" style="position:sticky;top:110px;">
                    <h4>Dokumen Terkait</h4>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-solid fa-user-shield"></i></span>
                        <div>
                            <div class="lb">Kebijakan</div>
                            <div class="vl"><a href="<?= $siteUrl ?>/kebijakan-privasi" style="color:var(--ink);">Kebijakan Privasi</a></div>
                        </div>
                    </div>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-solid fa-receipt"></i></span>
                        <div>
                            <div class="lb">Biaya</div>
                            <div class="vl"><a href="<?= $siteUrl ?>/biaya" style="color:var(--ink);">Biaya Penerbitan</a></div>
                        </div>
                    </div>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-solid fa-diagram-project"></i></span>
                        <div>
                            <div class="lb">Panduan</div>
                            <div class="vl"><a href="<?= $siteUrl ?>/proses" style="color:var(--ink);">Alur Penerbitan</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
