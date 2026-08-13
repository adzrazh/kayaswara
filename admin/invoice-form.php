<?php
/**
 * Admin: Buat Invoice dari Pesanan
 */
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$errors   = [];

if (!$order_id) {
    flash('error', 'ID pesanan tidak valid.');
    redirect('index.php?page=pesanan');
}

$order = fetch("SELECT * FROM orders WHERE id = ?", [$order_id]);
if (!$order) {
    flash('error', 'Pesanan tidak ditemukan.');
    redirect('index.php?page=pesanan');
}

// Check if active invoice already exists
$existing = fetch("SELECT id FROM invoices WHERE order_id = ? AND status != 'cancelled'", [$order_id]);
if ($existing) {
    redirect('index.php?page=invoice-detail&id=' . $existing['id']);
}

$csrf = csrf_token();

$serviceOptions = [
    'setup_ojs' => 'Penerbitan Buku', 'migrasi' => 'Konversi KTI',
    'kustomisasi' => 'Editing & Layout', 'pelatihan' => 'Desain Cover',
    'maintenance' => 'Distribusi & Pemasaran', 'indeksasi_doaj' => 'Cek Plagiasi',
    'indeksasi_sinta' => 'Konsultasi Penulisan', 'lainnya' => 'Lainnya',
];
$packageOptions = ['basic' => 'Basic', 'professional' => 'Professional', 'premium' => 'Premium', 'custom' => 'Custom'];

// Compute addons total from order
$orderAddons = [];
if (!empty($order['addons'])) {
    $orderAddons = json_decode($order['addons'], true) ?: [];
}
$orderAddonsTotal = 0;
foreach ($orderAddons as $_a) { $orderAddonsTotal += (int)($_a['price'] ?? 0); }

$defaults = [
    'subtotal'   => (int)($order['price'] ?? 0) + $orderAddonsTotal,
    'discount'   => 0,
    'show_pph23' => 1,
    'due_date'   => date('Y-m-d', strtotime('+3 days')),
    'admin_notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token keamanan tidak valid.';
    } else {
        $subtotal    = (int)preg_replace('/[^0-9]/', '', $_POST['subtotal'] ?? '0');
        $discount    = (int)preg_replace('/[^0-9]/', '', $_POST['discount'] ?? '0');
        $show_pph23  = isset($_POST['show_pph23']) ? 1 : 0;
        $due_date    = sanitize($_POST['due_date'] ?? '');
        $admin_notes = trim($_POST['admin_notes'] ?? '');

        if ($subtotal <= 0) $errors[] = 'Subtotal harus lebih dari 0.';
        if ($discount >= $subtotal) $errors[] = 'Diskon tidak boleh >= subtotal.';
        if (!empty($due_date) && !strtotime($due_date)) $errors[] = 'Format tanggal jatuh tempo tidak valid.';

        if (empty($errors)) {
            $totals = calculateInvoiceTotals($subtotal, $discount, (bool)$show_pph23);
            try {
                $inv_number = generateInvoiceNumber();
                $inv_token  = generateInvoiceToken();
                $inv_id = insert('invoices', [
                    'order_id'       => $order_id,
                    'invoice_number' => $inv_number,
                    'status'         => 'draft',
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'tax_pph23'      => $totals['pph23'],
                    'total'          => $totals['total'],
                    'due_date'       => $due_date ?: null,
                    'show_pph23'     => $show_pph23,
                    'admin_notes'    => $admin_notes,
                    'token'          => $inv_token,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);

                // Generate PDF immediately
                $inv = fetch("SELECT * FROM invoices WHERE id = ?", [$inv_id]);
                try {
                    generateInvoicePDF($inv, $order);
                } catch (Exception $e) {
                    // PDF generation failed — non-fatal, can retry from detail page
                }

                flash('success', 'Invoice <strong>' . htmlspecialchars($inv_number) . '</strong> berhasil dibuat!');
                redirect('index.php?page=invoice-detail&id=' . $inv_id);
            } catch (Exception $e) {
                $errors[] = 'Gagal menyimpan invoice: ' . $e->getMessage();
            }
        }

        // Restore form values on error
        $defaults = compact('subtotal','discount','show_pph23','due_date','admin_notes');
    }
}

$totalsPreview = calculateInvoiceTotals((int)$defaults['subtotal'], (int)$defaults['discount'], (bool)$defaults['show_pph23']);

