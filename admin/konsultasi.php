<?php
/**
 * Admin Konsultasi (CRM) List
 */

// Handle quick status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=konsultasi');
    }

    $id         = (int)($_POST['id'] ?? 0);
    $new_status = sanitize($_POST['status'] ?? '');
    $valid_statuses = ['new', 'contacted', 'follow_up', 'negotiation', 'closed_won', 'closed_lost'];

    if ($id > 0 && in_array($new_status, $valid_statuses)) {
        try {
            update('consultations', ['status' => $new_status], 'id = ?', [$id]);
            $label = getStatusLabel($new_status);
            flash('success', 'Status konsultasi berhasil diubah menjadi: ' . $label);
        } catch (Exception $e) {
            flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
    redirect('index.php?page=konsultasi');
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token keamanan tidak valid.');
        redirect('index.php?page=konsultasi');
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            delete('consultations', 'id = ?', [$id]);
            flash('success', 'Data konsultasi berhasil dihapus.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus konsultasi.');
        }
    }
    redirect('index.php?page=konsultasi');
}

// Get filter
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';

// Get status counts for tabs
$status_counts = [];
$all_statuses = ['new', 'contacted', 'follow_up', 'negotiation', 'closed_won', 'closed_lost'];
$total_count = 0;

try {
    $r = fetch("SELECT COUNT(*) as cnt FROM consultations");
    $total_count = $r ? (int)$r['cnt'] : 0;

    foreach ($all_statuses as $s) {
        $r = fetch("SELECT COUNT(*) as cnt FROM consultations WHERE status = ?", [$s]);
        $status_counts[$s] = $r ? (int)$r['cnt'] : 0;
    }
} catch (Exception $e) {}

// Build query
$query  = "SELECT * FROM consultations";
$params = [];
if ($filter_status !== 'all') {
    $query .= " WHERE status = ?";
    $params[] = $filter_status;
}
$query .= " ORDER BY created_at DESC";

$consultations = [];
try {
    $consultations = fetchAll($query, $params);
} catch (Exception $e) {}

// Service and status labels
$service_labels = getServiceTypes();

$status_tab_labels = [
    'all'         => 'Semua',
    'new'         => 'Baru',
    'contacted'   => 'Dihubungi',
    'follow_up'   => 'Follow Up',
    'negotiation' => 'Negosiasi',
    'closed_won'  => 'Closed Won',
    'closed_lost' => 'Closed Lost',
];

$csrf = csrf_token();

