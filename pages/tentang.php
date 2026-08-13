<?php
/**
 * Tentang (About) Page – Kayaswara Publishing
 */
$pageTitle = 'Tentang Kami – ' . getSetting('site_name', 'Kayaswara');
$siteUrl   = defined('SITE_URL') ? SITE_URL : '';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <div class="page-hero-content text-center fade-in-up">
            <h1 class="page-hero-title">Tentang Kami</h1>
            <p class="page-hero-subtitle">Mengenal lebih dekat Kayaswara — mitra penerbitan buku akademik Anda</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?= $siteUrl ?>/">Beranda</a></li>
                    <li class="breadcrumb-item active">Tentang Kami</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- ABOUT INTRO -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 fade-in-up">
                <span class="section-badge">Siapa Kami</span>
                <h2 class="section-title">Mitra Penerbitan Buku Akademik Indonesia</h2>
                <p class="text-muted mb-3">
                    CV. Kayaswara adalah penyedia jasa penerbitan buku akademik profesional yang berfokus pada buku ajar, buku referensi, monograf, dan karya ilmiah lainnya untuk komunitas akademik Indonesia.
                </p>
                <p class="text-muted mb-3">
                    Kami hadir sebagai respons terhadap kebutuhan dosen, peneliti, dan akademisi Indonesia untuk menerbitkan buku berkualitas tinggi secara mudah, cepat, dan terjangkau — dengan standar penerbitan profesional dan ISBN resmi.
                </p>
                <p class="text-muted">
                    Dalam perjalanan kami, Kayaswara telah membantu puluhan penulis dari berbagai bidang keilmuan — mulai dari pendidikan, kesehatan, teknik, hukum, ekonomi, hingga ilmu sosial dan humaniora — menerbitkan karya mereka.
                </p>
            </div>
            <div class="col-lg-6 fade-in-right">
                <div class="about-visual">
                    <div class="about-visual-grid">
                        <div class="about-stat-block" data-testid="about-stat-naskah">
                            <div class="about-stat-num">75+</div>
                            <div class="about-stat-lab">Naskah Terbit</div>
                        </div>
                        <div class="about-stat-block accent" data-testid="about-stat-penulis">
                            <div class="about-stat-num">40+</div>
                            <div class="about-stat-lab">Penulis Dilayani</div>
                        </div>
                        <div class="about-stat-block secondary" data-testid="about-stat-tahun">
                            <div class="about-stat-num">3+</div>
                            <div class="about-stat-lab">Tahun Pengalaman</div>
                        </div>
                        <div class="about-stat-block" data-testid="about-stat-kepuasan">
                            <div class="about-stat-num">98%</div>
                            <div class="about-stat-lab">Kepuasan Penulis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- VISION & MISSION -->
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Arah & Tujuan</span>
            <h2 class="section-title">Visi & Misi Kami</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="vm-card vm-card-vision fade-in-up">
                    <div class="vm-card-icon"><i class="fas fa-eye"></i></div>
                    <h3>Visi</h3>
                    <p>Menjadi mitra penerbitan buku akademik terdepan di Indonesia yang mendorong akselerasi publikasi karya ilmiah, meningkatkan kualitas buku pendidikan nasional, dan berkontribusi pada kemajuan ekosistem pengetahuan akademik bangsa.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vm-card vm-card-mission fade-in-up" style="animation-delay:0.1s">
                    <div class="vm-card-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Misi</h3>
                    <ul class="vm-mission-list">
                        <li>Menyediakan layanan penerbitan buku akademik berkualitas tinggi, terjangkau, dan mudah diakses oleh seluruh akademisi Indonesia.</li>
                        <li>Mendampingi penulis dari proses penulisan hingga distribusi buku dengan standar profesional.</li>
                        <li>Mendukung peningkatan karir akademik dosen melalui penerbitan buku ber-ISBN yang diakui secara nasional.</li>
                        <li>Terus berinovasi menghadirkan solusi penerbitan modern untuk mendukung ekosistem publikasi ilmiah yang berkelanjutan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Keunggulan Kami</span>
            <h2 class="section-title">Mengapa Kami Berbeda</h2>
            <p class="section-subtitle">Lima alasan utama mengapa puluhan penulis mempercayakan penerbitan buku mereka kepada Kayaswara</p>
        </div>
        <div class="row g-4">
            <?php
            $whyUs = [
                ['icon' => 'fas fa-book-open',        'title' => 'Fokus Buku Akademik',               'desc' => 'Kami tidak menerbitkan semua jenis buku. Fokus eksklusif kami pada buku akademik menjadikan kami ahli yang memahami kebutuhan spesifik buku ajar, referensi, dan monograf secara mendalam.'],
                ['icon' => 'fas fa-users-cog',         'title' => 'Tim Berlatar Belakang Akademik',    'desc' => 'Tim kami memiliki pengalaman langsung di dunia akademik — sebagai peneliti dan penulis. Pemahaman ini membantu kami memberikan solusi yang benar-benar relevan untuk kebutuhan penerbitan Anda.'],
                ['icon' => 'fas fa-graduation-cap',    'title' => 'Memahami Regulasi Akademik',        'desc' => 'Kami memahami kebutuhan kredit penerbitan buku dosen (BKD), persyaratan ISBN, dan standar penerbitan buku yang diakui untuk kenaikan jabatan akademik.'],
                ['icon' => 'fas fa-handshake',         'title' => 'Pendampingan Jangka Panjang',       'desc' => 'Hubungan kami dengan penulis tidak berhenti setelah buku terbit. Kami memberikan pendampingan berkelanjutan termasuk konsultasi, distribusi, dan cetak ulang.'],
                ['icon' => 'fas fa-shield-alt',        'title' => 'Transparansi & Kepercayaan',        'desc' => 'Anda selalu mendapatkan informasi yang jelas tentang proses, biaya, dan timeline. Penulis memiliki kontrol penuh atas naskahnya di setiap tahap penerbitan.'],
            ];
            foreach ($whyUs as $i => $item):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="why-card fade-in-up" style="animation-delay:<?= $i * 0.08 ?>s">
                    <div class="why-card-icon"><i class="<?= $item['icon'] ?>"></i></div>
                    <h4 class="why-card-title"><?= $item['title'] ?></h4>
                    <p class="why-card-desc"><?= $item['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Tim Kami</span>
            <h2 class="section-title">Kenali Tim di Balik Kayaswara</h2>
            <p class="section-subtitle">Didukung oleh profesional berpengalaman yang berdedikasi untuk kesuksesan buku Anda</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['name' => 'Tim Manajemen', 'role' => 'Founder & Pengelola', 'desc' => 'Mengelola seluruh operasional penerbitan dan memastikan setiap buku diterbitkan dengan standar kualitas tertinggi.', 'icon' => 'fas fa-user-tie'],
                ['name' => 'Tim Editor',   'role' => 'Editor & Proofreader', 'desc' => 'Tim editor berpengalaman yang memastikan setiap naskah bebas kesalahan dan sesuai standar penulisan akademik.', 'icon' => 'fas fa-user-edit'],
                ['name' => 'Tim Desain',   'role' => 'Layout & Cover Designer', 'desc' => 'Bertanggung jawab atas desain cover, layout buku, dan produksi visual yang menarik dan profesional.', 'icon' => 'fas fa-user-cog'],
                ['name' => 'Tim Konsultan', 'role' => 'Konsultan Penerbitan', 'desc' => 'Mendampingi penulis dari awal hingga akhir — mulai dari konsultasi, review naskah, hingga distribusi buku.', 'icon' => 'fas fa-user-friends'],
            ];
            foreach ($team as $i => $member):
            ?>
            <div class="col-sm-6 col-lg-3">
                <div class="team-card fade-in-up" style="animation-delay:<?= $i * 0.1 ?>s">
                    <div class="team-avatar"><i class="<?= $member['icon'] ?>"></i></div>
                    <div class="team-info">
                        <h5 class="team-name"><?= $member['name'] ?></h5>
                        <span class="team-role"><?= $member['role'] ?></span>
                        <p class="team-desc"><?= $member['desc'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonial -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Apa Kata Penulis Kami</span>
            <h2 class="section-title">Testimoni</h2>
        </div>
        <div class="row g-4">
            <?php
            $testimonials = [
                ['text' => '"Proses penerbitan buku ajar saya di Kayaswara sangat cepat dan profesional. Tim editor sangat teliti, desain cover-nya memuaskan, dan ISBN keluar tepat waktu. Sangat direkomendasikan untuk sesama dosen yang ingin menerbitkan buku."', 'name' => 'Dr. Hendra Kusuma, M.Si.', 'role' => 'Dosen – Universitas Negeri Semarang'],
                ['text' => '"Saya mengonversi disertasi saya menjadi buku referensi melalui Kayaswara. Prosesnya didampingi dari awal — mulai dari restrukturisasi konten, editing, hingga terbit. Hasilnya sangat profesional dan memuaskan."', 'name' => 'Prof. Dr. Siti Aminah, M.Pd.', 'role' => 'Guru Besar – Universitas Islam Indonesia'],
                ['text' => '"Kayaswara membantu saya menerbitkan buku pertama saya. Sebagai penulis pemula, saya sangat terbantu dengan konsultasi dan pendampingan yang diberikan. Sekarang buku saya sudah tersedia di toko buku online!"', 'name' => 'Dr. Agus Setiawan, M.T.', 'role' => 'Dosen – Institut Teknologi Sepuluh Nopember'],
            ];
            foreach ($testimonials as $i => $t):
            ?>
            <div class="col-md-4">
                <div class="testimonial-card fade-in-up" style="animation-delay:<?= $i * 0.1 ?>s">
                    <div class="testimonial-quote-icon"><i class="fas fa-quote-left"></i></div>
                    <p class="testimonial-text"><?= $t['text'] ?></p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><i class="fas fa-user-circle"></i></div>
                        <div>
                            <div class="testimonial-name"><?= $t['name'] ?></div>
                            <div class="testimonial-role"><?= $t['role'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
            <h2 class="cta-title">Bergabunglah bersama 40+ Penulis yang Telah Menerbitkan Buku Bersama Kami</h2>
            <p class="cta-subtitle">Mulailah perjalanan penerbitan buku Anda bersama tim ahli kami.</p>
            <div class="cta-actions">
                <a href="<?= $siteUrl ?>/konsultasi" class="btn btn-accent btn-lg">
                    <i class="fas fa-comments me-2"></i>Konsultasi Gratis
                </a>
                <a href="<?= $siteUrl ?>/portofolio" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-book me-2"></i>Lihat Portofolio
                </a>
            </div>
        </div>
    </div>
</section>
