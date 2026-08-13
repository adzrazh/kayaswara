<?php
/**
 * Home Page – Kayaswara Publishing
 */
$pageTitle = getSetting('site_name', 'Kayaswara') . ' – Jasa Penerbitan Buku Akademik Profesional';
$siteUrl   = defined('SITE_URL') ? SITE_URL : '';

// Load featured portfolio
$featured = fetchAll("SELECT * FROM portfolio WHERE status='published' ORDER BY is_featured DESC, created_at DESC LIMIT 6");

// Load latest blog posts
$latestPosts = fetchAll("SELECT * FROM blog_posts WHERE status='published' ORDER BY created_at DESC LIMIT 3");
?>

<!-- HERO SECTION -->
<section class="hero-section" aria-label="Hero">
    <div class="hero-shapes" aria-hidden="true">
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>
        <div class="hero-shape hero-shape-4"></div>
    </div>

    <div class="container hero-content">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-7 col-xl-6">
                <div class="fade-in-up">
                    <span class="hero-badge">
                        <i class="fas fa-book-open me-2"></i>Mitra Penerbitan Akademik Terpercaya
                    </span>
                    <h1 class="hero-title">
                        Terbitkan<br>
                        <span class="text-gradient">Buku Akademik</span><br>
                        Anda Bersama Kami
                    </h1>
                    <p class="hero-subtitle">
                        Kami membantu dosen, peneliti, dan akademisi di seluruh Indonesia menerbitkan buku ajar, buku referensi, monograf, dan karya ilmiah lainnya secara profesional — dari naskah hingga buku siap terbit.
                    </p>
                    <div class="hero-actions">
                        <a href="<?= $siteUrl ?>/konsultasi" class="btn btn-accent btn-lg" data-testid="hero-cta-konsultasi">
                            <i class="fas fa-comments me-2"></i>Konsultasi Gratis
                        </a>
                        <a href="<?= $siteUrl ?>/portofolio" class="btn btn-outline-light btn-lg" data-testid="hero-cta-portofolio">
                            <i class="fas fa-book me-2"></i>Lihat Portofolio
                        </a>
                    </div>
                    <div class="hero-trust mt-4">
                        <span class="hero-trust-item">
                            <i class="fas fa-truck text-accent me-1"></i> Pengiriman Seluruh Indonesia
                        </span>
                        <span class="hero-trust-item">
                            <i class="fas fa-headset text-accent me-1"></i> Pendampingan Penuh
                        </span>
                        <span class="hero-trust-item">
                            <i class="fas fa-certificate text-accent me-1"></i> Bergaransi
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-xl-6 d-none d-lg-block">
                <div class="hero-illustration fade-in-right">
                    <div class="browser-mockup">
                        <div class="browser-bar">
                            <span class="browser-dot dot-red"></span>
                            <span class="browser-dot dot-yellow"></span>
                            <span class="browser-dot dot-green"></span>
                            <div class="browser-url-bar">
                                <i class="fas fa-book me-1" style="font-size:0.65rem;"></i>
                                publisher.kayaswara.co.id
                            </div>
                        </div>
                        <div class="browser-content">
                            <div class="mock-header"></div>
                            <div class="mock-hero"></div>
                            <div class="mock-cards">
                                <div class="mock-card"></div>
                                <div class="mock-card"></div>
                                <div class="mock-card"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS COUNTER -->
