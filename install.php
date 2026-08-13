<?php
/**
 * Kayaswara — Web Installer
 * Standalone: runs before config.php exists.
 */

if (file_exists(__DIR__ . '/config.php')) {
    header('Location: index.php');
    exit;
}

$step    = isset($_GET['step']) ? (int) $_GET['step'] : 1;
$errors  = [];
$success = false;

/* ──────────────────────────────────────────────────────────────
   Schema
   ────────────────────────────────────────────────────────────── */
function kys_schema(): array {
    return [
        "CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        /* Katalog publikasi — daftar buku yang telah diterbitkan */
        "CREATE TABLE IF NOT EXISTS publications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255) DEFAULT NULL,
            slug VARCHAR(255) NOT NULL,
            authors VARCHAR(500) NOT NULL,
            editor VARCHAR(255) DEFAULT NULL,
            category ENUM('buku_ajar','buku_referensi','monograf','bunga_rampai','prosiding','lainnya') DEFAULT 'buku_referensi',
            subject VARCHAR(120) DEFAULT NULL,
            synopsis TEXT,
            cover VARCHAR(255) DEFAULT NULL,
            isbn VARCHAR(25) DEFAULT NULL,
            publish_year SMALLINT DEFAULT NULL,
            edition VARCHAR(50) DEFAULT NULL,
            pages INT DEFAULT NULL,
            dimensions VARCHAR(50) DEFAULT NULL,
            language VARCHAR(40) DEFAULT 'Indonesia',
            price BIGINT DEFAULT 0,
            purchase_url VARCHAR(500) DEFAULT NULL,
            is_featured TINYINT(1) DEFAULT 0,
            status ENUM('draft','published') DEFAULT 'published',
            views INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_pub_slug (slug),
            KEY idx_pub_status (status),
            KEY idx_pub_category (category),
            KEY idx_pub_year (publish_year)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS portfolio (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            image VARCHAR(255),
            client_name VARCHAR(255),
            client_institution VARCHAR(255),
            website_url VARCHAR(500),
            category ENUM('jurnal','konferensi','repositori','lainnya') DEFAULT 'jurnal',
            is_featured TINYINT(1) DEFAULT 0,
            status ENUM('draft','published') DEFAULT 'published',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            excerpt TEXT,
            content LONGTEXT,
            image VARCHAR(255),
            author VARCHAR(100) DEFAULT 'Redaksi',
            status ENUM('draft','published') DEFAULT 'draft',
            category VARCHAR(50) DEFAULT '',
            views INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tracking_code VARCHAR(30) UNIQUE NOT NULL,
            client_name VARCHAR(255) NOT NULL,
            client_email VARCHAR(255),
            client_phone VARCHAR(50),
            client_institution VARCHAR(255),
            service_type VARCHAR(100) DEFAULT 'setup_ojs',
            package_tier VARCHAR(50) DEFAULT '',
            description TEXT,
            status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
            notes TEXT,
            price BIGINT DEFAULT 0,
            addons TEXT NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS order_milestones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('pending','in_progress','completed') DEFAULT 'pending',
            completed_at DATETIME NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS notification_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            channel VARCHAR(30) NOT NULL DEFAULT 'whatsapp',
            provider VARCHAR(50) NOT NULL DEFAULT 'fonnte',
            recipient VARCHAR(50) NOT NULL,
            message TEXT,
            status ENUM('sent','failed') DEFAULT 'failed',
            response VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS consultations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            institution VARCHAR(255),
            service_type ENUM('setup_ojs','migrasi','kustomisasi','pelatihan','maintenance','lainnya') DEFAULT 'setup_ojs',
            budget_range VARCHAR(100),
            message TEXT,
            attachment_file VARCHAR(255) DEFAULT NULL,
            attachment_name VARCHAR(255) DEFAULT NULL,
            status ENUM('new','contacted','follow_up','negotiation','closed_won','closed_lost') DEFAULT 'new',
            priority ENUM('low','medium','high') DEFAULT 'medium',
            notes TEXT,
            follow_up_date DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS invoices (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id INT UNSIGNED NOT NULL,
            invoice_number VARCHAR(30) NOT NULL,
            status ENUM('draft','sent','paid','cancelled') NOT NULL DEFAULT 'draft',
            subtotal INT UNSIGNED NOT NULL DEFAULT 0,
            discount INT UNSIGNED NOT NULL DEFAULT 0,
            tax_pph23 INT UNSIGNED NOT NULL DEFAULT 0,
            total INT UNSIGNED NOT NULL DEFAULT 0,
            due_date DATE NULL,
            paid_at DATETIME NULL,
            show_pph23 TINYINT(1) NOT NULL DEFAULT 1,
            admin_notes TEXT NULL,
            token VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_invoice_number (invoice_number),
            UNIQUE KEY uq_invoice_token (token),
            KEY idx_order_id (order_id),
            KEY idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];
}

function kys_default_settings(): array {
    return [
        'site_name'        => 'Kayaswara',
        'site_tagline'     => 'Penerbit Buku Akademik',
        'legal_name'       => 'CV. Kayaswara',
        'primary_color'    => '#1F4B3F',
        'secondary_color'  => '#2F6B57',
        'accent_color'     => '#A9752F',
        'logo_path'        => '',
        'favicon_path'     => '',
        'whatsapp_number'  => '081213169703',
        'email_contact'    => 'kayaswara.jurnal@gmail.com',
        'address'          => 'Jln. Sunan Kalijaga Timur 10, Kec. Larangan, Kota Tangerang, Banten',
        'footer_text'      => '© ' . date('Y') . ' CV. Kayaswara. Seluruh hak cipta dilindungi.',
        'meta_description' => 'Kayaswara adalah penerbit buku akademik: buku ajar, buku referensi, monograf, dan bunga rampai karya dosen serta peneliti Indonesia.',
        'social_instagram' => '',
        'social_facebook'  => '',
        'social_youtube'   => '',
        'social_linkedin'  => '',
    ];
}

/* ──────────────────────────────────────────────────────────────
   Install
   ────────────────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $dbHost     = trim($_POST['db_host'] ?? 'localhost');
    $dbName     = trim($_POST['db_name'] ?? '');
    $dbUser     = trim($_POST['db_user'] ?? '');
    $dbPass     = $_POST['db_pass'] ?? '';
    $siteUrl    = rtrim(trim($_POST['site_url'] ?? ''), '/');
    $adminUser  = trim($_POST['admin_user'] ?? '');
    $adminPass  = $_POST['admin_pass'] ?? '';
    $adminName  = trim($_POST['admin_name'] ?? '');
    $adminEmail = trim($_POST['admin_email'] ?? '');

    if ($dbHost === '')    $errors[] = 'Host database wajib diisi.';
    if ($dbName === '')    $errors[] = 'Nama database wajib diisi.';
    if ($dbUser === '')    $errors[] = 'Username database wajib diisi.';
    if ($siteUrl === '')   $errors[] = 'URL situs wajib diisi.';
    if ($adminUser === '') $errors[] = 'Username admin wajib diisi.';
    if (strlen($adminPass) < 8) $errors[] = 'Password admin minimal 8 karakter.';
    if ($adminName === '') $errors[] = 'Nama admin wajib diisi.';

    if (!$errors) {
        try {
            $pdo = new PDO("mysql:host={$dbHost};charset=utf8mb4", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            $errors[] = 'Koneksi database gagal: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    if (!$errors) {
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");

            foreach (kys_schema() as $stmt) {
                $pdo->exec($stmt);
            }

            $ins = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
            foreach (kys_default_settings() as $k => $v) {
                $ins->execute([$k, $v]);
            }

            $pdo->prepare("INSERT IGNORE INTO admins (username, password, name, email) VALUES (?, ?, ?, ?)")
                ->execute([$adminUser, password_hash($adminPass, PASSWORD_BCRYPT), $adminName, $adminEmail]);

            $config  = "<?php\n";
            $config .= "define('DB_HOST', " . var_export($dbHost, true) . ");\n";
            $config .= "define('DB_NAME', " . var_export($dbName, true) . ");\n";
            $config .= "define('DB_USER', " . var_export($dbUser, true) . ");\n";
            $config .= "define('DB_PASS', " . var_export($dbPass, true) . ");\n";
            $config .= "define('DB_CHARSET', 'utf8mb4');\n";
            $config .= "define('SITE_URL', " . var_export($siteUrl, true) . ");\n";
            $config .= "define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);\n";
            $config .= "define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);\n";

            if (file_put_contents(__DIR__ . '/config.php', $config) === false) {
                $errors[] = 'Gagal menulis config.php. Pastikan direktori dapat ditulis oleh PHP.';
            } else {
                foreach ([
                    '/assets/uploads/publications',
                    '/assets/uploads/portfolio',
                    '/assets/uploads/blog',
                    '/assets/uploads/site',
                    '/assets/uploads/consultations',
                    '/invoices',
                ] as $dir) {
                    if (!is_dir(__DIR__ . $dir)) @mkdir(__DIR__ . $dir, 0755, true);
                }
                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = 'Kesalahan database: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}

$protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$guessedUrl = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instalasi — Kayaswara</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    :root { --primary:#1F4B3F; --secondary:#2F6B57; --accent:#A9752F; --paper:#FBFAF6; --line:#E3E0D4; --ink:#16211C; --ink-muted:#77857D; }
    body { font-family:'Inter',sans-serif; background:var(--paper); color:#45544D; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2.5rem 1rem; }
    body::before { content:''; position:fixed; inset:0; background:radial-gradient(circle at 15% 20%, rgba(47,107,87,.08), transparent 45%), radial-gradient(circle at 85% 80%, rgba(169,117,47,.08), transparent 45%); pointer-events:none; }
    .wrap { position:relative; width:100%; max-width:640px; }
    .card-install { background:#fff; border:1px solid var(--line); border-radius:16px; box-shadow:0 18px 48px rgba(22,33,28,.1); overflow:hidden; }
    .card-head { background:var(--primary); color:#fff; padding:2rem; }
    .card-head h1 { font-family:'Source Serif 4',Georgia,serif; font-size:1.6rem; margin:0 0 .25rem; }
    .card-head p { margin:0; opacity:.8; font-size:.9rem; }
    .card-body-install { padding:2rem; }
    .steps { display:flex; gap:.5rem; margin-bottom:1.75rem; }
    .steps div { flex:1; height:4px; border-radius:2px; background:#E3E0D4; }
    .steps div.on { background:var(--secondary); }
    h5 { font-family:'Source Serif 4',Georgia,serif; color:var(--ink); }
    .form-label { font-size:.84rem; font-weight:600; color:var(--ink); }
    .form-control { border:1.5px solid var(--line); border-radius:6px; padding:.62rem .85rem; font-size:.93rem; }
    .form-control:focus { border-color:var(--secondary); box-shadow:0 0 0 3px rgba(47,107,87,.12); }
    .form-text { font-size:.78rem; color:var(--ink-muted); }
    .btn-go { background:var(--primary); border:0; color:#fff; font-weight:600; padding:.8rem 1.5rem; border-radius:6px; width:100%; }
    .btn-go:hover { background:#163830; color:#fff; }
    .legend { font-size:.72rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--secondary); margin-bottom:.85rem; }
    .req-list { list-style:none; padding:0; margin:0 0 1.5rem; border:1px solid var(--line); border-radius:10px; overflow:hidden; }
    .req-list li { display:flex; justify-content:space-between; align-items:center; padding:.75rem 1rem; border-bottom:1px solid var(--line); font-size:.9rem; }
    .req-list li:last-child { border-bottom:0; }
    .pill { font-size:.72rem; font-weight:700; padding:.2rem .6rem; border-radius:99px; }
    .pill-ok { background:#E8F0EA; color:#1F5637; }
    .pill-no { background:#FBEDEC; color:#8A2C23; }
    .pill-info { background:#F4F2EA; color:#45544D; }
</style>
</head>
<body>
<div class="wrap">
<div class="card-install">
    <div class="card-head">
        <h1>Kayaswara</h1>
        <p>Instalasi situs penerbit buku akademik</p>
    </div>
    <div class="card-body-install">

    <?php if ($success): ?>
        <div class="text-center py-2">
            <div style="font-size:3rem;color:var(--secondary);margin-bottom:.75rem;"><i class="fa-solid fa-circle-check"></i></div>
            <h5 class="fw-bold mb-2">Instalasi Selesai</h5>
            <p class="text-muted mb-4" style="font-size:.93rem;">
                Situs siap digunakan. Langkah berikutnya: masuk ke panel admin, lengkapi profil penerbit,
                lalu isi <strong>Katalog Publikasi</strong> dengan buku-buku yang telah diterbitkan.
            </p>
            <div class="d-grid gap-2">
                <a href="index.php" class="btn btn-go"><i class="fa-solid fa-house me-2"></i>Buka Situs</a>
                <a href="admin/" class="btn btn-outline-secondary"><i class="fa-solid fa-gauge me-2"></i>Panel Admin</a>
            </div>
            <p class="form-text mt-3 mb-0">Demi keamanan, hapus <code>install.php</code> dari server.</p>
        </div>

    <?php elseif ($step === 1):
        $checks = [
            'PHP 7.4 atau lebih baru' => version_compare(PHP_VERSION, '7.4.0', '>='),
            'Ekstensi PDO'            => extension_loaded('pdo'),
            'Ekstensi PDO MySQL'      => extension_loaded('pdo_mysql'),
            'Ekstensi FileInfo'       => extension_loaded('fileinfo'),
            'Direktori dapat ditulis' => is_writable(__DIR__),
        ];
        $allOk = !in_array(false, $checks, true);
    ?>
        <div class="steps"><div class="on"></div><div></div></div>
        <h5 class="mb-3">Pemeriksaan Sistem</h5>
        <ul class="req-list">
            <?php foreach ($checks as $label => $ok): ?>
            <li>
                <span><?= htmlspecialchars($label) ?></span>
                <span class="pill <?= $ok ? 'pill-ok' : 'pill-no' ?>"><?= $ok ? 'Siap' : 'Gagal' ?></span>
            </li>
            <?php endforeach; ?>
            <li><span>Versi PHP terpasang</span><span class="pill pill-info"><?= PHP_VERSION ?></span></li>
        </ul>
        <?php if ($allOk): ?>
            <a href="install.php?step=2" class="btn btn-go"><i class="fa-solid fa-arrow-right me-2"></i>Lanjutkan</a>
        <?php else: ?>
            <div class="alert alert-danger mb-0" style="font-size:.9rem;">
                Sebagian persyaratan belum terpenuhi. Hubungi penyedia hosting Anda sebelum melanjutkan.
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="steps"><div class="on"></div><div class="on"></div></div>
        <h5 class="mb-3">Konfigurasi</h5>

        <?php if ($errors): ?>
        <div class="alert alert-danger" style="font-size:.88rem;">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="install.php?step=2">
            <div class="legend">Database</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Host</label>
                    <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Database</label>
                    <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($_POST['db_name'] ?? 'kayaswara') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($_POST['db_user'] ?? 'root') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>">
                    <div class="form-text">Kosongkan bila tidak ada.</div>
                </div>
            </div>

            <div class="legend">Situs</div>
            <div class="mb-4">
                <label class="form-label">URL Situs</label>
                <input type="url" name="site_url" class="form-control" value="<?= htmlspecialchars($_POST['site_url'] ?? $guessedUrl) ?>" required>
                <div class="form-text">Tanpa garis miring di akhir. Contoh: https://kayaswara.co.id</div>
            </div>

            <div class="legend">Akun Administrator</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="admin_name" class="form-control" value="<?= htmlspecialchars($_POST['admin_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="admin_user" class="form-control" value="<?= htmlspecialchars($_POST['admin_user'] ?? 'admin') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="admin_pass" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                </div>
            </div>

            <button type="submit" class="btn btn-go"><i class="fa-solid fa-database me-2"></i>Pasang Sekarang</button>
        </form>
    <?php endif; ?>

    </div>
</div>
</div>
</body>
</html>