require_once ADMIN_PATH . '/includes/header.php';
require_once ADMIN_PATH . '/includes/sidebar.php';
?>
<div class="admin-content">
    <div class="page-header">
        <div class="page-header-left">
            <h2>Buat Invoice</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?page=pesanan">Pesanan</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=pesanan-detail&id=<?= $order_id ?>"><?= htmlspecialchars($order['tracking_code']) ?></a></li>
                <li class="breadcrumb-item active">Buat Invoice</li>
            </ol></nav>
        </div>
        <div class="page-header-actions">
            <a href="index.php?page=pesanan-detail&id=<?= $order_id ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger mb-4">
        <?php foreach ($errors as $e): ?><div><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="invoiceForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="row g-4">

            <!-- Left: Form -->
            <div class="col-xl-7">

                <!-- Order Summary -->
                <div class="admin-card mb-4">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title"><i class="fas fa-clipboard-list"></i> Pesanan Terkait</h5>
                    </div>
                    <div style="padding:20px;">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Nama Klien</div>
                                <div style="font-weight:600;"><?= htmlspecialchars($order['client_name']) ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Kode Tracking</div>
                                <div style="font-weight:700;color:var(--primary);"><?= htmlspecialchars($order['tracking_code']) ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Layanan</div>
                                <div><?= htmlspecialchars($serviceOptions[$order['service_type']] ?? $order['service_type']) ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Paket</div>
                                <div><?= htmlspecialchars($packageOptions[$order['package_tier']] ?? $order['package_tier']) ?></div>
                            </div>
                            <?php if ($order['client_email']): ?>
                            <div class="col-sm-6">
                                <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Email Klien</div>
                                <div><?= htmlspecialchars($order['client_email']) ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if ($order['client_institution']): ?>
                            <div class="col-sm-6">
                                <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Institusi</div>
                                <div><?= htmlspecialchars($order['client_institution']) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="admin-card mb-4">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title"><i class="fas fa-file-invoice-dollar"></i> Detail Invoice</h5>
                    </div>
                    <div style="padding:20px;">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Subtotal (Rp) <span class="required">*</span></label>
                                <input type="text" name="subtotal" id="subtotal" class="form-control"
                                       value="<?= number_format((int)$defaults['subtotal'], 0, ',', '.') ?>"
                                       placeholder="2.500.000" required
                                       oninput="formatRupiah(this); recalc()">
                                <div class="form-hint">Harga sebelum diskon dan pajak (paket + add-ons, jika ada).</div>
                                <?php if (!empty($orderAddons)): ?>
                                <div style="margin-top:8px;background:#f0fdf4;border-radius:8px;padding:10px 12px;border:1px solid #bbf7d0;font-size:12px;">
                                    <div style="font-weight:700;color:#15803d;margin-bottom:6px;"><i class="fas fa-layer-group me-1"></i>Rincian Otomatis:</div>
                                    <div style="display:flex;justify-content:space-between;color:#374151;margin-bottom:3px;">
                                        <span>Harga Paket</span>
                                        <span>Rp <?= number_format((int)($order['price'] ?? 0), 0, ',', '.') ?></span>
                                    </div>
                                    <?php foreach ($orderAddons as $_a): ?>
                                    <div style="display:flex;justify-content:space-between;color:#374151;margin-bottom:3px;">
                                        <span>+ <?= htmlspecialchars($_a['name']) ?></span>
                                        <span>Rp <?= number_format((int)($_a['price'] ?? 0), 0, ',', '.') ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                    <div style="display:flex;justify-content:space-between;font-weight:700;color:#1e293b;border-top:1px solid #bbf7d0;margin-top:4px;padding-top:4px;">
                                        <span>Total Subtotal</span>
                                        <span>Rp <?= number_format($defaults['subtotal'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Diskon (Rp)</label>
                                <input type="text" name="discount" id="discount" class="form-control"
                                       value="<?= number_format((int)$defaults['discount'], 0, ',', '.') ?>"
                                       placeholder="0"
                                       oninput="formatRupiah(this); recalc()">
                                <div class="form-hint">Kosongkan jika tidak ada diskon.</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="date" name="due_date" class="form-control"
                                       value="<?= htmlspecialchars($defaults['due_date']) ?>">
                                <div class="form-hint">Default 3 hari kerja dari hari ini.</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">PPh 23 (2%)</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="show_pph23" name="show_pph23"
                                           value="1" <?= $defaults['show_pph23'] ? 'checked' : '' ?>
                                           onchange="recalc()">
                                    <label class="form-check-label" for="show_pph23" style="font-size:13px;">
                                        Tampilkan potongan PPh 23 (untuk klien badan usaha)
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan Admin (internal, tidak tampil di invoice)</label>
                                <textarea name="admin_notes" class="form-control" rows="3"
                                          placeholder="Kesepakatan, syarat khusus, dll."><?= htmlspecialchars($defaults['admin_notes']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-invoice me-2"></i>Buat Invoice & Generate PDF
                </button>
            </div>

            <!-- Right: Live Preview -->
            <div class="col-xl-5">
                <div class="admin-card" style="position:sticky;top:80px;">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title"><i class="fas fa-calculator"></i> Kalkulasi Harga</h5>
                    </div>
                    <div style="padding:24px;">
                        <table style="width:100%;font-size:14px;border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px 0;color:#64748b;">Subtotal</td>
                                <td style="padding:6px 0;text-align:right;font-weight:600;" id="previewSubtotal">Rp 0</td>
                            </tr>
                            <tr id="discountRow" style="display:none;">
                                <td style="padding:6px 0;color:#64748b;">Diskon</td>
                                <td style="padding:6px 0;text-align:right;color:#dc2626;" id="previewDiscount">-Rp 0</td>
                            </tr>
                            <tr id="pph23Row" style="display:none;">
                                <td style="padding:6px 0;color:#64748b;">PPh 23 (2%)*</td>
                                <td style="padding:6px 0;text-align:right;color:#B8860B;" id="previewPph23">-Rp 0</td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr style="margin:8px 0;border-color:#e2e8f0;"></td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;font-weight:800;font-size:15px;color:#1e293b;">Total Dibayar</td>
                                <td style="padding:6px 0;text-align:right;font-weight:800;font-size:15px;color:var(--primary);" id="previewTotal">Rp 0</td>
                            </tr>
                            <tr id="dpRow" style="display:none;">
                                <td style="padding:4px 0;font-size:12px;color:#94a3b8;">Minimal DP (50%)</td>
                                <td style="padding:4px 0;text-align:right;font-size:12px;color:#94a3b8;" id="previewDp">Rp 0</td>
                            </tr>
                        </table>
                        <div id="pph23Note" style="display:none;margin-top:12px;padding:10px 14px;background:#fef3c7;border-radius:8px;font-size:12px;color:#92400e;">
                            *) PPh 23 dipotong oleh pembeli badan usaha
                        </div>

                        <div style="margin-top:20px;padding:14px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;font-size:13px;color:#374151;">
                            <strong>Nomor Invoice:</strong><br>
                            <span style="font-family:monospace;font-size:14px;color:var(--primary);">
                                <?php
                                $year = date('Y');
                                $last = fetch("SELECT invoice_number FROM invoices WHERE invoice_number LIKE ? ORDER BY id DESC LIMIT 1", ["INV-{$year}-%"]);
                                $seq  = $last ? ((int)substr($last['invoice_number'], strrpos($last['invoice_number'], '-') + 1) + 1) : 1;
                                echo 'INV-' . $year . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function parseRupiah(s) {
    return parseInt((s || '0').replace(/[^0-9]/g,'')) || 0;
}
function formatRupiah(el) {
    var v = parseRupiah(el.value);
    el.value = v.toLocaleString('id-ID');
}
function recalc() {
    var sub = parseRupiah(document.getElementById('subtotal').value);
    var dis = parseRupiah(document.getElementById('discount').value);
    var pph = document.getElementById('show_pph23').checked;
    var after = sub - dis;
    var pph23 = pph ? Math.round(after * 0.02) : 0;
    var total = after - pph23;
    var dp = Math.ceil(total / 2);

    document.getElementById('previewSubtotal').textContent = 'Rp ' + sub.toLocaleString('id-ID');
    document.getElementById('discountRow').style.display = dis > 0 ? '' : 'none';
    document.getElementById('previewDiscount').textContent = '-Rp ' + dis.toLocaleString('id-ID');
    document.getElementById('pph23Row').style.display = pph && pph23 > 0 ? '' : 'none';
    document.getElementById('previewPph23').textContent = '-Rp ' + pph23.toLocaleString('id-ID');
    document.getElementById('pph23Note').style.display = pph && pph23 > 0 ? '' : 'none';
    document.getElementById('previewTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('dpRow').style.display = total > 0 ? '' : 'none';
    document.getElementById('previewDp').textContent = 'Rp ' + dp.toLocaleString('id-ID');
}
document.addEventListener('DOMContentLoaded', function(){
    recalc();
    // Strip format on submit
    document.getElementById('invoiceForm').addEventListener('submit', function(){
        document.getElementById('subtotal').value = parseRupiah(document.getElementById('subtotal').value);
        document.getElementById('discount').value = parseRupiah(document.getElementById('discount').value);
    });
});
</script>

<?php
$footerFile = ADMIN_PATH . '/includes/footer.php';
if (file_exists($footerFile)) require_once $footerFile;
?>
