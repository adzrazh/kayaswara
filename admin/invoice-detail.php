<?php
/**
 * Admin: Detail & Manajemen Invoice
 */
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { flash('error','Invoice tidak ditemukan.'); redirect('index.php?page=pesanan'); }

$inv = fetch("SELECT * FROM invoices WHERE id = ?", [$id]);
if (!$inv) { flash('error','Invoice tidak ditemukan.'); redirect('index.php?page=pesanan'); }

$order = fetch("SELECT * FROM orders WHERE id = ?", [$inv['order_id']]);
if (!$order) { flash('error','Data pesanan tidak ditemukan.'); redirect('index.php?page=pesanan'); }

$csrf = csrf_token();

// ── AJAX / POST Actions ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        if ($isAjax) { header('Content-Type:application/json'); echo json_encode(['success'=>false,'message'=>'Token keamanan tidak valid.']); exit; }
        flash('error','Token keamanan tidak valid.');
        redirect('index.php?page=invoice-detail&id='.$id);
    }

    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'send_email') {
        $result = sendInvoiceEmail($inv, $order);
        if ($result['success']) {
            update('invoices', ['status'=>'sent','updated_at'=>date('Y-m-d H:i:s')], 'id = ?', [$id]);
        }
        if ($isAjax) { header('Content-Type:application/json'); echo json_encode($result); exit; }
        flash($result['success']?'success':'error', $result['success']?'Invoice terkirim ke '.$order['client_email'].'.':$result['message']);
        redirect('index.php?page=invoice-detail&id='.$id);
    }

    if ($action === 'mark_paid') {
        update('invoices', ['status'=>'paid','paid_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')], 'id = ?', [$id]);
        if ($isAjax) { header('Content-Type:application/json'); echo json_encode(['success'=>true,'message'=>'Invoice ditandai lunas.']); exit; }
        flash('success','Invoice ditandai lunas.');
        redirect('index.php?page=invoice-detail&id='.$id);
    }

    if ($action === 'cancel') {
        update('invoices', ['status'=>'cancelled','updated_at'=>date('Y-m-d H:i:s')], 'id = ?', [$id]);
        if ($isAjax) { header('Content-Type:application/json'); echo json_encode(['success'=>true,'message'=>'Invoice dibatalkan.']); exit; }
        flash('success','Invoice dibatalkan.');
        redirect('index.php?page=invoice-detail&id='.$id);
    }

    if ($action === 'regenerate_pdf') {
        try {
            generateInvoicePDF($inv, $order);
            if ($isAjax) { header('Content-Type:application/json'); echo json_encode(['success'=>true,'message'=>'PDF berhasil di-generate ulang.']); exit; }
            flash('success','PDF berhasil di-generate ulang.');
        } catch (Exception $e) {
            if ($isAjax) { header('Content-Type:application/json'); echo json_encode(['success'=>false,'message'=>$e->getMessage()]); exit; }
            flash('error','Gagal generate PDF: '.$e->getMessage());
        }
        redirect('index.php?page=invoice-detail&id='.$id);
    }
}

// Reload fresh
$inv = fetch("SELECT * FROM invoices WHERE id = ?", [$id]);

$statusConfig = [
    'draft'     => ['label'=>'Draft',     'badge'=>'secondary', 'icon'=>'fa-file'],
    'sent'      => ['label'=>'Terkirim',  'badge'=>'info',      'icon'=>'fa-paper-plane'],
    'paid'      => ['label'=>'Lunas',     'badge'=>'success',   'icon'=>'fa-check-circle'],
    'cancelled' => ['label'=>'Dibatalkan','badge'=>'danger',    'icon'=>'fa-times-circle'],
];
$sc = $statusConfig[$inv['status']] ?? $statusConfig['draft'];

$siteUrl = defined('SITE_URL') ? rtrim(SITE_URL,'/') : (defined('APP_URL') ? rtrim(APP_URL,'/') : '');
$pdfUrl  = $siteUrl . '/invoice/' . $inv['token'];

$pdfDir  = defined('ROOT_PATH') ? ROOT_PATH . '/invoices' : dirname(__DIR__) . '/invoices';
$pdfFile = $pdfDir . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ','',$inv['invoice_number'])) . '.pdf';
$pdfExists = file_exists($pdfFile);

$serviceOpts = ['setup_ojs'=>'Penerbitan Buku','migrasi'=>'Konversi KTI','kustomisasi'=>'Editing & Layout','pelatihan'=>'Desain Cover','maintenance'=>'Distribusi & Pemasaran','indeksasi_doaj'=>'Cek Plagiasi','indeksasi_sinta'=>'Konsultasi Penulisan','lainnya'=>'Lainnya'];
$packageOpts = ['basic'=>'Basic','professional'=>'Professional','premium'=>'Premium','custom'=>'Custom'];

