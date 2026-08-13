<?php
/**
 * Layanan (Services) Page – Kayaswara Publishing
 */
$pageTitle = 'Layanan – ' . getSetting('site_name', 'Kayaswara');
$metaDesc  = 'Layanan penerbitan buku akademik lengkap: penerbitan buku ajar, konversi KTI, editing, layout, desain cover, ISBN, dan distribusi.';
$siteUrl   = defined('SITE_URL') ? SITE_URL : '';
?>

<!-- Page Header -->
<div class="page-hero">
    <div class="container">
        <div class="page-hero-content text-center fade-in-up">
            <h1 class="page-hero-title">Layanan Penerbitan Buku Akademik</h1>
            <p class="page-hero-subtitle">Dari naskah mentah hingga buku ber-ISBN siap terbit — kami menangani seluruh proses penerbitan buku akademik Anda dengan standar profesional.</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?= $siteUrl ?>/">Beranda</a></li>
                    <li class="breadcrumb-item active">Layanan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- SERVICES OVERVIEW -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="section-header text-center fade-in-up">
            <span class="section-badge">Solusi Komprehensif</span>
            <h2 class="section-title">Apa yang Kami Tawarkan</h2>
            <p class="section-subtitle">Solusi penerbitan menyeluruh untuk kebutuhan akademik Indonesia — setiap layanan dirancang untuk menghasilkan buku berkualitas tinggi.</p>
        </div>
        <div class="row g-4 mt-2">

            <!-- 1. Penerbitan Buku -->
            <div class="col-lg-6" id="setup">
                <div class="service-detail-card fade-in-up" data-testid="layanan-penerbitan">
                    <div class="service-detail-header" style="background: linear-gradient(135deg, var(--primary), #2a4a7f);">
                        <div class="service-detail-icon">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="service-detail-title">Penerbitan Buku</h3>
                            <p class="service-detail-tagline mb-0">Estimasi: 14–30 hari kerja</p>
                        </div>
                    </div>
                    <div class="service-detail-body">
                        <p>Layanan penerbitan buku akademik lengkap mulai dari review naskah, editing, layout, desain cover, pengurusan ISBN, hingga pencetakan. Kami menerbitkan buku ajar, buku referensi, monograf, dan buku ilmiah lainnya.</p>
                        <p>Setiap buku yang diterbitkan mendapatkan ISBN resmi dari Perpustakaan Nasional RI dan tersedia dalam format cetak maupun e-book.</p>
                        <h6 class="mt-3 mb-2 fw-700">Yang Anda Dapatkan:</h6>
                        <ul class="service-feature-list">
                            <li><i class="fas fa-check-circle"></i> Review & evaluasi naskah oleh tim editor</li>
                            <li><i class="fas fa-check-circle"></i> Editing substansi & tata bahasa</li>
                            <li><i class="fas fa-check-circle"></i> Layout & typesetting profesional</li>
                            <li><i class="fas fa-check-circle"></i> Desain cover buku custom</li>
                            <li><i class="fas fa-check-circle"></i> ISBN resmi & pencetakan buku</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 2. Konversi KTI -->
            <div class="col-lg-6" id="konversi">
                <div class="service-detail-card fade-in-up" style="animation-delay:0.1s" data-testid="layanan-konversi">
                    <div class="service-detail-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark));">
                        <div class="service-detail-icon">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="service-detail-title">Konversi KTI ke Buku</h3>
                            <p class="service-detail-tagline mb-0">Estimasi: 21–45 hari kerja</p>
                        </div>
                    </div>
                    <div class="service-detail-body">
                        <p>Layanan mengubah Karya Tulis Ilmiah (KTI) Anda menjadi buku ber-ISBN. Kami mengonversi skripsi, tesis, disertasi, jurnal, dan prosiding menjadi buku akademik yang memenuhi standar penerbitan.</p>
                        <p>Konversi KTI ke buku memberikan nilai tambah berupa kredit penerbitan buku bagi karir akademik Anda sebagai dosen atau peneliti.</p>
                        <h6 class="mt-3 mb-2 fw-700">Yang Anda Dapatkan:</h6>
                        <ul class="service-feature-list">
                            <li><i class="fas fa-check-circle"></i> Parafrase & restrukturisasi konten KTI</li>
                            <li><i class="fas fa-check-circle"></i> Penambahan bab & penyesuaian format buku</li>
                            <li><i class="fas fa-check-circle"></i> Editing substansi & bahasa</li>
                            <li><i class="fas fa-check-circle"></i> Layout, desain cover & ISBN</li>
                            <li><i class="fas fa-check-circle"></i> Konsultasi konten dengan penulis</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. Editing & Layout -->
            <div class="col-lg-6" id="editing">
                <div class="service-detail-card fade-in-up" data-testid="layanan-editing">
                    <div class="service-detail-header" style="background: linear-gradient(135deg, #245078, var(--primary));">
                        <div class="service-detail-icon">
                            <i class="fas fa-pen-fancy fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="service-detail-title">Editing & Layout</h3>
                            <p class="service-detail-tagline mb-0">Estimasi: 7–14 hari kerja</p>
                        </div>
                    </div>
                    <div class="service-detail-body">
                        <p>Layanan editing profesional untuk memastikan naskah Anda bebas dari kesalahan tata bahasa, ejaan, dan struktur kalimat. Tim editor kami berpengalaman dalam mengedit naskah akademik dari berbagai bidang keilmuan.</p>
                        <p>Layout buku dikerjakan sesuai standar penerbitan dengan typesetting yang rapi, pengaturan margin, header/footer, dan daftar isi otomatis.</p>
                        <h6 class="mt-3 mb-2 fw-700">Yang Anda Dapatkan:</h6>
                        <ul class="service-feature-list">
                            <li><i class="fas fa-check-circle"></i> Copy editing & proofreading</li>
                            <li><i class="fas fa-check-circle"></i> Editing tata bahasa & ejaan (EYD V)</li>
                            <li><i class="fas fa-check-circle"></i> Layout & typesetting buku</li>
                            <li><i class="fas fa-check-circle"></i> Daftar isi, indeks & referensi otomatis</li>
                            <li><i class="fas fa-check-circle"></i> Revisi hingga 3 kali</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. Desain Cover -->
            <div class="col-lg-6" id="desain">
                <div class="service-detail-card fade-in-up" style="animation-delay:0.1s" data-testid="layanan-desain">
                    <div class="service-detail-header" style="background: linear-gradient(135deg, #B8860B, #9A7009);">
                        <div class="service-detail-icon">
                            <i class="fas fa-palette fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="service-detail-title">Desain Cover Buku</h3>
                            <p class="service-detail-tagline mb-0">Estimasi: 5–7 hari kerja</p>
                        </div>
                    </div>
                    <div class="service-detail-body">
                        <p>Desain cover buku yang menarik, profesional, dan sesuai dengan identitas konten serta keilmuan penulis. Cover yang baik meningkatkan daya tarik buku di pasaran dan memberikan kesan pertama yang kuat.</p>
                        <p>Kami mendesain cover depan, punggung buku, dan cover belakang secara lengkap — siap cetak dalam berbagai ukuran standar buku.</p>
                        <h6 class="mt-3 mb-2 fw-700">Yang Anda Dapatkan:</h6>
                        <ul class="service-feature-list">
                            <li><i class="fas fa-check-circle"></i> 3 alternatif desain cover</li>
                            <li><i class="fas fa-check-circle"></i> Cover depan, punggung & belakang</li>
                            <li><i class="fas fa-check-circle"></i> Format siap cetak (PDF/AI)</li>
                            <li><i class="fas fa-check-circle"></i> Revisi desain hingga 3 kali</li>
                            <li><i class="fas fa-check-circle"></i> File sumber desain diserahkan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 5. ISBN & Distribusi -->
            <div class="col-lg-6" id="isbn">
                <div class="service-detail-card fade-in-up" data-testid="layanan-isbn">
                    <div class="service-detail-header" style="background: linear-gradient(135deg, var(--primary-dark), #0F2A45);">
                        <div class="service-detail-icon">
                            <i class="fas fa-barcode fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="service-detail-title">ISBN & Distribusi</h3>
                            <p class="service-detail-tagline mb-0">Estimasi: 7–14 hari kerja</p>
                        </div>
                    </div>
                    <div class="service-detail-body">
                        <p>Pengurusan ISBN (International Standard Book Number) resmi dari Perpustakaan Nasional Republik Indonesia. ISBN wajib untuk buku yang akan diperjualbelikan dan menjadi syarat kredit penerbitan buku dosen.</p>
                        <p>Kami juga membantu distribusi buku Anda ke marketplace online (Tokopedia, Shopee, dll), toko buku, dan perpustakaan institusi.</p>
                        <h6 class="mt-3 mb-2 fw-700">Yang Anda Dapatkan:</h6>
                        <ul class="service-feature-list">
                            <li><i class="fas fa-check-circle"></i> Pengurusan ISBN resmi Perpusnas RI</li>
                            <li><i class="fas fa-check-circle"></i> Barcode ISBN untuk cover buku</li>
                            <li><i class="fas fa-check-circle"></i> Distribusi ke marketplace online</li>
                            <li><i class="fas fa-check-circle"></i> Listing di katalog Perpusnas</li>
                            <li><i class="fas fa-check-circle"></i> Sertifikat penerbitan buku</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 6. Konsultasi & Pendampingan -->
            <div class="col-lg-6" id="konsultasi-pendampingan">
                <div class="service-detail-card fade-in-up" style="animation-delay:0.1s" data-testid="layanan-konsultasi">
                    <div class="service-detail-header" style="background: linear-gradient(135deg, var(--accent), var(--accent-dark));">
                        <div class="service-detail-icon">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="service-detail-title">Konsultasi & Pendampingan</h3>
                            <p class="service-detail-tagline mb-0">Fleksibel / via Zoom</p>
                        </div>
                    </div>
                    <div class="service-detail-body">
                        <p>Sesi konsultasi untuk penulis yang membutuhkan bimbingan dalam proses penulisan buku — mulai dari menentukan topik, struktur bab, hingga strategi penerbitan yang tepat.</p>
                        <p>Cocok untuk dosen yang baru pertama kali menulis buku atau ingin meningkatkan kualitas naskah sebelum diterbitkan.</p>
                        <h6 class="mt-3 mb-2 fw-700">Yang Anda Dapatkan:</h6>
                        <ul class="service-feature-list">
                            <li><i class="fas fa-check-circle"></i> Konsultasi strategi penerbitan</li>
                            <li><i class="fas fa-check-circle"></i> Bimbingan struktur & kerangka buku</li>
                            <li><i class="fas fa-check-circle"></i> Review naskah awal & feedback</li>
                            <li><i class="fas fa-check-circle"></i> Pendampingan proses penulisan</li>
                            <li><i class="fas fa-check-circle"></i> Sesi online via Zoom/Meet</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ADDITIONAL SERVICES -->
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up">
            <span class="section-badge">Layanan Tambahan</span>
            <h2 class="section-title">Bisa Dikombinasikan Sesuai Kebutuhan</h2>
            <p class="section-subtitle">Pilih layanan tambahan yang paling relevan untuk kebutuhan penerbitan Anda.</p>
        </div>
        <div class="row g-3 mt-3">
            <?php
            $additionalServices = [
                ['icon' => 'fas fa-print',         'title' => 'Cetak Buku (Print on Demand)', 'time' => '7–14 hari kerja',  'desc' => 'Cetak buku sesuai jumlah kebutuhan, mulai dari 1 eksemplar. Tersedia berbagai ukuran dan jenis kertas.'],
                ['icon' => 'fas fa-tablet-alt',    'title' => 'E-Book & Digital Publishing',   'time' => '5–7 hari kerja',  'desc' => 'Konversi buku ke format e-book (PDF, EPUB) untuk distribusi digital dan perpustakaan elektronik.'],
                ['icon' => 'fas fa-search',        'title' => 'Cek Plagiasi & Parafrase',     'time' => '3–5 hari kerja',  'desc' => 'Pengecekan plagiasi menggunakan tools profesional dan layanan parafrase untuk menurunkan tingkat kesamaan.'],
                ['icon' => 'fas fa-file-signature', 'title' => 'Penulisan Kata Pengantar & Review', 'time' => '3–5 hari kerja', 'desc' => 'Bantuan penulisan kata pengantar, prakata, dan review buku dari reviewer berpengalaman.'],
            ];
            foreach ($additionalServices as $i => $svc):
            ?>
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 shadow-sm fade-in-up" style="animation-delay:<?= $i * 0.08 ?>s; border: 1px solid var(--border);">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3" style="width:48px; height:48px; background: rgba(11,122,110,0.1); color: var(--secondary);">
                        <i class="<?= $svc['icon'] ?>"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-700"><?= $svc['title'] ?></h6>
                        <span class="badge bg-light text-muted mb-2" style="font-size:0.75rem;"><i class="fas fa-clock me-1"></i><?= $svc['time'] ?></span>
                        <p class="mb-0 small text-muted"><?= $svc['desc'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- HOW WE WORK -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="section-header text-center fade-in-up">
            <span class="section-badge">Alur Kerja Kami</span>
            <h2 class="section-title">Bagaimana Proses Penerbitan Berjalan</h2>
            <p class="section-subtitle">Proses transparan dan terstruktur untuk memastikan buku Anda terbit dengan kualitas terbaik</p>
        </div>
        <div class="process-timeline mt-5">
            <?php
            $steps = [
                ['num' => '01', 'icon' => 'fas fa-comments',        'title' => 'Konsultasi Awal',      'desc' => 'Sesi konsultasi gratis untuk memahami kebutuhan penerbitan Anda — jenis buku, target pembaca, timeline, dan anggaran yang tersedia.'],
                ['num' => '02', 'icon' => 'fas fa-clipboard-list',  'title' => 'Review Naskah',        'desc' => 'Tim editor kami melakukan review awal naskah Anda dan menyusun proposal kerja yang detail — mencakup scope, timeline, dan biaya.'],
                ['num' => '03', 'icon' => 'fas fa-pen-fancy',       'title' => 'Editing & Layout',     'desc' => 'Naskah diedit, dilayout, dan didesain cover-nya. Anda akan mendapatkan update progress dan kesempatan memberikan feedback di setiap tahap.'],
                ['num' => '04', 'icon' => 'fas fa-barcode',         'title' => 'ISBN & Finalisasi',    'desc' => 'Pengurusan ISBN resmi dan finalisasi seluruh materi buku. Anda melakukan final review sebelum buku masuk tahap cetak.'],
                ['num' => '05', 'icon' => 'fas fa-flag-checkered',  'title' => 'Cetak & Distribusi',   'desc' => 'Buku dicetak sesuai jumlah yang disepakati dan didistribusikan. Anda menerima buku fisik, e-book, dan sertifikat penerbitan.'],
            ];
            foreach ($steps as $i => $step):
            ?>
            <div class="process-step fade-in-up" style="animation-delay:<?= $i * 0.12 ?>s">
                <div class="process-step-number"><?= $step['num'] ?></div>
                <div class="process-step-content">
                    <div class="process-step-icon">
                        <i class="<?= $step['icon'] ?>"></i>
                    </div>
                    <h4 class="process-step-title"><?= $step['title'] ?></h4>
                    <p class="process-step-desc"><?= $step['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Pertanyaan Umum</span>
            <h2 class="section-title">Pertanyaan Seputar Layanan</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion faq-accordion fade-in-up" id="faqLayanan">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqL1">
                                Berapa lama proses penerbitan buku dari awal hingga terbit?
                            </button>
                        </h2>
                        <div id="faqL1" class="accordion-collapse collapse show" data-bs-parent="#faqLayanan">
                            <div class="accordion-body">
                                Untuk penerbitan lengkap (editing, layout, cover, ISBN, cetak), prosesnya memakan waktu <strong>14–30 hari kerja</strong> tergantung panjang naskah dan kompleksitas editing. Untuk konversi KTI ke buku, estimasinya <strong>21–45 hari kerja</strong> karena memerlukan proses parafrase dan restrukturisasi konten.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqL2">
                                Apakah buku yang diterbitkan mendapat ISBN resmi?
                            </button>
                        </h2>
                        <div id="faqL2" class="accordion-collapse collapse" data-bs-parent="#faqLayanan">
                            <div class="accordion-body">
                                Ya, setiap buku yang kami terbitkan mendapatkan <strong>ISBN resmi dari Perpustakaan Nasional RI</strong>. ISBN ini tercatat secara nasional dan diakui untuk keperluan kredit penerbitan buku dosen (BKD), kenaikan jabatan akademik, dan sertifikasi.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqL3">
                                Apakah bisa menerbitkan buku dengan jumlah cetak sedikit?
                            </button>
                        </h2>
                        <div id="faqL3" class="accordion-collapse collapse" data-bs-parent="#faqLayanan">
                            <div class="accordion-body">
                                Tentu! Kami mendukung sistem <strong>Print on Demand (PoD)</strong> — Anda bisa mencetak mulai dari <strong>1 eksemplar</strong>. Jumlah cetak bisa disesuaikan dengan kebutuhan, misalnya untuk mahasiswa dalam satu kelas atau tahun ajaran tertentu.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqL4">
                                Jenis buku apa saja yang bisa diterbitkan?
                            </button>
                        </h2>
                        <div id="faqL4" class="accordion-collapse collapse" data-bs-parent="#faqLayanan">
                            <div class="accordion-body">
                                Kami menerbitkan berbagai jenis buku akademik: <strong>Buku Ajar, Buku Referensi, Monograf, Book Chapter, Bunga Rampai, Modul Pembelajaran,</strong> dan buku ilmiah lainnya. Kami juga melayani konversi skripsi, tesis, disertasi, dan jurnal menjadi buku.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqL5">
                                Apakah penulis bisa konsultasi dulu sebelum memutuskan?
                            </button>
                        </h2>
                        <div id="faqL5" class="accordion-collapse collapse" data-bs-parent="#faqLayanan">
                            <div class="accordion-body">
                                Tentu! Kami menyediakan <strong>konsultasi gratis</strong> tanpa komitmen apapun. Anda bisa bertanya tentang proses penerbitan, jenis buku yang sesuai, estimasi biaya, atau hal lainnya. Setelah konsultasi, kami akan memberikan rekomendasi layanan yang paling sesuai.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="cta-shapes" aria-hidden="true">
        <div class="cta-shape cta-shape-1"></div>
        <div class="cta-shape cta-shape-2"></div>
    </div>
    <div class="container text-center">
        <div class="cta-content fade-in-up">
            <h2 class="cta-title">Siap Memulai? Konsultasi Gratis Menunggu Anda</h2>
            <p class="cta-subtitle">Ceritakan kebutuhan penerbitan Anda. Kami bantu analisis naskah dan rekomendasikan paket layanan terbaik — tanpa biaya konsultasi.</p>
            <div class="cta-actions">
                <a href="<?= $siteUrl ?>/konsultasi" class="btn btn-accent btn-lg">
                    <i class="fas fa-comments me-2"></i>Mulai Konsultasi
                </a>
                <a href="<?= $siteUrl ?>/harga" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-tags me-2"></i>Lihat Paket Harga
                </a>
            </div>
        </div>
    </div>
</section>
