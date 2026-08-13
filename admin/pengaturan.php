<?php
/**
 * Admin Pengaturan (Settings) - Tabbed interface
 */

$errors  = [];
$success = '';
$active_tab = sanitize($_GET['tab'] ?? 'umum');

// Allowed tabs
$valid_tabs = ['umum', 'tampilan', 'notifikasi', 'akun'];
if (!in_array($active_tab, $valid_tabs)) {
    $active_tab = 'umum';
}

// Handle AJAX test Email SMTP
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    && ($_POST['action'] ?? '') === 'test_email'
) {
    header('Content-Type: application/json; charset=utf-8');
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Token keamanan tidak valid.']);
        exit;
    }
    $testTo = trim(sanitize($_POST['email'] ?? ''));
    if (empty($testTo) || !filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Masukkan alamat email tujuan yang valid.']);
        exit;
    }
    $siteName = getSetting('site_name', 'Kayaswara');
    $result = sendSiteEmail($testTo, "[{$siteName}] Tes Email Berhasil", "Ini adalah pesan tes dari {$siteName}.\n\nJika Anda menerima email ini, konfigurasi email berhasil.");
    echo json_encode($result);
    exit;
}

// Handle AJAX test WA
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    && ($_POST['action'] ?? '') === 'test_wa'
) {
    header('Content-Type: application/json; charset=utf-8');
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Token keamanan tidak valid.']);
        exit;
    }
    $testPhone = trim(sanitize($_POST['phone'] ?? ''));
    if (empty($testPhone)) {
        echo json_encode(['success' => false, 'message' => 'Masukkan nomor tujuan.']);
        exit;
    }
    $testMsg = "Ini adalah pesan tes dari " . getSetting('site_name', 'Kayaswara') . ".\n\n"
        . "Jika Anda menerima pesan ini, notifikasi WhatsApp berhasil dikonfigurasi. \xE2\x9C\x85";
    $result = sendWhatsAppNotification($testPhone, $testMsg);
    echo json_encode($result);
    exit;
}

// Handle delete logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_logo') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=pengaturan&tab=tampilan');
    }
    $old_logo = getSetting('logo_path');
    if ($old_logo) {
        deleteImage('site/' . $old_logo);
        setSetting('logo_path', '');
        flash('success', 'Logo berhasil dihapus.');
    }
    redirect('index.php?page=pengaturan&tab=tampilan');
}