require_once ADMIN_PATH . '/includes/header.php';
require_once ADMIN_PATH . '/includes/sidebar.php';
?>
<div class="admin-content">
    <div class="page-header">
        <div class="page-header-left">
            <h2>Invoice <?= htmlspecialchars($inv['invoice_number']) ?></h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?page=pesanan">Pesanan</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=pesanan-detail&id=<?= $inv['order_id'] ?>"><?= htmlspecialchars($order['tracking_code']) ?></a></li>
                <li class="breadcrumb-item active">Invoice <?= htmlspecialchars($inv['invoice_number']) ?></li>
            </ol></nav>
        </div>
        <div class="page-header-actions">
            <a href="index.php?page=pesanan-detail&id=<?= $inv['order_id'] ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Pesanan
            </a>
            <?php if ($pdfExists): ?>
            <a href="<?= htmlspecialchars($pdfUrl) ?>" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-file-pdf me-1"></i>Lihat PDF
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Invoice Detail -->
        <div class="col-xl-8">
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-file-invoice-dollar"></i> Detail Invoice</h5>
                    <span class="badge bg-<?= $sc['badge'] ?>" style="font-size:13px;padding:6px 14px;">
                        <i class="fas <?= $sc['icon'] ?> me-1"></i><?= $sc['label'] ?>
                    </span>
                </div>
                <div style="padding:24px;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin-bottom:12px;">Info Invoice</div>
                            <?php foreach ([
                                ['Nomor Invoice', htmlspecialchars($inv['invoice_number'])],
                                ['Tanggal Dibuat', date('d F Y', strtotime($inv['created_at']))],
                                ['Jatuh Tempo', $inv['due_date'] ? date('d F Y', strtotime($inv['due_date'])) : '-'],
                                ['Dibayar Pada', $inv['paid_at'] ? date('d F Y H:i', strtotime($inv['paid_at'])) : '-'],
                            ] as [$lbl, $val]): ?>
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                                <span style="color:#64748b;"><?= $lbl ?></span>
                                <span style="font-weight:600;"><?= $val ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;margin-bottom:12px;">Klien</div>
                            <?php foreach (array_filter([
                                ['Nama', htmlspecialchars($order['client_name'])],
                                ['Institusi', $order['client_institution'] ? htmlspecialchars($order['client_institution']) : null],
                                ['Email', $order['client_email'] ? htmlspecialchars($order['client_email']) : null],
                                ['No. HP/WA', $order['client_phone'] ? htmlspecialchars($order['client_phone']) : null],
                                ['Layanan', htmlspecialchars($serviceOpts[$order['service_type']] ?? $order['service_type'])],
                                ['Paket', htmlspecialchars($packageOpts[$order['package_tier']] ?? $order['package_tier'])],
                            ], fn($r) => $r[1] !== null) as [$lbl, $val]): ?>
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                                <span style="color:#64748b;"><?= $lbl ?></span>
                                <span style="font-weight:600;"><?= $val ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Amount Table -->
                    <div style="margin-top:24px;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                            <thead>
                                <tr style="background:var(--primary);color:#fff;">
                                    <th style="padding:10px 16px;text-align:left;">Deskripsi</th>
                                    <th style="padding:10px 16px;text-align:center;">Qty</th>
                                    <th style="padding:10px 16px;text-align:right;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:12px 16px;font-weight:600;">
                                        <?= htmlspecialchars(($serviceOpts[$order['service_type']] ?? $order['service_type']) . ' – ' . ($packageOpts[$order['package_tier']] ?? $order['package_tier'])) ?>
                                        <?php if (!empty($order['description'])): ?>
                                        <div style="font-size:11px;color:#94a3b8;margin-top:3px;"><?= htmlspecialchars(mb_substr(strip_tags($order['description']), 0, 100)) ?><?= mb_strlen(strip_tags($order['description'])) > 100 ? '…' : '' ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:12px 16px;text-align:center;">1</td>
                                    <td style="padding:12px 16px;text-align:right;font-weight:600;">Rp <?= number_format($inv['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                            <tfoot style="background:#f8fafc;">
                                <?php if ($inv['discount'] > 0): ?>
                                <tr style="border-top:1px solid #e2e8f0;">
                                    <td colspan="2" style="padding:8px 16px;color:#64748b;">Diskon</td>
                                    <td style="padding:8px 16px;text-align:right;color:#dc2626;">-Rp <?= number_format($inv['discount'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($inv['show_pph23'] && $inv['tax_pph23'] > 0): ?>
                                <tr style="border-top:1px solid #e2e8f0;">
                                    <td colspan="2" style="padding:8px 16px;color:#64748b;">PPh 23 (2%)*</td>
                                    <td style="padding:8px 16px;text-align:right;color:#B8860B;">-Rp <?= number_format($inv['tax_pph23'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr style="border-top:2px solid var(--primary);">
                                    <td colspan="2" style="padding:12px 16px;font-weight:800;font-size:15px;">Total Dibayar</td>
                                    <td style="padding:12px 16px;text-align:right;font-weight:800;font-size:15px;color:var(--primary);">Rp <?= number_format($inv['total'], 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php if ($inv['show_pph23'] && $inv['tax_pph23'] > 0): ?>
                    <p style="font-size:11px;color:#94a3b8;margin-top:8px;margin-bottom:0;">*) PPh 23 dipotong oleh pembeli badan usaha sesuai ketentuan perpajakan.</p>
                    <?php endif; ?>

                    <?php if (!empty($inv['admin_notes'])): ?>
                    <div style="margin-top:16px;padding:12px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid var(--primary);">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">CATATAN ADMIN</div>
                        <div style="font-size:13px;color:#374151;"><?= nl2br(htmlspecialchars($inv['admin_notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="col-xl-4">
            <!-- PDF -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-file-pdf"></i> File PDF</h5>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:10px;">
                    <?php if ($pdfExists): ?>
                    <div style="padding:10px 14px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;font-size:13px;color:#374151;">
                        <i class="fas fa-check-circle me-1" style="color:#16a34a;"></i> PDF sudah tersedia
                    </div>
                    <a href="<?= htmlspecialchars($pdfUrl) ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>Lihat / Download PDF
                    </a>
                    <div style="font-size:11px;color:#94a3b8;word-break:break-all;">
                        Link pelanggan: <a href="<?= htmlspecialchars($pdfUrl) ?>" target="_blank" style="color:var(--primary);"><?= htmlspecialchars($pdfUrl) ?></a>
                    </div>
                    <?php else: ?>
                    <div style="padding:10px 14px;background:#fef3c7;border-radius:8px;border:1px solid #fcd34d;font-size:13px;color:#92400e;">
                        <i class="fas fa-exclamation-triangle me-1"></i> PDF belum tersedia
                    </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="regenerate_pdf">
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-2"></i><?= $pdfExists ? 'Generate Ulang PDF' : 'Generate PDF' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Actions -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-bolt"></i> Aksi</h5>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:10px;">
                    <?php if (in_array($inv['status'], ['draft','sent']) && !empty($order['client_email'])): ?>
                    <form method="POST" id="sendEmailForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="send_email">
                        <button type="submit" class="btn btn-info text-white w-100" id="sendEmailBtn">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Invoice ke Email Klien
                        </button>
                    </form>
                    <div style="font-size:12px;color:#94a3b8;">Akan dikirim ke: <strong><?= htmlspecialchars($order['client_email']) ?></strong></div>
                    <?php elseif (empty($order['client_email'])): ?>
                    <div style="padding:10px 14px;background:#fef2f2;border-radius:8px;font-size:13px;color:#991b1b;">
                        <i class="fas fa-exclamation-circle me-1"></i> Email klien tidak tersedia
                    </div>
                    <?php endif; ?>

                    <?php if ($inv['status'] !== 'paid' && $inv['status'] !== 'cancelled'): ?>
                    <form method="POST" onsubmit="return confirm('Tandai invoice ini sebagai LUNAS?')">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="mark_paid">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-double me-2"></i>Tandai Lunas
                        </button>
                    </form>
                    <?php endif; ?>

                    <?php if ($inv['status'] === 'paid'): ?>
                    <div style="padding:12px 16px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;text-align:center;font-size:13px;font-weight:700;color:#15803d;">
                        <i class="fas fa-check-circle me-2"></i>INVOICE LUNAS
                        <div style="font-size:11px;font-weight:400;margin-top:4px;"><?= $inv['paid_at'] ? date('d F Y H:i', strtotime($inv['paid_at'])) : '' ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!in_array($inv['status'], ['paid','cancelled'])): ?>
                    <form method="POST" onsubmit="return confirm('Batalkan invoice ini? Tindakan ini tidak dapat diurungkan.')">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-times me-2"></i>Batalkan Invoice
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DP Reminder -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-hand-holding-usd"></i> Info Pembayaran</h5>
                </div>
                <div style="padding:16px 20px;">
                    <div style="font-size:13px;color:#64748b;margin-bottom:12px;">Minimal DP (50%):</div>
                    <div style="font-size:22px;font-weight:800;color:var(--primary);">
                        Rp <?= number_format((int)ceil($inv['total'] / 2), 0, ',', '.') ?>
                    </div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:4px;">dari total Rp <?= number_format($inv['total'], 0, ',', '.') ?></div>
                    <?php if ($inv['due_date']): ?>
                    <div style="margin-top:12px;padding:8px 12px;background:#fef3c7;border-radius:8px;font-size:12px;color:#92400e;">
                        <i class="fas fa-clock me-1"></i>Jatuh tempo: <?= date('d F Y', strtotime($inv['due_date'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sendEmailForm')?.addEventListener('submit', function(e) {
    if (!confirm('Kirim invoice ke <?= addslashes($order['client_email']) ?>?')) e.preventDefault();
});
</script>

<?php
$footerFile = ADMIN_PATH . '/includes/footer.php';
if (file_exists($footerFile)) require_once $footerFile;
?>
