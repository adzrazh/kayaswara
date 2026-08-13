<?php
/**
 * Harga (Pricing) Page – Kayaswara Publishing
 */
$pageTitle = 'Paket & Harga – ' . getSetting('site_name', 'Kayaswara');
$metaDesc  = 'Paket harga penerbitan buku akademik yang transparan dan kompetitif. Penerbitan buku ajar, referensi, monograf, konversi KTI.';
$siteUrl   = defined('SITE_URL') ? SITE_URL : '';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <div class="page-hero-content text-center fade-in-up">
            <h1 class="page-hero-title">Paket & Harga Penerbitan</h1>
            <p class="page-hero-subtitle">Transparansi harga dengan paket yang jelas. Pilih paket penerbitan yang sesuai dengan kebutuhan dan anggaran Anda.</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?= $siteUrl ?>/">Beranda</a></li>
                    <li class="breadcrumb-item active">Harga</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- PRICING CARDS -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row g-4 align-items-stretch justify-content-center">

            <!-- BASIC -->
            <div class="col-md-6 col-lg-4">
                <div class="pricing-card fade-in-up" data-testid="pricing-basic">
                    <div class="pricing-card-header">
                        <div class="pricing-tier-icon"><i class="fas fa-seedling"></i></div>
                        <h3 class="pricing-tier-name">Basic</h3>
                        <p class="pricing-tier-desc">Cocok untuk penulis yang sudah memiliki naskah siap terbit dan butuh layanan cetak.</p>
                        <div class="pricing-price">
                            <span class="pricing-amount" style="font-size:1.5rem;">Hubungi Kami</span>
                        </div>
                        <p class="pricing-period text-muted">Harga disesuaikan kebutuhan</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-feature-list">
                            <li><i class="fas fa-check-circle text-success"></i> Review naskah awal</li>
                            <li><i class="fas fa-check-circle text-success"></i> Layout & typesetting standar</li>
                            <li><i class="fas fa-check-circle text-success"></i> Desain cover standar (1 opsi)</li>
                            <li><i class="fas fa-check-circle text-success"></i> Distribusi Toko Online</li>
                            <li><i class="fas fa-check-circle text-success"></i> Cetak 10 eksemplar</li>
                            <li><i class="fas fa-check-circle text-success"></i> Sertifikat penerbitan</li>
                            <li><i class="fas fa-check-circle text-success"></i> Support 14 hari</li>
                            <li class="text-muted"><i class="fas fa-times-circle"></i> Editing substansi</li>
                            <li class="text-muted"><i class="fas fa-times-circle"></i> E-book format</li>
                            <li class="text-muted"><i class="fas fa-times-circle"></i> Distribusi online</li>
                        </ul>
                        <a href="<?= $siteUrl ?>/konsultasi?paket=basic" class="btn btn-outline-primary w-100 mt-3">
                            <i class="fas fa-comments me-1"></i> Konsultasi Paket Basic
                        </a>
                    </div>
                </div>
            </div>

            <!-- PROFESSIONAL (Popular) -->
            <div class="col-md-6 col-lg-4">
                <div class="pricing-card pricing-card-popular fade-in-up" style="animation-delay:0.1s" data-testid="pricing-professional">
                    <div class="pricing-popular-badge"><i class="fas fa-star me-1"></i> Paling Populer</div>
                    <div class="pricing-card-header">
                        <div class="pricing-tier-icon"><i class="fas fa-rocket"></i></div>
                        <h3 class="pricing-tier-name">Professional</h3>
                        <p class="pricing-tier-desc">Paket terlengkap untuk penerbitan buku akademik berkualitas tinggi dengan editing profesional.</p>
                        <div class="pricing-price">
                            <span class="pricing-amount" style="font-size:1.5rem;">Hubungi Kami</span>
                        </div>
                        <p class="pricing-period" style="opacity:0.8;">Harga disesuaikan kebutuhan</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-feature-list">
                            <li><i class="fas fa-check-circle"></i> Semua fitur paket Basic</li>
                            <li><i class="fas fa-check-circle"></i> Editing substansi & bahasa lengkap</li>
                            <li><i class="fas fa-check-circle"></i> Desain cover custom (3 opsi)</li>
                            <li><i class="fas fa-check-circle"></i> E-book (PDF & EPUB)</li>
                            <li><i class="fas fa-check-circle"></i> Cetak 25 eksemplar</li>
                            <li><i class="fas fa-check-circle"></i> Distribusi toko buku online</li>
                            <li><i class="fas fa-check-circle"></i> Listing perpustakaan digital</li>
                            <li><i class="fas fa-check-circle"></i> Support 30 hari</li>
                            <li class="text-muted" style="opacity:0.7;"><i class="fas fa-times-circle"></i> Konversi KTI ke buku</li>
                            <li class="text-muted" style="opacity:0.7;"><i class="fas fa-times-circle"></i> Konsultasi penulisan</li>
                        </ul>
                        <a href="<?= $siteUrl ?>/konsultasi?paket=professional" class="btn btn-accent w-100 mt-3">
                            <i class="fas fa-rocket me-1"></i> Konsultasi Paket Professional
                        </a>
                    </div>
                </div>
            </div>

            <!-- PREMIUM -->
            <div class="col-md-6 col-lg-4">
                <div class="pricing-card fade-in-up" style="animation-delay:0.2s" data-testid="pricing-premium">
                    <div class="pricing-card-header">
                        <div class="pricing-tier-icon"><i class="fas fa-crown"></i></div>
                        <h3 class="pricing-tier-name">Premium</h3>
                        <p class="pricing-tier-desc">Full service termasuk konversi KTI, pendampingan penulisan, dan distribusi menyeluruh.</p>
                        <div class="pricing-price">
                            <span class="pricing-amount" style="font-size:1.5rem;">Hubungi Kami</span>
                        </div>
                        <p class="pricing-period text-muted">Harga disesuaikan kebutuhan</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-feature-list">
                            <li><i class="fas fa-check-circle text-success"></i> Semua fitur paket Professional</li>
                            <li><i class="fas fa-check-circle text-success"></i> Konversi KTI ke buku</li>
                            <li><i class="fas fa-check-circle text-success"></i> Konsultasi & pendampingan penulisan</li>
                            <li><i class="fas fa-check-circle text-success"></i> Cek plagiasi profesional</li>
                            <li><i class="fas fa-check-circle text-success"></i> Desain premium + ilustrasi</li>
                            <li><i class="fas fa-check-circle text-success"></i> Cetak 50 eksemplar</li>
                            <li><i class="fas fa-check-circle text-success"></i> Distribusi menyeluruh (online + offline)</li>
                            <li><i class="fas fa-check-circle text-success"></i> Priority support 60 hari</li>
                        </ul>
                        <a href="<?= $siteUrl ?>/konsultasi?paket=premium" class="btn btn-outline-primary w-100 mt-3">
                            <i class="fas fa-crown me-1"></i> Konsultasi Paket Premium
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="pricing-note text-center mt-4 fade-in-up">
            <p class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Harga final disesuaikan berdasarkan panjang naskah, kompleksitas editing, jumlah cetak, dan layanan tambahan yang dipilih.
                <strong>Konsultasikan kebutuhan Anda</strong> untuk mendapatkan penawaran yang tepat.
            </p>
        </div>
    </div>