// Handle settings save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=pengaturan&tab=' . $active_tab);
    }

    $form_tab = sanitize($_POST['tab'] ?? 'umum');

    // --- Tab 1: Umum ---
    if ($form_tab === 'umum') {
        $settings_umum = [
            'site_name'        => sanitize($_POST['site_name'] ?? ''),
            'site_tagline'     => sanitize($_POST['site_tagline'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
            'email_contact'    => sanitize($_POST['email_contact'] ?? ''),
            'whatsapp_number'  => sanitize($_POST['whatsapp_number'] ?? ''),
            'address'          => sanitize($_POST['address'] ?? ''),
            'footer_text'      => sanitize($_POST['footer_text'] ?? ''),
        ];

        if (empty($settings_umum['site_name'])) {
            $errors[] = 'Nama situs wajib diisi.';
        }

        if (empty($errors)) {
            try {
                foreach ($settings_umum as $key => $value) {
                    setSetting($key, $value);
                }
                flash('success', 'Pengaturan umum berhasil disimpan.');
                redirect('index.php?page=pengaturan&tab=umum');
            } catch (Exception $e) {
                $errors[] = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
            }
        }
    }

    // --- Tab 2: Tampilan ---
    if ($form_tab === 'tampilan') {
        $settings_tampilan = [
            'primary_color'   => sanitize($_POST['primary_color'] ?? '#1A3C5E'),
            'secondary_color' => sanitize($_POST['secondary_color'] ?? '#1A3C5E'),
            'accent_color'    => sanitize($_POST['accent_color'] ?? '#1A3C5E'),
        ];

        // Logo upload
        if (!empty($_FILES['logo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $errors[] = 'Format logo tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
            } elseif ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Ukuran logo terlalu besar (maks 2MB).';
            } else {
                $logo_path = uploadImage($_FILES['logo'], 'site');
                if ($logo_path) {
                    $old_logo = getSetting('logo_path');
                    if ($old_logo) deleteImage('site/' . $old_logo);
                    $settings_tampilan['logo_path'] = $logo_path;
                } else {
                    $errors[] = 'Gagal mengunggah logo.';
                }
            }
        }

        // Favicon upload
        if (!empty($_FILES['favicon']['name'])) {
            $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['ico','png','jpg','jpeg'])) {
                $errors[] = 'Format favicon tidak didukung. Gunakan ICO atau PNG.';
            } elseif ($_FILES['favicon']['size'] > 512 * 1024) {
                $errors[] = 'Ukuran favicon terlalu besar (maks 512KB).';
            } else {
                $fav_path = uploadImage($_FILES['favicon'], 'site');
                if ($fav_path) {
                    $old_fav = getSetting('favicon_path');
                    if ($old_fav) deleteImage('site/' . $old_fav);
                    $settings_tampilan['favicon_path'] = $fav_path;
                } else {
                    $errors[] = 'Gagal mengunggah favicon.';
                }
            }
        }

        if (empty($errors)) {
            try {
                foreach ($settings_tampilan as $key => $value) {
                    setSetting($key, $value);
                }
                flash('success', 'Pengaturan tampilan berhasil disimpan.');
                redirect('index.php?page=pengaturan&tab=tampilan');
            } catch (Exception $e) {
                $errors[] = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
            }
        }
    }

    // --- Tab 3: Notifikasi WA + Email ---
    if ($form_tab === 'notifikasi') {
        $wa_settings = [
            'wa_notif_enabled'    => isset($_POST['wa_notif_enabled']) ? '1' : '0',
            'wa_api_provider'     => sanitize($_POST['wa_api_provider'] ?? 'fonnte'),
            'wa_api_token'        => trim($_POST['wa_api_token'] ?? ''),
            'wa_api_url'          => sanitize(trim($_POST['wa_api_url'] ?? '')),
            'wa_notif_template'   => trim($_POST['wa_notif_template'] ?? ''),
            'smtp_enabled'        => isset($_POST['smtp_enabled']) ? '1' : '0',
            'smtp_host'           => sanitize(trim($_POST['smtp_host'] ?? '')),
            'smtp_port'           => sanitize(trim($_POST['smtp_port'] ?? '587')),
            'smtp_user'           => sanitize(trim($_POST['smtp_user'] ?? '')),
            'smtp_pass'           => trim($_POST['smtp_pass'] ?? ''),
            'smtp_encryption'     => sanitize($_POST['smtp_encryption'] ?? 'tls'),
            'smtp_from_name'      => sanitize(trim($_POST['smtp_from_name'] ?? '')),
        ];

        $validProviders = ['fonnte', 'wablas', 'custom'];
        if (!in_array($wa_settings['wa_api_provider'], $validProviders)) {
            $errors[] = 'Provider WA API tidak valid.';
        }
        if ($wa_settings['wa_notif_enabled'] === '1' && empty($wa_settings['wa_api_token'])) {
            $errors[] = 'API Token wajib diisi jika notifikasi diaktifkan.';
        }
        if ($wa_settings['wa_api_provider'] === 'custom' && $wa_settings['wa_notif_enabled'] === '1' && empty($wa_settings['wa_api_url'])) {
            $errors[] = 'URL API wajib diisi untuk provider custom.';
        }
        if ($wa_settings['smtp_enabled'] === '1') {
            if (empty($wa_settings['smtp_host'])) $errors[] = 'SMTP Host wajib diisi jika email diaktifkan.';
            if (empty($wa_settings['smtp_user'])) $errors[] = 'SMTP Username/email wajib diisi.';
            if (empty($wa_settings['smtp_pass'])) $errors[] = 'SMTP Password wajib diisi.';
        }
        // Keep old smtp_pass if left blank (don't overwrite with empty)
        if (empty($wa_settings['smtp_pass'])) {
            unset($wa_settings['smtp_pass']); // don't overwrite existing password
        }

        if (empty($errors)) {
            try {
                foreach ($wa_settings as $key => $value) {
                    setSetting($key, $value);
                }
                flash('success', 'Pengaturan notifikasi berhasil disimpan.');
                redirect('index.php?page=pengaturan&tab=notifikasi');
            } catch (Exception $e) {
                $errors[] = 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
    }

    // --- Tab 4: Akun Admin ---
    if ($form_tab === 'akun') {
        $old_password  = $_POST['old_password'] ?? '';
        $new_password  = $_POST['new_password'] ?? '';
        $confirm_pass  = $_POST['confirm_password'] ?? '';

        if (empty($old_password) || empty($new_password) || empty($confirm_pass)) {
            $errors[] = 'Semua kolom password wajib diisi.';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'Password baru minimal 8 karakter.';
        } elseif ($new_password !== $confirm_pass) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        } else {
            try {
                $admin = fetch("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']]);
                if (!$admin || !password_verify($old_password, $admin['password'])) {
                    $errors[] = 'Password lama tidak benar.';
                } else {
                    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                    update('admins', ['password' => $hashed], 'id = ?', [$_SESSION['admin_id']]);
                    flash('success', 'Password berhasil diubah.');
                    redirect('index.php?page=pengaturan&tab=akun');
                }
            } catch (Exception $e) {
                $errors[] = 'Gagal mengubah password: ' . $e->getMessage();
            }
        }
    }
}

// Load current settings
$settings = [];
try {
    $settings = getAllSettings();
} catch (Exception $e) {}

// Defaults
$defaults = [
    'site_name'        => 'Kayaswara',
    'site_tagline'     => 'Jasa Penerbitan & Pencetakan Buku Akademik Profesional',
    'meta_description' => 'Jasa penerbitan buku akademik profesional untuk dosen dan akademisi Indonesia.',
    'email_contact'    => '',
    'whatsapp_number'  => '',
    'address'          => '',
    'footer_text'      => '© ' . date('Y') . ' Kayaswara. All rights reserved.',
    'primary_color'    => '#1A3C5E',
    'secondary_color'  => '#1A3C5E',
    'accent_color'     => '#1A3C5E',
    'logo_path'        => '',
    'favicon_path'     => '',
    'wa_notif_enabled'  => '0',
    'wa_api_provider'   => 'fonnte',
    'wa_api_token'      => '',
    'wa_api_url'        => '',
    'wa_notif_template' => '',
    'smtp_enabled'      => '0',
    'smtp_host'         => '',
    'smtp_port'         => '587',
    'smtp_user'         => '',
    'smtp_pass'         => '',
    'smtp_encryption'   => 'tls',
    'smtp_from_name'    => '',
];

foreach ($defaults as $k => $v) {
    if (!isset($settings[$k])) {
        $settings[$k] = $v;
    }
}

$csrf = csrf_token();

require_once ADMIN_PATH . '/includes/header.php';
require_once ADMIN_PATH . '/includes/sidebar.php';
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h2>Pengaturan Situs</h2>
            <p>Konfigurasi identitas, tampilan, dan keamanan website Anda.</p>
        </div>
    </div>

    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger mb-4">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <div class="admin-card mb-0" style="border-bottom:none;border-radius:14px 14px 0 0;">
        <div style="padding:0 8px;">
            <ul class="nav nav-tabs border-0" style="margin:0;">
                <li class="nav-item">
                    <a class="nav-link <?= $active_tab === 'umum' ? 'active' : '' ?>"
                       href="index.php?page=pengaturan&tab=umum">
                        <i class="fas fa-globe"></i>
                        Umum
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_tab === 'tampilan' ? 'active' : '' ?>"
                       href="index.php?page=pengaturan&tab=tampilan">
                        <i class="fas fa-palette"></i>
                        Tampilan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_tab === 'notifikasi' ? 'active' : '' ?>"
                       href="index.php?page=pengaturan&tab=notifikasi">
                        <i class="fas fa-bell"></i>
                        Notifikasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_tab === 'akun' ? 'active' : '' ?>"
                       href="index.php?page=pengaturan&tab=akun">
                        <i class="fas fa-shield-alt"></i>
                        Keamanan Akun
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="admin-card" style="border-radius:0 0 14px 14px;border-top:none;">
        <div class="admin-card-body">

            <!-- ===================== TAB: UMUM ===================== -->
            <?php if ($active_tab === 'umum'): ?>
            <form method="POST" data-loading>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="tab" value="umum">

                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            Identitas Situs
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="site_name">
                                    Nama Situs <span class="required">*</span>
                                </label>
                                <input type="text" id="site_name" name="site_name"
                                       class="form-control"
                                       value="<?= htmlspecialchars($settings['site_name']) ?>"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="site_tagline">Tagline</label>
                                <input type="text" id="site_tagline" name="site_tagline"
                                       class="form-control"
                                       value="<?= htmlspecialchars($settings['site_tagline']) ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="meta_description">
                                Deskripsi Meta (SEO)
                                <small class="text-muted fw-normal">(maks. 160 karakter)</small>
                            </label>
                            <textarea id="meta_description" name="meta_description"
                                      class="form-control" rows="3"
                                      maxlength="160"><?= htmlspecialchars($settings['meta_description']) ?></textarea>
                        </div>

                        <div class="form-section-title">
                            <i class="fas fa-address-book"></i>
                            Kontak
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="email_contact">
                                    <i class="fas fa-envelope me-1" style="color:#1A3C5E;"></i>
                                    Email Kontak
                                </label>
                                <input type="email" id="email_contact" name="email_contact"
                                       class="form-control"
                                       placeholder="kayaswara.jurnal@gmail.com"
                                       value="<?= htmlspecialchars($settings['email_contact']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="whatsapp_number">
                                    <i class="fab fa-whatsapp me-1" style="color:#16a34a;"></i>
                                    Nomor WhatsApp
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8fafc;font-size:12px;">+62</span>
                                    <input type="text" id="whatsapp_number" name="whatsapp_number"
                                           class="form-control"
                                           placeholder="81234567890"
                                           value="<?= htmlspecialchars($settings['whatsapp_number']) ?>">
                                </div>
                                <div class="form-hint">Tanpa angka 0 di depan. Contoh: 81234567890</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="address">Alamat Lengkap</label>
                            <textarea id="address" name="address"
                                      class="form-control" rows="3"
                                      placeholder="Jl. Contoh No. 123, Kota, Provinsi, Indonesia"><?= htmlspecialchars($settings['address']) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="footer_text">Teks Footer</label>
                            <input type="text" id="footer_text" name="footer_text"
                                   class="form-control"
                                   value="<?= htmlspecialchars($settings['footer_text']) ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan Umum
                        </button>
                    </div>

                    <div class="col-xl-4">
                        <div style="background:linear-gradient(135deg,rgba(26,60,94,0.06),rgba(26,54,93,0.06));border-radius:14px;padding:24px;border:1px solid rgba(26,60,94,0.15);">
                            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:14px;">
                                <i class="fas fa-lightbulb me-1" style="color:#1A3C5E;"></i>
                                Panduan
                            </div>
                            <ul style="font-size:13px;color:#64748b;padding-left:18px;margin:0;line-height:2;">
                                <li><strong>Nama Situs</strong>: tampil di browser tab & header</li>
                                <li><strong>Tagline</strong>: slogan singkat di bawah logo</li>
                                <li><strong>Meta Deskripsi</strong>: untuk SEO Google (maks 160 karakter)</li>
                                <li><strong>WA Number</strong>: digunakan untuk tombol "Hubungi" di website</li>
                                <li><strong>Footer Text</strong>: teks hak cipta di bagian bawah website</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ===================== TAB: TAMPILAN ===================== -->
            <?php elseif ($active_tab === 'tampilan'): ?>
            <form method="POST" enctype="multipart/form-data" data-loading id="tampilanForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="tab" value="tampilan">

                <div class="row g-4">
                    <!-- Left: Color Pickers -->
                    <div class="col-xl-6">
                        <div class="form-section-title">
                            <i class="fas fa-palette"></i>
                            Skema Warna
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="primary_color">Warna Primer</label>
                            <div class="input-group">
                                <input type="color" id="primary_color" name="primary_color"
                                       class="form-control form-control-color"
                                       value="<?= htmlspecialchars($settings['primary_color']) ?>"
                                       style="width:50px;padding:4px;"
                                       oninput="updateColorPreview()">
                                <input type="text" id="primary_hex" class="form-control"
                                       value="<?= htmlspecialchars($settings['primary_color']) ?>"
                                       style="max-width:120px;"
                                       oninput="syncColor('primary_color', this.value)">
                                <span class="input-group-text" style="font-size:12px;color:#64748b;">Navigasi, heading</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="secondary_color">Warna Sekunder</label>
                            <div class="input-group">
                                <input type="color" id="secondary_color" name="secondary_color"
                                       class="form-control form-control-color"
                                       value="<?= htmlspecialchars($settings['secondary_color']) ?>"
                                       style="width:50px;padding:4px;"
                                       oninput="updateColorPreview()">
                                <input type="text" id="secondary_hex" class="form-control"
                                       value="<?= htmlspecialchars($settings['secondary_color']) ?>"
                                       style="max-width:120px;"
                                       oninput="syncColor('secondary_color', this.value)">
                                <span class="input-group-text" style="font-size:12px;color:#64748b;">Aksen, tombol sekunder</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="accent_color">Warna Aksen</label>
                            <div class="input-group">
                                <input type="color" id="accent_color" name="accent_color"
                                       class="form-control form-control-color"
                                       value="<?= htmlspecialchars($settings['accent_color']) ?>"
                                       style="width:50px;padding:4px;"
                                       oninput="updateColorPreview()">
                                <input type="text" id="accent_hex" class="form-control"
                                       value="<?= htmlspecialchars($settings['accent_color']) ?>"
                                       style="max-width:120px;"
                                       oninput="syncColor('accent_color', this.value)">
                                <span class="input-group-text" style="font-size:12px;color:#64748b;">CTA, badge highlight</span>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-image"></i>
                            Logo & Favicon
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="logo">Logo Website</label>
                            <?php if (!empty($settings['logo_path'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="../assets/uploads/site/<?= htmlspecialchars($settings['logo_path']) ?>"
                                     alt="Current Logo" id="logoPreview"
                                     style="height:60px;max-width:200px;object-fit:contain;border-radius:8px;border:1.5px solid #e2e8f0;padding:8px;background:#fff;">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus logo? Ikon default akan digunakan.');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="action" value="delete_logo">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Hapus Logo
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="mb-2" id="logoPreviewWrap" style="display:none;">
                                <img id="logoPreview" src="" alt="" style="height:60px;max-width:200px;object-fit:contain;border-radius:8px;border:1.5px solid #e2e8f0;padding:8px;background:#fff;">
                            </div>
                            <?php endif; ?>
                            <input type="file" id="logo" name="logo"
                                   class="form-control"
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   onchange="previewLogoImage(this, 'logoPreview')">
                            <div class="form-hint">JPG, PNG, GIF, atau WEBP. Maks 2MB. (SVG tidak didukung karena dapat mengandung skrip berbahaya.)</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="favicon">Favicon</label>
                            <?php if (!empty($settings['favicon_path'])): ?>
                            <div class="mb-2">
                                <img src="../assets/uploads/site/<?= htmlspecialchars($settings['favicon_path']) ?>"
                                     alt="Favicon" id="faviconPreview"
                                     style="width:32px;height:32px;object-fit:contain;border-radius:4px;border:1.5px solid #e2e8f0;">
                            </div>
                            <?php else: ?>
                            <div class="mb-2" id="faviconPreviewWrap" style="display:none;">
                                <img id="faviconPreview" src="" alt="" style="width:32px;height:32px;object-fit:contain;border-radius:4px;border:1.5px solid #e2e8f0;">
                            </div>
                            <?php endif; ?>
                            <input type="file" id="favicon" name="favicon"
                                   class="form-control"
                                   accept=".ico,image/png,image/jpeg"
                                   onchange="previewLogoImage(this, 'faviconPreview')">
                            <div class="form-hint">ICO atau PNG 32×32px. Maks 512KB.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan Tampilan
                        </button>
                    </div>

                    <!-- Right: Live Preview -->
                    <div class="col-xl-6">
                        <div class="form-section-title">
                            <i class="fas fa-eye"></i>
                            Preview Warna (Live)
                        </div>

                        <div class="color-preview-card" id="colorPreviewCard">
                            <div class="color-preview-bar" id="colorBar"
                                 style="background:linear-gradient(90deg, <?= $settings['primary_color'] ?>, <?= $settings['secondary_color'] ?>, <?= $settings['accent_color'] ?>);"></div>
                            <div class="color-preview-body">
                                <div style="margin-bottom:16px;">
                                    <h4 id="previewTitle" style="color:<?= $settings['primary_color'] ?>;font-weight:800;margin-bottom:4px;font-size:18px;">
                                        Kayaswara
                                    </h4>
                                    <p id="previewSubtitle" style="color:#64748b;font-size:13px;margin:0;">
                                        Penerbit Naskah Akademik
                                    </p>
                                </div>
                                <div style="margin-bottom:16px;">
                                    <span id="previewBtnPrimary" class="color-preview-btn"
                                          style="background:<?= $settings['primary_color'] ?>;">
                                        Mulai Konsultasi
                                    </span>
                                    <span id="previewBtnSecondary" class="color-preview-btn color-preview-btn-sec"
                                          style="background:<?= $settings['secondary_color'] ?>;">
                                        Lihat Portofolio
                                    </span>
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <span id="previewBadge" style="background:<?= $settings['accent_color'] ?>;color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                                        #1 Penerbit Akademik
                                    </span>
                                    <span style="background:#f1f5f9;color:#475569;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                                        Terpercaya
                                    </span>
                                </div>

                                <div style="height:1px;background:#e2e8f0;margin:16px 0;"></div>

                                <!-- Nav Preview -->
                                <div id="previewNav" style="background:<?= $settings['primary_color'] ?>;border-radius:8px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                                    <span style="color:#fff;font-weight:700;font-size:13px;">Kayaswara</span>
                                    <div style="display:flex;gap:16px;">
                                        <span style="color:rgba(255,255,255,0.75);font-size:12px;">Layanan</span>
                                        <span style="color:#fff;font-size:12px;border-bottom:2px solid <?= $settings['secondary_color'] ?>;">Portofolio</span>
                                        <span style="color:rgba(255,255,255,0.75);font-size:12px;">Blog</span>
                                    </div>
                                </div>

                                <div style="margin-top:12px;display:flex;gap:8px;">
                                    <!-- Card preview -->
                                    <div style="flex:1;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                                        <div style="height:4px;background:<?= $settings['secondary_color'] ?>;"></div>
                                        <div style="padding:10px;font-size:12px;">
                                            <div style="font-weight:700;color:<?= $settings['primary_color'] ?>;margin-bottom:4px;">Penerbitan Buku</div>
                                            <div style="color:#64748b;font-size:11px;">Instalasi & konfigurasi</div>
                                        </div>
                                    </div>
                                    <div style="flex:1;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                                        <div style="height:4px;background:<?= $settings['accent_color'] ?>;"></div>
                                        <div style="padding:10px;font-size:12px;">
                                            <div style="font-weight:700;color:<?= $settings['primary_color'] ?>;margin-bottom:4px;">Editing</div>
                                            <div style="color:#64748b;font-size:11px;">Editing & Layout</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Color palette display -->
                        <div style="display:flex;gap:8px;margin-top:16px;">
                            <div style="text-align:center;flex:1;">
                                <div id="swatchPrimary" style="height:40px;border-radius:8px;background:<?= $settings['primary_color'] ?>;margin-bottom:4px;"></div>
                                <div style="font-size:11px;color:#64748b;">Primer</div>
                                <div id="hexPrimaryDisplay" style="font-size:11px;font-weight:700;color:#374151;"><?= $settings['primary_color'] ?></div>
                            </div>
                            <div style="text-align:center;flex:1;">
                                <div id="swatchSecondary" style="height:40px;border-radius:8px;background:<?= $settings['secondary_color'] ?>;margin-bottom:4px;"></div>
                                <div style="font-size:11px;color:#64748b;">Sekunder</div>
                                <div id="hexSecondaryDisplay" style="font-size:11px;font-weight:700;color:#374151;"><?= $settings['secondary_color'] ?></div>
                            </div>
                            <div style="text-align:center;flex:1;">
                                <div id="swatchAccent" style="height:40px;border-radius:8px;background:<?= $settings['accent_color'] ?>;margin-bottom:4px;"></div>
                                <div style="font-size:11px;color:#64748b;">Aksen</div>
                                <div id="hexAccentDisplay" style="font-size:11px;font-weight:700;color:#374151;"><?= $settings['accent_color'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ===================== TAB: NOTIFIKASI WA ===================== -->
            <?php elseif ($active_tab === 'notifikasi'): ?>
            <form method="POST" data-loading id="notifForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="tab" value="notifikasi">

                <div class="row g-4">
                    <div class="col-xl-8">
                        <!-- Enable/Disable Toggle -->
                        <div style="background:linear-gradient(135deg,rgba(22,163,74,0.06),rgba(26,60,94,0.06));border-radius:14px;padding:20px 24px;margin-bottom:24px;border:1px solid rgba(22,163,74,0.15);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:44px;height:44px;background:#16a34a;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:15px;color:#1e293b;">Notifikasi WhatsApp Otomatis</div>
                                    <div style="font-size:12px;color:#64748b;">Kirim update progres pesanan langsung ke WhatsApp klien</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="wa_notif_enabled" name="wa_notif_enabled" value="1"
                                       <?= ($settings['wa_notif_enabled'] ?? '0') === '1' ? 'checked' : '' ?>
                                       style="width:48px;height:24px;cursor:pointer;"
                                       onchange="toggleWaFields()">
                                <label class="form-check-label" for="wa_notif_enabled" style="font-weight:600;font-size:13px;cursor:pointer;">
                                    <?= ($settings['wa_notif_enabled'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' ?>
                                </label>
                            </div>
                        </div>

                        <div id="waSettingsFields" style="<?= ($settings['wa_notif_enabled'] ?? '0') !== '1' ? 'opacity:0.5;pointer-events:none;' : '' ?>">
                            <!-- Provider Selection -->
                            <div class="form-section-title">
                                <i class="fas fa-plug"></i>
                                Provider API WhatsApp
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="wa_api_provider">
                                        Provider <span class="required">*</span>
                                    </label>
                                    <select id="wa_api_provider" name="wa_api_provider" class="form-select" onchange="toggleCustomUrl()">
                                        <option value="fonnte" <?= ($settings['wa_api_provider'] ?? '') === 'fonnte' ? 'selected' : '' ?>>Fonnte</option>
                                        <option value="wablas" <?= ($settings['wa_api_provider'] ?? '') === 'wablas' ? 'selected' : '' ?>>Wablas</option>
                                        <option value="custom" <?= ($settings['wa_api_provider'] ?? '') === 'custom' ? 'selected' : '' ?>>Custom / Lainnya</option>
                                    </select>
                                    <div class="form-hint">Pilih penyedia layanan WA API yang Anda gunakan.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="wa_api_token">
                                        <i class="fas fa-key me-1" style="color:#1A3C5E;"></i>
                                        API Token <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" id="wa_api_token" name="wa_api_token"
                                               class="form-control"
                                               placeholder="Masukkan token API"
                                               value="<?= htmlspecialchars($settings['wa_api_token'] ?? '') ?>">
                                        <span class="input-group-text" onclick="togglePwField('wa_api_token', this)" style="cursor:pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="form-hint">Token dari dashboard provider (Fonnte/Wablas/dll).</div>
                                </div>
                            </div>

                            <!-- Custom URL (hidden unless custom selected) -->
                            <div class="mb-4" id="customUrlGroup" style="<?= ($settings['wa_api_provider'] ?? '') !== 'custom' ? 'display:none;' : '' ?>">
                                <label class="form-label" for="wa_api_url">
                                    <i class="fas fa-link me-1" style="color:#1A3C5E;"></i>
                                    URL Endpoint API
                                </label>
                                <input type="url" id="wa_api_url" name="wa_api_url"
                                       class="form-control"
                                       placeholder="https://api.yourprovider.com/send"
                                       value="<?= htmlspecialchars($settings['wa_api_url'] ?? '') ?>">
                                <div class="form-hint">Endpoint API untuk mengirim pesan (method POST, body JSON: phone + message).</div>
                            </div>

                            <!-- Message Template -->
                            <div class="form-section-title">
                                <i class="fas fa-comment-dots"></i>
                                Template Pesan
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="wa_notif_template">
                                    Template Notifikasi Milestone
                                </label>
                                <textarea id="wa_notif_template" name="wa_notif_template"
                                          class="form-control" rows="8"
                                          style="font-family:'Courier New',monospace;font-size:13px;line-height:1.6;"
                                          placeholder="Kosongkan untuk menggunakan template default..."><?= htmlspecialchars($settings['wa_notif_template'] ?? '') ?></textarea>
                                <div class="form-hint">Gunakan placeholder: <code>{client_name}</code>, <code>{tracking_code}</code>, <code>{milestone}</code>, <code>{status}</code>, <code>{progress}</code>, <code>{site_name}</code>, <code>{tracking_url}</code></div>
                            </div>

                            <!-- Template Preview -->
                            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:24px;">
                                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#16a34a;margin-bottom:8px;">
                                    <i class="fab fa-whatsapp me-1"></i> Preview Template Default
                                </div>
                                <div style="font-size:13px;color:#374151;white-space:pre-line;line-height:1.6;font-family:'Plus Jakarta Sans',sans-serif;">Halo Ahmad Yani! 👋

Update pesanan Anda (*KYSWR-06042026-001*):

📌 *Editing & Proofreading*
Status: *Selesai*
Progres: 30%

Pantau detail di:
https://domain.com/tracking?code=KYSWR-06042026-001

_Kayaswara_</div>
                            </div>

                            <!-- Test Button -->
                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px;">
                                <div style="flex:1;min-width:200px;">
                                    <label class="form-label mb-1" style="font-size:13px;">Tes Kirim Notifikasi</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:#f8fafc;font-size:12px;">+62</span>
                                        <input type="text" id="wa_test_phone" class="form-control" placeholder="81234567890">
                                        <button type="button" class="btn btn-outline-success" onclick="testWaNotif()" id="waTestBtn">
                                            <i class="fab fa-whatsapp me-1"></i>Kirim Tes
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="waTestResult" style="display:none;padding:10px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;"></div>
                        </div>

                        <!-- ===== Email / SMTP Settings ===== -->
                        <hr style="margin:28px 0;border-color:#e2e8f0;">

                        <div style="background:linear-gradient(135deg,rgba(59,130,246,0.06),rgba(26,60,94,0.06));border-radius:14px;padding:20px 24px;margin-bottom:24px;border:1px solid rgba(59,130,246,0.15);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:44px;height:44px;background:#3b82f6;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:15px;color:#1e293b;">Notifikasi Email (SMTP)</div>
                                    <div style="font-size:12px;color:#64748b;">Kirim email konfirmasi pesanan ke klien secara otomatis</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smtp_enabled" name="smtp_enabled" value="1"
                                       <?= ($settings['smtp_enabled'] ?? '0') === '1' ? 'checked' : '' ?>
                                       style="width:48px;height:24px;cursor:pointer;"
                                       onchange="toggleSmtpFields()">
                                <label class="form-check-label" for="smtp_enabled" style="font-weight:600;font-size:13px;cursor:pointer;">
                                    <?= ($settings['smtp_enabled'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' ?>
                                </label>
                            </div>
                        </div>

                        <div id="smtpSettingsFields" style="<?= ($settings['smtp_enabled'] ?? '0') !== '1' ? 'opacity:0.5;pointer-events:none;' : '' ?>">
                            <div class="form-section-title">
                                <i class="fas fa-server"></i>
                                Konfigurasi SMTP
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label" for="smtp_host">SMTP Host <span class="required">*</span></label>
                                    <input type="text" id="smtp_host" name="smtp_host" class="form-control"
                                           placeholder="Contoh: smtp.gmail.com, mail.domain.com"
                                           value="<?= htmlspecialchars($settings['smtp_host']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="smtp_port">Port</label>
                                    <input type="number" id="smtp_port" name="smtp_port" class="form-control"
                                           placeholder="587" min="1" max="65535"
                                           value="<?= htmlspecialchars($settings['smtp_port']) ?>">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_encryption">Enkripsi</label>
                                    <select id="smtp_encryption" name="smtp_encryption" class="form-select">
                                        <option value="tls" <?= $settings['smtp_encryption'] === 'tls' ? 'selected' : '' ?>>TLS (port 587) — dianjurkan</option>
                                        <option value="ssl" <?= $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' ?>>SSL (port 465)</option>
                                        <option value="none" <?= $settings['smtp_encryption'] === 'none' ? 'selected' : '' ?>>Tidak ada enkripsi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_from_name">Nama Pengirim</label>
                                    <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-control"
                                           placeholder="Nama yang muncul di inbox"
                                           value="<?= htmlspecialchars($settings['smtp_from_name']) ?>">
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_user">Username / Email SMTP <span class="required">*</span></label>
                                    <input type="email" id="smtp_user" name="smtp_user" class="form-control"
                                           placeholder="email@domain.com"
                                           value="<?= htmlspecialchars($settings['smtp_user']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_pass">Password SMTP <span class="required">*</span></label>
                                    <input type="password" id="smtp_pass" name="smtp_pass" class="form-control"
                                           placeholder="<?= !empty($settings['smtp_pass']) ? '(tersimpan — kosongkan jika tidak diubah)' : 'Password atau App Password' ?>">
                                    <div class="form-hint">Untuk Gmail: gunakan <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a>, bukan password biasa.</div>
                                </div>
                            </div>

                            <!-- Test Email -->
                            <div class="mb-2">
                                <label class="form-label">Tes Kirim Email</label>
                                <div class="d-flex gap-2">
                                    <input type="email" id="testEmailTarget" class="form-control" style="max-width:280px;" placeholder="email@tujuan.com">
                                    <button type="button" class="btn btn-outline-primary" onclick="testEmailNotif()" id="emailTestBtn">
                                        <i class="fas fa-paper-plane me-1"></i>Kirim Tes
                                    </button>
                                </div>
                                <div id="emailTestResult" style="display:none;padding:10px 16px;border-radius:10px;font-size:13px;margin-top:8px;"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan Notifikasi
                        </button>
                    </div>

                    <!-- Right: Guide -->
                    <div class="col-xl-4">
                        <div style="background:linear-gradient(135deg,rgba(22,163,74,0.06),rgba(26,60,94,0.06));border-radius:14px;padding:24px;border:1px solid rgba(22,163,74,0.15);">
                            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:14px;">
                                <i class="fas fa-lightbulb me-1" style="color:#1A3C5E;"></i>
                                Panduan Setup
                            </div>
                            <ol style="font-size:13px;color:#64748b;padding-left:18px;margin:0;line-height:2.2;">
                                <li>Daftar di <a href="https://fonnte.com" target="_blank" rel="noopener" style="color:#16a34a;font-weight:600;">Fonnte.com</a> atau <a href="https://wablas.com" target="_blank" rel="noopener" style="color:#16a34a;font-weight:600;">Wablas.com</a></li>
                                <li>Hubungkan nomor WhatsApp Anda</li>
                                <li>Salin <strong>API Token</strong> dari dashboard provider</li>
                                <li>Tempel token di kolom sebelah kiri</li>
                                <li>Aktifkan toggle &amp; kirim tes</li>
                            </ol>

                            <div style="height:1px;background:rgba(22,163,74,0.15);margin:16px 0;"></div>

                            <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;">
                                <i class="fas fa-info-circle me-1"></i> Kapan Notifikasi Dikirim?
                            </div>
                            <ul style="font-size:12px;color:#64748b;padding-left:16px;margin:0;line-height:2;">
                                <li>Saat milestone diubah ke <strong>Sedang Dikerjakan</strong></li>
                                <li>Saat milestone diubah ke <strong>Selesai</strong></li>
                                <li>Tidak dikirim saat di-reset ke Menunggu</li>
                                <li>Hanya jika nomor telepon klien terisi</li>
                            </ul>

                            <div style="height:1px;background:rgba(22,163,74,0.15);margin:16px 0;"></div>

                            <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;">
                                <i class="fas fa-history me-1"></i> Log Notifikasi
                            </div>
                            <?php
                            $recentLogs = [];
                            try {
                                $recentLogs = fetchAll(
                                    "SELECT recipient, status, response, created_at FROM notification_logs ORDER BY id DESC LIMIT 5"
                                );
                            } catch (Exception $e) {}
                            ?>
                            <?php if (!empty($recentLogs)): ?>
                            <div style="max-height:200px;overflow-y:auto;">
                                <?php foreach ($recentLogs as $log): ?>
                                <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid rgba(0,0,0,0.05);font-size:11px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:<?= $log['status'] === 'sent' ? '#16a34a' : '#dc2626' ?>;flex-shrink:0;"></span>
                                    <span style="color:#374151;font-weight:600;"><?= htmlspecialchars(substr($log['recipient'], 0, 6)) ?>***</span>
                                    <span style="color:#94a3b8;margin-left:auto;white-space:nowrap;"><?= date('d/m H:i', strtotime($log['created_at'])) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p style="font-size:12px;color:#94a3b8;margin:0;">Belum ada log notifikasi.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Email Preview Section -->
                <?php
                $prev_site = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
                $prev_color = getSetting('primary_color', '#1A3C5E');
                $prev_tracking = 'TRK-20260409';
                $prev_url = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/tracking';
                ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;">
                            <div style="padding:18px 24px 0;border-bottom:1px solid #f1f5f9;">
                                <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:12px;">
                                    <i class="fas fa-eye me-2" style="color:<?= htmlspecialchars($prev_color) ?>;"></i>
                                    Preview Email ke Pelanggan
                                </div>
                                <ul class="nav" style="gap:4px;" id="emailPreviewTabs">
                                    <li class="nav-item">
                                        <button class="nav-link active" style="font-size:12px;padding:6px 14px;border-radius:8px 8px 0 0;" onclick="switchEmailTab('konfirmasi',this)">
                                            <i class="fas fa-check-circle me-1"></i>Konfirmasi Pesanan
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" style="font-size:12px;padding:6px 14px;border-radius:8px 8px 0 0;" onclick="switchEmailTab('milestone',this)">
                                            <i class="fas fa-flag-checkered me-1"></i>Update Milestone
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div style="padding:24px;background:#f8fafc;">

                                <!-- Tab: Konfirmasi Pesanan -->
                                <div id="emailTab-konfirmasi">
                                    <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden;font-family:Arial,sans-serif;font-size:14px;color:#374151;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                                        <div style="background:<?= htmlspecialchars($prev_color) ?>;padding:24px 28px;text-align:center;">
                                            <div style="color:#fff;font-size:20px;font-weight:800;letter-spacing:-0.3px;"><?= htmlspecialchars($prev_site) ?></div>
                                            <div style="color:rgba(255,255,255,0.75);font-size:12px;margin-top:4px;">Konfirmasi Pesanan</div>
                                        </div>
                                        <div style="padding:28px;">
                                            <p style="margin:0 0 16px;">Yth. <strong>Budi Santoso</strong>,</p>
                                            <p style="margin:0 0 16px;color:#64748b;">Terima kasih telah mempercayakan kebutuhan penerbitan buku Anda kepada kami. Pesanan Anda telah berhasil dibuat dengan detail berikut:</p>
                                            <div style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;padding:16px 20px;margin-bottom:20px;">
                                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin-bottom:12px;">Detail Pesanan</div>
                                                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                                                    <tr><td style="padding:4px 0;color:#64748b;width:40%;">Kode Tracking</td><td style="padding:4px 0;font-weight:700;color:<?= htmlspecialchars($prev_color) ?>;"><?= $prev_tracking ?></td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Layanan</td><td style="padding:4px 0;font-weight:600;">Penerbitan Buku Lengkap</td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Paket</td><td style="padding:4px 0;font-weight:600;">Profesional</td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Institusi</td><td style="padding:4px 0;">Universitas Indonesia</td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">No. HP/WA</td><td style="padding:4px 0;">081234567890</td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Estimasi Biaya</td><td style="padding:4px 0;font-weight:700;">Rp 2.500.000</td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Status</td><td style="padding:4px 0;"><span style="background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Menunggu</span></td></tr>
                                                </table>
                                            </div>
                                            <div style="background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;padding:12px 16px;margin-bottom:20px;font-size:13px;">
                                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin-bottom:8px;">Deskripsi Kebutuhan</div>
                                                <p style="margin:0;color:#374151;">Penerbitan buku ajar Manajemen Pendidikan, termasuk editing, layout, dan desain cover.</p>
                                            </div>
                                            <div style="text-align:center;margin:24px 0;">
                                                <a href="<?= htmlspecialchars($prev_url) ?>" style="background:<?= htmlspecialchars($prev_color) ?>;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;display:inline-block;">
                                                    <i class="fas fa-search"></i> Pantau Pesanan
                                                </a>
                                                <div style="margin-top:10px;font-size:12px;color:#94a3b8;">Masukkan kode: <strong><?= $prev_tracking ?></strong></div>
                                            </div>
                                            <p style="margin:0;color:#64748b;font-size:13px;">Tim kami akan segera menghubungi Anda untuk konfirmasi lebih lanjut.</p>
                                        </div>
                                        <div style="background:#f8fafc;padding:16px 28px;text-align:center;border-top:1px solid #e2e8f0;">
                                            <div style="font-size:12px;color:#94a3b8;">© <?= date('Y') ?> <?= htmlspecialchars($prev_site) ?> — Email otomatis, tidak perlu dibalas.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Milestone Update -->
                                <div id="emailTab-milestone" style="display:none;">
                                    <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden;font-family:Arial,sans-serif;font-size:14px;color:#374151;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                                        <div style="background:<?= htmlspecialchars($prev_color) ?>;padding:24px 28px;text-align:center;">
                                            <div style="color:#fff;font-size:20px;font-weight:800;letter-spacing:-0.3px;"><?= htmlspecialchars($prev_site) ?></div>
                                            <div style="color:rgba(255,255,255,0.75);font-size:12px;margin-top:4px;">Update Progress Pesanan</div>
                                        </div>
                                        <div style="padding:28px;">
                                            <p style="margin:0 0 16px;">Yth. <strong>Budi Santoso</strong>,</p>
                                            <p style="margin:0 0 20px;color:#64748b;">Kami ingin menginformasikan bahwa salah satu tahapan pesanan Anda telah selesai.</p>
                                            <div style="background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;padding:16px 20px;margin-bottom:20px;">
                                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin-bottom:12px;">Update Pesanan</div>
                                                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                                                    <tr><td style="padding:4px 0;color:#64748b;width:40%;">Kode Tracking</td><td style="padding:4px 0;font-weight:700;color:<?= htmlspecialchars($prev_color) ?>;"><?= $prev_tracking ?></td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Tahapan Selesai</td><td style="padding:4px 0;font-weight:700;">✅ Layout & Typesetting</td></tr>
                                                    <tr><td style="padding:4px 0;color:#64748b;">Progres</td><td style="padding:4px 0;">
                                                        <div style="display:flex;align-items:center;gap:8px;">
                                                            <div style="flex:1;background:#e2e8f0;border-radius:20px;height:8px;">
                                                                <div style="width:33%;background:<?= htmlspecialchars($prev_color) ?>;border-radius:20px;height:8px;"></div>
                                                            </div>
                                                            <span style="font-weight:700;font-size:12px;">33%</span>
                                                        </div>
                                                        <div style="font-size:12px;color:#64748b;margin-top:4px;">1 dari 3 tahap selesai</div>
                                                    </td></tr>
                                                </table>
                                            </div>
                                            <div style="text-align:center;margin:24px 0;">
                                                <a href="<?= htmlspecialchars($prev_url) ?>" style="background:<?= htmlspecialchars($prev_color) ?>;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;display:inline-block;">
                                                    <i class="fas fa-search"></i> Lihat Detail Progress
                                                </a>
                                                <div style="margin-top:10px;font-size:12px;color:#94a3b8;">Masukkan kode: <strong><?= $prev_tracking ?></strong></div>
                                            </div>
                                            <p style="margin:0;color:#64748b;font-size:13px;">Tim kami akan terus bekerja pada tahapan berikutnya. Kami akan menginformasikan kembali setiap ada tahapan yang selesai.</p>
                                        </div>
                                        <div style="background:#f8fafc;padding:16px 28px;text-align:center;border-top:1px solid #e2e8f0;">
                                            <div style="font-size:12px;color:#94a3b8;">© <?= date('Y') ?> <?= htmlspecialchars($prev_site) ?> — Email otomatis, tidak perlu dibalas.</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div style="padding:12px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;">
                                <i class="fas fa-info-circle me-1"></i>
                                Preview menggunakan data contoh. Warna header mengikuti warna utama yang diatur di tab Tampilan.
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                function switchEmailTab(tab, btn) {
                    document.getElementById('emailTab-konfirmasi').style.display = 'none';
                    document.getElementById('emailTab-milestone').style.display = 'none';
                    document.getElementById('emailTab-' + tab).style.display = 'block';
                    document.querySelectorAll('#emailPreviewTabs .nav-link').forEach(function(el){ el.classList.remove('active'); });
                    btn.classList.add('active');
                }
                </script>

            </form>

            <!-- ===================== TAB: AKUN ===================== -->
            <?php elseif ($active_tab === 'akun'): ?>
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div style="background:linear-gradient(135deg,rgba(220,38,38,0.04),rgba(26,54,93,0.04));border-radius:14px;padding:24px;margin-bottom:24px;border:1px solid rgba(220,38,38,0.1);">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                            <div style="width:40px;height:40px;background:rgba(26,54,93,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#1A3C5E;">
                                <i class="fas fa-shield-alt fa-lg"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:14px;color:#1e293b;">Ubah Password Admin</div>
                                <div style="font-size:12px;color:#64748b;">
                                    Akun: <strong><?= htmlspecialchars($_SESSION['admin_user'] ?? '') ?></strong>
                                </div>
                            </div>
                        </div>
                        <p style="font-size:13px;color:#64748b;margin:0;">
                            Pastikan password baru Anda kuat dan unik. Minimal 8 karakter,
                            kombinasi huruf, angka, dan karakter spesial.
                        </p>
                    </div>

                    <form method="POST" data-loading>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="tab" value="akun">

                        <div class="mb-4">
                            <label class="form-label" for="old_password">
                                <i class="fas fa-lock me-1" style="color:#64748b;"></i>
                                Password Lama <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="old_password" name="old_password"
                                       class="form-control"
                                       placeholder="Masukkan password saat ini"
                                       required autocomplete="current-password">
                                <span class="input-group-text" onclick="togglePwField('old_password', this)" style="cursor:pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="new_password">
                                <i class="fas fa-key me-1" style="color:#1A3C5E;"></i>
                                Password Baru <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password"
                                       class="form-control"
                                       placeholder="Minimal 8 karakter"
                                       minlength="8"
                                       required autocomplete="new-password"
                                       oninput="checkPasswordStrength(this.value)">
                                <span class="input-group-text" onclick="togglePwField('new_password', this)" style="cursor:pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <!-- Password strength indicator -->
                            <div style="margin-top:8px;">
                                <div style="height:4px;background:#e2e8f0;border-radius:2px;overflow:hidden;">
                                    <div id="strengthBar" style="height:100%;width:0%;transition:all 0.3s;border-radius:2px;"></div>
                                </div>
                                <div id="strengthText" style="font-size:11px;color:#94a3b8;margin-top:4px;"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="confirm_password">
                                <i class="fas fa-check-double me-1" style="color:#16a34a;"></i>
                                Konfirmasi Password Baru <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" name="confirm_password"
                                       class="form-control"
                                       placeholder="Ulangi password baru"
                                       required autocomplete="new-password"
                                       oninput="checkConfirmMatch()">
                                <span class="input-group-text" onclick="togglePwField('confirm_password', this)" style="cursor:pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="confirmMsg" style="font-size:11px;margin-top:4px;"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Password Baru
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$extra_js = '
<script>
// ---- Color Preview Live Update ----
function updateColorPreview() {
    const primary   = document.getElementById("primary_color").value;
    const secondary = document.getElementById("secondary_color").value;
    const accent    = document.getElementById("accent_color").value;

    // Update hex inputs
    const phex = document.getElementById("primary_hex");
    const shex = document.getElementById("secondary_hex");
    const ahex = document.getElementById("accent_hex");
    if (phex) phex.value = primary;
    if (shex) shex.value = secondary;
    if (ahex) ahex.value = accent;

    // Update color bar
    const bar = document.getElementById("colorBar");
    if (bar) bar.style.background = "linear-gradient(90deg," + primary + "," + secondary + "," + accent + ")";

    // Update preview elements
    const title = document.getElementById("previewTitle");
    if (title) title.style.color = primary;

    const btnPrimary = document.getElementById("previewBtnPrimary");
    if (btnPrimary) btnPrimary.style.background = primary;

    const btnSec = document.getElementById("previewBtnSecondary");
    if (btnSec) btnSec.style.background = secondary;

    const badge = document.getElementById("previewBadge");
    if (badge) badge.style.background = accent;

    const nav = document.getElementById("previewNav");
    if (nav) nav.style.background = primary;

    // Swatches
    const sp = document.getElementById("swatchPrimary");
    const ss = document.getElementById("swatchSecondary");
    const sa = document.getElementById("swatchAccent");
    if (sp) sp.style.background = primary;
    if (ss) ss.style.background = secondary;
    if (sa) sa.style.background = accent;

    // Hex displays
    const dp = document.getElementById("hexPrimaryDisplay");
    const ds = document.getElementById("hexSecondaryDisplay");
    const da = document.getElementById("hexAccentDisplay");
    if (dp) dp.textContent = primary;
    if (ds) ds.textContent = secondary;
    if (da) da.textContent = accent;
}

function syncColor(inputId, hexValue) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hexValue)) {
        const input = document.getElementById(inputId);
        if (input) input.value = hexValue;
        updateColorPreview();
    }
}

// ---- Logo / Favicon Preview ----
function previewLogoImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(previewId);
            if (img) {
                img.src = e.target.result;
                img.style.display = "block";
                const wrap = document.getElementById(previewId + "Wrap");
                if (wrap) wrap.style.display = "block";
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ---- Password Strength Check ----
function checkPasswordStrength(password) {
    const bar  = document.getElementById("strengthBar");
    const text = document.getElementById("strengthText");
    if (!bar || !text) return;

    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const levels = [
        { pct: "20%",  color: "#dc2626", label: "Sangat Lemah" },
        { pct: "40%",  color: "#f59e0b", label: "Lemah" },
        { pct: "60%",  color: "#1A3C5E", label: "Sedang" },
        { pct: "80%",  color: "#16a34a", label: "Kuat" },
        { pct: "100%", color: "#15803d", label: "Sangat Kuat" },
    ];

    const level = levels[Math.min(score, 4)];
    bar.style.width = password.length > 0 ? level.pct : "0%";
    bar.style.background = level.color;
    text.textContent = password.length > 0 ? level.label : "";
    text.style.color = level.color;
}

function checkConfirmMatch() {
    const newPw  = document.getElementById("new_password").value;
    const confPw = document.getElementById("confirm_password").value;
    const msg    = document.getElementById("confirmMsg");
    if (!msg) return;

    if (confPw.length === 0) {
        msg.textContent = "";
    } else if (newPw === confPw) {
        msg.textContent = "✓ Password cocok";
        msg.style.color = "#16a34a";
    } else {
        msg.textContent = "✗ Password tidak cocok";
        msg.style.color = "#dc2626";
    }
}

function togglePwField(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector("i");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

// ---- WA Notification Settings ----
function toggleWaFields() {
    const enabled = document.getElementById("wa_notif_enabled");
    const fields  = document.getElementById("waSettingsFields");
    const label   = enabled.parentElement.querySelector("label");
    if (!enabled || !fields) return;

    if (enabled.checked) {
        fields.style.opacity = "1";
        fields.style.pointerEvents = "auto";
        if (label) label.textContent = "Aktif";
    } else {
        fields.style.opacity = "0.5";
        fields.style.pointerEvents = "none";
        if (label) label.textContent = "Nonaktif";
    }
}

function toggleCustomUrl() {
    const provider = document.getElementById("wa_api_provider");
    const group    = document.getElementById("customUrlGroup");
    if (!provider || !group) return;
    group.style.display = provider.value === "custom" ? "block" : "none";
}

function testWaNotif() {
    const phone = document.getElementById("wa_test_phone").value.trim();
    if (!phone) { alert("Masukkan nomor WA tujuan."); return; }

    // Must save settings first, then test via AJAX
    const btn = document.getElementById("waTestBtn");
    const result = document.getElementById("waTestResult");
    btn.disabled = true;
    btn.innerHTML = \'<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...\';
    result.style.display = "none";

    const formData = new FormData();
    formData.append("csrf_token", document.querySelector("[name=csrf_token]").value);
    formData.append("action", "test_wa");
    formData.append("phone", phone);

    fetch(window.location.href, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        result.style.display = "block";
        if (data.success) {
            result.style.background = "#f0fdf4";
            result.style.color = "#16a34a";
            result.style.border = "1px solid #bbf7d0";
            result.innerHTML = \'<i class="fas fa-check-circle me-1"></i>\' + data.message;
        } else {
            result.style.background = "#fef2f2";
            result.style.color = "#dc2626";
            result.style.border = "1px solid #fecaca";
            result.innerHTML = \'<i class="fas fa-exclamation-circle me-1"></i>\' + data.message;
        }
        btn.disabled = false;
        btn.innerHTML = \'<i class="fab fa-whatsapp me-1"></i>Kirim Tes\';
    })
    .catch(function() {
        result.style.display = "block";
        result.style.background = "#fef2f2";
        result.style.color = "#dc2626";
        result.style.border = "1px solid #fecaca";
        result.innerHTML = \'<i class="fas fa-exclamation-circle me-1"></i>Gagal menghubungi server.\';
        btn.disabled = false;
        btn.innerHTML = \'<i class="fab fa-whatsapp me-1"></i>Kirim Tes\';
    });
}

function toggleSmtpFields() {
    const enabled = document.getElementById("smtp_enabled");
    const fields  = document.getElementById("smtpSettingsFields");
    const label   = enabled.parentElement.querySelector("label");
    if (!enabled || !fields) return;
    if (enabled.checked) {
        fields.style.opacity = "1";
        fields.style.pointerEvents = "auto";
        if (label) label.textContent = "Aktif";
    } else {
        fields.style.opacity = "0.5";
        fields.style.pointerEvents = "none";
        if (label) label.textContent = "Nonaktif";
    }
}

function testEmailNotif() {
    const email = document.getElementById("testEmailTarget").value.trim();
    if (!email) { alert("Masukkan alamat email tujuan."); return; }

    const btn    = document.getElementById("emailTestBtn");
    const result = document.getElementById("emailTestResult");
    btn.disabled = true;
    btn.innerHTML = \'<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...\';
    result.style.display = "none";

    const formData = new FormData();
    formData.append("csrf_token", document.querySelector("[name=csrf_token]").value);
    formData.append("action", "test_email");
    formData.append("email", email);

    fetch(window.location.href, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        result.style.display = "block";
        if (data.success) {
            result.style.background = "#eff6ff";
            result.style.color = "#2563eb";
            result.style.border = "1px solid #bfdbfe";
            result.innerHTML = \'<i class="fas fa-check-circle me-1"></i>\' + data.message;
        } else {
            result.style.background = "#fef2f2";
            result.style.color = "#dc2626";
            result.style.border = "1px solid #fecaca";
            result.innerHTML = \'<i class="fas fa-exclamation-circle me-1"></i>\' + data.message;
        }
        btn.disabled = false;
        btn.innerHTML = \'<i class="fas fa-paper-plane me-1"></i>Kirim Tes\';
    })
    .catch(function() {
        result.style.display = "block";
        result.style.background = "#fef2f2";
        result.style.color = "#dc2626";
        result.style.border = "1px solid #fecaca";
        result.innerHTML = \'<i class="fas fa-exclamation-circle me-1"></i>Gagal menghubungi server.\';
        btn.disabled = false;
        btn.innerHTML = \'<i class="fas fa-paper-plane me-1"></i>Kirim Tes\';
    });
}
</script>
';
require_once ADMIN_PATH . '/includes/footer.php';
?>
