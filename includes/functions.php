<?php
/**
 * Core Functions - Kayaswara Publishing
 * All helper functions for the admin panel and frontend.
 *
 * Note: This is a stub/placeholder file created by the admin builder.
 * The full implementation is created by the main site builder agent.
 * The admin panel depends on all functions listed below.
 */

// ============================================================
// PDO Singleton
// ============================================================

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host    = defined('DB_HOST')    ? DB_HOST    : 'localhost';
        $dbname  = defined('DB_NAME')    ? DB_NAME    : 'kayaswara_publisher';
        $user    = defined('DB_USER')    ? DB_USER    : 'root';
        $pass    = defined('DB_PASS')    ? DB_PASS    : '';
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log('DB connection error: ' . $e->getMessage());
            die('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2 style="color:#dc2626;">Koneksi Database Gagal</h2><p>Periksa konfigurasi database di config.php.</p></div>');
        }
    }
    return $pdo;
}

// ============================================================
// Query Helpers
// ============================================================

function query(string $sql, array $params = []): PDOStatement {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetch(string $sql, array $params = []) {
    return query($sql, $params)->fetch();
}

function fetchAll(string $sql, array $params = []): array {
    return query($sql, $params)->fetchAll();
}

function insert(string $table, array $data) {
    $cols = implode(', ', array_map(fn($k) => "`{$k}`", array_keys($data)));
    $plcr = implode(', ', array_fill(0, count($data), '?'));
    query("INSERT INTO `{$table}` ({$cols}) VALUES ({$plcr})", array_values($data));
    return db()->lastInsertId();
}

function update(string $table, array $data, string $where, array $where_params = []): int {
    $set = implode(', ', array_map(fn($k) => "`{$k}` = ?", array_keys($data)));
    $stmt = query("UPDATE `{$table}` SET {$set} WHERE {$where}", array_merge(array_values($data), $where_params));
    return $stmt->rowCount();
}

function delete(string $table, string $where, array $params = []): int {
    return query("DELETE FROM `{$table}` WHERE {$where}", $params)->rowCount();
}

// ============================================================
// Settings
// ============================================================

function getSetting(string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        try {
            $row = fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
            $cache[$key] = $row ? ($row['setting_value'] ?? $default) : $default;
        } catch (Exception $e) {
            $cache[$key] = $default;
        }
    }
    return $cache[$key];
}

function setSetting(string $key, string $value): void {
    try {
        $exists = fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        if ($exists) {
            update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
        } else {
            insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
        // Clear cache
        // (static cache will be stale, acceptable for single-request lifecycle)
    } catch (Exception $e) {
        // silently fail or log
    }
}

function getAllSettings(): array {
    try {
        $rows = fetchAll("SELECT setting_key, setting_value FROM settings");
        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    } catch (Exception $e) {
        return [];
    }
}

// ============================================================
// Helpers
// ============================================================

function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    // Transliterate common Indonesian/Latin characters
    $replace = [
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'ñ' => 'n', 'ç' => 'c',
    ];
    $text = strtr($text, $replace);
    $text = preg_replace('/[^a-z0-9\s\-]/', '', $text);
    $text = preg_replace('/[\s\-]+/', '-', $text);
    return trim($text, '-');
}

function uploadImage(array $file, string $folder) {
    $upload_dir = dirname(__DIR__) . '/assets/uploads/' . $folder . '/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // SVG removed – can embed JavaScript (XSS risk)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_ext   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $mime = mime_content_type($file['tmp_name']);
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($mime, $allowed_types) || !in_array($ext, $allowed_ext)) {
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $filename;
    }

    return false;
}

function deleteImage(string $path): bool {
    $full_path = dirname(__DIR__) . '/assets/uploads/' . $path;
    if (file_exists($full_path) && is_file($full_path)) {
        return unlink($full_path);
    }
    return false;
}

function truncate(string $text, int $length = 150): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