</section>

<!-- COMPARISON TABLE -->
<section class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Perbandingan Paket</span>
            <h2 class="section-title">Fitur Lengkap Setiap Paket</h2>
        </div>
        <div class="table-responsive fade-in-up">
            <table class="pricing-compare-table" data-testid="pricing-comparison-table">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th class="text-center">Basic</th>
                        <th class="text-center" style="background:var(--primary); color:#fff;">Professional</th>
                        <th class="text-center">Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="compare-section-header">Editing & Konten</td></tr>
                    <tr><td>Review naskah awal</td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Editing tata bahasa & ejaan</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Editing substansi & struktur</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Konversi KTI ke buku</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Cek plagiasi profesional</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>

                    <tr><td colspan="4" class="compare-section-header">Desain & Produksi</td></tr>
                    <tr><td>Layout & typesetting</td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Desain cover buku</td><td class="text-center">1 opsi</td><td class="text-center">3 opsi</td><td class="text-center">Premium</td></tr>
                    <tr><td>E-book (PDF & EPUB)</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>

                    <tr><td colspan="4" class="compare-section-header">Distribusi & Pemasaran</td></tr>
                    <tr><td>Distribusi Toko Online</td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Jumlah cetak termasuk</td><td class="text-center">10 eks</td><td class="text-center">25 eks</td><td class="text-center">50 eks</td></tr>
                    <tr><td>Distribusi toko buku online</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Distribusi offline</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>

                    <tr><td colspan="4" class="compare-section-header">Pendampingan & Support</td></tr>
                    <tr><td>Konsultasi penulisan</td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-times text-muted"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Sertifikat penerbitan</td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td><td class="text-center"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Periode support</td><td class="text-center">14 hari</td><td class="text-center">30 hari</td><td class="text-center">60 hari</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="section-header text-center fade-in-up mb-5">
            <span class="section-badge">Pertanyaan Umum</span>
            <h2 class="section-title">Pertanyaan Seputar Harga & Pembayaran</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion faq-accordion fade-in-up" id="faqHarga">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqH1">Mengapa tidak ada harga pasti di website?</button>
                        </h2>
                        <div id="faqH1" class="accordion-collapse collapse show" data-bs-parent="#faqHarga">
                            <div class="accordion-body">
                                Setiap naskah memiliki kebutuhan berbeda — panjang naskah, tingkat editing, jumlah cetak, dan kompleksitas desain tidak sama. Dengan konsultasi, kami memberikan <strong>penawaran yang tepat dan transparan</strong> sesuai kebutuhan spesifik Anda.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqH2">Bagaimana metode pembayarannya?</button>
                        </h2>
                        <div id="faqH2" class="accordion-collapse collapse" data-bs-parent="#faqHarga">
                            <div class="accordion-body">
                                Kami menerima pembayaran via <strong>transfer bank</strong> dan <strong>QRIS</strong>. Pembayaran dilakukan bertahap: DP 50% sebelum pengerjaan dan pelunasan setelah buku selesai. Detail disampaikan dalam proposal.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqH3">Apakah bisa menggabungkan beberapa layanan?</button>
                        </h2>
                        <div id="faqH3" class="accordion-collapse collapse" data-bs-parent="#faqHarga">
                            <div class="accordion-body">
                                Tentu! Semua layanan bisa <strong>dikombinasikan</strong> sesuai kebutuhan. Kami juga memberikan penawaran khusus untuk paket bundling. Konsultasikan kebutuhan Anda agar kami bisa menyusun paket yang paling optimal.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqH4">Berapa lama waktu untuk mendapatkan penawaran?</button>
                        </h2>
                        <div id="faqH4" class="accordion-collapse collapse" data-bs-parent="#faqHarga">
                            <div class="accordion-body">
                                Setelah konsultasi, kami mengirimkan <strong>proposal tertulis dalam 1–3 hari kerja</strong> lengkap dengan rincian layanan, timeline, dan estimasi biaya. Anda tidak perlu berkomitmen saat konsultasi.
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
            <h2 class="cta-title">Tidak Yakin Paket Mana yang Tepat?</h2>
            <p class="cta-subtitle">Konsultasi gratis. Kami bantu analisis naskah dan rekomendasikan paket yang paling sesuai dengan kebutuhan dan budget Anda.</p>
            <div class="cta-actions">
                <a href="<?= $siteUrl ?>/konsultasi" class="btn btn-accent btn-lg">
                    <i class="fas fa-comments me-2"></i>Konsultasi Gratis Sekarang
                </a>
            </div>
        </div>
    </div>
</section>