<section class="stats-section">
    <div class="container">
        <div class="row g-3 g-md-4">
            <div class="col-6 col-md-3">
                <div class="stat-card fade-in-up" data-testid="stat-naskah">
                    <div class="stat-number" data-count="75">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Naskah Terbit</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card fade-in-up" style="animation-delay:0.1s" data-testid="stat-penulis">
                    <div class="stat-number" data-count="40">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Penulis Dilayani</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card fade-in-up" style="animation-delay:0.2s" data-testid="stat-kepuasan">
                    <div class="stat-number" data-count="98">0</div>
                    <div class="stat-suffix">%</div>
                    <div class="stat-label">Tingkat Kepuasan</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card fade-in-up" style="animation-delay:0.3s" data-testid="stat-pengalaman">
                    <div class="stat-number" data-count="3">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Tahun Pengalaman</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES / LAYANAN -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="section-header text-center fade-in-up">
            <span class="section-badge">Layanan Kami</span>
            <h2 class="section-title">Solusi Lengkap Penerbitan Buku Akademik</h2>
            <p class="section-subtitle">
                Dari naskah mentah hingga buku siap terbit dan terdistribusi — kami menyediakan layanan penerbitan end-to-end untuk kebutuhan akademik Anda.
            </p>
        </div>
        <div class="row g-4 mt-2">
            <?php
            $features = [
                ['icon' => 'fas fa-book',           'title' => 'Penerbitan Buku',     'desc' => 'Layanan penerbitan buku akademik lengkap — mulai dari review naskah, editing, layout, desain cover, hingga cetak. Buku ajar, referensi, dan monograf.', 'color' => 'primary'],
                ['icon' => 'fas fa-file-alt',       'title' => 'Konversi KTI',        'desc' => 'Ubah karya tulis ilmiah Anda (skripsi, tesis, disertasi, jurnal, prosiding) menjadi buku yang siap terbit dan bernilai kredit akademik.', 'color' => 'secondary'],
                ['icon' => 'fas fa-pen-fancy',      'title' => 'Editing & Layout',    'desc' => 'Tim editor profesional kami memastikan naskah Anda bebas kesalahan tata bahasa, konsisten secara akademik, dan tertata rapi sesuai standar penerbitan.', 'color' => 'accent'],
                ['icon' => 'fas fa-palette',        'title' => 'Desain Cover Buku',   'desc' => 'Desain cover buku yang menarik dan profesional sesuai identitas penulis serta standar penerbitan — meningkatkan daya tarik pembaca.', 'color' => 'primary'],
                ['icon' => 'fas fa-bullhorn',        'title' => 'Distribusi & Pemasaran',   'desc' => 'Distribusi buku ke toko buku online maupun offline di seluruh Indonesia, serta pemasaran melalui kanal digital.', 'color' => 'secondary'],
                ['icon' => 'fas fa-chalkboard-teacher', 'title' => 'Konsultasi & Pendampingan', 'desc' => 'Pendampingan penuh dari awal hingga akhir proses penerbitan. Konsultasi gratis untuk menentukan jenis buku, strategi penerbitan, dan kebutuhan Anda.', 'color' => 'accent'],
            ];
            foreach ($features as $i => $f):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card fade-in-up" style="animation-delay:<?= $i * 0.08 ?>s" data-testid="feature-card-<?= $i ?>">
                    <div class="feature-icon-wrap feature-icon-<?= $f['color'] ?>">
                        <i class="<?= $f['icon'] ?>"></i>
                    </div>
                    <h3 class="feature-title"><?= $f['title'] ?></h3>
                    <p class="feature-desc"><?= $f['desc'] ?></p>
                    <a href="<?= $siteUrl ?>/layanan" class="feature-link">
                        Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5 fade-in-up">
            <a href="<?= $siteUrl ?>/layanan" class="btn btn-primary btn-lg" data-testid="lihat-semua-layanan">
                <i class="fas fa-list me-2"></i>Lihat Semua Layanan
            </a>
        </div>
    </div>
</section>