function formatDate(string $date, string $format = 'd M Y'): string {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

// ============================================================
// Auth
// ============================================================

function isLoggedIn(): bool {
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['admin_id']);
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ============================================================
// CSRF
// ============================================================

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================================
// Flash Messages
// ============================================================

function flash(string $type, string $message): void {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

function getFlash(): array {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

// ============================================================
// Sanitize
// ============================================================

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize rich HTML content (e.g. blog posts) to prevent XSS and SEO injection.
 * Strips dangerous tags (script, iframe, style, form, object, embed, meta, link, base)
 * and dangerous attributes (on*, style, srcdoc, data:, javascript:).
 * Allows safe formatting tags only.
 */
function sanitizeHtml(string $html): string {
    if (empty(trim($html))) return '';

    // 1. Strip outright dangerous tags and everything inside them
    $html = preg_replace(
        '#<(script|style|iframe|frame|frameset|object|embed|applet|base|form|input|button|select|textarea|meta|link|svg|math)[^>]*>.*?</\1>#is',
        '',
        $html
    );
    // 2. Strip any remaining opening/self-closing dangerous tags
    $html = preg_replace(
        '#<(script|style|iframe|frame|frameset|object|embed|applet|base|form|input|button|select|textarea|meta|link|svg|math)[^>]*/?>?#i',
        '',
        $html
    );

    // 3. Strip event handler attributes (onclick, onload, onmouseover, etc.)
    $html = preg_replace('/\bon\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $html);

    // 4. Strip dangerous href/src values (javascript:, data:, vbscript:)
    $html = preg_replace('/\b(href|src|action)\s*=\s*["\']?\s*(javascript|vbscript|data):/i', '$1="#"', $html);

    // 5. Strip style attributes that could be used for hidden text SEO injection
    //    (visibility:hidden, display:none, opacity:0, font-size:0, color:transparent, position:absolute off-screen)
    $html = preg_replace_callback('/ style\s*=\s*["\']([^"\']*)["\']/', function($m) {
        $style = $m[1];
        // If it contains hiding/SEO-injection patterns, remove the style entirely
        if (preg_match('/display\s*:\s*none|visibility\s*:\s*hidden|opacity\s*:\s*0|font-size\s*:\s*0|color\s*:\s*(transparent|rgba?\(0,0,0,0)|position\s*:\s*(absolute|fixed)/i', $style)) {
            return ''; // remove the style attribute
        }
        return $m[0]; // keep safe styles
    }, $html);

    // 6. Ensure all URLs in hrefs are relative or http(s)
    $html = preg_replace_callback('/href\s*=\s*"([^"]*)"/i', function($m) {
        $url = $m[1];
        if (!empty($url) && !preg_match('/^(https?:\/\/|\/|#|\?)/i', $url)) {
            return 'href="#"';
        }
        return $m[0];
    }, $html);

    return $html;
}

// ============================================================
// Pagination
// ============================================================

function getPagination(int $total, int $perPage, int $currentPage): array {
    $totalPages  = (int)ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset      = ($currentPage - 1) * $perPage;

    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'offset'      => $offset,
        'has_prev'    => $currentPage > 1,
        'has_next'    => $currentPage < $totalPages,
        'prev'        => $currentPage - 1,
        'next'        => $currentPage + 1,
    ];
}

// ============================================================
// Status Badges (Bootstrap class names)
// ============================================================

function getStatusBadge(string $status): string {
    $map = [
        'new'         => 'primary',
        'contacted'   => 'info',
        'follow_up'   => 'warning',
        'negotiation' => 'secondary',
        'closed_won'  => 'success',
        'closed_lost' => 'danger',
        'published'   => 'success',
        'draft'       => 'warning',
    ];
    return $map[$status] ?? 'secondary';
}

function getStatusLabel(string $status): string {
    $map = [
        'new'         => 'Baru',
        'contacted'   => 'Dihubungi',
        'follow_up'   => 'Follow Up',
        'negotiation' => 'Negosiasi',
        'closed_won'  => 'Closed Won',
        'closed_lost' => 'Closed Lost',
        'published'   => 'Publikasi',
        'draft'       => 'Draft',
    ];
    return $map[$status] ?? ucfirst(str_replace('_', ' ', $status));
}

function getPriorityBadge(string $priority): string {
    $map = [
        'low'    => 'secondary',
        'medium' => 'warning',
        'high'   => 'danger',
    ];
    return $map[$priority] ?? 'secondary';
}

// ============================================================
// Order Tracking
// ============================================================

/**
 * Generate tracking code: KYSWR-ddMMyyyy-NNN
 */
function generateTrackingCode(): string {
    $dateStr = date('dmY'); // e.g., 06042026
    $prefix  = 'KYSWR-' . $dateStr . '-';

    // Count existing orders with this date prefix
    $row = fetch(
        "SELECT COUNT(*) as cnt FROM orders WHERE tracking_code LIKE ?",
        [$prefix . '%']
    );
    $nextNum = ($row ? (int)$row['cnt'] : 0) + 1;

    return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}

/**
 * Default milestones for a new order.
 * Returns array of [title, description].
 */
function getDefaultMilestones(string $serviceType = 'setup_ojs'): array {
    $base = [
        ['Pesanan Diterima',               'Pesanan penerbitan buku telah diterima dan sedang diproses oleh tim kami.'],
        ['Review Naskah Awal',             'Tim editor melakukan review awal naskah untuk menentukan lingkup pekerjaan.'],
        ['Editing & Proofreading',         'Proses editing tata bahasa, ejaan, dan konsistensi konten naskah.'],
        ['Layout & Typesetting',           'Penataan layout buku, pengaturan halaman, margin, header/footer, dan daftar isi.'],
        ['Desain Cover Buku',              'Pembuatan desain cover buku sesuai identitas konten dan preferensi penulis.'],
        ['Review & Revisi Penulis',        'Penulis melakukan review hasil editing dan layout. Revisi dilakukan sesuai feedback.'],
        ['Distribusi & Pemasaran',                'Pengajuan dan distribusi buku ke toko offline dan online.'],
        ['Finalisasi & Pracetak',          'Finalisasi seluruh materi buku dan persiapan file untuk proses pencetakan.'],
        ['Pencetakan Buku',                'Proses cetak buku sesuai jumlah eksemplar yang disepakati.'],
        ['Serah Terima & Distribusi',      'Buku diserahterimakan kepada penulis lengkap dengan sertifikat penerbitan.'],
    ];

    return $base;
}

function getOrderStatusLabel(string $status): string {
    $map = [
        'pending'     => 'Menunggu',
        'in_progress' => 'Dikerjakan',
        'completed'   => 'Selesai',
        'cancelled'   => 'Dibatalkan',
    ];
    return $map[$status] ?? ucfirst($status);
}

function getOrderStatusBadge(string $status): string {
    $map = [
        'pending'     => 'warning',
        'in_progress' => 'info',
        'completed'   => 'success',
        'cancelled'   => 'danger',
    ];
    return $map[$status] ?? 'secondary';
}

function getMilestoneStatusLabel(string $status): string {
    $map = [
        'pending'     => 'Menunggu',
        'in_progress' => 'Sedang Dikerjakan',
        'completed'   => 'Selesai',
    ];
    return $map[$status] ?? ucfirst($status);
}

// ============================================================
// WhatsApp Notification
// ============================================================

/**
 * Send WhatsApp notification via configured API provider.
 * Supported providers: fonnte, wablas, custom.
 * Returns ['success' => bool, 'message' => string]
 */
function sendWhatsAppNotification(string $phone, string $message): array {
    $enabled  = getSetting('wa_notif_enabled', '0');
    if ($enabled !== '1') {
        return ['success' => false, 'message' => 'Notifikasi WA dinonaktifkan.'];
    }

    $provider = getSetting('wa_api_provider', 'fonnte');
    $token    = getSetting('wa_api_token', '');
    if (empty($token)) {
        return ['success' => false, 'message' => 'API token belum dikonfigurasi.'];
    }

    // Normalize phone: remove spaces, dashes, leading 0, ensure starts with 62
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    if (str_starts_with($phone, '+')) $phone = substr($phone, 1);
    if (str_starts_with($phone, '0'))  $phone = '62' . substr($phone, 1);
    if (!str_starts_with($phone, '62')) $phone = '62' . $phone;

    $result = ['success' => false, 'message' => ''];

    try {
        switch ($provider) {
            case 'fonnte':
                $result = _sendViaFonnte($phone, $message, $token);
                break;
            case 'wablas':
                $result = _sendViaWablas($phone, $message, $token);
                break;
            case 'custom':
                $customUrl = getSetting('wa_api_url', '');
                if (empty($customUrl)) {
                    return ['success' => false, 'message' => 'URL API custom belum diisi.'];
                }
                $result = _sendViaCustom($phone, $message, $token, $customUrl);
                break;
            default:
                $result = ['success' => false, 'message' => 'Provider tidak dikenali.'];
        }
    } catch (Exception $e) {
        $result = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }

    // Log notification
    _logNotification($phone, $message, $provider, $result);

    return $result;
}

function _sendViaFonnte(string $phone, string $message, string $token): array {
    $ch = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'target'      => $phone,
            'message'     => $message,
            'countryCode' => '62',
        ]),
        CURLOPT_HTTPHEADER     => ['Authorization: ' . $token],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) return ['success' => false, 'message' => 'cURL error: ' . $err];

    $json = json_decode($resp, true);
    if ($code === 200 && isset($json['status']) && $json['status'] === true) {
        return ['success' => true, 'message' => 'Terkirim via Fonnte.'];
    }
    return ['success' => false, 'message' => 'Fonnte: ' . ($json['reason'] ?? $json['message'] ?? $resp)];
}

