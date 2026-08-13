<?php
/**
 * Kayaswara — Front controller.
 * Maps a clean URL (or ?page=) to a file in /pages and renders it inside the site shell.
 */

// Not installed yet → installer
if (!file_exists(__DIR__ . '/config.php')) {
    header('Location: install.php');
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Harden session cookie before starting
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Security headers
header_remove('X-Powered-By');
ini_set('display_errors', 0);
error_reporting(0);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
header("Content-Security-Policy: default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
    "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; " .
    "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
    "img-src 'self' data: https:; " .
    "connect-src 'self' https://api.fonnte.com https://app.wablas.com; " .
    "frame-ancestors 'none'; " .
    "base-uri 'self'; " .
    "form-action 'self';");

// ──────────────────────────────────────────────────────────────
// ROUTING
//   $routes: first URL segment  →  [page file, detail page file for /segment/{slug}]
//   Legacy slugs are kept so existing links and bookmarks keep working.
// ──────────────────────────────────────────────────────────────
$routes = [
    ''                  => ['home',              null],
    'home'              => ['home',              null],
    'beranda'           => ['home',              null],
    'publikasi'         => ['publikasi',         'publikasi-detail'],
    'katalog'           => ['publikasi',         'publikasi-detail'],
    'layanan'           => ['layanan',           null],
    'proses'            => ['proses',            null],
    'biaya'             => ['harga',             null],
    'harga'             => ['harga',             null],
    'wawasan'           => ['blog',              'blog-detail'],
    'blog'              => ['blog',              'blog-detail'],
    'tentang'           => ['tentang',           null],
    'portofolio'        => ['portofolio',        'portofolio-detail'],
    'kirim-naskah'      => ['konsultasi',        null],
    'konsultasi'        => ['konsultasi',        null],
    'lacak'             => ['tracking',          null],
    'tracking'          => ['tracking',          null],
    'kebijakan-privasi' => ['kebijakan-privasi', null],
    'kebijakan-refund'  => ['kebijakan-refund',  null],
    'invoice'           => ['invoice',           null],
];

if (isset($_GET['page'])) {
    // Query-string routing (hosts without mod_rewrite, and internal rewrites)
    $requested = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string) $_GET['page']);
    $known = [];
    foreach ($routes as $r) {
        $known[] = $r[0];
        if ($r[1]) $known[] = $r[1];
    }
    $page = in_array($requested, $known, true) ? $requested : '404';
} else {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $basePath   = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

    $path = $requestUri;
    if ($basePath !== '' && strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
    }
    $path = trim((string) strtok($path, '?'), '/');

    $segments  = $path === '' ? [''] : explode('/', $path);
    $firstSeg  = strtolower($segments[0]);
    $secondSeg = $segments[1] ?? '';

    if (!isset($routes[$firstSeg])) {
        $page = '404';
    } else {
        [$listPage, $detailPage] = $routes[$firstSeg];

        if ($secondSeg !== '' && $detailPage !== null) {
            $_GET['slug'] = $secondSeg;
            $page = $detailPage;
        } elseif ($secondSeg !== '' && $firstSeg === 'invoice') {
            $_GET['token'] = $secondSeg;
            $page = 'invoice';
        } elseif ($secondSeg !== '') {
            $page = '404';
        } else {
            $page = $listPage;
        }
    }
}

$pageFile    = __DIR__ . '/pages/' . $page . '.php';
$currentPage = $page ?: 'home';

// AJAX submissions must answer before any shell output
if (in_array($page, ['konsultasi', 'tracking'], true)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    require $pageFile;
    exit;
}

// Invoice streams a PDF — bypass the HTML shell entirely
if ($page === 'invoice' && file_exists($pageFile)) {
    require $pageFile;
    exit;
}

// ──────────────────────────────────────────────────────────────
// RENDER
// ──────────────────────────────────────────────────────────────
$pageTitle = getSetting('site_name', 'Kayaswara');

ob_start();
if (file_exists($pageFile)) {
    require $pageFile;
} else {
    http_response_code(404);
    require __DIR__ . '/pages/404.php';
}
$pageContent = ob_get_clean();

require __DIR__ . '/includes/header.php';
echo $pageContent;
require __DIR__ . '/includes/footer.php';