<!-- FEATURED PORTFOLIO -->
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up">
            <span class="section-badge">Karya Terbitan Kami</span>
            <h2 class="section-title">Portofolio Buku Terpilih</h2>
            <p class="section-subtitle">
                Berbagai buku akademik berkualitas yang telah kami terbitkan bersama para penulis dari kalangan dosen dan akademisi di Indonesia.
            </p>
        </div>

        <?php if (!empty($featured)): ?>
        <div class="row g-4 mt-2">
            <?php foreach ($featured as $i => $item): ?>
            <div class="col-md-6 col-lg-4">
                <div class="portfolio-card fade-in-up" style="animation-delay:<?= $i * 0.08 ?>s">
                    <div class="portfolio-card-img">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/portfolio/<?= htmlspecialchars($item['image']) ?>"
                                 alt="<?= htmlspecialchars($item['title']) ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="portfolio-card-placeholder">
                                <i class="fas fa-book"></i>
                            </div>
                        <?php endif; ?>
                        <div class="portfolio-card-overlay">
                            <a href="<?= $siteUrl ?>/portofolio/<?= htmlspecialchars($item['slug']) ?>"
                               class="btn btn-light btn-sm">
                                <i class="fas fa-eye me-1"></i> Lihat Detail
                            </a>
                        </div>
                        <?php if ($item['is_featured']): ?>
                        <span class="portfolio-featured-badge">
                            <i class="fas fa-star me-1"></i>Unggulan
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="portfolio-card-body">
                        <span class="category-badge category-<?= htmlspecialchars($item['category']) ?>">
                            <?= ucfirst(htmlspecialchars($item['category'])) ?>
                        </span>
                        <h4 class="portfolio-card-title">
                            <a href="<?= $siteUrl ?>/portofolio/<?= htmlspecialchars($item['slug']) ?>">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h4>
                        <?php if (!empty($item['client_institution'])): ?>
                        <p class="portfolio-institution">
                            <i class="fas fa-university me-1 text-muted"></i>
                            <?= htmlspecialchars($item['client_institution']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- Placeholder cards when DB is empty -->
        <div class="row g-4 mt-2">
            <?php
            $placeholders = [
                ['title' => 'Buku Ajar Metode Penelitian', 'inst' => 'Universitas Indonesia', 'cat' => 'jurnal'],
                ['title' => 'Buku Referensi Manajemen Pendidikan', 'inst' => 'Universitas Gadjah Mada', 'cat' => 'konferensi'],
                ['title' => 'Monograf Teknologi Informasi', 'inst' => 'Institut Teknologi Bandung', 'cat' => 'repositori'],
                ['title' => 'Buku Ajar Statistik Terapan', 'inst' => 'Universitas Airlangga', 'cat' => 'jurnal'],
                ['title' => 'Buku Referensi Hukum Bisnis', 'inst' => 'Universitas Diponegoro', 'cat' => 'konferensi'],
                ['title' => 'Monograf Ilmu Keperawatan', 'inst' => 'Universitas Brawijaya', 'cat' => 'repositori'],
            ];
            foreach ($placeholders as $i => $p):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="portfolio-card fade-in-up" style="animation-delay:<?= $i * 0.08 ?>s">
                    <div class="portfolio-card-img">
                        <div class="portfolio-card-placeholder">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="portfolio-card-body">
                        <span class="category-badge category-<?= $p['cat'] ?>">
                            <?= ucfirst($p['cat']) ?>
                        </span>
                        <h4 class="portfolio-card-title"><?= $p['title'] ?></h4>
                        <p class="portfolio-institution">
                            <i class="fas fa-university me-1 text-muted"></i><?= $p['inst'] ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="text-center mt-5 fade-in-up">
            <a href="<?= $siteUrl ?>/portofolio" class="btn btn-outline-primary btn-lg" data-testid="lihat-semua-portofolio">
                <i class="fas fa-book me-2"></i>Lihat Semua Portofolio
            </a>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 fade-in-up">
                <span class="section-badge">Keunggulan Kami</span>
                <h2 class="section-title">Mengapa Memilih Kayaswara sebagai Mitra Penerbitan?</h2>
                <p class="text-muted mb-4">
                    Kami bukan sekadar penyedia jasa cetak — kami adalah mitra penerbitan yang memahami kebutuhan unik buku akademik Indonesia.
                </p>
                <div class="why-list">
                    <?php
                    $whyItems = [
                        ['icon' => 'fas fa-award',          'title' => 'Fokus pada Buku Akademik',            'desc' => 'Spesialisasi kami adalah buku ajar, buku referensi, dan monograf — bukan penerbit umum.'],
                        ['icon' => 'fas fa-handshake',      'title' => 'Pendampingan dari Awal hingga Akhir', 'desc' => 'Kami mendampingi Anda di setiap tahap — dari konsultasi, editing, layout, hingga pemasaran dan distribusi.'],
                        ['icon' => 'fas fa-graduation-cap', 'title' => 'Memahami Dunia Akademik',             'desc' => 'Tim kami berlatar belakang akademik dan memahami kebutuhan kredit penerbitan buku dosen.'],
                        ['icon' => 'fas fa-bolt',           'title' => 'Proses Cepat & Transparan',           'desc' => 'Timeline yang realistis dengan update progress berkala. Anda selalu tahu status naskah Anda.'],
                    ];
                    foreach ($whyItems as $item):
                    ?>
                    <div class="why-item">
                        <div class="why-icon">
                            <i class="<?= $item['icon'] ?>"></i>
                        </div>
                        <div class="why-text">
                            <h5><?= $item['title'] ?></h5>
                            <p><?= $item['desc'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6 fade-in-right">
                <div class="testimonial-card">
                    <div class="testimonial-quote-icon"><i class="fas fa-quote-left"></i></div>
                    <p class="testimonial-text">
                        "Kayaswara membantu saya mengubah disertasi menjadi buku referensi dalam waktu kurang dari 1 bulan. Proses editing dan layout sangat profesional, dan desain cover-nya memuaskan. Tim konsultan sangat responsif dan mendampingi saya dari awal hingga buku siap terbit."
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <div class="testimonial-name">Dr. Ratna Megawati, M.Pd.</div>
                            <div class="testimonial-role">Dosen – Universitas Pendidikan Indonesia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- LATEST BLOG POSTS -->
<?php if (!empty($latestPosts)): ?>
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up">
            <span class="section-badge">Artikel Terbaru</span>
            <h2 class="section-title">Blog & Tips Penerbitan</h2>
            <p class="section-subtitle">Artikel, panduan, dan tips terkini seputar penulisan dan penerbitan buku akademik.</p>
        </div>
        <div class="row g-4 mt-2">
            <?php foreach ($latestPosts as $i => $post): ?>
            <div class="col-md-4">
                <article class="blog-card fade-in-up" style="animation-delay:<?= $i * 0.1 ?>s">
                    <div class="blog-card-img">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/blog/<?= htmlspecialchars($post['image']) ?>"
                                 alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="blog-card-placeholder">
                                <i class="fas fa-pen-nib"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="blog-card-body">
                        <div class="blog-card-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i><?= formatDate($post['created_at']) ?></span>
                            <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($post['author']) ?></span>
                        </div>
                        <h4 class="blog-card-title">
                            <a href="<?= $siteUrl ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h4>
                        <p class="blog-card-excerpt"><?= htmlspecialchars(truncate($post['excerpt'] ?: $post['content'], 120)) ?></p>
                        <a href="<?= $siteUrl ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="blog-read-more">
                            Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5 fade-in-up">
            <a href="<?= $siteUrl ?>/blog" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-book-open me-2"></i>Lihat Semua Artikel
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA SECTION -->
<section class="cta-section">
    <div class="cta-shapes" aria-hidden="true">
        <div class="cta-shape cta-shape-1"></div>
        <div class="cta-shape cta-shape-2"></div>
    </div>
    <div class="container text-center">
        <div class="cta-content fade-in-up">
            <h2 class="cta-title">Siap Menerbitkan Buku Anda?</h2>
            <p class="cta-subtitle">
                Dapatkan konsultasi gratis dan penawaran terbaik untuk kebutuhan penerbitan buku akademik Anda. Tim kami siap membantu Anda mulai hari ini.
            </p>
            <div class="cta-actions">
                <a href="<?= $siteUrl ?>/konsultasi" class="btn btn-accent btn-lg" data-testid="cta-konsultasi">
                    <i class="fas fa-comments me-2"></i>Konsultasi Gratis Sekarang
                </a>
                <a href="<?= $siteUrl ?>/harga" class="btn btn-outline-light btn-lg" data-testid="cta-harga">
                    <i class="fas fa-tags me-2"></i>Lihat Paket Harga
                </a>
            </div>
        </div>
    </div>
</section>