function _sendViaWablas(string $phone, string $message, string $token): array {
    $ch = curl_init('https://pati.wablas.com/api/send-message');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'phone'   => $phone,
            'message' => $message,
        ]),
        CURLOPT_HTTPHEADER     => ['Authorization: ' . $token],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) return ['success' => false, 'message' => 'cURL error: ' . $err];

    $json = json_decode($resp, true);
    if ($code === 200 && isset($json['status']) && $json['status'] === true) {
        return ['success' => true, 'message' => 'Terkirim via Wablas.'];
    }
    return ['success' => false, 'message' => 'Wablas: ' . ($json['message'] ?? $resp)];
}

function _sendViaCustom(string $phone, string $message, string $token, string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'phone'   => $phone,
            'message' => $message,
        ]),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) return ['success' => false, 'message' => 'cURL error: ' . $err];
    if ($code >= 200 && $code < 300) {
        return ['success' => true, 'message' => 'Terkirim via Custom API.'];
    }
    return ['success' => false, 'message' => 'HTTP ' . $code . ': ' . substr($resp, 0, 200)];
}

function _logNotification(string $phone, string $message, string $provider, array $result): void {
    try {
        insert('notification_logs', [
            'channel'    => 'whatsapp',
            'provider'   => $provider,
            'recipient'  => $phone,
            'message'    => mb_substr($message, 0, 2000),
            'status'     => $result['success'] ? 'sent' : 'failed',
            'response'   => mb_substr($result['message'] ?? '', 0, 500),
        ]);
    } catch (Exception $e) {
        error_log('Failed to log notification: ' . $e->getMessage());
    }
}

/**
 * Build WhatsApp message for milestone update from template.
 * Placeholders: {tracking_code}, {client_name}, {milestone}, {status}, {progress}, {site_name}, {tracking_url}
 */
