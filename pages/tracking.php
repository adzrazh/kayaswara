<?php
/**
 * Lacak Naskah — pencarian progres berdasarkan kode pelacakan.
 * POST (AJAX) mengembalikan JSON berisi ringkasan pesanan dan tahapannya.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Lacak Naskah — ' . $siteName;
$metaDesc  = 'Pantau tahapan pengerjaan naskah Anda di ' . $siteName . ' menggunakan kode pelacakan yang diterima saat kesepakatan penerbitan.';

// ──────────────────────────────────────────────────────────────
// POST — pencarian kode
// ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $code = trim(sanitize($_POST['tracking_code'] ?? ''));

    if ($code === '') {
        echo json_encode(['success' => false, 'message' => 'Masukkan kode pelacakan naskah Anda.']);
        exit;
    }

    if (!preg_match('/^KYSWR-\d{8}-\d{3,}$/i', $code)) {
        echo json_encode(['success' => false, 'message' => 'Format kode tidak sesuai. Contoh: KYSWR-06042026-001']);
        exit;
    }

    try {
        $order = fetch(
            "SELECT id, tracking_code, client_name, client_institution, service_type,
                    package_tier, status, created_at, updated_at, addons
             FROM orders WHERE tracking_code = ?",
            [strtoupper($code)]
        );
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi gangguan pada sistem. Silakan coba lagi.']);
        exit;
    }

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Kode pelacakan tidak ditemukan. Periksa kembali kode yang Anda terima.']);
        exit;
    }

    $milestones = [];
    try {
        $milestones = fetchAll(
            "SELECT id, title, description, status, completed_at, sort_order
             FROM order_milestones WHERE order_id = ?
             ORDER BY sort_order ASC, id ASC",
            [$order['id']]
        );
    } catch (Exception $e) {}

    $totalMs     = count($milestones);
    $completedMs = 0;
    foreach ($milestones as $ms) {
        if ($ms['status'] === 'completed') $completedMs++;
    }
    $progressPct = $totalMs > 0 ? (int) round(($completedMs / $totalMs) * 100) : 0;

    echo json_encode([
        'success' => true,
        'order'   => [
            'tracking_code'   => $order['tracking_code'],
            'client_name'     => $order['client_name'],
            'institution'     => $order['client_institution'] ?: '—',
            'service_type'    => getServiceTypeLabel($order['service_type']),
            'package_tier'    => getPackageTierLabel((string) $order['package_tier']),
            'status'          => $order['status'],
            'status_label'    => getOrderStatusLabel($order['status']),
            'status_badge'    => getOrderStatusBadge($order['status']),
            'created_at'      => date('d M Y', strtotime($order['created_at'])),
            'updated_at'      => date('d M Y, H:i', strtotime($order['updated_at'])),
            'progress'        => $progressPct,
            'completed_count' => $completedMs,
            'total_count'     => $totalMs,
            'addons'          => !empty($order['addons']) ? (json_decode($order['addons'], true) ?: []) : [],
        ],
        'milestones' => array_map(static function ($ms) {
            return [
                'title'        => $ms['title'],
                'description'  => $ms['description'],
                'status'       => $ms['status'],
                'status_label' => getMilestoneStatusLabel($ms['status']),
                'completed_at' => $ms['completed_at'] ? date('d M Y, H:i', strtotime($ms['completed_at'])) : null,
            ];
        }, $milestones),
    ]);
    exit;
}
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Lacak Naskah</li>
        </ol>
        <span class="eyebrow">Status Pengerjaan</span>
        <h1>Lacak Naskah</h1>
        <p>Masukkan kode pelacakan yang Anda terima saat kesepakatan penerbitan untuk melihat tahap yang sedang berjalan.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="track-panel reveal">
                    <div class="track-panel-icon"><i class="fa-solid fa-location-crosshairs"></i></div>
                    <h2 class="h3 mb-2">Cek Posisi Naskah Anda</h2>
                    <p class="text-muted mb-4">Kode pelacakan berformat <strong>KYSWR-DDMMYYYY-NNN</strong>.</p>

                    <form id="trackForm" class="track-form" autocomplete="off">
                        <label class="visually-hidden" for="trackCode">Kode pelacakan</label>
                        <input type="text" class="form-control" id="trackCode" maxlength="25" required
                               placeholder="KYSWR-06042026-001">
                        <button type="submit" class="btn btn-primary" id="trackBtn">
                            <span class="btn-label"><i class="fa-solid fa-magnifying-glass"></i>Cek Progres</span>
                            <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Mencari…</span>
                        </button>
                    </form>

                    <div id="trackError" class="alert alert-danger mt-3 mb-0 d-none text-start" role="alert"></div>

                    <p class="form-text mt-3 mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Kode dikirim melalui surel setelah perjanjian penerbitan disepakati.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hasil -->
<section id="trackResult" class="section pt-0 d-none">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <div class="track-summary mb-4">
                    <div class="track-summary-head">
                        <div>
                            <span class="lb">Kode Pelacakan</span>
                            <h2 class="code" id="rCode"></h2>
                        </div>
                        <span class="chip chip-gold" id="rStatus"></span>
                    </div>
                    <div class="track-summary-body">
                        <div class="row g-2">
                            <div class="col-sm-6"><div class="track-field"><span class="lb">Penulis</span><span class="vl" id="rName"></span></div></div>
                            <div class="col-sm-6"><div class="track-field"><span class="lb">Institusi</span><span class="vl" id="rInst"></span></div></div>
                            <div class="col-sm-6"><div class="track-field"><span class="lb">Lingkup layanan</span><span class="vl" id="rService"></span></div></div>
                            <div class="col-sm-6"><div class="track-field"><span class="lb">Paket</span><span class="vl" id="rTier"></span></div></div>
                            <div class="col-sm-6"><div class="track-field"><span class="lb">Tanggal masuk</span><span class="vl" id="rCreated"></span></div></div>
                            <div class="col-sm-6"><div class="track-field"><span class="lb">Pembaruan terakhir</span><span class="vl" id="rUpdated"></span></div></div>
                        </div>

                        <div class="track-progress">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-600" style="font-size:.9rem;">Progres pengerjaan</span>
                                <span class="fw-700" style="color:var(--primary);" id="rPct">0%</span>
                            </div>
                            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" id="rBar" style="width:0%"></div>
                            </div>
                            <div class="text-muted mt-2" style="font-size:.84rem;" id="rCount"></div>
                        </div>
                    </div>
                </div>

                <h3 class="h4 mb-3">Rincian Tahapan</h3>
                <div class="tl" id="rTimeline"></div>

            </div>
        </div>
    </div>
</section>

<!-- Bantuan -->
<section class="section paper-grain">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Bantuan</span>
            <h2>Cara kerja pelacakan</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 reveal">
                <div class="step-card">
                    <div class="step-num">01</div>
                    <h3 class="h6">Kesepakatan penerbitan</h3>
                    <p>Setelah naskah diterima dan penawaran disetujui, redaksi membuka berkas pengerjaan.</p>
                </div>
            </div>
            <div class="col-md-4 reveal" data-reveal-delay="0.07">
                <div class="step-card">
                    <div class="step-num">02</div>
                    <h3 class="h6">Kode dikirim</h3>
                    <p>Kode pelacakan dikirim ke surel Anda bersama ringkasan lingkup pekerjaan.</p>
                </div>
            </div>
            <div class="col-md-4 reveal" data-reveal-delay="0.14">
                <div class="step-card">
                    <div class="step-num">03</div>
                    <h3 class="h6">Pantau kapan saja</h3>
                    <p>Masukkan kode di halaman ini untuk melihat tahap yang selesai dan yang sedang dikerjakan.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 reveal">
            <p class="text-muted mb-2">Belum memiliki kode pelacakan?</p>
            <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary">
                <i class="fa-regular fa-paper-plane"></i>Kirim Naskah Terlebih Dahulu
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form   = document.getElementById('trackForm');
    var input  = document.getElementById('trackCode');
    var btn    = document.getElementById('trackBtn');
    var label  = btn.querySelector('.btn-label');
    var load   = btn.querySelector('.btn-loading');
    var errEl  = document.getElementById('trackError');
    var result = document.getElementById('trackResult');

    input.addEventListener('input', function () { this.value = this.value.toUpperCase(); });

    var pre = new URLSearchParams(window.location.search).get('kode') || new URLSearchParams(window.location.search).get('code');
    if (pre) {
        input.value = pre.toUpperCase();
        form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit'));
    }

    function showError(msg) {
        errEl.textContent = msg;
        errEl.classList.remove('d-none');
    }

    function esc(s) {
        var d = document.createElement('div');
        d.textContent = s || '';
        return d.innerHTML;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var code = input.value.trim();
        if (!code) { showError('Masukkan kode pelacakan naskah Anda.'); return; }

        label.classList.add('d-none');
        load.classList.remove('d-none');
        btn.disabled = true;
        errEl.classList.add('d-none');
        result.classList.add('d-none');

        var fd = new FormData();
        fd.append('tracking_code', code);

        fetch(window.location.href, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            label.classList.remove('d-none');
            load.classList.add('d-none');
            btn.disabled = false;
            if (!data.success) { showError(data.message); return; }
            render(data.order, data.milestones);
        })
        .catch(function () {
            label.classList.remove('d-none');
            load.classList.add('d-none');
            btn.disabled = false;
            showError('Gagal menghubungi server. Periksa koneksi internet Anda.');
        });
    });

    function render(order, milestones) {
        document.getElementById('rCode').textContent    = order.tracking_code;
        document.getElementById('rName').textContent    = order.client_name;
        document.getElementById('rInst').textContent    = order.institution;
        document.getElementById('rService').textContent = order.service_type;
        document.getElementById('rTier').textContent    = order.package_tier;
        document.getElementById('rCreated').textContent = order.created_at;
        document.getElementById('rUpdated').textContent = order.updated_at;
        document.getElementById('rStatus').textContent  = order.status_label;
        document.getElementById('rPct').textContent     = order.progress + '%';
        document.getElementById('rCount').textContent   = order.completed_count + ' dari ' + order.total_count + ' tahapan selesai';

        var bar = document.getElementById('rBar');
        bar.style.width = '0%';
        bar.parentElement.setAttribute('aria-valuenow', order.progress);
        window.setTimeout(function () { bar.style.width = order.progress + '%'; }, 120);

        var tl = document.getElementById('rTimeline');
        tl.innerHTML = '';

        if (!milestones.length) {
            tl.innerHTML = '<p class="text-muted">Tahapan pengerjaan belum disusun untuk naskah ini.</p>';
        }

        milestones.forEach(function (ms, i) {
            var state = ms.status === 'completed' ? 'is-done' : (ms.status === 'in_progress' ? 'is-active' : '');
            var dot   = ms.status === 'completed'
                ? '<span class="tl-dot done"><i class="fa-solid fa-check"></i></span>'
                : (ms.status === 'in_progress'
                    ? '<span class="tl-dot active"><i class="fa-solid fa-spinner fa-spin"></i></span>'
                    : '<span class="tl-dot">' + (i + 1) + '</span>');

            var stamp = '';
            if (ms.completed_at) {
                stamp = '<span class="tl-stamp"><i class="fa-solid fa-circle-check"></i>Selesai ' + esc(ms.completed_at) + '</span>';
            } else if (ms.status === 'in_progress') {
                stamp = '<span class="tl-stamp now"><i class="fa-regular fa-clock"></i>Sedang dikerjakan</span>';
            }

            var item = document.createElement('div');
            item.className = 'tl-item ' + state;
            item.innerHTML = dot +
                '<div class="tl-body">' +
                    '<h4 class="tl-title">' + esc(ms.title) + '</h4>' +
                    '<p class="tl-desc">' + esc(ms.description) + '</p>' +
                    stamp +
                '</div>';
            tl.appendChild(item);
        });

        result.classList.remove('d-none');
        window.setTimeout(function () {
            result.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
    }
});
</script>