require_once ADMIN_PATH . '/includes/header.php';
require_once ADMIN_PATH . '/includes/sidebar.php';
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h2>Pengajuan Naskah</h2>
            <p>Naskah dan pertanyaan yang masuk melalui formulir di situs.</p>
        </div>
        <div class="page-header-actions">
            <a href="index.php?page=export" class="btn btn-outline-secondary">
                <i class="fas fa-download me-1"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="status-filter-tabs">
        <a href="index.php?page=konsultasi"
           class="status-filter-btn <?= $filter_status === 'all' ? 'active' : '' ?>">
            <i class="fas fa-list"></i>
            Semua
            <span class="status-filter-count"><?= $total_count ?></span>
        </a>

        <?php
        $tab_colors = [
            'new'         => ['bg' => '#E4EDF4', 'color' => '#23566F'],
            'contacted'   => ['bg' => '#E8F0EA', 'color' => '#1F5637'],
            'follow_up'   => ['bg' => '#F7EEDF', 'color' => '#7A5A0C'],
            'negotiation' => ['bg' => '#EDEAE0', 'color' => '#5A5344'],
            'closed_won'  => ['bg' => '#E1EFE6', 'color' => '#1F5637'],
            'closed_lost' => ['bg' => '#FBEDEC', 'color' => '#8A2C23'],
        ];
        $tab_icons = [
            'new'         => 'fa-bell',
            'contacted'   => 'fa-phone',
            'follow_up'   => 'fa-redo',
            'negotiation' => 'fa-handshake',
            'closed_won'  => 'fa-check-circle',
            'closed_lost' => 'fa-times-circle',
        ];
        foreach ($all_statuses as $s):
            $is_active = ($filter_status === $s);
        ?>
        <a href="index.php?page=konsultasi&status=<?= $s ?>"
           class="status-filter-btn <?= $is_active ? 'active' : '' ?>"
           <?php if (!$is_active): ?>
           style="border-color:<?= $tab_colors[$s]['color'] ?>30;"
           <?php endif; ?>>
            <i class="fas <?= $tab_icons[$s] ?>"></i>
            <?= $status_tab_labels[$s] ?>
            <span class="status-filter-count"><?= $status_counts[$s] ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Consultations Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title">
                <i class="fas fa-comments"></i>
                <?= $filter_status === 'all' ? 'Semua Konsultasi' : $status_tab_labels[$filter_status] ?>
                <span class="badge" style="background:#E3E0D4;color:#45544D;font-size:11px;margin-left:4px;">
                    <?= count($consultations) ?> data
                </span>
            </h5>
        </div>
        <div class="admin-card-body">
            <?php if (!empty($consultations)): ?>
            <div class="table-responsive">
                <table class="table admin-datatable" id="konsultasiTable">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Nama / Email</th>
                            <th>Institusi</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Follow Up</th>
                            <th>Masuk</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultations as $i => $k):
                            // Check if follow-up date is overdue
                            $is_overdue = false;
                            if (!empty($k['follow_up_date']) && $k['status'] !== 'closed_won' && $k['status'] !== 'closed_lost') {
                                $is_overdue = strtotime($k['follow_up_date']) < time();
                            }
                        ?>
                        <tr class="<?= $is_overdue ? 'overdue-row' : '' ?>">
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($k['name']) ?></div>
                                <div style="font-size:11px;color:#77857D;"><?= htmlspecialchars($k['email']) ?></div>
                                <?php if (!empty($k['phone'])): ?>
                                <div style="font-size:11px;color:#A6B0A9;">
                                    <i class="fas fa-phone fa-xs me-1"></i><?= htmlspecialchars($k['phone']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:13px;"><?= htmlspecialchars($k['institution'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span style="font-size:12.5px;">
                                    <?= htmlspecialchars($service_labels[$k['service_type']] ?? $k['service_type']) ?>
                                </span>
                                <?php if (!empty($k['budget_range'])): ?>
                                <div style="font-size:11px;color:#A6B0A9;"><?= htmlspecialchars($k['budget_range']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Quick Status Update -->
                                <form method="POST" id="statusForm_<?= $k['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                    <select name="status"
                                            class="form-select form-select-sm status-select"
                                            style="font-size:12px;padding:4px 8px;min-width:130px;border-radius:20px;font-weight:600;"
                                            onchange="document.getElementById('statusForm_<?= $k['id'] ?>').submit()">
                                        <?php foreach ($all_statuses as $s): ?>
                                        <option value="<?= $s ?>" <?= $k['status'] === $s ? 'selected' : '' ?>>
                                            <?= getStatusLabel($s) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="badge badge-priority-<?= htmlspecialchars($k['priority']) ?>">
                                    <?php
                                    $prio_labels = ['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi'];
                                    echo $prio_labels[$k['priority']] ?? $k['priority'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($k['follow_up_date'])): ?>
                                    <span style="font-size:12px;<?= $is_overdue ? 'color:#dc2626;font-weight:700;' : 'color:#77857D;' ?>">
                                        <?php if ($is_overdue): ?>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?php endif; ?>
                                        <?= date('d M Y', strtotime($k['follow_up_date'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#A6B0A9;font-size:12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px;color:#77857D;white-space:nowrap;">
                                <?= function_exists('formatDate') ? formatDate($k['created_at']) : date('d M Y', strtotime($k['created_at'])) ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <!-- View Detail -->
                                    <a href="index.php?page=konsultasi-detail&id=<?= $k['id'] ?>"
                                       class="btn btn-xs btn-primary"
                                       data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- WhatsApp -->
                                    <?php if (!empty($k['phone'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $k['phone']) ?>?text=Halo%20<?= urlencode($k['name']) ?>,%20kami%20dari%20Kayaswara%20ingin%20menindaklanjuti%20konsultasi%20Anda."
                                       target="_blank"
                                       class="btn btn-xs btn-outline-success"
                                       data-bs-toggle="tooltip" title="WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <?php endif; ?>

                                    <!-- Delete -->
                                    <form method="POST" style="display:inline;"
                                          onsubmit="return confirmDelete(this, '<?= addslashes(htmlspecialchars($k['name'])) ?>')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-danger"
                                                data-bs-toggle="tooltip" title="Hapus">
                                            <i class="fas fa-trash"></i>
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
                <i class="fas fa-comments empty-state-icon"></i>
                <h4>Tidak Ada Data Konsultasi</h4>
                <p>
                    <?php if ($filter_status !== 'all'): ?>
                        Tidak ada konsultasi dengan status "<?= $status_tab_labels[$filter_status] ?>".
                    <?php else: ?>
                        Belum ada permintaan konsultasi yang masuk.
                    <?php endif; ?>
                </p>
                <?php if ($filter_status !== 'all'): ?>
                <a href="index.php?page=konsultasi" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i>Lihat Semua
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overdue Legend -->
    <?php if (!empty($consultations)): ?>
    <div style="margin-top:12px;font-size:12px;color:#77857D;display:flex;align-items:center;gap:8px;">
        <div style="width:14px;height:14px;background:#fff8f0;border:1px solid #fed7aa;border-radius:3px;"></div>
        <span>Baris dengan latar kuning = follow-up date telah lewat / overdue</span>
    </div>
    <?php endif; ?>
</div>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>