function buildMilestoneNotifMessage(array $order, string $milestoneTitle, string $newStatus): string {
    $defaultTemplate = "Halo {client_name}! 👋\n\n"
        . "Update pesanan Anda (*{tracking_code}*):\n\n"
        . "📌 *{milestone}*\n"
        . "Status: *{status}*\n"
        . "Progres: {progress}%\n\n"
        . "Pantau detail di:\n{tracking_url}\n\n"
        . "_{site_name}_";

    $template = getSetting('wa_notif_template', $defaultTemplate);
    if (empty(trim($template))) $template = $defaultTemplate;

    // Calculate progress
    $totalMs = 0;
    $completedMs = 0;
    try {
        $milestones = fetchAll(
            "SELECT status FROM order_milestones WHERE order_id = ?",
            [$order['id']]
        );
        $totalMs = count($milestones);
        foreach ($milestones as $ms) {
            if ($ms['status'] === 'completed') $completedMs++;
        }
    } catch (Exception $e) {}
    $progressPct = $totalMs > 0 ? round(($completedMs / $totalMs) * 100) : 0;

    // Build tracking URL
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $trackingUrl = $siteUrl . '/tracking?code=' . urlencode($order['tracking_code']);

    $replacements = [
        '{tracking_code}' => $order['tracking_code'],
        '{client_name}'   => $order['client_name'],
        '{milestone}'     => $milestoneTitle,
        '{status}'        => getMilestoneStatusLabel($newStatus),
        '{progress}'      => (string)$progressPct,
        '{site_name}'     => getSetting('site_name', 'Kayaswara'),
        '{tracking_url}'  => $trackingUrl,
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

/**
 * Send an email notification to the customer when a milestone is marked completed.
 * Returns ['success'=>bool, 'message'=>string].
 */
function sendMilestoneEmail(array $order, string $milestoneTitle, int $milestoneId): array {
    if (empty($order['client_email'])) {
        return ['success' => false, 'message' => 'No customer email.'];
    }

    $site_name = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
    $siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
    $trackingUrl = $siteUrl . '/tracking';

    // Calculate progress
    $totalMs = 0;
    $completedMs = 0;
    try {
        $milestones  = fetchAll("SELECT status FROM order_milestones WHERE order_id = ?", [$order['id']]);
        $totalMs     = count($milestones);
        foreach ($milestones as $ms) {
            if ($ms['status'] === 'completed') $completedMs++;
        }
    } catch (Exception $e) {}
    $progressPct = $totalMs > 0 ? round(($completedMs / $totalMs) * 100) : 0;
    $isFinished  = ($completedMs === $totalMs && $totalMs > 0);

    $subject = "[{$site_name}] Update Pesanan {$order['tracking_code']} – {$milestoneTitle} Selesai";

    $body  = "Yth. {$order['client_name']},\r\n\r\n";
    if ($isFinished) {
        $body .= "Selamat! Seluruh tahapan pesanan Anda telah selesai dikerjakan.\r\n\r\n";
    } else {
        $body .= "Kami ingin menginformasikan bahwa salah satu tahapan pesanan Anda telah selesai.\r\n\r\n";
    }
    $body .= "====================================\r\n";
    $body .= " UPDATE PESANAN\r\n";
    $body .= "====================================\r\n";
    $body .= "Kode Tracking : {$order['tracking_code']}\r\n";
    $body .= "Tahapan Selesai: {$milestoneTitle}\r\n";
    $body .= "Progres       : {$progressPct}% ({$completedMs} dari {$totalMs} tahap)\r\n";
    $body .= "====================================\r\n\r\n";
    $body .= "Pantau seluruh detail dan progres pesanan Anda di:\r\n";
    $body .= "{$trackingUrl}\r\n\r\n";
    $body .= "Masukkan kode tracking <{$order['tracking_code']}> untuk melihat detail.\r\n\r\n";
    if ($isFinished) {
        $body .= "Terima kasih telah mempercayakan kebutuhan penerbitan buku Anda kepada kami.\r\n";
        $body .= "Kami berharap layanan kami memberikan manfaat bagi jurnal Anda.\r\n\r\n";
    } else {
        $body .= "Tim kami akan terus bekerja pada tahapan berikutnya.\r\n";
        $body .= "Kami akan menginformasikan kembali setiap ada tahapan yang selesai.\r\n\r\n";
    }
    $body .= "Hormat kami,\r\n{$site_name}";

    return sendSiteEmail($order['client_email'], $subject, $body);
}

// ============================================================
// Email Helper (SMTP native or mail() fallback)
// ============================================================

/**
 * Send an email using SMTP if configured in settings, otherwise fall back to mail().
 * Returns ['success'=>bool, 'message'=>string].
 */
function sendSiteEmail(string $to, string $subject, string $body): array {
    $smtpEnabled = getSetting('smtp_enabled', '0');

    if ($smtpEnabled === '1') {
        $host       = getSetting('smtp_host', '');
        $port       = (int) getSetting('smtp_port', '587');
        $user       = getSetting('smtp_user', '');
        $pass       = getSetting('smtp_pass', '');
        $enc        = getSetting('smtp_encryption', 'tls');
        $fromName   = getSetting('smtp_from_name', '') ?: getSetting('site_name', 'Website');

        if (empty($host) || empty($user) || empty($pass)) {
            return ['success' => false, 'message' => 'Konfigurasi SMTP tidak lengkap.'];
        }

        return _sendViaSMTP($to, $subject, $body, $host, $port, $user, $pass, $enc, $fromName);
    }

    // Fallback: PHP mail()
    $siteName   = html_entity_decode(getSetting('site_name', 'Website'), ENT_QUOTES, 'UTF-8');
    $fromEmail  = getSetting('email_contact', '');
    if (empty($fromEmail)) {
        $fromEmail = 'noreply@' . (parse_url(defined('SITE_URL') ? SITE_URL : 'http://localhost', PHP_URL_HOST) ?? 'localhost');
    }
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers  = "From: {$siteName} <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$fromEmail}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";
    $sent = @mail($to, $encodedSubject, base64_encode($body), $headers);
    return $sent
        ? ['success' => true,  'message' => 'Email terkirim via mail().']
        : ['success' => false, 'message' => 'Gagal mengirim email. Aktifkan SMTP di Pengaturan → Notifikasi, atau hubungi hosting untuk konfigurasi mail server.'];
}

/**
 * Low-level SMTP sender using PHP streams (no library required).
 */
function _sendViaSMTP(string $to, string $subject, string $body,
                       string $host, int $port, string $user, string $pass,
                       string $enc, string $fromName): array {
    $timeout = 15;
    $fromEmail = $user;

    // Determine connection type
    if ($enc === 'ssl') {
        $remote = "ssl://{$host}:{$port}";
    } else {
        $remote = "tcp://{$host}:{$port}";
    }

    $errno = 0; $errstr = '';
    $fp = @stream_socket_client($remote, $errno, $errstr, $timeout);
    if (!$fp) {
        return ['success' => false, 'message' => "Tidak dapat terhubung ke SMTP {$host}:{$port} — {$errstr}"];
    }
    stream_set_timeout($fp, $timeout);

    $recv = function() use ($fp): string {
        $data = '';
        while (!feof($fp)) {
            $line = fgets($fp, 512);
            if ($line === false) break;
            $data .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') break; // last line
        }
        return $data;
    };
    $send = function(string $cmd) use ($fp, $recv): string {
        fwrite($fp, $cmd . "\r\n");
        return $recv();
    };

    // Read greeting
    $r = $recv();
    if (substr($r, 0, 3) !== '220') {
        fclose($fp);
        return ['success' => false, 'message' => "SMTP greeting gagal: {$r}"];
    }

    // EHLO
    $r = $send("EHLO " . (gethostname() ?: 'localhost'));
    if (substr($r, 0, 3) !== '250') {
        fclose($fp);
        return ['success' => false, 'message' => "EHLO gagal: {$r}"];
    }

    // STARTTLS
    if ($enc === 'tls') {
        $r = $send("STARTTLS");
        if (substr($r, 0, 3) !== '220') {
            fclose($fp);
            return ['success' => false, 'message' => "STARTTLS gagal: {$r}"];
        }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($fp);
            return ['success' => false, 'message' => 'TLS handshake gagal.'];
        }
        // Re-EHLO after TLS
        $r = $send("EHLO " . (gethostname() ?: 'localhost'));
        if (substr($r, 0, 3) !== '250') {
            fclose($fp);
            return ['success' => false, 'message' => "Re-EHLO setelah TLS gagal: {$r}"];
        }
    }

    // AUTH LOGIN
    $r = $send("AUTH LOGIN");
    if (substr($r, 0, 3) !== '334') {
        fclose($fp);
        return ['success' => false, 'message' => "AUTH LOGIN gagal: {$r}"];
    }
    $r = $send(base64_encode($user));
    if (substr($r, 0, 3) !== '334') {
        fclose($fp);
        return ['success' => false, 'message' => "SMTP username ditolak."];
    }
    $r = $send(base64_encode($pass));
    if (substr($r, 0, 3) !== '235') {
        fclose($fp);
        return ['success' => false, 'message' => "SMTP password ditolak. Periksa kredensial SMTP."];
    }

    // MAIL FROM
    $r = $send("MAIL FROM: <{$fromEmail}>");
    if (substr($r, 0, 3) !== '250') {
        fclose($fp);
        return ['success' => false, 'message' => "MAIL FROM gagal: {$r}"];
    }

    // RCPT TO
    $r = $send("RCPT TO: <{$to}>");
    if (substr($r, 0, 3) !== '250') {
        fclose($fp);
        return ['success' => false, 'message' => "RCPT TO gagal: {$r}"];
    }

    // DATA
    $r = $send("DATA");
    if (substr($r, 0, 3) !== '354') {
        fclose($fp);
        return ['success' => false, 'message' => "DATA gagal: {$r}"];
    }

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $date = date('r');
    $msg  = "Date: {$date}\r\n";
    $msg .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
    $msg .= "To: <{$to}>\r\n";
    $msg .= "Subject: {$encodedSubject}\r\n";
    $msg .= "MIME-Version: 1.0\r\n";
    $msg .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $msg .= "Content-Transfer-Encoding: base64\r\n";
    $msg .= "\r\n";
    $msg .= chunk_split(base64_encode($body), 76, "\r\n");
    $msg .= "\r\n.\r\n";
    fwrite($fp, $msg);
    $r = $recv();
    if (substr($r, 0, 3) !== '250') {
        fclose($fp);
        return ['success' => false, 'message' => "Pengiriman pesan gagal: {$r}"];
    }

    $send("QUIT");
    fclose($fp);
    return ['success' => true, 'message' => "Email berhasil dikirim via SMTP ke {$to}."];
}

// ============================================================
// Navigation Helper
// ============================================================

function activeClass(string $page, string $current): string {
    // Also match detail pages (e.g. portofolio-detail matches portofolio)
    if ($page === $current) return 'active';
    if (strpos($current, $page . '-') === 0) return 'active';
    return '';
}

// ============================================================
// Invoice System
// ============================================================

// ---- Info Perusahaan (edit sesuai kebutuhan) ---------------
if (!defined('INV_COMPANY_NAME'))    define('INV_COMPANY_NAME',    'CV. Kayaswara');
if (!defined('INV_COMPANY_TAGLINE')) define('INV_COMPANY_TAGLINE', 'Jasa Penerbitan & Pencetakan Buku Akademik Profesional');
if (!defined('INV_COMPANY_ADDRESS')) define('INV_COMPANY_ADDRESS', 'Jln. Sunan Kalijaga Timur 10, Kec. Larangan, Kota Tangerang, Banten');
if (!defined('INV_COMPANY_PHONE'))   define('INV_COMPANY_PHONE',   '081213169703');
if (!defined('INV_COMPANY_EMAIL'))   define('INV_COMPANY_EMAIL',   'kayaswara.jurnal@gmail.com');
if (!defined('INV_COMPANY_NPWP'))    define('INV_COMPANY_NPWP',    'XX.XXX.XXX.X-XXX.XXX'); // ganti dengan NPWP asli

// ---- Rekening Pembayaran -----------------------------------
function getInvoiceBankAccounts(): array {
    return [
        ['bank' => 'Bank [Nama Bank 1]',  'rekening' => 'XXXX-XXXX-XXXX', 'atas_nama' => '[Nama Pemilik]'],
        ['bank' => 'Bank [Nama Bank 2]',  'rekening' => 'XXXX-XXXX-XXXX', 'atas_nama' => '[Nama Pemilik]'],
    ];
}

function generateInvoiceNumber(): string {
    $year = date('Y');
    $last = fetch("SELECT invoice_number FROM invoices WHERE invoice_number LIKE ? ORDER BY id DESC LIMIT 1", ["INV-{$year}-%"]);
    if ($last) {
        $seq = (int)substr($last['invoice_number'], strrpos($last['invoice_number'], '-') + 1) + 1;
    } else {
        $seq = 1;
    }
    return sprintf('INV-%s-%03d', $year, $seq);
}

function generateInvoiceToken(): string {
    return bin2hex(random_bytes(24));
}

function calculateInvoiceTotals(int $subtotal, int $discount, bool $showPph23 = true): array {
    $afterDiscount = $subtotal - $discount;
    $pph23  = $showPph23 ? (int)round($afterDiscount * 0.02) : 0;
    $total  = $afterDiscount - $pph23;
    return [
        'subtotal'       => $subtotal,
        'discount'       => $discount,
        'after_discount' => $afterDiscount,
        'pph23'          => $pph23,
        'total'          => $total,
    ];
}

function _pdfText(string $s): string {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $s) ?: $s;
}

