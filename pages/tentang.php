<?php
/**
 * Tentang Kayaswara — profil penerbit, identitas legal, prinsip redaksi.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$legalName = html_entity_decode(getSetting('legal_name', 'CV. Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Tentang Kami — ' . $siteName;
$metaDesc  = 'Profil ' . $legalName . ', penerbit buku akademik: identitas penerbit, prinsip redaksi, dan lini terbitan.';

$address    = getSetting('address', '');
$email      = getSetting('email_contact', '');
$whatsapp   = getSetting('whatsapp_number', '');
$waDigits   = $whatsapp ? preg_replace('/\D/', '', $whatsapp) : '';
$foundedYr  = getSetting('founded_year', '');
$legalNib   = getSetting('legal_nib', '');
$legalAkta  = getSetting('legal_akta', '');
$legalNpwp  = getSetting('legal_npwp', '');

$identity = array_filter([
    'Nama penerbit'   => $siteName,
    'Badan usaha'     => $legalName,
    'Bidang usaha'    => 'Penerbitan buku',
    'Tahun berdiri'   => $foundedYr,
    'NIB'             => $legalNib,
    'Akta pendirian'  => $legalAkta,
    'NPWP'            => $legalNpwp,
    'Alamat redaksi'  => $address,
], static fn($v) => trim((string) $v) !== '');

$principles = [
    ['fa-scale-balanced', 'Telaah sebelum terbit', 'Kelayakan naskah dinilai lebih dahulu. Keputusan terbit tidak ditentukan oleh besarnya biaya yang dibayarkan penulis.'],
    ['fa-file-signature', 'Kesepakatan tertulis', 'Lingkup pekerjaan, jadwal, dan biaya dituangkan dalam dokumen yang dapat dibaca ulang kedua pihak.'],
    ['fa-shield-halved', 'Menjaga hak penulis', 'Hak cipta isi buku tetap milik penulis. Naskah tidak dibagikan kepada pihak di luar tim redaksi yang menangani.'],
    ['fa-book-bookmark', 'Katalog yang terbuka', 'Setiap terbitan dicatat lengkap dengan data bibliografinya dan dapat ditelusuri publik di halaman katalog.'],
    ['fa-recycle', 'Produksi seperlunya', 'Jumlah cetak disesuaikan kebutuhan nyata untuk menekan sisa produksi; berkas digital disiapkan sebagai pendamping.'],
    ['fa-handshake-angle', 'Pendampingan, bukan sekadar jasa', 'Penulis dilibatkan pada setiap keputusan penting: hasil suntingan, rancangan sampul, dan persetujuan akhir.'],
];

$imprints = getPublicationCategories();
unset($imprints['lainnya']);

$team = [
    ['Pemimpin Redaksi', 'Menentukan arah lini terbitan dan mengambil keputusan akhir atas hasil telaah naskah.', 'fa-user-tie'],
    ['Dewan Telaah', 'Menilai kelayakan naskah sesuai bidang keilmuan masing-masing.', 'fa-users-viewfinder'],
    ['Penyunting Naskah', 'Mengerjakan penyuntingan substansi dan bahasa, serta menyusun catatan revisi.', 'fa-pen-fancy'],
    ['Tata Letak & Desain', 'Menata isi buku dan merancang sampul sampai berkas siap cetak.', 'fa-palette'],
];

// Statistik nyata dari basis data
$bookCount = 0; $authorCount = 0; $yearSpan = '';
try {
    $bookCount = (int) (fetch("SELECT COUNT(*) c FROM publications WHERE status='published'")['c'] ?? 0);
    $row = fetch("SELECT MIN(publish_year) a, MAX(publish_year) b FROM publications WHERE status='published' AND publish_year > 0");
    if ($row && $row['a']) $yearSpan = $row['a'] == $row['b'] ? $row['a'] : $row['a'] . '–' . $row['b'];
} catch (Exception $e) {}
try {
    $authorCount = (int) (fetch("SELECT COUNT(DISTINCT authors) c FROM publications WHERE status='published'")['c'] ?? 0);
} catch (Exception $e) {}
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Tentang</li>
        </ol>
        <span class="eyebrow">Profil Penerbit</span>
        <h1>Tentang <?= htmlspecialchars($siteName) ?></h1>
        <p>Penerbit buku akademik yang bekerja dengan dosen, peneliti, dan mahasiswa pascasarjana di Indonesia.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7 reveal">
                <span class="eyebrow">Siapa Kami</span>
                <h2>Menjaga jarak antara naskah dan meja cetak</h2>
                <div class="rule-accent"></div>
                <div class="prose">
                    <p>
                        <?= htmlspecialchars($legalName) ?> adalah badan usaha yang bergerak di bidang penerbitan buku.
                        Kami menerbitkan buku ajar, buku referensi, monograf, dan bunga rampai — jenis terbitan yang
                        pembacanya menuntut ketelitian: mahasiswa yang memakainya untuk kuliah, dan peneliti yang
                        mengutipnya untuk karya berikutnya.
                    </p>
                    <p>
                        Karena itu kami menaruh satu tahap yang tidak dapat dilewati siapa pun: telaah redaksi.
                        Naskah dibaca lebih dahulu, dinilai kelayakannya, lalu diputuskan. Sebagian naskah kami terima,
                        sebagian dikembalikan disertai catatan perbaikan. Cara ini membuat proses terasa lebih panjang,
                        tetapi menjaga agar setiap judul yang keluar dari katalog kami pantas dipertanggungjawabkan.
                    </p>
                    <p>
                        Setelah naskah diterima, pekerjaan berlanjut ke penyuntingan, tata letak, desain sampul,
                        produksi, hingga pencatatan pada katalog penerbit. Penulis dilibatkan di tiap titik keputusan
                        dan dapat memantau posisi naskahnya secara daring.
                    </p>
                </div>
            </div>

            <div class="col-lg-5 reveal" data-reveal-delay="0.1">
                <div class="side-panel">
                    <h4>Identitas Penerbit</h4>
                    <table class="biblio-table">
                        <tbody>
                        <?php foreach ($identity as $label => $value): ?>
                            <tr>
                                <th scope="row"><?= htmlspecialchars($label) ?></th>
                                <td><?= nl2br(htmlspecialchars((string) $value)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="side-panel">
                    <h4>Hubungi Redaksi</h4>
                    <?php if ($email): ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-regular fa-envelope"></i></span>
                        <div>
                            <div class="lb">Surel</div>
                            <div class="vl"><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($whatsapp): ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-brands fa-whatsapp"></i></span>
                        <div>
                            <div class="lb">WhatsApp</div>
                            <div class="vl"><a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($whatsapp) ?></a></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-regular fa-clock"></i></span>
                        <div>
                            <div class="lb">Jam kerja</div>
                            <div class="vl">Senin–Jumat, 08.00–17.00 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Angka -->
<?php if ($bookCount > 0): ?>
<section class="section-sm paper-grain">
    <div class="container">
        <div class="row g-3 justify-content-center">
            <div class="col-6 col-lg-3 reveal">
                <div class="figure-card">
                    <div class="num" data-count-to="<?= $bookCount ?>"><?= $bookCount ?></div>
                    <div class="lbl">Judul dalam katalog</div>
                </div>
            </div>
            <?php if ($authorCount > 0): ?>
            <div class="col-6 col-lg-3 reveal" data-reveal-delay="0.06">
                <div class="figure-card">
                    <div class="num" data-count-to="<?= $authorCount ?>"><?= $authorCount ?></div>
                    <div class="lbl">Penulis & tim penulis</div>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-6 col-lg-3 reveal" data-reveal-delay="0.12">
                <div class="figure-card">
                    <div class="num"><?= count($imprints) ?></div>
                    <div class="lbl">Lini terbitan</div>
                </div>
            </div>
            <?php if ($yearSpan !== ''): ?>
            <div class="col-6 col-lg-3 reveal" data-reveal-delay="0.18">
                <div class="figure-card">
                    <div class="num" style="font-size:1.6rem;"><?= htmlspecialchars($yearSpan) ?></div>
                    <div class="lbl">Rentang tahun terbit</div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Prinsip -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Prinsip Redaksi</span>
            <h2>Enam hal yang kami pegang</h2>
            <p>Bukan slogan — enam hal ini menentukan bagaimana keputusan diambil di meja redaksi.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($principles as $i => [$icon, $title, $desc]): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= ($i % 3) * 0.07 ?>">
                <div class="card-plain">
                    <div class="service-icon"><i class="fa-solid <?= $icon ?>"></i></div>
                    <h3 class="h6 mb-2"><?= $title ?></h3>
                    <p class="mb-0" style="font-size:.92rem;"><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Lini terbitan -->
<section class="section paper-grain">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal">
                <span class="eyebrow">Lini Terbitan</span>
                <h2>Apa saja yang kami terbitkan</h2>
                <div class="rule-accent"></div>
                <p>Fokus kami adalah terbitan ilmiah. Naskah di luar lini berikut dapat tetap diajukan, dan akan dinilai kasus per kasus oleh redaksi.</p>
                <a href="<?= $siteUrl ?>/publikasi" class="btn btn-outline-primary mt-2">
                    <i class="fa-solid fa-book-open"></i>Lihat Katalog
                </a>
            </div>
            <div class="col-lg-7 reveal" data-reveal-delay="0.1">
                <div class="legal-grid">
                    <?php foreach ($imprints as $key => $label): ?>
                    <div class="legal-cell">
                        <div class="lb">Lini</div>
                        <div class="vl"><?= htmlspecialchars($label) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Struktur redaksi -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Struktur Kerja</span>
            <h2>Siapa mengerjakan apa</h2>
            <p>Naskah berpindah tangan secara terstruktur, bukan ditangani satu orang dari awal sampai akhir.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($team as $i => [$role, $desc, $icon]): ?>
            <div class="col-md-6 col-lg-3 reveal" data-reveal-delay="<?= $i * 0.07 ?>">
                <div class="person-card">
                    <div class="person-avatar"><i class="fa-solid <?= $icon ?>"></i></div>
                    <h3 class="h6 mb-1"><?= $role ?></h3>
                    <p><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Ingin berdiskusi dengan redaksi?</h2>
                    <p>Kirim naskah atau kerangkanya, atau hubungi kami langsung pada jam kerja.</p>
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
