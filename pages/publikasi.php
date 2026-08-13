<?php
/**
 * Publikasi (Catalog) Page – Kayaswara Publishing
 */
$pageTitle = 'Katalog Publikasi – ' . getSetting('site_name', 'Kayaswara');
$metaDesc  = 'Daftar buku dan karya ilmiah yang telah diterbitkan oleh Kayaswara Publishing. Temukan buku referensi, monograf, dan bahan ajar berkualitas.';
$siteUrl   = defined('SITE_URL') ? SITE_URL : '';
?>

<main class="page-publikasi bg-light pb-5">
    <div class="container pt-5">
        <div class="text-center mb-5 fade-in-up">
            <h1 class="fw-800 text-dark">Katalog Publikasi</h1>
            <p class="text-muted">Buku-buku berkualitas yang diterbitkan oleh Kayaswara Publishing.</p>
        </div>

        <div class="row g-4">
            <!-- Dummy Book 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 350px;">
                        <i class="fas fa-book fa-4x text-muted"></i>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">Buku Referensi</span>
                        <h5 class="card-title fw-bold">Pengantar Pendidikan Modern</h5>
                        <p class="card-text text-muted small mb-2">Penulis: Dr. Ahmad Fikri, M.Pd.</p>
                        <p class="card-text small">Membahas tentang konsep dasar pendidikan modern di era digital dan tantangannya di Indonesia.</p>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="alert('Segera Hadir')">Detail Buku</button>
                    </div>
                </div>
            </div>

            <!-- Dummy Book 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 350px;">
                        <i class="fas fa-book fa-4x text-muted"></i>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2">Buku Ajar</span>
                        <h5 class="card-title fw-bold">Dasar-Dasar Pemrograman Web</h5>
                        <p class="card-text text-muted small mb-2">Penulis: Prof. Dr. Andi Suryadi, S.Kom., M.Kom.</p>
                        <p class="card-text small">Buku pegangan untuk mahasiswa semester satu dalam memahami HTML, CSS, dan Javascript.</p>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="alert('Segera Hadir')">Detail Buku</button>
                    </div>
                </div>
            </div>

            <!-- Dummy Book 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 350px;">
                        <i class="fas fa-book fa-4x text-muted"></i>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-accent mb-2">Monograf</span>
                        <h5 class="card-title fw-bold">Analisis Struktur Tanah Vulkanik</h5>
                        <p class="card-text text-muted small mb-2">Penulis: Dr. Siti Nurhaliza, S.T., M.T.</p>
                        <p class="card-text small">Hasil penelitian komprehensif mengenai struktur dan daya dukung tanah di area pegunungan.</p>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="alert('Segera Hadir')">Detail Buku</button>
                    </div>
                </div>
            </div>

            <!-- Dummy Book 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 350px;">
                        <i class="fas fa-book fa-4x text-muted"></i>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">Buku Referensi</span>
                        <h5 class="card-title fw-bold">Etika Profesi Kedokteran</h5>
                        <p class="card-text text-muted small mb-2">Penulis: dr. Budi Santoso, Sp.PD., K-GEH.</p>
                        <p class="card-text small">Tinjauan mendalam mengenai kode etik dan dilema medis dalam praktik kedokteran modern.</p>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="alert('Segera Hadir')">Detail Buku</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="text-muted">Katalog lengkap akan segera diperbarui dengan buku-buku cetakan terbaru kami.</p>
        </div>
    </div>
</main>
