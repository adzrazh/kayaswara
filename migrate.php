<?php
/**
 * Kayaswara — Database migration
 * Brings an existing installation up to the current schema. Safe to re-run.
 * Delete this file from the server once it has been executed.
 */

// CLI or localhost only
$allowedIps = ['127.0.0.1', '::1'];
if (PHP_SAPI !== 'cli' && !in_array($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $allowedIps, true)) {
    http_response_code(403);
    exit('Akses ditolak. Jalankan script ini dari localhost atau CLI.');
}

if (!file_exists(__DIR__ . '/config.php')) {
    die('config.php tidak ditemukan. Jalankan install.php terlebih dahulu.');
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$results = [];

$run = function (string $label, callable $fn) use (&$results) {
    try {
        $outcome = $fn();
        $results[] = ['type' => $outcome === false ? 'skip' : 'ok', 'msg' => $label . ($outcome === false ? ' — sudah ada, dilewati.' : ' — berhasil.')];
    } catch (Throwable $e) {
        $results[] = ['type' => 'error', 'msg' => $label . ' — gagal: ' . $e->getMessage()];
    }
};

$columnExists = function (string $table, string $column): bool {
    try {
        return (bool) fetch("SHOW COLUMNS FROM `{$table}` LIKE ?", [$column]);
    } catch (Throwable $e) {
        return false;
    }
};

/* 1 — Katalog publikasi */
$run('Tabel publications', function () {
    db()->exec("CREATE TABLE IF NOT EXISTS publications (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    return true;
});

/* 2 — Tabel inti (aman dijalankan berulang) */
$run('Tabel inti', function () {
    $tables = [
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
    foreach ($tables as $sql) {
        db()->exec($sql);
    }
    return true;
});

/* 3 — Kolom lama yang mungkin belum ada */
$run('Kolom orders.price', function () use ($columnExists) {
    if ($columnExists('orders', 'price')) return false;
    db()->exec("ALTER TABLE orders ADD COLUMN price BIGINT DEFAULT 0 AFTER notes");
    return true;
});

$run('Kolom orders.addons', function () use ($columnExists) {
    if ($columnExists('orders', 'addons')) return false;
    db()->exec("ALTER TABLE orders ADD COLUMN addons TEXT NULL DEFAULT NULL AFTER price");
    return true;
});

$run('Kolom blog_posts.category', function () use ($columnExists) {
    if ($columnExists('blog_posts', 'category')) return false;
    db()->exec("ALTER TABLE blog_posts ADD COLUMN category VARCHAR(50) DEFAULT '' AFTER status");
    return true;
});

$run('Kolom consultations.attachment_file', function () use ($columnExists) {
    if ($columnExists('consultations', 'attachment_file')) return false;
    db()->exec("ALTER TABLE consultations
                ADD COLUMN attachment_file VARCHAR(255) DEFAULT NULL AFTER message,
                ADD COLUMN attachment_name VARCHAR(255) DEFAULT NULL AFTER attachment_file");
    return true;
});

/* 4 — Pengaturan baru */
$run('Pengaturan identitas & sosial media', function () {
    $defaults = [
        'legal_name'       => 'CV. Kayaswara',
        'social_instagram' => '',
        'social_facebook'  => '',
        'social_youtube'   => '',
        'social_linkedin'  => '',
    ];
    foreach ($defaults as $k => $v) {
        if (!fetch("SELECT id FROM settings WHERE setting_key = ?", [$k])) {
            insert('settings', ['setting_key' => $k, 'setting_value' => $v]);
        }
    }
    return true;
});

/* 5 — Palet warna baru (hanya bila masih memakai nilai lama) */
$run('Palet warna penerbit', function () {
    // Nilai bawaan dari versi-versi sebelumnya, termasuk palet hijau yang
    // sempat dipakai. Warna yang sudah disesuaikan sendiri oleh pemilik situs
    // sengaja tidak disentuh.
    $legacy = [
        'primary_color'   => ['#1F4B3F', '#1E3A5F', '#8C1D18'],
        'secondary_color' => ['#2F6B57', '#1A3C5E', '#0B7A6E'],
        'accent_color'    => ['#A9752F', '#C4880C'],
    ];
    $new = [
        'primary_color'   => '#1A3C5E',
        'secondary_color' => '#2E6188',
        'accent_color'    => '#B8860B',
    ];
    $changed = false;
    foreach ($legacy as $key => $oldValues) {
        $row = fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        if (!$row) {
            insert('settings', ['setting_key' => $key, 'setting_value' => $new[$key]]);
            $changed = true;
        } elseif (in_array(strtoupper((string) $row['setting_value']), array_map('strtoupper', $oldValues), true)) {
            update('settings', ['setting_value' => $new[$key]], 'setting_key = ?', [$key]);
            $changed = true;
        }
    }
    return $changed ?: false;
});

/* 6 — Nama merek: "Kayaswara Publisher" → "Kayaswara" (sesuai legalitas) */
$run('Normalisasi nama penerbit', function () {
    $row = fetch("SELECT setting_value FROM settings WHERE setting_key = 'site_name'");
    if (!$row) return false;
    $name = trim((string) $row['setting_value']);
    $clean = trim(preg_replace('/\s*(publisher|publishing|press)\s*$/i', '', $name));
    if ($clean !== '' && $clean !== $name) {
        update('settings', ['setting_value' => $clean], 'setting_key = ?', ['site_name']);
        return true;
    }
    return false;
});

/* 7 — Direktori unggahan */
$run('Direktori unggahan', function () {
    $made = false;
    foreach (['/assets/uploads/publications', '/assets/uploads/consultations', '/invoices'] as $dir) {
        if (!is_dir(__DIR__ . $dir)) {
            @mkdir(__DIR__ . $dir, 0755, true);
            $made = true;
        }
    }
    return $made ?: false;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Migrasi Database — Kayaswara</title>
<style>
    body { font-family: system-ui, sans-serif; max-width: 720px; margin: 60px auto; padding: 0 20px; color:#41525F; background:#F7F9FB; }
    h1 { color:#1A3C5E; font-size:1.5rem; }
    ul { list-style:none; padding:0; }
    li { padding:.6rem .9rem; border:1px solid #DCE5ED; border-radius:8px; margin-bottom:.5rem; background:#fff; font-size:.93rem; }
    .ok { border-left:3px solid #2E6188; }
    .skip { border-left:3px solid #C0CCD8; color:#74838E; }
    .error { border-left:3px solid #B3392E; color:#8A2C23; font-weight:600; }
    .warn { background:#FAF2E0; border:1px solid #D8C79A; padding:14px 18px; border-radius:8px; margin-top:24px; font-size:.92rem; }
    code { background:#E2EAF1; padding:.1em .35em; border-radius:4px; }
</style>
</head>
<body>
<h1>Migrasi Database Kayaswara</h1>
<ul>
<?php foreach ($results as $r): ?>
    <li class="<?= htmlspecialchars($r['type']) ?>"><?= htmlspecialchars($r['msg']) ?></li>
<?php endforeach; ?>
</ul>
<div class="warn">
    <strong>Penting:</strong> hapus file <code>migrate.php</code> dari server setelah migrasi selesai.
</div>
</body>
</html>
