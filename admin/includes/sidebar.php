<?php
/**
 * Admin sidebar + topbar.
 * Expects $adminTitles / $current_page from includes/header.php.
 */
$current_page = $_GET['page'] ?? 'dashboard';
$site_name    = function_exists('getSetting') ? getSetting('site_name', 'Kayaswara') : 'Kayaswara';

$new_count = 0;
$pending_orders = 0;
$draft_pubs = 0;
if (function_exists('fetch')) {
    try {
        $r = fetch("SELECT COUNT(*) cnt FROM consultations WHERE status = 'new'");
        $new_count = $r ? (int) $r['cnt'] : 0;
    } catch (Exception $e) {}
    try {
        $r = fetch("SELECT COUNT(*) cnt FROM orders WHERE status IN ('pending','in_progress')");
        $pending_orders = $r ? (int) $r['cnt'] : 0;
    } catch (Exception $e) {}
    try {
        $r = fetch("SELECT COUNT(*) cnt FROM publications WHERE status = 'draft'");
        $draft_pubs = $r ? (int) $r['cnt'] : 0;
    } catch (Exception $e) {}
}

$menu_groups = [
    'Redaksi' => [
        ['page' => 'dashboard',  'icon' => 'fa-gauge-high',     'label' => 'Dashboard'],
        ['page' => 'konsultasi', 'icon' => 'fa-inbox',          'label' => 'Pengajuan Naskah', 'badge' => $new_count, 'match' => ['konsultasi', 'konsultasi-detail']],
        ['page' => 'pesanan',    'icon' => 'fa-clipboard-list', 'label' => 'Pesanan & Progres', 'badge' => $pending_orders, 'match' => ['pesanan', 'pesanan-form', 'pesanan-detail', 'invoice-form', 'invoice-detail']],
    ],
    'Terbitan' => [
        ['page' => 'publikasi',  'icon' => 'fa-book-open',      'label' => 'Katalog Publikasi', 'badge' => $draft_pubs, 'match' => ['publikasi', 'publikasi-form']],
        ['page' => 'portofolio', 'icon' => 'fa-handshake',      'label' => 'Kerja Sama',        'match' => ['portofolio', 'portofolio-form']],
        ['page' => 'blog',       'icon' => 'fa-newspaper',      'label' => 'Wawasan',           'match' => ['blog', 'blog-form']],
    ],
    'Sistem' => [
        ['page' => 'pengaturan', 'icon' => 'fa-sliders',        'label' => 'Pengaturan'],
        ['page' => 'export',     'icon' => 'fa-file-export',    'label' => 'Ekspor Data'],
    ],
];

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$initials = '';
foreach (array_slice(explode(' ', $admin_name), 0, 2) as $p) {
    $initials .= strtoupper(substr($p, 0, 1));
}
?>
<aside class="admin-sidebar" id="adminSidebar">
    <a href="index.php" class="sidebar-brand">
        <span class="sidebar-brand-icon"><i class="fa-solid fa-book-bookmark"></i></span>
        <span class="sidebar-brand-text">
            <span class="sidebar-brand-name"><?= htmlspecialchars($site_name) ?></span>
            <span class="sidebar-brand-sub">Panel Redaksi</span>
        </span>
    </a>

    <nav class="sidebar-nav">
        <?php
        // Prefixed loop variables: this file is included into each page's scope,
        // so plain names like $items/$item would clobber the page's own data.
        foreach ($menu_groups as $sb_group => $sb_items): ?>
        <div class="sidebar-section-title"><?= htmlspecialchars($sb_group) ?></div>
        <?php foreach ($sb_items as $sb_item):
            $sb_active = ($current_page === $sb_item['page'])
                || (!empty($sb_item['match']) && in_array($current_page, $sb_item['match'], true));
        ?>
        <div class="sidebar-nav-item">
            <a href="index.php?page=<?= $sb_item['page'] ?>" class="sidebar-nav-link <?= $sb_active ? 'active' : '' ?>">
                <span class="sidebar-nav-icon"><i class="fa-solid <?= $sb_item['icon'] ?>"></i></span>
                <span class="sidebar-nav-label"><?= $sb_item['label'] ?></span>
                <?php if (!empty($sb_item['badge'])): ?><span class="sidebar-badge"><?= (int) $sb_item['badge'] ?></span><?php endif; ?>
            </a>
        </div>
        <?php endforeach; ?>
        <?php endforeach; ?>

        <div class="sidebar-divider"></div>

        <div class="sidebar-nav-item">
            <a href="../index.php" target="_blank" rel="noopener" class="sidebar-nav-link">
                <span class="sidebar-nav-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
                <span class="sidebar-nav-label">Lihat Situs</span>
            </a>
        </div>
        <div class="sidebar-nav-item">
            <a href="index.php?page=logout" class="sidebar-nav-link text-danger-soft"
               onclick="return confirm('Keluar dari panel admin?')">
                <span class="sidebar-nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                <span class="sidebar-nav-label">Keluar</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= htmlspecialchars($initials ?: 'A') ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?= htmlspecialchars($admin_name) ?></span>
                <span class="sidebar-user-role">Administrator</span>
            </div>
        </div>
    </div>
</aside>

<div class="admin-main" id="adminMain">
    <header class="admin-topbar">
        <button class="topbar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Buka/tutup menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-breadcrumb">
            <h1><?= htmlspecialchars($adminTitles[$current_page] ?? 'Panel Admin') ?></h1>
        </div>

        <div class="topbar-actions">
            <?php if ($new_count > 0): ?>
            <a href="index.php?page=konsultasi" class="topbar-icon-btn" title="<?= $new_count ?> pengajuan naskah baru">
                <i class="fa-regular fa-bell"></i>
                <span class="topbar-notification-dot"></span>
            </a>
            <?php endif; ?>

            <a href="../index.php" target="_blank" rel="noopener" class="topbar-icon-btn" title="Lihat situs">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>

            <div class="dropdown">
                <div class="topbar-user dropdown-toggle" data-bs-toggle="dropdown" role="button" tabindex="0">
                    <div class="topbar-avatar"><?= htmlspecialchars($initials ?: 'A') ?></div>
                    <span class="topbar-user-name"><?= htmlspecialchars($admin_name) ?></span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:10px;min-width:190px;margin-top:6px;">
                    <li><h6 class="dropdown-header">Akun Admin</h6></li>
                    <li><a class="dropdown-item" href="index.php?page=pengaturan&tab=akun"><i class="fa-solid fa-key me-2 text-muted"></i>Ubah Kata Sandi</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="index.php?page=logout" onclick="return confirm('Keluar dari panel admin?')"><i class="fa-solid fa-right-from-bracket me-2"></i>Keluar</a></li>
                </ul>
            </div>
        </div>
    </header>