function _hexToRgb(string $hex): array {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return [(int)hexdec(substr($hex,0,2)), (int)hexdec(substr($hex,2,2)), (int)hexdec(substr($hex,4,2))];
}

function generateInvoicePDF(array $inv, array $order): string {
    require_once __DIR__ . '/fpdf/fpdf.php';

    if (!class_exists('SnAdaPDF')) {
        class SnAdaPDF extends FPDF {
            protected $angle      = 0;
            protected $extgstates = [];
            public    $wmText     = '';
            public    $wmColor    = [220, 220, 220];
            public    $footerTxt  = '';

            // ── PDF transparency (ExtGState) support ──────────────────────
            function SetAlpha($alpha, $bm = 'Normal') {
                $n = count($this->extgstates) + 1;
                $this->extgstates[$n] = [
                    'parms' => ['ca' => $alpha, 'CA' => $alpha, 'BM' => '/'.$bm]
                ];
                $this->_out(sprintf('/GS%d gs', $n));
            }

            function _putextgstates() {
                for ($i = 1; $i <= count($this->extgstates); $i++) {
                    $this->_newobj();
                    $this->extgstates[$i]['n'] = $this->n;
                    $p = $this->extgstates[$i]['parms'];
                    $this->_put(sprintf(
                        '<</Type /ExtGState /ca %.3F /CA %.3F /BM %s>>',
                        $p['ca'], $p['CA'], $p['BM']
                    ));
                    $this->_put('endobj');
                }
            }

            function _putresourcedict() {
                parent::_putresourcedict();
                if (!empty($this->extgstates)) {
                    $this->_put('/ExtGState <<');
                    foreach ($this->extgstates as $k => $gs) {
                        $this->_put('/GS' . $k . ' ' . $gs['n'] . ' 0 R');
                    }
                    $this->_put('>>');
                }
            }

            function _putresources() {
                $this->_putextgstates();
                parent::_putresources();
            }

            // ── Header: watermark drawn FIRST so content renders on top ───
            function Header() {
                if (!empty($this->wmText)) {
                    $this->DrawWatermark($this->wmText, $this->wmColor);
                }
            }

            // ── Footer: separator line + page text only ───────────────────
            function Footer() {
                $lgray = [100, 116, 139];
                $pW    = $this->GetPageWidth();
                $this->SetY(-14);
                $this->SetDrawColor(...$lgray);
                $this->SetLineWidth(0.2);
                $this->Line(15, $this->GetY(), $pW - 15, $this->GetY());
                $this->SetFont('Helvetica', 'I', 7.5);
                $this->SetTextColor(...$lgray);
                $this->Cell(0, 6, $this->footerTxt, 0, 0, 'C');
            }

            // ── Rotation helpers ──────────────────────────────────────────
            function Rotate($angle, $x = -1, $y = -1) {
                if ($x === -1) $x = $this->x;
                if ($y === -1) $y = $this->y;
                if ($this->angle !== 0) $this->_out('Q');
                $this->angle = $angle;
                if ($angle !== 0) {
                    $rad = $angle * M_PI / 180;
                    $c = cos($rad); $s = sin($rad);
                    $cx = $x * $this->k; $cy = ($this->h - $y) * $this->k;
                    $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.5F %.5F cm',
                        $c, $s, -$s, $c,
                        $cx * (1 - $c) + $cy * $s,
                        $cy * (1 - $c) - $cx * $s));
                }
            }

            function _endpage() {
                if ($this->angle !== 0) { $this->angle = 0; $this->_out('Q'); }
                parent::_endpage();
            }

            // ── Watermark stamp (50% opacity, centred, 45° diagonal) ──────
            function DrawWatermark($text, $color = [220, 220, 220]) {
                $pw = $this->GetPageWidth(); $ph = $this->GetPageHeight();
                $cx = $pw / 2; $cy = $ph / 2;
                list($r, $g, $b) = $color;

                // Apply 50% transparency before drawing anything
                $this->SetAlpha(0.50);

                $this->SetFont('Helvetica', 'B', 90);
                $tw    = $this->GetStringWidth($text);
                $capH  = 90 / (72 / 25.4) * 0.72;
                $descH = 90 / (72 / 25.4) * 0.20;
                $pad   = 5;

                $basY = $cy + ($capH - $descH) / 2;
                $boxX = $cx - $tw / 2 - $pad;
                $boxY = $basY - $capH - $pad;
                $boxW = $tw + $pad * 2;
                $boxH = $capH + $descH + $pad * 2;

                $this->Rotate(45, $cx, $cy);

                $this->SetDrawColor($r, $g, $b);
                $this->SetLineWidth(2.5);
                $this->Rect($boxX, $boxY, $boxW, $boxH, 'D');

                $this->SetLineWidth(0.7);
                $this->Rect($boxX + 3, $boxY + 3, $boxW - 6, $boxH - 6, 'D');

                $this->SetTextColor($r, $g, $b);
                $this->Text($cx - $tw / 2, $basY, $text);

                $this->Rotate(0);

                // Restore full opacity so rest of page is not affected
                $this->SetAlpha(1.0);
                $this->SetLineWidth(0.3);
                $this->SetDrawColor(226, 232, 240);
                $this->SetTextColor(0, 0, 0);
            }
        }
    }

    // Determine watermark  (no watermark for 'sent' — clean invoice)
    $wmMap = [
        'draft'     => ['text' => 'DRAFT',  'color' => [245, 195, 130]],
        'paid'      => ['text' => 'LUNAS',  'color' => [130, 210, 130]],
        'cancelled' => ['text' => 'BATAL',  'color' => [195, 195, 195]],
    ];
    $wm = $wmMap[$inv['status']] ?? null;

    $pdf = new SnAdaPDF('P', 'mm', 'A4');
    if ($wm) { $pdf->wmText = $wm['text']; $pdf->wmColor = $wm['color']; }
    $pdf->footerTxt = _pdfText('Terima kasih atas kepercayaan Anda – ' . INV_COMPANY_NAME . ' | ' . INV_COMPANY_EMAIL . ' | ' . INV_COMPANY_PHONE);
    $pdf->SetMargins(15,15,15);
    $pdf->SetAutoPageBreak(true, 25); // 25mm = room for Footer()
    $pdf->AddPage();

    [$pr,$pg,$pb] = _hexToRgb(getSetting('primary_color','#1A3C5E'));
    $lgray=[100,116,139]; $dark=[30,41,59]; $lbg=[248,250,252];
    $pW=$pdf->GetPageWidth(); $cW=$pW-30;

    // Header bar
    $pdf->SetFillColor($pr,$pg,$pb);
    $pdf->Rect(0,0,$pW,28,'F');

    // Logo (optional) — left side of header bar
    $logoX    = 15; $logoEndX = 15;
    $logoFile = getSetting('logo_path', '');
    $logoPath = dirname(__DIR__) . '/assets/uploads/site/' . $logoFile;
    if ($logoFile && file_exists($logoPath) &&
        in_array(strtolower(pathinfo($logoPath, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif'])) {
        $dim = @getimagesize($logoPath);
        if ($dim && $dim[0] > 0 && $dim[1] > 0) {
            $logoH  = 20.0; // target height mm
            $logoW  = round($logoH * $dim[0] / $dim[1], 2);
            if ($logoW > 38) { $logoW = 38.0; $logoH = round($logoW * $dim[1] / $dim[0], 2); }
            $logoY  = (28 - $logoH) / 2; // vertically centred in bar
            try { $pdf->Image($logoPath, $logoX, $logoY, $logoW, $logoH); } catch (\Throwable $e) {}
            $logoEndX = $logoX + $logoW + 4;
        }
    }

    // Company name + tagline (left), INVOICE label + number (right)
    $txtW = $cW - ($logoEndX - 15);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Helvetica','B',16); $pdf->SetXY($logoEndX, 5);
    $pdf->Cell($txtW * 0.55, 9, _pdfText(INV_COMPANY_NAME), 0, 0, 'L');
    $pdf->SetFont('Helvetica','B',14); $pdf->SetXY(15, 5);
    $pdf->Cell($cW, 9, 'INVOICE', 0, 0, 'R');
    $pdf->SetFont('Helvetica','', 8); $pdf->SetXY($logoEndX, 16);
    $pdf->Cell($txtW * 0.55, 5, _pdfText(INV_COMPANY_TAGLINE), 0, 0, 'L');
    $pdf->SetXY(15, 16); $pdf->Cell($cW, 5, _pdfText($inv['invoice_number']), 0, 0, 'R');

    // Company info (left)
    $y=36;
    $pdf->SetFont('Helvetica','B',9); $pdf->SetTextColor(...$dark); $pdf->SetXY(15,$y);
    $pdf->Cell(85,5,_pdfText(INV_COMPANY_NAME),0,2,'L');
    foreach([INV_COMPANY_TAGLINE,INV_COMPANY_ADDRESS,'Tel: '.INV_COMPANY_PHONE,'Email: '.INV_COMPANY_EMAIL,'NPWP: '.INV_COMPANY_NPWP] as $ci){
        $pdf->SetFont('Helvetica','',8); $pdf->SetTextColor(...$lgray); $pdf->SetX(15);
        $pdf->Cell(85,4.5,_pdfText($ci),0,2,'L');
    }

    // Invoice meta (right)
    $serviceOpts=['setup_ojs'=>'Penerbitan Buku','migrasi'=>'Konversi KTI','kustomisasi'=>'Editing & Layout','pelatihan'=>'Desain Cover','maintenance'=>'Distribusi & Pemasaran','indeksasi_doaj'=>'Cek Plagiasi','indeksasi_sinta'=>'Konsultasi Penulisan','lainnya'=>'Lainnya'];
    $packageOpts=['basic'=>'Basic','professional'=>'Professional','premium'=>'Premium','custom'=>'Custom'];
    $statusLabels=['draft'=>'DRAFT','sent'=>'TERKIRIM','paid'=>'LUNAS','cancelled'=>'BATAL'];
    $rx=110; $lW=42; $vW=$cW-($rx-15)-$lW; $ry=$y;
    foreach([
        ['No. Invoice',$inv['invoice_number']],
        ['Tanggal',date('d/m/Y',strtotime($inv['created_at']))],
        ['Jatuh Tempo',$inv['due_date']?date('d/m/Y',strtotime($inv['due_date'])):'-'],
        ['Status',$statusLabels[$inv['status']]??strtoupper($inv['status'])],
    ] as [$l,$v]){
        $pdf->SetFont('Helvetica','',8); $pdf->SetTextColor(...$lgray); $pdf->SetXY($rx,$ry);
        $pdf->Cell($lW,5.5,_pdfText($l),0,0,'L');
        $pdf->SetFont('Helvetica','B',8); $pdf->SetTextColor(...$dark);
        $pdf->Cell($vW,5.5,_pdfText($v),0,0,'L'); $ry+=5.5;
    }

    // Divider
    $pdf->SetDrawColor(226,232,240); $pdf->SetLineWidth(0.3);
    $yDiv=max($pdf->GetY(),$ry)+5;
    $pdf->Line(15,$yDiv,$pW-15,$yDiv); $y=$yDiv+6;

    // Bill To box
    $pdf->SetFillColor(...$lbg); $pdf->Rect(15,$y,$cW*0.55,28,'F');
    $pdf->SetFont('Helvetica','B',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetXY(18,$y+2);
    $pdf->Cell(0,5,'TAGIHAN KEPADA',0,2,'L');
    $pdf->SetFont('Helvetica','B',9); $pdf->SetTextColor(...$dark); $pdf->SetX(18);
    $pdf->Cell(0,5.5,_pdfText($order['client_name']),0,2,'L');
    $pdf->SetFont('Helvetica','',8); $pdf->SetTextColor(...$lgray);
    foreach(array_filter([$order['client_institution']??'',$order['client_email']??'',$order['client_phone']??'']) as $cf){
        $pdf->SetX(18); $pdf->Cell(0,4.5,_pdfText($cf),0,2,'L');
    }

    // Order meta box
    $pdf->SetFillColor(240,253,244);
    $ox=15+$cW*0.58; $ow=$cW*0.42;
    $pdf->Rect($ox,$y,$ow,28,'F');
    $pdf->SetFont('Helvetica','B',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetXY($ox+3,$y+2);
    $pdf->Cell(0,5,'DETAIL PESANAN',0,2,'L');
    foreach([['Kode',$order['tracking_code']],['Layanan',$serviceOpts[$order['service_type']]??$order['service_type']],['Paket',$packageOpts[$order['package_tier']]??$order['package_tier']]] as [$ol,$ov]){
        $pdf->SetFont('Helvetica','',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetX($ox+3);
        $pdf->Cell(18,4.5,_pdfText($ol.':'),0,0,'L');
        $pdf->SetFont('Helvetica','B',7.5); $pdf->SetTextColor(...$dark);
        $pdf->Cell($ow-22,4.5,_pdfText($ov),0,2,'L');
    }
    $y+=34;

    // Items table header
    $pdf->SetFillColor($pr,$pg,$pb); $pdf->SetTextColor(255,255,255); $pdf->SetFont('Helvetica','B',9);
    $c1=$cW*0.6; $c2=$cW*0.15; $c3=$cW*0.25;
    $pdf->SetXY(15,$y);
    $pdf->Cell($c1,7,'DESKRIPSI',0,0,'L');
    $pdf->Cell($c2,7,'QTY',0,0,'C');
    $pdf->Cell($c3,7,'JUMLAH',0,0,'R');
    $y+=7;

    // Parse add-ons from order
    $addonItems = [];
    if (!empty($order['addons'])) {
        $addonItems = json_decode($order['addons'], true) ?: [];
    }
    $addonsTotal = 0;
    foreach ($addonItems as $ai) { $addonsTotal += (int)($ai['price'] ?? 0); }

    // Main service row — show base price (inv['subtotal'] minus addons) if addons exist, else full subtotal
    $basePrice = $addonsTotal > 0 ? max(0, (int)$inv['subtotal'] - $addonsTotal) : (int)$inv['subtotal'];

    $pdf->SetFillColor(...$lbg); $pdf->SetTextColor(...$dark); $pdf->SetFont('Helvetica','B',9);
    $svcLabel=($serviceOpts[$order['service_type']]??$order['service_type']).' - '.($packageOpts[$order['package_tier']]??$order['package_tier']);
    $pdf->SetXY(15,$y);
    $pdf->Cell($c1,6.5,_pdfText($svcLabel),0,0,'L');
    $pdf->Cell($c2,6.5,'1',0,0,'C');
    $pdf->SetFont('Helvetica','',9);
    $pdf->Cell($c3,6.5,'Rp '.number_format($basePrice,0,',','.'),0,0,'R');
    $y+=7;

    if(!empty($order['description'])){
        $desc=strip_tags($order['description']);
        $short=mb_strlen($desc)>100?mb_substr($desc,0,100).'...':$desc;
        $pdf->SetFont('Helvetica','I',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetXY(15,$y);
        $pdf->Cell($c1+$c2,5,_pdfText($short),0,2,'L'); $y+=5;
    }

    // Add-on rows
    foreach ($addonItems as $ai) {
        $pdf->SetFillColor(255,255,255); $pdf->SetTextColor(...$dark); $pdf->SetFont('Helvetica','',8.5);
        $pdf->SetXY(15,$y);
        $pdf->Cell($c1,6,_pdfText('  + '.$ai['name']),0,0,'L');
        $pdf->Cell($c2,6,'1',0,0,'C');
        $pdf->SetFont('Helvetica','',8.5);
        $pdf->Cell($c3,6,'Rp '.number_format((int)($ai['price']??0),0,',','.'),0,0,'R');
        $y+=6;
    }

    // Totals
    $pdf->SetDrawColor(226,232,240); $pdf->SetLineWidth(0.3);
    $pdf->Line(15,$y+2,$pW-15,$y+2); $y+=6;
    $tX=15+$cW*0.55; $tW=$cW*0.45;
    $trows=[['Subtotal',$inv['subtotal'],false]];
    if($inv['discount']>0) $trows[]=['Diskon',-$inv['discount'],false];
    if($inv['show_pph23']&&$inv['tax_pph23']>0) $trows[]=['PPh 23 (2%)*',-$inv['tax_pph23'],false];
    foreach($trows as [$tl,$ta]){
        $pdf->SetFont('Helvetica','',8.5); $pdf->SetTextColor(...$lgray); $pdf->SetXY($tX,$y);
        $pdf->Cell($tW*0.52,5.5,_pdfText($tl),0,0,'L');
        $pdf->SetTextColor(...$dark);
        $pfx=$ta<0?'-Rp ':'Rp ';
        $pdf->Cell($tW*0.48,5.5,$pfx.number_format(abs($ta),0,',','.'),0,0,'R'); $y+=5.5;
    }
    $pdf->SetFillColor($pr,$pg,$pb); $pdf->Rect($tX,$y+2,$tW,8,'F');
    $pdf->SetFont('Helvetica','B',9.5); $pdf->SetTextColor(255,255,255); $pdf->SetXY($tX,$y+2);
    $pdf->Cell($tW*0.52,8,'TOTAL DIBAYAR',0,0,'L');
    $pdf->Cell($tW*0.48,8,'Rp '.number_format($inv['total'],0,',','.'),0,0,'R');
    $y+=16;

    if($inv['show_pph23']&&$inv['tax_pph23']>0){
        $pdf->SetFont('Helvetica','I',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetXY(15,$y);
        $pdf->Cell($cW,5,'*) PPh 23 dipotong oleh pembeli badan usaha sesuai ketentuan perpajakan Indonesia.',0,2,'L'); $y+=6;
    }
    $y+=4;

    // Payment info
    $pdf->SetFillColor(...$lbg); $pdf->Rect(15,$y,$cW,36,'F');
    $pdf->SetFont('Helvetica','B',8.5); $pdf->SetTextColor(...$dark); $pdf->SetXY(18,$y+3);
    $pdf->Cell(0,5,'INFORMASI PEMBAYARAN',0,2,'L');
    $banks=getInvoiceBankAccounts();
    $bW=($cW-6)/max(count($banks)+1,2); $bX=18; $bY=$y+10;
    foreach($banks as $b){
        $pdf->SetFont('Helvetica','B',8); $pdf->SetTextColor(...$dark); $pdf->SetXY($bX,$bY);
        $pdf->Cell($bW,5,_pdfText($b['bank']),0,2,'L');
        $pdf->SetFont('Helvetica','B',8.5); $pdf->SetTextColor($pr,$pg,$pb); $pdf->SetX($bX);
        $pdf->Cell($bW,4.5,_pdfText($b['rekening']),0,2,'L');
        $pdf->SetFont('Helvetica','',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetX($bX);
        $pdf->Cell($bW,4,_pdfText('a.n. '.$b['atas_nama']),0,2,'L');
        $bX+=$bW+3;
    }
    $pdf->SetFont('Helvetica','B',8); $pdf->SetTextColor(...$dark); $pdf->SetXY($bX,$bY);
    $pdf->Cell($bW,5,'QRIS',0,2,'L');
    $pdf->SetFont('Helvetica','',7.5); $pdf->SetTextColor(...$lgray); $pdf->SetX($bX);
    $pdf->Cell($bW,4.5,'Scan QRIS atau hubungi',0,2,'L'); $pdf->SetX($bX);
    $pdf->Cell($bW,4,'kami untuk kode QRIS.',0,2,'L');
    $y+=42;

    // Terms
    $pdf->SetFont('Helvetica','B',8); $pdf->SetTextColor(...$lgray); $pdf->SetXY(15,$y);
    $pdf->Cell(0,5,'SYARAT & KETENTUAN',0,2,'L');
    $pdf->SetFont('Helvetica','',7.5);
    foreach(['1. Minimal DP 50% dari total sebelum pengerjaan dimulai.','2. Pelunasan sebelum file/akses diserahkan.','3. Invoice berlaku 3 hari kerja sejak diterbitkan.','4. Garansi revisi 7 hari setelah pekerjaan diserahkan.','5. Refund sesuai kebijakan refund yang berlaku.'] as $t){
        $pdf->SetX(15); $pdf->Cell(0,4.5,_pdfText($t),0,2,'L');
    }

    $dir=defined('ROOT_PATH')?ROOT_PATH.'/invoices':dirname(__DIR__).'/invoices';
    if(!is_dir($dir)) mkdir($dir,0755,true);
    $fn=preg_replace('/[^A-Za-z0-9\-]/','',str_replace(' ','',$inv['invoice_number'])).'.pdf';
    $fp=$dir.'/'.$fn;
    $pdf->Output('F',$fp);
    return $fp;
}

function sendInvoiceEmail(array $inv, array $order): array {
    if(empty($order['client_email'])) return['success'=>false,'message'=>'No client email.'];
    $siteName=html_entity_decode(getSetting('site_name','Kayaswara'),ENT_QUOTES,'UTF-8');
    $siteUrl=defined('SITE_URL')?rtrim(SITE_URL,'/') :(defined('APP_URL')?rtrim(APP_URL,'/'):'');
    $dlUrl=$siteUrl.'/invoice/'.$inv['token'];
    $due=$inv['due_date']?date('d F Y',strtotime($inv['due_date'])):'-';
    $subject="[{$siteName}] Invoice ".$inv['invoice_number']." – Rp ".number_format($inv['total'],0,',','.');
    $body ="Yth. {$order['client_name']},\r\n\r\n";
    $body.="Bersama email ini kami sampaikan invoice untuk pesanan Anda.\r\n\r\n";
    $body.="====================================\r\n INVOICE ".$inv['invoice_number']."\r\n====================================\r\n";
    $body.="Kode Pesanan : {$order['tracking_code']}\r\n";
    $body.="Tanggal      : ".date('d F Y',strtotime($inv['created_at']))."\r\n";
    $body.="Jatuh Tempo  : {$due}\r\n";
    $body.="Total Bayar  : Rp ".number_format($inv['total'],0,',','.')."\r\n";
    if($inv['tax_pph23']>0&&$inv['show_pph23']) $body.="(PPh 23 2%: Rp ".number_format($inv['tax_pph23'],0,',','.')." dipotong oleh pembeli badan usaha)\r\n";
    // Add-ons breakdown
    if (!empty($order['addons'])) {
        $addonsArr = json_decode($order['addons'], true) ?: [];
        if (!empty($addonsArr)) {
            $body .= "\r\nRincian Layanan Tambahan:\r\n";
            foreach ($addonsArr as $a) {
                $body .= "  + " . $a['name'] . " : Rp " . number_format((int)($a['price'] ?? 0), 0, ',', '.') . "\r\n";
            }
        }
    }
    $body.="====================================\r\n\r\n";
    $body.="Unduh invoice PDF di:\r\n{$dlUrl}\r\n\r\n";
    $body.="Minimal DP 50%: Rp ".number_format((int)ceil($inv['total']/2),0,',','.')."\r\n\r\n";
    $body.="Hormat kami,\r\n{$siteName}";
    return sendSiteEmail($order['client_email'],$subject,$body);
}
