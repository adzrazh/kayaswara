<?php
/**
 * Kirim Naskah — formulir pengajuan naskah ke redaksi.
 * Menangani POST (AJAX) dan menyimpan ke tabel `consultations`.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Kirim Naskah — ' . $siteName;
$metaDesc  = 'Kirimkan naskah buku akademik Anda ke redaksi ' . $siteName . ' untuk ditelaah. Tanggapan awal dalam 1×24 jam kerja.';

// ──────────────────────────────────────────────────────────────
// POST — validasi, unggah berkas, simpan
// ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Sesi Anda telah berakhir. Muat ulang halaman lalu kirim kembali.']);
        exit;
    }

    $errors      = [];
    $name        = sanitize($_POST['name'] ?? '');
    $email       = sanitize($_POST['email'] ?? '');
    $phone       = sanitize($_POST['phone'] ?? '');
    $institution = sanitize($_POST['institution'] ?? '');
    $serviceType = sanitize($_POST['service_type'] ?? 'setup_ojs');
    $budgetRange = sanitize($_POST['budget_range'] ?? '');
    $message     = sanitize($_POST['message'] ?? '');

    $validServices = array_keys(getServiceTypes());

    if (empty($name) || mb_strlen($name) < 2) $errors[] = 'Nama lengkap wajib diisi (minimal 2 karakter).';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Alamat surel tidak valid.';
    if (!in_array($serviceType, $validServices, true)) $errors[] = 'Jenis layanan tidak dikenali.';
    if (mb_strlen($message) < 10) $errors[] = 'Deskripsi naskah minimal 10 karakter.';

    // Lampiran naskah
    $uploadedFile = '';
    $uploadedOrigName = '';
    if (!empty($_FILES['manuscript_file']) && $_FILES['manuscript_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['manuscript_file'];

        if ($file['size'] > 20 * 1024 * 1024) {
            $errors[] = 'Ukuran berkas maksimal 20MB.';
        }

        $allowedExt = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'zip', 'rar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = 'Format berkas tidak didukung. Gunakan PDF, DOC, DOCX, ODT, RTF, TXT, ZIP, atau RAR.';
        }

        if (empty($errors)) {
            $uploadDir = dirname(__DIR__) . '/assets/uploads/consultations/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $safeName = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $safeName)) {
                $uploadedFile     = $safeName;
                $uploadedOrigName = $file['name'];
            } else {
                $errors[] = 'Berkas gagal diunggah. Silakan coba lagi.';
            }
        }
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }

    try {
        $data = [
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'institution'  => $institution,
            'service_type' => $serviceType,
            'budget_range' => $budgetRange,
            'message'      => $message,
            'status'       => 'new',
            'priority'     => 'medium',
        ];
        if (!empty($uploadedFile)) {
            $data['attachment_file'] = $uploadedFile;
            $data['attachment_name'] = $uploadedOrigName;
        }
        insert('consultations', $data);

        echo json_encode([
            'success' => true,
            'message' => 'Pengajuan naskah Anda telah kami terima.',
        ]);
    } catch (Exception $e) {
        error_log('Manuscript submission error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi gangguan pada sistem. Silakan coba lagi atau hubungi kami langsung.']);
    }
    exit;
}

// ──────────────────────────────────────────────────────────────
// GET — tampilan formulir
// ──────────────────────────────────────────────────────────────
$services   = getServiceTypes();
$prePaket   = sanitize($_GET['paket'] ?? '');
$serviceMap = ['basic' => 'setup_ojs', 'professional' => 'kustomisasi', 'premium' => 'migrasi'];
$preService = $serviceMap[$prePaket] ?? 'setup_ojs';

$email    = getSetting('email_contact', '');
$whatsapp = getSetting('whatsapp_number', '');
$waDigits = $whatsapp ? preg_replace('/\D/', '', $whatsapp) : '';

$expectations = [
    ['Berkas diperiksa kelengkapannya', 'Redaksi memastikan naskah dan data penulis lengkap. Bila ada yang kurang, kami menghubungi Anda lebih dahulu.', '1×24 jam kerja'],
    ['Naskah ditelaah redaksi', 'Penilaian keaslian, kedalaman kajian, struktur, dan kesesuaian dengan lini terbitan kami.', '5–10 hari kerja'],
    ['Hasil telaah disampaikan', 'Diterima, diterima dengan revisi, atau belum layak terbit — seluruhnya disertai catatan tertulis.', 'Setelah telaah'],
    ['Penawaran & perjanjian', 'Bila naskah diterima, lingkup pekerjaan, jadwal, dan biaya dikirim untuk Anda setujui.', '2–3 hari kerja'],
];
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Kirim Naskah</li>
        </ol>
        <span class="eyebrow">Pengajuan Naskah</span>
        <h1>Kirim Naskah ke Redaksi</h1>
        <p>Isi formulir berikut dan lampirkan berkas naskah Anda. Tidak ada biaya apa pun pada tahap telaah.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5">

            <!-- Formulir -->
            <div class="col-lg-7 reveal">
                <div class="form-panel">
                    <div class="form-panel-head">
                        <h3>Formulir Pengajuan Naskah</h3>
                        <p>Kolom bertanda <span class="text-accent fw-700">*</span> wajib diisi.</p>
                    </div>
                    <div class="form-panel-body">

                        <div id="formAlert" class="alert alert-danger d-none" role="alert"></div>

                        <div id="formSuccess" class="d-none text-center py-4">
                            <div style="font-size:3rem;color:var(--secondary);"><i class="fa-solid fa-circle-check"></i></div>
                            <h3 class="h4 mt-3 mb-2">Naskah Anda Sudah Kami Terima</h3>
                            <p class="text-muted mb-4">
                                Redaksi akan memeriksa kelengkapan berkas dan menghubungi Anda dalam
                                <strong>1×24 jam kerja</strong> melalui surel atau WhatsApp.
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="<?= $siteUrl ?>/proses" class="btn btn-outline-primary"><i class="fa-solid fa-diagram-project"></i>Lihat Alur Penerbitan</a>
                                <?php if ($waDigits): ?>
                                <a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                                    <i class="fa-brands fa-whatsapp"></i>Hubungi Redaksi
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <form id="manuscriptForm" novalidate enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div class="form-step"><span>1</span>Identitas Penulis</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="f_name" class="form-label">Nama lengkap &amp; gelar <span class="req">*</span></label>
                                    <input type="text" class="form-control" id="f_name" name="name" required minlength="2"
                                           placeholder="Dr. Nama Lengkap, M.Pd.">
                                    <div class="invalid-feedback">Nama lengkap wajib diisi.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="f_email" class="form-label">Surel <span class="req">*</span></label>
                                    <input type="email" class="form-control" id="f_email" name="email" required
                                           placeholder="nama@institusi.ac.id">
                                    <div class="invalid-feedback">Masukkan alamat surel yang valid.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="f_phone" class="form-label">Nomor WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-brands fa-whatsapp"></i></span>
                                        <input type="tel" class="form-control" id="f_phone" name="phone" placeholder="08xx-xxxx-xxxx">
                                    </div>
                                    <div class="form-text">Untuk tanggapan yang lebih cepat.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="f_inst" class="form-label">Institusi / afiliasi</label>
                                    <input type="text" class="form-control" id="f_inst" name="institution" placeholder="Universitas / lembaga">
                                </div>
                            </div>

                            <div class="form-step"><span>2</span>Tentang Naskah</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="f_service" class="form-label">Kebutuhan utama <span class="req">*</span></label>
                                    <select class="form-select" id="f_service" name="service_type" required>
                                        <?php foreach ($services as $val => $label): ?>
                                        <option value="<?= $val ?>" <?= $preService === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="f_budget" class="form-label">Perkiraan anggaran</label>
                                    <select class="form-select" id="f_budget" name="budget_range">
                                        <option value="">Belum menentukan</option>
                                        <option value="&lt; 3 juta">Di bawah Rp 3.000.000</option>
                                        <option value="3 - 6 juta">Rp 3.000.000 – Rp 6.000.000</option>
                                        <option value="6 - 10 juta">Rp 6.000.000 – Rp 10.000.000</option>
                                        <option value="&gt; 10 juta">Di atas Rp 10.000.000</option>
                                        <option value="Belum tahu">Fleksibel / menunggu penawaran</option>
                                    </select>
                                    <div class="form-text">Membantu kami menyusun lingkup pekerjaan yang realistis.</div>
                                </div>
                                <div class="col-12">
                                    <label for="f_message" class="form-label">Deskripsi naskah <span class="req">*</span></label>
                                    <textarea class="form-control" id="f_message" name="message" rows="6" required minlength="10"
                                              placeholder="Jelaskan singkat: jenis naskah (buku ajar / referensi / monograf / bunga rampai), bidang ilmu, perkiraan jumlah halaman, tingkat kesiapan naskah, dan target pembaca."></textarea>
                                    <div class="invalid-feedback">Deskripsi naskah wajib diisi (minimal 10 karakter).</div>
                                    <div class="form-text">Semakin rinci keterangan Anda, semakin tepat penilaian awal redaksi.</div>
                                </div>
                            </div>

                            <div class="form-step"><span>3</span>Berkas Naskah</div>
                            <div class="mb-4">
                                <div class="file-drop" id="fileDrop">
                                    <input type="file" id="f_file" name="manuscript_file"
                                           accept=".pdf,.doc,.docx,.odt,.rtf,.txt,.zip,.rar"
                                           aria-label="Unggah berkas naskah">
                                    <div data-drop-idle>
                                        <div class="file-drop-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                        <p class="mb-1 fw-600">Klik atau seret berkas ke sini</p>
                                        <p class="mb-0 small text-muted">PDF, DOC, DOCX, ODT, RTF, TXT, ZIP, RAR — maksimal 20MB</p>
                                    </div>
                                    <div class="file-chip d-none" data-drop-picked>
                                        <i class="fa-regular fa-file-lines" style="font-size:1.35rem;color:var(--primary);"></i>
                                        <div>
                                            <div class="fw-600" style="font-size:.9rem;" data-drop-name></div>
                                            <div class="text-muted" style="font-size:.78rem;" data-drop-size></div>
                                        </div>
                                        <button type="button" class="file-chip-remove" data-drop-remove aria-label="Hapus berkas">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    Opsional pada tahap ini, tetapi naskah yang dilampirkan dapat langsung masuk antrean telaah.
                                </div>
                            </div>

                            <div class="privacy-note mb-4">
                                <i class="fa-solid fa-lock"></i>
                                <span>
                                    Berkas dan data Anda hanya diakses tim redaksi yang menangani, tidak dibagikan kepada
                                    pihak ketiga, dan tidak digunakan di luar keperluan telaah serta penerbitan.
                                </span>
                            </div>

                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg w-100">
                                <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sisi kanan -->
            <div class="col-lg-5 reveal" data-reveal-delay="0.1">
                <div class="side-panel">
                    <h4>Setelah Anda Menekan Kirim</h4>
                    <div class="rail mt-3">
                        <?php foreach ($expectations as $i => [$t, $d, $when]): ?>
                        <div class="rail-item">
                            <span class="rail-dot"><?= $i + 1 ?></span>
                            <h3 class="h6 mb-1"><?= $t ?></h3>
                            <p class="mb-1" style="font-size:.9rem;"><?= $d ?></p>
                            <span class="rail-note"><i class="fa-regular fa-clock"></i><?= $when ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="side-panel">
                    <h4>Cara Lain Menghubungi Kami</h4>
                    <?php if ($email): ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-regular fa-envelope"></i></span>
                        <div>
                            <div class="lb">Surel redaksi</div>
                            <div class="vl"><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($whatsapp): ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-brands fa-whatsapp"></i></span>
                        <div>
                            <div class="lb">WhatsApp</div>
                            <div class="vl"><a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($whatsapp) ?></a></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-regular fa-clock"></i></span>
                        <div>
                            <div class="lb">Jam kerja</div>
                            <div class="vl">Senin–Jumat, 08.00–17.00 WIB</div>
                        </div>
                    </div>
                </div>

                <div class="notice mt-4">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>
                        Belum yakin naskah Anda sudah lengkap? Periksa
                        <a href="<?= $siteUrl ?>/proses#ketentuan">ketentuan naskah</a> terlebih dahulu.
                    </span>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.getElementById('manuscriptForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var form    = this;
    var btn     = document.getElementById('submitBtn');
    var alertEl = document.getElementById('formAlert');
    var okEl    = document.getElementById('formSuccess');

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        var firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim…';
    alertEl.classList.add('d-none');

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            form.classList.add('d-none');
            okEl.classList.remove('d-none');
            okEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alertEl.textContent = data.message;
            alertEl.classList.remove('d-none');
            alertEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-regular fa-paper-plane"></i>Kirim Naskah';
        }
    })
    .catch(function () {
        alertEl.textContent = 'Koneksi terputus. Periksa jaringan Anda lalu coba lagi.';
        alertEl.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-regular fa-paper-plane"></i>Kirim Naskah';
    });
});
</script>
