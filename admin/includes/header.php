<?php
/**
 * Admin Header — Kayaswara
 * Shell + stylesheet for the whole admin panel.
 * Class names are kept stable so every existing admin page keeps working.
 */

$site_name    = function_exists('getSetting') ? getSetting('site_name', 'Kayaswara') : 'Kayaswara';
$admin_name   = $_SESSION['admin_name'] ?? 'Admin';
$current_page = $_GET['page'] ?? 'dashboard';

// NOTE: this file is included into each page's scope. Keep local variables
// prefixed so they never overwrite data the page is about to render.
$new_konsultasi_count = 0;
if (function_exists('fetch')) {
    try {
        $hdr_row = fetch("SELECT COUNT(*) as cnt FROM consultations WHERE status = 'new'");
        $new_konsultasi_count = $hdr_row ? (int) $hdr_row['cnt'] : 0;
    } catch (Exception $e) {}
}

$adminTitles = [
    'dashboard'         => 'Dashboard',
    'publikasi'         => 'Katalog Publikasi',
    'publikasi-form'    => isset($_GET['id']) ? 'Ubah Publikasi' : 'Tambah Publikasi',
    'pesanan'           => 'Pesanan',
    'pesanan-form'      => isset($_GET['id']) ? 'Ubah Pesanan' : 'Pesanan Baru',
    'pesanan-detail'    => 'Detail Pesanan',
    'portofolio'        => 'Kerja Sama Institusi',
    'portofolio-form'   => isset($_GET['id']) ? 'Ubah Kerja Sama' : 'Tambah Kerja Sama',
    'blog'              => 'Wawasan',
    'blog-form'         => isset($_GET['id']) ? 'Ubah Tulisan' : 'Tulisan Baru',
    'konsultasi'        => 'Pengajuan Naskah',
    'konsultasi-detail' => 'Detail Pengajuan',
    'invoice-form'      => 'Tagihan',
    'invoice-detail'    => 'Detail Tagihan',
    'pengaturan'        => 'Pengaturan',
    'export'            => 'Ekspor Data',
];
$page_title = $adminTitles[$current_page] ?? 'Panel Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($page_title) ?> — Admin <?= htmlspecialchars($site_name) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <style>
    /* ==========================================================
       KAYASWARA ADMIN — stylesheet
       ========================================================== */
    :root {
        --sidebar-width: 254px;
        --sidebar-collapsed-width: 72px;
        --topbar-height: 62px;

        --primary: #1F4B3F;
        --primary-hover: #163830;
        --secondary: #2F6B57;
        --secondary-hover: #255643;
        --accent: #A9752F;

        --sidebar-bg: #16302A;
        --sidebar-hover: rgba(255,255,255,.07);
        --sidebar-active-bg: rgba(169,117,47,.18);
        --sidebar-text: #C3D2CB;
        --sidebar-heading: #74897F;

        --content-bg: #F5F4EE;
        --card-bg: #FFFFFF;
        --topbar-bg: #FFFFFF;
        --border-color: #E3E0D4;
        --border-strong: #CFCBBB;

        --text-primary: #16211C;
        --text-secondary: #45544D;
        --text-muted: #77857D;

        --success: #2E7D4F;
        --warning: #A9752F;
        --danger:  #B3392E;
        --info:    #2C6E8F;

        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 14px;

        --card-shadow: 0 1px 2px rgba(22,33,28,.05), 0 4px 16px rgba(22,33,28,.04);
        --card-shadow-hover: 0 6px 20px rgba(22,33,28,.09);
        --transition: all .22s cubic-bezier(.4,0,.2,1);
        --font-main: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        --font-display: 'Source Serif 4', Georgia, serif;
    }

    *, *::before, *::after { box-sizing: border-box; }
    html, body { height: 100%; margin: 0; }

    body {
        font-family: var(--font-main);
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-secondary);
        background: var(--content-bg);
        overflow-x: hidden;
    }

    a { color: var(--primary); text-decoration: none; }
    a:hover { color: var(--accent); }

    .admin-wrapper { display: flex; min-height: 100vh; }

    /* ---------- Sidebar ---------- */
    .admin-sidebar {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        min-height: 100vh;
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 1030;
        display: flex;
        flex-direction: column;
        transition: var(--transition);
        overflow: hidden;
    }
    .admin-sidebar.collapsed { width: var(--sidebar-collapsed-width); }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 16px;
        min-height: var(--topbar-height);
        border-bottom: 1px solid rgba(255,255,255,.08);
        text-decoration: none;
        flex-shrink: 0;
    }
    .sidebar-brand-icon {
        width: 36px; height: 36px;
        flex: 0 0 36px;
        border-radius: var(--radius-sm);
        background: rgba(255,255,255,.1);
        color: #fff;
        display: grid; place-items: center;
        font-size: 15px;
    }
    .sidebar-brand-text { flex: 1; overflow: hidden; }
    .sidebar-brand-name {
        display: block;
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 15px;
        color: #F3F6F4;
        white-space: nowrap;
        line-height: 1.2;
    }
    .sidebar-brand-sub {
        display: block;
        font-size: 10.5px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--sidebar-heading);
        white-space: nowrap;
    }

    .sidebar-nav { flex: 1; padding: 14px 0; overflow-y: auto; overflow-x: hidden; }
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 2px; }

    .sidebar-section-title {
        padding: 10px 20px 5px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .13em;
        text-transform: uppercase;
        color: var(--sidebar-heading);
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar-nav-item { margin: 2px 10px; }
    .sidebar-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 12px;
        border-radius: var(--radius-sm);
        color: var(--sidebar-text);
        font-weight: 500;
        font-size: 13.5px;
        white-space: nowrap;
        position: relative;
        transition: var(--transition);
    }
    .sidebar-nav-link:hover { background: var(--sidebar-hover); color: #fff; }
    .sidebar-nav-link.active { background: var(--sidebar-active-bg); color: #fff; font-weight: 600; }
    .sidebar-nav-link.active::before {
        content: '';
        position: absolute;
        left: -10px; top: 50%;
        transform: translateY(-50%);
        width: 3px; height: 22px;
        background: var(--accent);
        border-radius: 0 3px 3px 0;
    }
    .sidebar-nav-icon { width: 20px; text-align: center; font-size: 14px; flex-shrink: 0; }
    .sidebar-nav-label { flex: 1; overflow: hidden; text-overflow: ellipsis; }
    .sidebar-badge {
        background: var(--accent);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
        min-width: 20px;
        text-align: center;
    }
    .sidebar-divider { height: 1px; background: rgba(255,255,255,.08); margin: 10px 16px; }
    .sidebar-nav-link.text-danger-soft { color: #E9A9A2; }
    .sidebar-nav-link.text-danger-soft:hover { background: rgba(179,57,46,.18); color: #F3C3BD; }

    .sidebar-footer { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,.08); flex-shrink: 0; }
    .sidebar-user { display: flex; align-items: center; gap: 10px; }
    .sidebar-avatar {
        width: 34px; height: 34px; flex: 0 0 34px;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        display: grid; place-items: center;
        font-weight: 700; font-size: 13px;
    }
    .sidebar-user-info { flex: 1; overflow: hidden; }
    .sidebar-user-name { display: block; font-size: 13px; font-weight: 600; color: #F3F6F4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sidebar-user-role { display: block; font-size: 11px; color: var(--sidebar-heading); }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(22,33,28,.5);
        z-index: 1020;
        opacity: 0;
        transition: opacity .25s ease;
    }
    .sidebar-overlay.active { opacity: 1; }

    /* ---------- Main ---------- */
    .admin-main {
        flex: 1;
        margin-left: var(--sidebar-width);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: var(--transition);
    }

    .admin-topbar {
        background: var(--topbar-bg);
        border-bottom: 1px solid var(--border-color);
        height: var(--topbar-height);
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 0 22px;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .topbar-toggle {
        width: 36px; height: 36px;
        border: 1px solid var(--border-color);
        background: #fff;
        border-radius: var(--radius-sm);
        color: var(--text-secondary);
        display: grid; place-items: center;
        cursor: pointer;
        font-size: 15px;
        transition: var(--transition);
    }
    .topbar-toggle:hover { background: var(--content-bg); color: var(--text-primary); }

    .topbar-breadcrumb { flex: 1; min-width: 0; }
    .topbar-breadcrumb h1 {
        font-family: var(--font-display);
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .topbar-breadcrumb p { font-size: 12px; color: var(--text-muted); margin: 1px 0 0; }

    .topbar-actions { display: flex; align-items: center; gap: 8px; }
    .topbar-icon-btn {
        width: 36px; height: 36px;
        border: 1px solid var(--border-color);
        background: #fff;
        border-radius: var(--radius-sm);
        color: var(--text-secondary);
        display: grid; place-items: center;
        font-size: 14px;
        position: relative;
        transition: var(--transition);
    }
    .topbar-icon-btn:hover { background: var(--content-bg); color: var(--primary); border-color: var(--border-strong); }
    .topbar-notification-dot {
        position: absolute; top: 6px; right: 6px;
        width: 8px; height: 8px;
        background: var(--accent);
        border-radius: 50%;
        border: 2px solid #fff;
    }

    .topbar-user {
        display: flex; align-items: center; gap: 10px;
        padding: 5px 10px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: #fff;
        cursor: pointer;
        transition: var(--transition);
    }
    .topbar-user:hover { background: var(--content-bg); }
    .topbar-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        display: grid; place-items: center;
        font-weight: 700; font-size: 12px;
    }
    .topbar-user-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }

    .admin-content { flex: 1; padding: 24px; overflow-x: hidden; }

    /* ---------- Cards ---------- */
    .admin-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: var(--transition);
    }
    .admin-card:hover { box-shadow: var(--card-shadow-hover); }
    .admin-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--border-color);
        background: #FCFBF7;
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
    }
    .admin-card-title {
        font-family: var(--font-display);
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .admin-card-title i { color: var(--secondary); font-size: 15px; }
    .admin-card-body { padding: 22px; }

    /* ---------- Stat cards ---------- */
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        padding: 20px;
        display: flex; align-items: flex-start; gap: 14px;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
    }
    .stat-card::after {
        content: '';
        position: absolute; inset: auto 0 0 0;
        height: 3px;
        background: var(--stat-color, var(--secondary));
        transform: scaleX(0);
        transform-origin: left;
        transition: var(--transition);
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--card-shadow-hover); }
    .stat-card:hover::after { transform: scaleX(1); }

    .stat-icon {
        width: 48px; height: 48px; flex: 0 0 48px;
        border-radius: var(--radius-md);
        display: grid; place-items: center;
        font-size: 19px;
    }
    .stat-info { flex: 1; min-width: 0; }
    .stat-value {
        font-family: var(--font-display);
        font-size: 26px; font-weight: 700;
        color: var(--text-primary);
        line-height: 1.1;
        margin-bottom: 2px;
    }
    .stat-label { font-size: 12.5px; color: var(--text-muted); font-weight: 500; }
    .stat-change { font-size: 12px; margin-top: 5px; display: flex; align-items: center; gap: 4px; }
    .stat-change.up { color: var(--success); }
    .stat-change.down { color: var(--danger); }

    .stat-card-primary { --stat-color: var(--primary); }
    .stat-card-primary .stat-icon { background: rgba(31,75,63,.1); color: var(--primary); }
    .stat-card-success { --stat-color: var(--success); }
    .stat-card-success .stat-icon { background: rgba(46,125,79,.1); color: var(--success); }
    .stat-card-warning { --stat-color: var(--warning); }
    .stat-card-warning .stat-icon { background: rgba(169,117,47,.12); color: var(--warning); }
    .stat-card-info { --stat-color: var(--secondary); }
    .stat-card-info .stat-icon { background: rgba(47,107,87,.1); color: var(--secondary); }
    .stat-card-danger { --stat-color: var(--danger); }
    .stat-card-danger .stat-icon { background: rgba(179,57,46,.1); color: var(--danger); }

    /* ---------- Buttons ---------- */
    .btn {
        font-family: var(--font-main);
        font-weight: 600;
        font-size: 13.5px;
        border-radius: var(--radius-sm);
        display: inline-flex; align-items: center; gap: 6px;
        transition: var(--transition);
    }
    .btn-primary, .btn-primary:focus { background: var(--primary); border-color: var(--primary); color: #fff; }
    .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); color: #fff; }
    .btn-secondary, .btn-secondary:focus { background: var(--secondary); border-color: var(--secondary); color: #fff; }
    .btn-secondary:hover { background: var(--secondary-hover); border-color: var(--secondary-hover); color: #fff; }
    .btn-outline-primary { color: var(--primary); border-color: var(--border-strong); }
    .btn-outline-primary:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
    .btn-outline-secondary { color: var(--text-secondary); border-color: var(--border-strong); }
    .btn-outline-secondary:hover { background: var(--content-bg); border-color: var(--border-strong); color: var(--text-primary); }
    .btn-success { background: var(--success); border-color: var(--success); }
    .btn-danger  { background: var(--danger);  border-color: var(--danger); }
    .btn-warning { background: var(--warning); border-color: var(--warning); color: #fff; }
    .btn-warning:hover { color: #fff; }
    .btn-sm { font-size: 12px; padding: 5px 11px; }
    .btn-xs { font-size: 11px; padding: 3px 8px; border-radius: 4px; }

    /* ---------- Forms ---------- */
    .form-label { font-weight: 600; font-size: 12.8px; color: var(--text-primary); margin-bottom: 5px; }
    .form-label .required { color: var(--danger); margin-left: 2px; }
    .form-control, .form-select {
        font-family: var(--font-main);
        font-size: 13.5px;
        color: var(--text-primary);
        background: #fff;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 8px 13px;
        transition: var(--transition);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(47,107,87,.12);
    }
    .form-control::placeholder { color: #A6B0A9; }
    .form-hint, .form-text { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    .form-section-title {
        font-family: var(--font-display);
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
        display: flex; align-items: center; gap: 8px;
    }
    .form-section-title i { color: var(--secondary); }
    .input-group-text { background: var(--content-bg); border: 1.5px solid var(--border-color); color: var(--text-muted); font-size: 13px; }
    .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }

    /* ---------- Tables ---------- */
    .table { font-size: 13.5px; color: var(--text-primary); margin-bottom: 0; }
    .table thead th {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--text-muted);
        background: #FCFBF7;
        border-bottom: 2px solid var(--border-color);
        padding: 11px 15px;
        white-space: nowrap;
    }
    .table tbody td { padding: 11px 15px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
    .table tbody tr:last-child td { border-bottom: 0; }
    .table tbody tr:hover td { background: #FBFAF6; }
    .table-thumbnail {
        width: 48px; height: 36px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
    }
    .table-cover {
        width: 40px; height: 56px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid var(--border-color);
        background: var(--content-bg);
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 5px 10px;
        font-family: var(--font-main);
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: var(--secondary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(47,107,87,.12);
    }
    .dataTables_wrapper .dataTables_length select { width: auto; min-width: 74px; display: inline-block; }
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label { font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px; margin: 0; }
    .dataTables_wrapper .dataTables_filter label { justify-content: flex-end; }
    .dataTables_wrapper .dataTables_info { font-size: 12px; color: var(--text-muted); }
    .dataTables_wrapper .dataTables_paginate .paginate_button { font-size: 13px; border-radius: var(--radius-sm) !important; }
    /* DataTables' Bootstrap 5 integration renders pagination as .page-item/.page-link */
    .dataTables_wrapper .pagination .page-link {
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        font-size: 13px;
        padding: 5px 11px;
    }
    .dataTables_wrapper .pagination .page-link:hover { background: var(--content-bg); color: var(--primary); }
    .dataTables_wrapper .pagination .page-item.active .page-link,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #fff !important;
    }
    .dataTables_wrapper .pagination .page-item.disabled .page-link { color: var(--border-strong); }

    /* ---------- Badges ---------- */
    .badge { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; letter-spacing: .02em; }
    .badge.bg-primary   { background: var(--primary) !important; }
    .badge.bg-secondary { background: var(--text-muted) !important; }
    .badge.bg-success   { background: var(--success) !important; }
    .badge.bg-warning   { background: var(--warning) !important; color: #fff !important; }
    .badge.bg-danger    { background: var(--danger) !important; }
    .badge.bg-info      { background: var(--info) !important; color: #fff !important; }

    .badge-status-new          { background:#E4EDF4; color:#23566F; }
    .badge-status-contacted    { background:#E8F0EA; color:#1F5637; }
    .badge-status-follow_up    { background:#F7EEDF; color:#7A5A0C; }
    .badge-status-negotiation  { background:#EDE9E0; color:#5A5344; }
    .badge-status-closed_won   { background:#E1EFE6; color:#1F5637; }
    .badge-status-closed_lost  { background:#FBEDEC; color:#8A2C23; }
    .badge-status-published    { background:#E1EFE6; color:#1F5637; }
    .badge-status-draft        { background:#EDEAE0; color:#5A5344; }
    .badge-priority-low        { background:#F1F0E9; color:#45544D; }
    .badge-priority-medium     { background:#F7EEDF; color:#7A5A0C; }
    .badge-priority-high       { background:#FBEDEC; color:#8A2C23; }

    /* ---------- Flash ---------- */
    .flash-container {
        position: fixed;
        top: 78px; right: 22px;
        z-index: 9999;
        display: flex; flex-direction: column; gap: 10px;
        max-width: 390px;
        width: calc(100% - 44px);
    }
    .flash-message {
        background: #fff;
        border: 1px solid var(--border-color);
        border-left: 3px solid;
        border-radius: var(--radius-md);
        padding: 13px 16px;
        box-shadow: 0 10px 30px rgba(22,33,28,.14);
        display: flex; align-items: flex-start; gap: 11px;
        animation: flashIn .3s ease forwards;
    }
    @keyframes flashIn { from { opacity:0; transform:translateX(18px);} to { opacity:1; transform:none;} }
    @keyframes flashOut { from { opacity:1; transform:none;} to { opacity:0; transform:translateX(18px);} }
    .flash-message.flash-out { animation: flashOut .28s ease forwards; }
    .flash-message.success { border-left-color: var(--success); }
    .flash-message.error   { border-left-color: var(--danger); }
    .flash-message.warning { border-left-color: var(--warning); }
    .flash-message.info    { border-left-color: var(--info); }
    .flash-icon { font-size: 15px; margin-top: 2px; flex-shrink: 0; }
    .flash-message.success .flash-icon { color: var(--success); }
    .flash-message.error   .flash-icon { color: var(--danger); }
    .flash-message.warning .flash-icon { color: var(--warning); }
    .flash-message.info    .flash-icon { color: var(--info); }
    .flash-content { flex: 1; }
    .flash-title { font-weight: 700; font-size: 13px; color: var(--text-primary); }
    .flash-text  { font-size: 12.5px; color: var(--text-secondary); line-height: 1.45; }
    .flash-close { background: none; border: 0; color: var(--text-muted); cursor: pointer; font-size: 13px; padding: 0; }
    .flash-close:hover { color: var(--text-primary); }

    /* ---------- Page header ---------- */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; margin-bottom: 22px; flex-wrap: wrap;
    }
    .page-header-left h2 {
        font-family: var(--font-display);
        font-size: 21px; font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 3px;
    }
    .page-header-left p { font-size: 13px; color: var(--text-muted); margin: 0; }
    .page-header-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* ---------- Media preview ---------- */
    .img-preview-wrap { position: relative; display: inline-block; }
    .img-preview {
        width: 120px; height: 90px;
        object-fit: cover;
        border-radius: var(--radius-md);
        border: 2px solid var(--border-color);
    }
    .img-preview:hover { border-color: var(--secondary); }
    .img-preview-large { width: 200px; height: 140px; }
    .img-preview-cover { width: 120px; height: 168px; }
    .img-remove-btn {
        position: absolute; top: -8px; right: -8px;
        width: 24px; height: 24px;
        background: var(--danger);
        color: #fff; border: 0; border-radius: 50%;
        font-size: 11px;
        display: grid; place-items: center;
        cursor: pointer;
        transition: var(--transition);
    }
    .img-remove-btn:hover { background: #93281F; transform: scale(1.08); }

    /* ---------- Tabs ---------- */
    .nav-tabs { border-bottom: 2px solid var(--border-color); gap: 4px; }
    .nav-tabs .nav-link {
        font-weight: 600; font-size: 13.5px;
        color: var(--text-muted);
        border: 0;
        border-radius: var(--radius-sm) var(--radius-sm) 0 0;
        padding: 10px 18px;
        display: flex; align-items: center; gap: 8px;
    }
    .nav-tabs .nav-link:hover { color: var(--text-primary); background: #FCFBF7; }
    .nav-tabs .nav-link.active {
        color: var(--primary);
        background: var(--card-bg);
        border-bottom: 2px solid var(--primary);
        margin-bottom: -2px;
    }

    /* ---------- Filter pills ---------- */
    .status-filter-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 18px; }
    .status-filter-btn {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1.5px solid var(--border-color);
        background: #fff;
        font-size: 12.5px; font-weight: 600;
        color: var(--text-secondary);
        display: flex; align-items: center; gap: 6px;
        transition: var(--transition);
    }
    .status-filter-btn:hover { border-color: var(--secondary); color: var(--primary); }
    .status-filter-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }
    .status-filter-count { font-size: 11px; padding: 1px 7px; border-radius: 10px; background: rgba(255,255,255,.22); }
    .status-filter-btn:not(.active) .status-filter-count { background: var(--content-bg); }

    /* ---------- Detail view ---------- */
    .detail-item { display: flex; flex-direction: column; gap: 2px; padding: 11px 0; border-bottom: 1px solid var(--border-color); }
    .detail-item:last-child { border-bottom: 0; }
    .detail-label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text-muted); }
    .detail-value { font-size: 14px; font-weight: 500; color: var(--text-primary); }

    /* ---------- Timeline ---------- */
    .timeline { position: relative; padding-left: 26px; }
    .timeline::before { content: ''; position: absolute; left: 7px; top: 4px; bottom: 0; width: 2px; background: var(--border-color); }
    .timeline-item { position: relative; margin-bottom: 15px; }
    .timeline-dot {
        position: absolute; left: -23px; top: 5px;
        width: 12px; height: 12px;
        border-radius: 50%;
        background: var(--secondary);
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px var(--border-color);
    }
    .timeline-content { background: #FCFBF7; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 11px 15px; }
    .timeline-time { font-size: 11px; color: var(--text-muted); margin-bottom: 3px; }
    .timeline-text { font-size: 13px; color: var(--text-primary); line-height: 1.5; }

    .chart-container { position: relative; padding: 8px 0; }

    /* ---------- Quick actions ---------- */
    .quick-action-btn {
        display: flex; align-items: center; gap: 12px;
        padding: 13px 16px;
        background: #fff;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-weight: 600; font-size: 13.5px;
        transition: var(--transition);
    }
    .quick-action-btn:hover {
        border-color: var(--secondary);
        color: var(--primary);
        background: #FBFAF6;
        transform: translateY(-1px);
    }
    .quick-action-icon {
        width: 38px; height: 38px; flex: 0 0 38px;
        border-radius: var(--radius-sm);
        background: rgba(47,107,87,.1);
        color: var(--secondary);
        display: grid; place-items: center;
        font-size: 15px;
    }

    .export-icon-card { text-align: center; padding: 30px 22px; }
    .export-icon {
        width: 60px; height: 60px;
        border-radius: var(--radius-lg);
        background: rgba(47,107,87,.1);
        color: var(--secondary);
        font-size: 25px;
        display: grid; place-items: center;
        margin: 0 auto 15px;
    }

    /* ---------- Empty state ---------- */
    .empty-state { text-align: center; padding: 44px 22px; }
    .empty-state-icon { font-size: 42px; color: var(--border-strong); margin-bottom: 14px; display: block; }
    .empty-state h4 { font-family: var(--font-display); font-size: 17px; font-weight: 700; color: var(--text-secondary); margin-bottom: 7px; }
    .empty-state p { color: var(--text-muted); font-size: 13px; margin-bottom: 18px; }

    /* ---------- Colour preview (settings) ---------- */
    .color-preview-card { border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border-color); }
    .color-preview-bar { height: 8px; background: linear-gradient(90deg, var(--preview-primary,#1F4B3F), var(--preview-secondary,#2F6B57), var(--preview-accent,#A9752F)); }
    .color-preview-body { padding: 18px; background: #fff; }
    .color-preview-btn { display: inline-block; padding: 8px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; color: #fff; background: var(--preview-primary,#1F4B3F); margin-right: 8px; }
    .color-preview-btn-sec { background: var(--preview-secondary,#2F6B57); }
    .color-preview-btn-acc { background: var(--preview-accent,#A9752F); }

    /* ---------- Editor ---------- */
    .editor-toolbar {
        display: flex; flex-wrap: wrap; gap: 4px;
        padding: 8px 11px;
        background: #FCFBF7;
        border: 1.5px solid var(--border-color);
        border-bottom: 0;
        border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    }
    .editor-toolbar .toolbar-btn {
        width: 31px; height: 31px;
        border: 1px solid var(--border-color);
        background: #fff;
        border-radius: 4px;
        font-size: 12.5px; font-weight: 600;
        color: var(--text-primary);
        display: grid; place-items: center;
        cursor: pointer;
        transition: var(--transition);
    }
    .editor-toolbar .toolbar-btn:hover { background: var(--secondary); border-color: var(--secondary); color: #fff; }
    .editor-toolbar .toolbar-sep { width: 1px; background: var(--border-color); margin: 4px; }
    .editor-textarea {
        border-radius: 0 0 var(--radius-sm) var(--radius-sm) !important;
        min-height: 340px;
        resize: vertical;
        font-family: ui-monospace, 'Cascadia Code', Consolas, monospace;
        font-size: 13px;
    }

    .action-btns { display: flex; gap: 4px; flex-wrap: nowrap; }
    .breadcrumb { font-size: 12px; margin: 0; padding: 0; background: transparent; }
    .breadcrumb-item a { color: var(--text-secondary); }
    .breadcrumb-item.active { color: var(--text-muted); }

    .alert { border-radius: var(--radius-md); border: 1px solid transparent; font-size: 13.5px; }
    .alert-success { background:#E8F0EA; border-color:rgba(46,125,79,.25); color:#1F5637; }
    .alert-danger  { background:#FBEDEC; border-color:rgba(179,57,46,.22); color:#8A2C23; }
    .alert-warning { background:#F7EEDF; border-color:rgba(169,117,47,.25); color:#7A5A0C; }
    .alert-info    { background:#EAF2F6; border-color:rgba(44,110,143,.22); color:#23566F; }

    .form-select { padding-right: 32px; }

    .loading-overlay {
        position: fixed; inset: 0;
        background: rgba(251,250,246,.82);
        display: grid; place-items: center;
        z-index: 9998;
        backdrop-filter: blur(2px);
    }
    .loading-spinner {
        width: 44px; height: 44px;
        border: 4px solid var(--border-color);
        border-top-color: var(--secondary);
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .admin-footer {
        background: var(--card-bg);
        border-top: 1px solid var(--border-color);
        padding: 13px 24px;
        font-size: 12px;
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
    }

    .text-primary-custom { color: var(--primary); }
    .text-secondary-custom { color: var(--secondary); }
    .text-accent { color: var(--accent); }
    .bg-primary-custom { background: var(--primary); }
    .bg-secondary-custom { background: var(--secondary); }
    .fw-700 { font-weight: 700; }
    .fw-800 { font-weight: 800; }
    .rounded-custom { border-radius: var(--radius-md); }
    .serif { font-family: var(--font-display); }
    .overdue-row td { background: #FCF4E8 !important; }
    .overdue-row:hover td { background: #F8EDDC !important; }

    /* ---------- Responsive ---------- */
    @media (max-width: 1024px) {
        .admin-sidebar { width: var(--sidebar-collapsed-width); }
        .admin-sidebar .sidebar-brand-text,
        .admin-sidebar .sidebar-nav-label,
        .admin-sidebar .sidebar-section-title,
        .admin-sidebar .sidebar-badge,
        .admin-sidebar .sidebar-user-info { display: none; }
        .admin-sidebar .sidebar-brand { justify-content: center; padding: 0 8px; }
        .admin-sidebar .sidebar-nav-item { margin: 2px 6px; }
        .admin-sidebar .sidebar-nav-link { justify-content: center; padding: 10px 8px; }
        .admin-sidebar .sidebar-nav-link.active::before { display: none; }
        .admin-sidebar .sidebar-footer { padding: 12px 6px; }
        .admin-sidebar .sidebar-user { justify-content: center; }
        .admin-main { margin-left: var(--sidebar-collapsed-width); }
    }

    @media (max-width: 1024px) and (min-width: 769px) {
        .sidebar-nav-link { position: relative; }
        .sidebar-nav-link .sidebar-nav-label {
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: #0F211C;
            color: #F3F6F4;
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            display: block !important;
            transition: opacity .2s ease;
            box-shadow: 0 6px 18px rgba(0,0,0,.3);
        }
        .sidebar-nav-link:hover .sidebar-nav-label { opacity: 1; }
    }

    @media (max-width: 768px) {
        .admin-sidebar { width: var(--sidebar-width); transform: translateX(-100%); }
        .admin-sidebar.mobile-open { transform: none; }
        .admin-sidebar .sidebar-brand-text,
        .admin-sidebar .sidebar-nav-label,
        .admin-sidebar .sidebar-section-title,
        .admin-sidebar .sidebar-badge,
        .admin-sidebar .sidebar-user-info { display: block; }
        .admin-sidebar .sidebar-brand { justify-content: flex-start; padding: 0 16px; }
        .admin-sidebar .sidebar-nav-item { margin: 2px 10px; }
        .admin-sidebar .sidebar-nav-link { justify-content: flex-start; padding: 9px 12px; }
        .admin-sidebar .sidebar-nav-link.active::before { display: block; }
        .admin-sidebar .sidebar-footer { padding: 14px 16px; }
        .admin-sidebar .sidebar-user { justify-content: flex-start; }
        .admin-main { margin-left: 0; }
        .sidebar-overlay { display: block; }
        .admin-content { padding: 16px; }
        .topbar-user-name { display: none; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .flash-container { top: 72px; right: 12px; left: 12px; width: auto; max-width: none; }
    }
    </style>
</head>
<body>

<?php
$hdr_flash = function_exists('getFlash') ? getFlash() : [];
if (!empty($hdr_flash)): ?>
<div class="flash-container" id="flashContainer">
    <?php
    $hdr_icons  = ['success' => 'fa-circle-check', 'error' => 'fa-circle-xmark', 'warning' => 'fa-triangle-exclamation', 'info' => 'fa-circle-info'];
    $hdr_titles = ['success' => 'Berhasil', 'error' => 'Gagal', 'warning' => 'Perhatian', 'info' => 'Informasi'];
    foreach ($hdr_flash as $hdr_msg):
        $hdr_type = $hdr_msg['type'] ?? 'info';
    ?>
    <div class="flash-message <?= htmlspecialchars($hdr_type) ?>">
        <i class="fa-solid <?= $hdr_icons[$hdr_type] ?? 'fa-circle-info' ?> flash-icon"></i>
        <div class="flash-content">
            <div class="flash-title"><?= htmlspecialchars($hdr_titles[$hdr_type] ?? 'Info') ?></div>
            <div class="flash-text"><?= htmlspecialchars($hdr_msg['message']) ?></div>
        </div>
        <button class="flash-close" onclick="dismissFlash(this)" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="admin-wrapper">
