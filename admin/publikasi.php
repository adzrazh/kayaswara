<?php
/**
 * Admin — Katalog Publikasi (daftar).
 */

$categories = getPublicationCategories();

// Hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=publikasi');
    }
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $row = fetch("SELECT cover FROM publications WHERE id = ?", [$id]);
            if ($row && !empty($row['cover'])) {
                deleteImage('publications/' . $row['cover']);
            }
            delete('publications', 'id = ?', [$id]);
            flash('success', 'Judul berhasil dihapus dari katalog.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus judul: ' . $e->getMessage());
        }
    }
    redirect('index.php?page=publikasi');
}

// Ubah status terbit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_status') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=publikasi');
    }
    $id  = (int) ($_POST['id'] ?? 0);
    $new = ($_POST['current_status'] ?? '') === 'published' ? 'draft' : 'published';
    if ($id > 0) {
        try {
            update('publications', ['status' => $new], 'id = ?', [$id]);
            flash('success', 'Status diubah menjadi ' . ($new === 'published' ? 'Tayang' : 'Draf') . '.');
        } catch (Exception $e) {
            flash('error', 'Gagal mengubah status.');
        }
    }
    redirect('index.php?page=publikasi');
}

// Sorot / lepas sorotan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_featured') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=publikasi');
    }
    $id  = (int) ($_POST['id'] ?? 0);
    $new = (int) ($_POST['current'] ?? 0) === 1 ? 0 : 1;
    if ($id > 0) {
        try {
            update('publications', ['is_featured' => $new], 'id = ?', [$id]);
            flash('success', $new ? 'Judul ditandai sebagai unggulan.' : 'Tanda unggulan dilepas.');
        } catch (Exception $e) {
            flash('error', 'Gagal mengubah sorotan.');
        }
    }
    redirect('index.php?page=publikasi');
}

$items       = [];
$tableExists = true;
$stats = ['total' => 0, 'published' => 0, 'draft' => 0, 'featured' => 0];

try {
    $items = fetchAll("SELECT * FROM publications ORDER BY is_featured DESC, publish_year DESC, id DESC");
    foreach ($items as $it) {
        $stats['total']++;
        if ($it['status'] === 'published') $stats['published']++;
        if ($it['status'] === 'draft')     $stats['draft']++;
        if ((int) $it['is_featured'] === 1) $stats['featured']++;
    }
} catch (Exception $e) {
    $tableExists = false;
}

$csrf = csrf_token();

require_once ADMIN_PATH . '/includes/header.php';
require_once ADMIN_PATH . '/includes/sidebar.php';
?>

<div class="admin-content">

    <div class="page-header">
        <div class="page-header-left">
            <h2>Katalog Publikasi</h2>
            <p>Daftar buku terbitan yang tampil pada halaman Publikasi di situs.</p>
        </div>
        <div class="page-header-actions">
            <a href="../publikasi" target="_blank" rel="noopener" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>Lihat Katalog
            </a>
            <a href="index.php?page=publikasi-form" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>Tambah Judul
            </a>
        </div>
    </div>

    <?php if (!$tableExists): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        Tabel <code>publications</code> belum ada pada basis data. Jalankan <code>migrate.php</code> terlebih dahulu.
    </div>
    <?php else: ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="fa-solid fa-book-bookmark"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['total'] ?></div>
                    <div class="stat-label">Total judul</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['published'] ?></div>
                    <div class="stat-label">Tayang di situs</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="fa-regular fa-pen-to-square"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['draft'] ?></div>
                    <div class="stat-label">Masih draf</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['featured'] ?></div>
                    <div class="stat-label">Unggulan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title">
                <i class="fa-solid fa-book-open"></i>Daftar Terbitan
            </h5>
            <a href="index.php?page=publikasi-form" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-plus"></i>Tambah
            </a>
        </div>
        <div class="admin-card-body">
            <?php if (!empty($items)): ?>
            <div class="table-responsive">
                <table class="table admin-datatable">
                    <thead>
                        <tr>
                            <th width="60">Sampul</th>
                            <th>Judul &amp; Penulis</th>
                            <th>Kategori</th>
                            <th width="70">Tahun</th>
                            <th>Status</th>
                            <th width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <?php if (!empty($it['cover'])): ?>
                                    <img src="../assets/uploads/publications/<?= htmlspecialchars($it['cover']) ?>"
                                         alt="Sampul" class="table-cover">
                                <?php else: ?>
                                    <div class="table-cover d-grid" style="place-items:center;">
                                        <i class="fa-solid fa-book" style="color:var(--border-strong);"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:600;max-width:320px;">
                                    <?= htmlspecialchars(truncate($it['title'], 70)) ?>
                                    <?php if ((int) $it['is_featured'] === 1): ?>
                                        <i class="fa-solid fa-star ms-1" style="color:var(--accent);font-size:11px;" title="Unggulan"></i>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:12px;color:var(--text-muted);">
                                    <?= htmlspecialchars(truncate($it['authors'], 70)) ?>
                                </div>
                                <?php if (!empty($it['isbn'])): ?>
                                <div style="font-size:11px;color:var(--text-muted);">ISBN <?= htmlspecialchars($it['isbn']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background:var(--content-bg);color:var(--text-secondary);">
                                    <?= htmlspecialchars($categories[$it['category']] ?? $it['category']) ?>
                                </span>
                            </td>
                            <td><?= $it['publish_year'] ? (int) $it['publish_year'] : '—' ?></td>
                            <td>
                                <span class="badge badge-status-<?= htmlspecialchars($it['status']) ?>">
                                    <?= $it['status'] === 'published' ? 'Tayang' : 'Draf' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?page=publikasi-form&id=<?= (int) $it['id'] ?>"
                                       class="btn btn-xs btn-outline-primary" data-bs-toggle="tooltip" title="Ubah">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
                                        <input type="hidden" name="current_status" value="<?= htmlspecialchars($it['status']) ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-secondary"
                                                data-bs-toggle="tooltip"
                                                title="<?= $it['status'] === 'published' ? 'Jadikan draf' : 'Tayangkan' ?>">
                                            <i class="fa-solid <?= $it['status'] === 'published' ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                        </button>
                                    </form>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
                                        <input type="hidden" name="current" value="<?= (int) $it['is_featured'] ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-secondary"
                                                data-bs-toggle="tooltip"
                                                title="<?= (int) $it['is_featured'] === 1 ? 'Lepas unggulan' : 'Jadikan unggulan' ?>">
                                            <i class="<?= (int) $it['is_featured'] === 1 ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                        </button>
                                    </form>

                                    <form method="POST" style="display:inline;"
                                          onsubmit="return deleteConfirm('<?= addslashes(htmlspecialchars($it['title'])) ?>')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-danger"
                                                data-bs-toggle="tooltip" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-book-open empty-state-icon"></i>
                <h4>Katalog masih kosong</h4>
                <p>Tambahkan buku yang telah diterbitkan agar tampil pada halaman Publikasi.</p>
                <a href="index.php?page=publikasi-form" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i>Tambah Judul Pertama
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>
