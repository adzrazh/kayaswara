<?php
/**
 * Global site header. Included by index.php after page content is buffered.
 * Variables available: $currentPage, $pageTitle, $metaDesc
 */

$siteName    = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$siteTagline = html_entity_decode(getSetting('site_tagline', 'Jasa Penerbitan & Pencetakan Buku Akademik Profesional'), ENT_QUOTES, 'UTF-8');
$logoPath    = getSetting('logo_path', '');
$faviconPath = getSetting('favicon_path', '');
$primaryColor   = getSetting('primary_color', '#1A3C5E');
$secondaryColor = getSetting('secondary_color', '#1A3C5E');
$accentColor    = getSetting('accent_color', '#B8860B');
$metaDescription = $metaDesc ?? getSetting('meta_description', 'Jasa penerbitan buku akademik profesional untuk dosen, peneliti, dan akademisi Indonesia. Buku ajar, referensi, monograf ber-ISBN resmi.');

$siteUrl = defined('SITE_URL') ? SITE_URL : '';
$faviconHref = !empty($faviconPath) ? $siteUrl . '/assets/uploads/site/' . $faviconPath : $siteUrl . '/assets/images/favicon.ico';

$flashMessages = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? $siteName) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:type" content="website">
    <title><?= htmlspecialchars($pageTitle ?? $siteName) ?></title>
    <?php if (!empty($faviconPath)): ?>
    <link rel="icon" href="<?= htmlspecialchars($faviconHref) ?>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Font Awesome 6.5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?= $siteUrl ?>/assets/css/style.css">

    <!-- Dynamic CSS Variables from DB Settings -->
    <style>
        :root {
            --primary:   <?= htmlspecialchars($primaryColor) ?>;
            --secondary: <?= htmlspecialchars($secondaryColor) ?>;
            --accent:    <?= htmlspecialchars($accentColor) ?>;
        }
    </style>
</head>
<body>


