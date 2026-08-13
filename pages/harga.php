<?php
/**
 * Biaya Penerbitan — paket layanan & perbandingan lingkup kerja.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Biaya Penerbitan — ' . $siteName;
$metaDesc  = 'Rincian paket layanan penerbitan buku akademik ' . $siteName . ' beserta lingkup kerja setiap paket dan cara memperoleh penawaran tertulis.';

$packages = [
    [
        'key'      => 'basic',
        'icon'     => 'fa-seedling',
        'name'     => 'Dasar',
        'desc'     => 'Untuk naskah yang sudah rapi dan hanya memerlukan penyuntingan bahasa serta produksi.',
        'featured' => false,
        'on'  => ['Telaah redaksi', 'Penyuntingan bahasa & ejaan', 'Tata letak isi standar', 'Desain sampul (1 rancangan)', 'Contoh cetak sebelum produksi', 'Pencatatan pada katalog penerbit', 'Cetak 10 eksemplar'],
        'off' => ['Penyuntingan substansi', 'Berkas digital (PDF/EPUB)', 'Distribusi daring'],
    ],
    [
        'key'      => 'professional',
        'icon'     => 'fa-book-open-reader',
        'name'     => 'Profesional',
        'desc'     => 'Lingkup paling sering dipilih: penyuntingan penuh, desain khusus, dan penyebaran daring.',
        'featured' => true,
        'on'  => ['Seluruh lingkup paket Dasar', 'Penyuntingan substansi & bahasa', 'Dua putaran revisi penulis', 'Desain sampul (3 rancangan)', 'Berkas digital PDF & EPUB', 'Distribusi kanal daring', 'Cetak 25 eksemplar'],
        'off' => ['Konversi karya tulis ilmiah', 'Pendampingan penulisan'],
    ],
    [
        'key'      => 'premium',
        'icon'     => 'fa-award',
        'name'     => 'Lengkap',
        'desc'     => 'Untuk naskah yang berangkat dari disertasi, tesis, atau laporan penelitian.',
        'featured' => false,
        'on'  => ['Seluruh lingkup paket Profesional', 'Konversi karya tulis ilmiah menjadi buku', 'Pendampingan penulisan per bab', 'Pemeriksaan keaslian naskah', 'Desain sampul & ilustrasi khusus', 'Distribusi daring dan luring', 'Cetak 50 eksemplar'],
        'off' => [],
    ],
];

$compare = [
    'Telaah & Penyuntingan' => [
        ['Telaah kelayakan naskah', true, true, true],
        ['Penyuntingan bahasa & ejaan', true, true, true],
        ['Penyuntingan substansi', false, true, true],
        ['Putaran revisi penulis', '1×', '2×', '2×'],
        ['Pemeriksaan keaslian naskah', false, false, true],
        ['Konversi karya tulis ilmiah', false, false, true],
    ],
    'Desain & Produksi' => [
        ['Tata letak isi', true, true, true],
        ['Rancangan sampul', '1 opsi', '3 opsi', 'Khusus'],
        ['Contoh cetak sebelum produksi', true, true, true],
        ['Berkas digital (PDF & EPUB)', false, true, true],
        ['Jumlah cetak termasuk', '10 eks.', '25 eks.', '50 eks.'],
    ],
    'Katalog & Distribusi' => [
        ['Pencatatan pada katalog penerbit', true, true, true],
        ['Distribusi kanal daring', false, true, true],
        ['Distribusi luring', false, false, true],
        ['Laporan penyebaran', false, true, true],
    ],
    'Pendampingan' => [
        ['Pendampingan penulisan per bab', false, false, true],
        ['Masa pendampingan setelah terbit', '14 hari', '30 hari', '60 hari'],
    ],
];

$faqs = [
    ['Mengapa nominal biaya tidak dicantumkan di halaman ini?',
     'Biaya bergantung pada tebal naskah, tingkat penyuntingan yang dibutuhkan, spesifikasi cetak, dan jumlah eksemplar. Angka yang dipasang tanpa melihat naskah hampir selalu meleset, karena itu kami menyusun penawaran setelah naskah ditelaah.'],
    ['Kapan saya menerima penawaran tertulis?',
     'Penawaran dikirim 2–3 hari kerja setelah hasil telaah naskah keluar, memuat rincian pekerjaan, jadwal, dan biaya per komponen.'],
    ['Bagaimana cara pembayarannya?',
     'Pembayaran dilakukan bertahap melalui transfer bank atau QRIS: uang muka minimal 50% sebelum pengerjaan, dan pelunasan sebelum berkas final atau buku diserahkan. Setiap pembayaran disertai tagihan resmi.'],
    ['Apakah paket dapat disesuaikan?',
     'Bisa. Paket di atas adalah titik awal; komponen dapat ditambah atau dikurangi sesuai kondisi naskah dan kebutuhan Anda.'],
    ['Bagaimana bila naskah dinyatakan belum layak terbit?',
     'Tidak ada biaya yang timbul dari proses telaah. Anda menerima catatan perbaikan dan dipersilakan mengirim ulang naskah setelah diperbaiki.'],
];

$icon = static function ($v) {
    if ($v === true)  return '<i class="fa-solid fa-check compare-yes"></i>';
    if ($v === false) return '<i class="fa-solid fa-minus compare-no"></i>';
    return htmlspecialchars((string) $v);
};
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Biaya</li>
        </ol>
        <span class="eyebrow">Transparansi Biaya</span>
        <h1>Biaya Penerbitan</h1>
        <p>Kami tidak memasang angka sebelum membaca naskah. Yang kami cantumkan di sini adalah lingkup pekerjaan setiap paket, supaya Anda tahu persis apa yang dikerjakan.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <?php foreach ($packages as $i => $p): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= $i * 0.08 ?>">
                <article class="price-card <?= $p['featured'] ? 'is-featured' : '' ?>">
                    <?php if ($p['featured']): ?><span class="price-flag">Paling Banyak Dipilih</span><?php endif; ?>
                    <div class="service-icon"><i class="fa-solid <?= $p['icon'] ?>"></i></div>
                    <h2 class="price-name">Paket <?= $p['name'] ?></h2>
                    <p class="price-desc"><?= $p['desc'] ?></p>
                    <div class="price-amount">Penawaran Tertulis</div>
                    <p class="price-hint">Disusun setelah naskah ditelaah redaksi.</p>

                    <ul class="price-features">
                        <?php foreach ($p['on'] as $f): ?>
                        <li><i class="fa-solid fa-check"></i><span><?= $f ?></span></li>
                        <?php endforeach; ?>
                        <?php foreach ($p['off'] as $f): ?>
                        <li class="off"><i class="fa-solid fa-minus"></i><span><?= $f ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <a href="<?= $siteUrl ?>/kirim-naskah?paket=<?= $p['key'] ?>"
                       class="btn <?= $p['featured'] ? 'btn-primary' : 'btn-outline-primary' ?> w-100">
                        <i class="fa-regular fa-paper-plane"></i>Minta Penawaran
                    </a>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="notice mt-4 reveal">
            <i class="fa-solid fa-circle-info"></i>
            <span>
                Biaya akhir dipengaruhi tebal naskah, tingkat penyuntingan, spesifikasi cetak, dan jumlah eksemplar.
                Seluruh komponen dirinci dalam dokumen penawaran sebelum pekerjaan dimulai.
            </span>
        </div>
    </div>
</section>

<!-- Perbandingan -->
<section class="section paper-grain">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Perbandingan</span>
            <h2>Lingkup kerja setiap paket</h2>
            <p>Rincian pekerjaan yang termasuk dan tidak termasuk dalam masing-masing paket.</p>
        </div>

        <div class="table-responsive reveal">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th style="min-width:240px;">Komponen Pekerjaan</th>
                        <th class="text-center">Dasar</th>
                        <th class="text-center is-featured">Profesional</th>
                        <th class="text-center">Lengkap</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($compare as $group => $rows): ?>
                    <tr class="group-row"><td colspan="4"><?= $group ?></td></tr>
                    <?php foreach ($rows as [$label, $a, $b, $c]): ?>
                    <tr>
                        <td><?= $label ?></td>
                        <td class="text-center"><?= $icon($a) ?></td>
                        <td class="text-center"><?= $icon($b) ?></td>
                        <td class="text-center"><?= $icon($c) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Cara memperoleh penawaran -->
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal">
                <span class="eyebrow">Cara Kerjanya</span>
                <h2>Tiga langkah menuju penawaran</h2>
                <div class="rule-accent"></div>
                <p>Tidak ada biaya yang timbul sampai Anda menyetujui penawaran secara tertulis.</p>
                <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary mt-2">
                    <i class="fa-regular fa-paper-plane"></i>Mulai dari Sini
                </a>
            </div>
            <div class="col-lg-7 reveal" data-reveal-delay="0.1">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="step-card">
                            <div class="step-num">01</div>
                            <h3 class="h6">Kirim naskah</h3>
                            <p>Lampirkan berkas dan jelaskan singkat isi karya Anda.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step-card">
                            <div class="step-num">02</div>
                            <h3 class="h6">Telaah redaksi</h3>
                            <p>Redaksi menilai kelayakan dan kebutuhan penyuntingan.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step-card">
                            <div class="step-num">03</div>
                            <h3 class="h6">Penawaran tertulis</h3>
                            <p>Rincian pekerjaan, jadwal, dan biaya dikirim kepada Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section pt-0">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Pertanyaan Umum</span>
            <h2>Seputar biaya &amp; pembayaran</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion faq reveal" id="faqBiaya">
                    <?php foreach ($faqs as $i => [$q, $a]): ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#fb<?= $i ?>"
                                    aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                                <?= $q ?>
                            </button>
                        </h3>
                        <div id="fb<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqBiaya">
                            <div class="accordion-body"><?= $a ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
