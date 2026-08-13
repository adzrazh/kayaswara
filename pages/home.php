<?php
/**
 * Beranda — Kayaswara
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = $siteName . ' — Penerbit Buku Akademik';
$metaDesc  = 'Kayaswara menerbitkan buku ajar, buku referensi, monograf, dan bunga rampai karya dosen serta peneliti Indonesia, melalui proses telaah dan penyuntingan yang terdokumentasi.';

$categories = getPublicationCategories();

// Katalog terbaru
$latestBooks = [];
$bookCount   = 0;
try {
    $latestBooks = fetchAll("SELECT * FROM publications WHERE status = 'published' ORDER BY is_featured DESC, publish_year DESC, id DESC LIMIT 4");
    $bookCount   = (int) (fetch("SELECT COUNT(*) c FROM publications WHERE status = 'published'")['c'] ?? 0);
} catch (Exception $e) {}

// Institusi mitra (portofolio kerja sama)
$partnerCount = 0;
try {
    $partnerCount = (int) (fetch("SELECT COUNT(DISTINCT client_institution) c FROM portfolio WHERE status='published' AND client_institution <> ''")['c'] ?? 0);
} catch (Exception $e) {}

// Wawasan terbaru
$latestPosts = [];
try {
    $latestPosts = fetchAll("SELECT title, slug, excerpt, image, created_at, category FROM blog_posts WHERE status='published' ORDER BY created_at DESC LIMIT 3");
} catch (Exception $e) {}

$scopes = [
    ['fa-chalkboard-user', 'Buku Ajar', 'Buku pegangan perkuliahan yang disusun mengikuti capaian pembelajaran dan silabus program studi.'],
    ['fa-book-open-reader', 'Buku Referensi', 'Kajian mendalam pada satu bidang keilmuan, ditujukan bagi akademisi dan praktisi.'],
    ['fa-microscope', 'Monograf', 'Terbitan satu topik penelitian yang utuh, umumnya bersumber dari hasil riset penulis.'],
    ['fa-users-rectangle', 'Bunga Rampai', 'Kumpulan tulisan beberapa penulis dalam satu tema, dikoordinasikan oleh editor.'],
    ['fa-people-roof', 'Prosiding', 'Kumpulan makalah hasil seminar atau konferensi ilmiah yang telah melalui penelaahan.'],
];

$steps = [
    ['Pengiriman Naskah', 'Penulis mengirim naskah beserta data diri dan deskripsi singkat karya melalui formulir daring.'],
    ['Telaah Redaksi', 'Redaksi menilai kelayakan naskah: keaslian, kedalaman kajian, struktur, dan kesesuaian dengan lini terbitan.'],
    ['Penyuntingan & Tata Letak', 'Naskah yang diterima disunting, ditata letak, dan dirancang sampulnya bersama penulis.'],
    ['Produksi & Distribusi', 'Buku dicetak, dicatat dalam katalog penerbit, lalu disebarkan sesuai kesepakatan dengan penulis.'],
];
?>

<!-- ════════════════ HERO ════════════════ -->
<section class="hero">
    <div class="container hero-inner">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal">
                <span class="eyebrow">Penerbit Buku Akademik</span>
                <h1 class="hero-title">Menerbitkan gagasan ilmiah menjadi <em>buku yang layak dirujuk</em>.</h1>
                <p class="hero-lead">
                    Kayaswara mendampingi dosen, peneliti, dan mahasiswa pascasarjana menerbitkan karya tulis —
                    dari telaah naskah, penyuntingan, tata letak, hingga produksi dan pencatatan katalog.
                </p>
                <div class="hero-actions">
                    <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary btn-lg">
                        <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
                    </a>
                    <a href="<?= $siteUrl ?>/publikasi" class="btn btn-ghost btn-lg">
                        <i class="fa-solid fa-book-open"></i>Telusuri Katalog
                    </a>
                </div>
                <div class="hero-meta">
                    <div class="hero-meta-item">
                        <span class="num" data-count-to="<?= $bookCount ?>"><?= $bookCount ?></span>
                        <span class="lbl">Judul dalam katalog</span>
                    </div>
                    <div class="hero-meta-item">
                        <span class="num"><?= count($scopes) ?></span>
                        <span class="lbl">Lini terbitan</span>
                    </div>
                    <?php if ($partnerCount > 0): ?>
                    <div class="hero-meta-item">
                        <span class="num" data-count-to="<?= $partnerCount ?>"><?= $partnerCount ?></span>
                        <span class="lbl">Institusi mitra</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6 reveal" data-reveal-delay="0.12">
                <div class="hero-art">
                    <div class="hero-art-frame" aria-hidden="true"></div>
                    <?php
                    $artClasses = ['b1', 'b2', 'b3'];
                    $artBooks   = array_slice($latestBooks, 0, 3);
                    if (!empty($artBooks)):
                        foreach ($artBooks as $i => $b):
                            $cover = publicationCoverUrl($b['cover'] ?? '');
                    ?>
                    <div class="hero-art-card <?= $artClasses[$i] ?>">
                        <div class="book-cover">
                            <?php if ($cover): ?>
                                <img src="<?= htmlspecialchars($cover) ?>" alt="Sampul <?= htmlspecialchars($b['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="book-cover-fallback">
                                    <span class="bcf-mark"><?= htmlspecialchars($siteName) ?></span>
                                    <span class="bcf-title"><?= htmlspecialchars($b['title']) ?></span>
                                    <span class="bcf-mark"><?= htmlspecialchars(getPublicationCategoryLabel($b['category'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <?php foreach (['Buku Ajar', 'Buku Referensi', 'Monograf'] as $i => $lbl): ?>
                    <div class="hero-art-card <?= $artClasses[$i] ?>">
                        <div class="book-cover">
                            <div class="book-cover-fallback">
                                <span class="bcf-mark"><?= htmlspecialchars($siteName) ?></span>
                                <span class="bcf-title"><?= $lbl ?></span>
                                <span class="bcf-mark">Katalog Penerbit</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════ PRINSIP KERJA ════════════════ -->
<section class="section-sm paper-grain">
    <div class="container">
        <div class="trust-strip">
            <span class="trust-strip-item"><i class="fa-solid fa-magnifying-glass"></i>Setiap naskah ditelaah redaksi</span>
            <span class="trust-strip-item"><i class="fa-solid fa-file-signature"></i>Perjanjian penerbitan tertulis</span>
            <span class="trust-strip-item"><i class="fa-solid fa-shield-halved"></i>Pemeriksaan keaslian naskah</span>
            <span class="trust-strip-item"><i class="fa-solid fa-list-check"></i>Progres dapat dilacak daring</span>
        </div>
    </div>
</section>

<!-- ════════════════ LINI TERBITAN ════════════════ -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Lini Terbitan</span>
            <h2>Karya ilmiah yang kami terbitkan</h2>
            <p>Lima lini terbitan yang menjadi fokus redaksi, seluruhnya berbasis kaidah penulisan akademik.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($scopes as $i => [$icon, $title, $desc]): ?>
            <div class="col-md-6 col-lg reveal" data-reveal-delay="<?= $i * 0.07 ?>">
                <article class="card-plain">
                    <div class="service-icon"><i class="fa-solid <?= $icon ?>"></i></div>
                    <h3 class="h5"><?= $title ?></h3>
                    <p class="mb-0" style="font-size:.92rem;"><?= $desc ?></p>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ════════════════ KATALOG TERBARU ════════════════ -->
<section class="section paper-grain">
    <div class="container">
        <div class="section-head-split reveal">
            <div class="section-head">
                <span class="eyebrow">Katalog Publikasi</span>
                <h2>Terbitan terbaru</h2>
                <p>Daftar buku yang telah diterbitkan dan tercatat dalam katalog penerbit.</p>
            </div>
            <a href="<?= $siteUrl ?>/publikasi" class="btn btn-ghost">
                Lihat Seluruh Katalog<i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if (!empty($latestBooks)): ?>
        <div class="row g-4">
            <?php foreach ($latestBooks as $i => $b):
                $cover = publicationCoverUrl($b['cover'] ?? '');
            ?>
            <div class="col-6 col-lg-3 reveal" data-reveal-delay="<?= $i * 0.06 ?>">
                <article class="book-card">
                    <a class="book-cover" href="<?= $siteUrl ?>/publikasi/<?= htmlspecialchars($b['slug']) ?>" aria-label="<?= htmlspecialchars($b['title']) ?>">
                        <span class="book-cover-tag chip chip-gold"><?= htmlspecialchars(getPublicationCategoryLabel($b['category'])) ?></span>
                        <?php if ($cover): ?>
                            <img src="<?= htmlspecialchars($cover) ?>" alt="Sampul <?= htmlspecialchars($b['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="book-cover-fallback">
                                <span class="bcf-mark"><?= htmlspecialchars($siteName) ?></span>
                                <span class="bcf-title"><?= htmlspecialchars($b['title']) ?></span>
                                <span class="bcf-mark"><?= htmlspecialchars($b['publish_year'] ?: '') ?></span>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="book-body">
                        <h3 class="book-title">
                            <a href="<?= $siteUrl ?>/publikasi/<?= htmlspecialchars($b['slug']) ?>"><?= htmlspecialchars($b['title']) ?></a>
                        </h3>
                        <p class="book-author"><?= htmlspecialchars($b['authors']) ?></p>
                        <div class="book-foot">
                            <span><?= htmlspecialchars($b['publish_year'] ?: '—') ?></span>
                            <span><?= $b['pages'] ? (int) $b['pages'] . ' hlm.' : '' ?></span>
                        </div>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state reveal">
            <i class="fa-solid fa-book"></i>
            <h4>Katalog sedang disiapkan</h4>
            <p>Daftar terbitan akan segera tersedia di halaman katalog publikasi.</p>
            <a href="<?= $siteUrl ?>/publikasi" class="btn btn-outline-primary btn-sm">Buka Halaman Katalog</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ════════════════ ALUR PENERBITAN ════════════════ -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Alur Penerbitan</span>
            <h2>Empat tahap dari naskah menjadi buku</h2>
            <p>Setiap tahap didokumentasikan, dan penulis dapat memantau posisi naskahnya kapan saja.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($steps as $i => [$title, $desc]): ?>
            <div class="col-md-6 col-lg-3 reveal" data-reveal-delay="<?= $i * 0.07 ?>">
                <article class="step-card">
                    <div class="step-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
                    <h3><?= $title ?></h3>
                    <p><?= $desc ?></p>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4 reveal">
            <a href="<?= $siteUrl ?>/proses" class="btn-link-arrow">
                Pelajari alur lengkap &amp; ketentuan naskah<i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- ════════════════ MENGAPA KAYASWARA ════════════════ -->
<section class="section paper-grain">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal">
                <span class="eyebrow">Cara Kami Bekerja</span>
                <h2>Standar kerja redaksi yang dapat penulis periksa</h2>
                <div class="rule-accent"></div>
                <ul class="check-list">
                    <li><i class="fa-solid fa-check"></i><span><strong>Telaah sebelum menerbitkan.</strong> Naskah dinilai kelayakannya lebih dahulu; tidak semua naskah kami terima.</span></li>
                    <li><i class="fa-solid fa-check"></i><span><strong>Penyuntingan berjenjang.</strong> Penyuntingan substansi dan bahasa dikerjakan terpisah, dengan catatan revisi yang dikembalikan kepada penulis.</span></li>
                    <li><i class="fa-solid fa-check"></i><span><strong>Hak penulis dijaga.</strong> Hak cipta tetap milik penulis; lingkup kerja sama dituangkan dalam perjanjian tertulis.</span></li>
                    <li><i class="fa-solid fa-check"></i><span><strong>Transparan sejak awal.</strong> Rincian pekerjaan, jadwal, dan biaya disampaikan tertulis sebelum produksi dimulai.</span></li>
                    <li><i class="fa-solid fa-check"></i><span><strong>Katalog terbuka.</strong> Setiap terbitan dicatat lengkap dengan data bibliografinya pada halaman katalog.</span></li>
                </ul>
                <a href="<?= $siteUrl ?>/tentang" class="btn btn-outline-primary mt-3">
                    <i class="fa-solid fa-landmark"></i>Profil Penerbit
                </a>
            </div>

            <div class="col-lg-6 reveal" data-reveal-delay="0.1">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="figure-card">
                            <div class="num">4</div>
                            <div class="lbl">Tahap kerja redaksi</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="figure-card">
                            <div class="num">2</div>
                            <div class="lbl">Putaran revisi penulis</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="figure-card">
                            <div class="num">100%</div>
                            <div class="lbl">Hak cipta di tangan penulis</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="figure-card">
                            <div class="num">1×24</div>
                            <div class="lbl">Jam kerja untuk balasan awal</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="notice">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>
                                Naskah yang belum memenuhi standar tidak langsung ditolak — redaksi memberi catatan
                                perbaikan agar penulis dapat mengirimkannya kembali.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════ WAWASAN ════════════════ -->
<?php if (!empty($latestPosts)): ?>
<section class="section">
    <div class="container">
        <div class="section-head-split reveal">
            <div class="section-head">
                <span class="eyebrow">Wawasan</span>
                <h2>Catatan redaksi untuk penulis</h2>
                <p>Panduan praktis seputar penulisan dan penerbitan karya akademik.</p>
            </div>
            <a href="<?= $siteUrl ?>/wawasan" class="btn btn-ghost">Semua Tulisan<i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="row g-4">
            <?php foreach ($latestPosts as $i => $p): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= $i * 0.07 ?>">
                <article class="article-card">
                    <a class="article-thumb" href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($p['slug']) ?>">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/blog/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <i class="fa-regular fa-file-lines"></i>
                        <?php endif; ?>
                    </a>
                    <div class="article-body">
                        <div class="article-meta">
                            <?php if (!empty($p['category'])): ?><span><i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($p['category']) ?></span><?php endif; ?>
                            <span><i class="fa-regular fa-calendar me-1"></i><?= formatDate($p['created_at']) ?></span>
                        </div>
                        <h3 class="article-title">
                            <a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a>
                        </h3>
                        <p class="article-excerpt"><?= htmlspecialchars(truncate($p['excerpt'] ?? '', 130)) ?></p>
                        <a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($p['slug']) ?>" class="btn-link-arrow">Baca<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ════════════════ CTA ════════════════ -->
<section class="section pt-0">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Punya naskah yang siap ditelaah?</h2>
                    <p>Kirimkan berkas dan deskripsi singkat karya Anda. Redaksi memberi tanggapan awal dalam 1×24 jam kerja.</p>
                </div>
                <div class="col-lg-4">
                    <div class="cta-actions justify-content-lg-end">
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-accent btn-lg">
                            <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
