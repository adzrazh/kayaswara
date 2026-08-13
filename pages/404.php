<?php
/**
 * Halaman tidak ditemukan.
 */
http_response_code(404);
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Halaman tidak ditemukan — ' . $siteName;
$metaDesc  = 'Halaman yang Anda cari tidak tersedia di situs ' . $siteName . '.';
?>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div class="serif" style="font-size:clamp(4rem,3rem+6vw,7rem);font-weight:700;color:var(--paper-deep);line-height:1;">404</div>
                <h1 class="h2 mt-2">Halaman tidak ditemukan</h1>
                <p class="lead">
                    Alamat yang Anda tuju tidak tersedia, sudah dipindahkan, atau salah ketik.
                    Silakan lanjutkan dari salah satu tautan berikut.
                </p>

                <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                    <a href="<?= $siteUrl ?>/" class="btn btn-primary"><i class="fa-solid fa-house"></i>Beranda</a>
                    <a href="<?= $siteUrl ?>/publikasi" class="btn btn-ghost"><i class="fa-solid fa-book-open"></i>Katalog Publikasi</a>
                    <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-ghost"><i class="fa-regular fa-paper-plane"></i>Kirim Naskah</a>
                </div>

                <div class="row g-3 mt-5 text-start">
                    <div class="col-md-4">
                        <a class="card-plain d-block h-100" href="<?= $siteUrl ?>/layanan">
                            <div class="service-icon"><i class="fa-solid fa-pen-nib"></i></div>
                            <h2 class="h6 mb-1">Layanan</h2>
                            <p class="mb-0 text-muted" style="font-size:.88rem;">Lingkup pekerjaan redaksi dan produksi.</p>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="card-plain d-block h-100" href="<?= $siteUrl ?>/proses">
                            <div class="service-icon"><i class="fa-solid fa-diagram-project"></i></div>
                            <h2 class="h6 mb-1">Alur Penerbitan</h2>
                            <p class="mb-0 text-muted" style="font-size:.88rem;">Tahapan naskah sampai menjadi buku.</p>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="card-plain d-block h-100" href="<?= $siteUrl ?>/lacak">
                            <div class="service-icon"><i class="fa-solid fa-location-crosshairs"></i></div>
                            <h2 class="h6 mb-1">Lacak Naskah</h2>
                            <p class="mb-0 text-muted" style="font-size:.88rem;">Pantau progres dengan kode pelacakan.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