<nav id="mainNav" class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: var(--primary); z-index: 1050;">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $siteUrl ?>/">
            <?php if (!empty($logoPath)): ?>
                <img src="<?= $siteUrl ?>/assets/uploads/site/<?= htmlspecialchars($logoPath) ?>"
                     alt="<?= htmlspecialchars($siteName) ?> logo"
                     class="navbar-logo-img">
            <?php else: ?>
                <!-- Inline SVG Logo (fallback when no logo uploaded) -->
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Kayaswara Logo">
                    <rect width="38" height="38" rx="9" fill="rgba(255,255,255,0.15)"/>
                    <path d="M10 28V12l9-4 9 4v16H22v-6h-6v6z" fill="rgba(255,255,255,0.95)"/>
                    <rect x="15" y="14" width="8" height="2" rx="1" fill="<?= htmlspecialchars($secondaryColor) ?>"/>
                    <rect x="15" y="18" width="8" height="2" rx="1" fill="<?= htmlspecialchars($secondaryColor) ?>" opacity="0.6"/>
                </svg>
            <?php endif; ?>
            <!-- Site name & tagline always shown beside logo/icon (desktop & mobile) -->
            <div class="d-flex flex-column gap-1">
                <span class="fw-700 fs-6 lh-1"><?= htmlspecialchars($siteName) ?></span>
                <span class="navbar-tagline"><?= htmlspecialchars($siteTagline) ?></span>
            </div>
        </a>

        <!-- Mobile Toggle → Offcanvas -->
        <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"
                aria-controls="mobileMenu" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Desktop Navigation (hidden on mobile) -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('home', $currentPage) ?>" href="<?= $siteUrl ?>/">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('layanan', $currentPage) ?>" href="<?= $siteUrl ?>/layanan">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('portofolio', $currentPage) ?>" href="<?= $siteUrl ?>/portofolio">Portofolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('blog', $currentPage) ?>" href="<?= $siteUrl ?>/blog">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('harga', $currentPage) ?>" href="<?= $siteUrl ?>/harga">Harga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('tentang', $currentPage) ?>" href="<?= $siteUrl ?>/tentang">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= activeClass('tracking', $currentPage) ?>" href="<?= $siteUrl ?>/tracking">
                        <i class="fas fa-search me-1"></i>Tracking
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-accent nav-cta" href="<?= $siteUrl ?>/konsultasi" data-testid="nav-cta-konsultasi">
                        <i class="fas fa-comments me-1"></i> Konsultasi Gratis
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Mobile Offcanvas Menu (slide from right) -->
<div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel"
     style="background-color: var(--primary); max-width: 300px; top: 64px; height: calc(100dvh - 64px);">
    <div class="offcanvas-header border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
        <h5 class="offcanvas-title text-white" id="mobileMenuLabel">
            <i class="fas fa-bars me-2"></i>Menu
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <nav class="flex-grow-1">
            <ul class="list-unstyled m-0">
                <?php
                $menuItems = [
                    ['page' => 'home', 'label' => 'Beranda', 'icon' => 'fa-home', 'url' => '/'],
                    ['page' => 'layanan', 'label' => 'Layanan', 'icon' => 'fa-cogs', 'url' => '/layanan'],
                    ['page' => 'portofolio', 'label' => 'Portofolio', 'icon' => 'fa-briefcase', 'url' => '/portofolio'],
                    ['page' => 'blog', 'label' => 'Blog', 'icon' => 'fa-newspaper', 'url' => '/blog'],
                    ['page' => 'harga', 'label' => 'Harga', 'icon' => 'fa-tags', 'url' => '/harga'],
                    ['page' => 'tentang', 'label' => 'Tentang', 'icon' => 'fa-info-circle', 'url' => '/tentang'],
                    ['page' => 'tracking', 'label' => 'Tracking Pesanan', 'icon' => 'fa-search-location', 'url' => '/tracking'],
                ];
                foreach ($menuItems as $mi):
                    $isActive = activeClass($mi['page'], $currentPage) === 'active';
                ?>
                <li>
                    <a href="<?= $siteUrl . $mi['url'] ?>"
                       class="d-flex align-items-center gap-3 px-4 py-3 text-white text-decoration-none"
                       style="font-size:0.95rem; font-weight:<?= $isActive ? '700' : '500' ?>; background:<?= $isActive ? 'rgba(255,255,255,0.1)' : 'transparent' ?>; border-left:3px solid <?= $isActive ? 'var(--accent)' : 'transparent' ?>;">
                        <i class="fas <?= $mi['icon'] ?>" style="width:20px; text-align:center; opacity:0.7;"></i>
                        <?= $mi['label'] ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
  <script>
  (function(){
    window.addEventListener('scroll', function(){
      var nav = document.getElementById('mainNav');
      if (!nav) return;
      if (window.scrollY > 10) {
        nav.classList.add('scrolled');
      } else {
        nav.classList.remove('scrolled');
      }
    }, {passive: true});
  })();
  </script>
  
        <div class="p-4 mt-auto" style="border-top:1px solid rgba(255,255,255,0.1);">
            <a href="<?= $siteUrl ?>/konsultasi"
               class="btn btn-accent d-block text-center fw-600"
               style="width:90%; margin:0 auto; padding:0.75rem 1rem; font-size:0.95rem; border-radius:10px;">
                <i class="fas fa-comments me-2"></i>Konsultasi Gratis
            </a>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (!empty($flashMessages)): ?>
<div class="flash-container position-fixed top-0 end-0 p-3" style="z-index:9999; margin-top:70px;">
    <?php foreach ($flashMessages as $msg): ?>
        <?php
        $alertMap = ['success' => 'alert-success', 'error' => 'alert-danger', 'warning' => 'alert-warning', 'info' => 'alert-info'];
        $alertClass = isset($alertMap[$msg['type']]) ? $alertMap[$msg['type']] : 'alert-info';
        ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show shadow" role="alert">
            <?= $msg['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Main Content Wrapper -->
<main id="mainContent">
