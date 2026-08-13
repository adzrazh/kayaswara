<?php
/**
 * Global site header — Kayaswara
 * Included by index.php after the page body has been buffered.
 * Available: $currentPage, $pageTitle, $metaDesc
 */

$siteUrl     = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName    = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$siteTagline = html_entity_decode(getSetting('site_tagline', 'Penerbit Buku Akademik'), ENT_QUOTES, 'UTF-8');
$logoPath    = getSetting('logo_path', '');
$faviconPath = getSetting('favicon_path', '');

$primaryColor   = getSetting('primary_color', '#1A3C5E');
$secondaryColor = getSetting('secondary_color', '#2E6188');
$accentColor    = getSetting('accent_color', '#B8860B');

$metaDescription = $metaDesc ?? getSetting(
    'meta_description',
    'Kayaswara adalah penerbit buku akademik untuk dosen, peneliti, dan mahasiswa: buku ajar, buku referensi, monograf, dan bunga rampai.'
);

$emailContact = getSetting('email_contact', '');
$waNumber     = getSetting('whatsapp_number', '');
$waDigits     = $waNumber ? preg_replace('/\D/', '', $waNumber) : '';

$faviconHref  = !empty($faviconPath) ? $siteUrl . '/assets/uploads/site/' . $faviconPath : '';
$flashMessages = getFlash();

// Primary navigation — single source of truth for desktop + mobile
$navItems = [
    ['key' => 'home',      'label' => 'Beranda',   'url' => '/',           'icon' => 'fa-house'],
    ['key' => 'publikasi', 'label' => 'Publikasi', 'url' => '/publikasi',  'icon' => 'fa-book-open'],
    ['key' => 'layanan',   'label' => 'Layanan',   'url' => '/layanan',    'icon' => 'fa-pen-nib'],
    ['key' => 'proses',    'label' => 'Proses',    'url' => '/proses',     'icon' => 'fa-diagram-project'],
    ['key' => 'harga',     'label' => 'Biaya',     'url' => '/biaya',      'icon' => 'fa-receipt'],
    ['key' => 'blog',      'label' => 'Wawasan',   'url' => '/wawasan',    'icon' => 'fa-newspaper'],
    ['key' => 'tentang',   'label' => 'Tentang',   'url' => '/tentang',    'icon' => 'fa-landmark'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $siteName) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="<?= htmlspecialchars($primaryColor) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? $siteName) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <?php if (!empty($faviconHref)): ?>
    <link rel="icon" href="<?= htmlspecialchars($faviconHref) ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $siteUrl ?>/assets/css/style.css">

    <style>
        :root {
            --primary:   <?= htmlspecialchars($primaryColor) ?>;
            --secondary: <?= htmlspecialchars($secondaryColor) ?>;
            --accent:    <?= htmlspecialchars($accentColor) ?>;
        }
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .main-nav .nav-link { font-size: .87rem; padding-left: .6rem; padding-right: .6rem; }
            .main-nav .nav-link::after { left: .6rem; right: .6rem; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<a class="skip-link" href="#main">Lompat ke konten utama</a>

<!-- Utility bar -->
<div class="site-topbar d-none d-md-block">
    <div class="container">
        <div class="topbar-inner">
            <div class="topbar-links">
                <?php if (!empty($emailContact)): ?>
                <a href="mailto:<?= htmlspecialchars($emailContact) ?>">
                    <i class="fa-regular fa-envelope"></i><?= htmlspecialchars($emailContact) ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($waDigits)): ?>
                <a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i><?= htmlspecialchars($waNumber) ?>
                </a>
                <?php endif; ?>
            </div>
            <div class="topbar-links">
                <span><i class="fa-regular fa-clock"></i>Senin–Jumat, 08.00–17.00 WIB</span>
                <a href="<?= $siteUrl ?>/lacak"><i class="fa-solid fa-location-crosshairs"></i>Lacak Naskah</a>
            </div>
        </div>
    </div>
</div>

<!-- Masthead -->
<header class="site-header">
    <nav class="navbar navbar-expand-lg py-0" aria-label="Navigasi utama">
        <div class="container">
            <a class="brand" href="<?= $siteUrl ?>/">
                <span class="brand-mark">
                    <?php if (!empty($logoPath)): ?>
                        <img src="<?= $siteUrl ?>/assets/uploads/site/<?= htmlspecialchars($logoPath) ?>" alt="Logo <?= htmlspecialchars($siteName) ?>">
                    <?php else: ?>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M3 5.5C5.4 4.2 8.2 4.2 10.6 5.5v13c-2.4-1.3-5.2-1.3-7.6 0v-13Z" fill="currentColor" opacity=".9"/>
                            <path d="M21 5.5c-2.4-1.3-5.2-1.3-7.6 0v13c2.4-1.3 5.2-1.3 7.6 0v-13Z" fill="currentColor" opacity=".55"/>
                            <path d="M12 4v16" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
                        </svg>
                    <?php endif; ?>
                </span>
                <span>
                    <span class="brand-name"><?= htmlspecialchars($siteName) ?></span>
                    <span class="brand-sub"><?= htmlspecialchars($siteTagline) ?></span>
                </span>
            </a>

            <div class="d-none d-lg-flex align-items-center ms-auto gap-3">
                <ul class="main-nav navbar-nav">
                    <?php foreach ($navItems as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= activeClass($item['key'], $currentPage) ?>" href="<?= $siteUrl . $item['url'] ?>">
                            <?= $item['label'] ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="nav-actions">
                    <a class="btn btn-primary btn-sm" href="<?= $siteUrl ?>/kirim-naskah">
                        <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
                    </a>
                </div>
            </div>

            <button class="nav-toggle d-lg-none ms-auto" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileNav"
                    aria-controls="mobileNav" aria-label="Buka menu navigasi">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>
</header>

<!-- Mobile drawer -->
<div class="offcanvas offcanvas-end mobile-nav" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileNavLabel"><?= htmlspecialchars($siteName) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <ul class="mobile-nav-list">
            <?php foreach ($navItems as $item): ?>
            <li>
                <a href="<?= $siteUrl . $item['url'] ?>" class="<?= activeClass($item['key'], $currentPage) ?>">
                    <i class="fa-solid <?= $item['icon'] ?>"></i><?= $item['label'] ?>
                </a>
            </li>
            <?php endforeach; ?>
            <li>
                <a href="<?= $siteUrl ?>/lacak" class="<?= activeClass('tracking', $currentPage) ?>">
                    <i class="fa-solid fa-location-crosshairs"></i>Lacak Naskah
                </a>
            </li>
        </ul>
        <div class="mobile-nav-foot">
            <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary w-100">
                <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
            </a>
            <?php if (!empty($emailContact)): ?>
            <p class="mb-0 mt-3 small text-muted">
                <i class="fa-regular fa-envelope me-1"></i><?= htmlspecialchars($emailContact) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($flashMessages)): ?>
<div class="flash-stack">
    <?php foreach ($flashMessages as $msg):
        $map = ['success' => 'alert-success', 'error' => 'alert-danger', 'warning' => 'alert-warning', 'info' => 'alert-info'];
        $cls = $map[$msg['type']] ?? 'alert-info';
    ?>
    <div class="alert <?= $cls ?> alert-dismissible fade show shadow-sm" role="alert">
        <?= htmlspecialchars($msg['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main id="main" class="flex-grow-1">
