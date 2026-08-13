<?php
/**
 * Admin — Tambah / ubah judul dalam katalog publikasi.
 */

$categories = getPublicationCategories();
$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;

$item = [
    'title' => '', 'subtitle' => '', 'slug' => '', 'authors' => '', 'editor' => '',
    'category' => 'buku_referensi', 'subject' => '', 'synopsis' => '', 'cover' => '',
    'isbn' => '', 'publish_year' => date('Y'), 'edition' => 'Cetakan Pertama', 'pages' => '',
    'dimensions' => '', 'language' => 'Indonesia', 'price' => 0, 'purchase_url' => '',
    'is_featured' => 0, 'status' => 'published',
];

$tableExists = true;
if ($isEdit) {
    try {
        $row = fetch("SELECT * FROM publications WHERE id = ?", [$id]);
        if (!$row) {
            flash('error', 'Judul tidak ditemukan.');
            redirect('index.php?page=publikasi');
        }
        $item = array_merge($item, $row);
    } catch (Exception $e) {
        $tableExists = false;
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token keamanan tidak valid. Muat ulang halaman.';
    }

    $data = [
        'title'        => sanitize($_POST['title'] ?? ''),
        'subtitle'     => sanitize($_POST['subtitle'] ?? ''),
        'authors'      => sanitize($_POST['authors'] ?? ''),
        'editor'       => sanitize($_POST['editor'] ?? ''),
        'category'     => sanitize($_POST['category'] ?? 'buku_referensi'),
        'subject'      => sanitize($_POST['subject'] ?? ''),
        'synopsis'     => trim((string) ($_POST['synopsis'] ?? '')),
        'isbn'         => sanitize($_POST['isbn'] ?? ''),
        'publish_year' => (int) ($_POST['publish_year'] ?? 0),
        'edition'      => sanitize($_POST['edition'] ?? ''),
        'pages'        => (int) ($_POST['pages'] ?? 0),
        'dimensions'   => sanitize($_POST['dimensions'] ?? ''),
        'language'     => sanitize($_POST['language'] ?? 'Indonesia'),
        'price'        => (int) preg_replace('/\D/', '', (string) ($_POST['price'] ?? '0')),
        'purchase_url' => trim((string) ($_POST['purchase_url'] ?? '')),
        'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
        'status'       => ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
    ];

    $item = array_merge($item, $data);

    if ($data['title'] === '')   $errors[] = 'Judul buku wajib diisi.';
    if ($data['authors'] === '') $errors[] = 'Nama penulis wajib diisi.';
    if (!isset($categories[$data['category']])) $errors[] = 'Kategori tidak dikenali.';
    if ($data['publish_year'] > 0 && ($data['publish_year'] < 1900 || $data['publish_year'] > (int) date('Y') + 1)) {
        $errors[] = 'Tahun terbit tidak wajar.';
    }
    if ($data['purchase_url'] !== '' && !filter_var($data['purchase_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Tautan pemesanan harus berupa URL yang valid.';
    }

    // Sampul
    $coverFile = $item['cover'] ?? '';
    if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $uploaded = uploadImage($_FILES['cover'], 'publications');
        if ($uploaded === false) {
            $errors[] = 'Sampul gagal diunggah. Gunakan JPG, PNG, WEBP, atau GIF berukuran maksimal 2MB.';
        } else {
            if (!empty($item['cover'])) deleteImage('publications/' . $item['cover']);
            $coverFile = $uploaded;
        }
    }
    if (!empty($_POST['remove_cover']) && !empty($item['cover'])) {
        deleteImage('publications/' . $item['cover']);
        $coverFile = '';
    }

    if (empty($errors)) {
        $payload = $data;
        $payload['cover']        = $coverFile;
        $payload['publish_year'] = $data['publish_year'] > 0 ? $data['publish_year'] : null;
        $payload['pages']        = $data['pages'] > 0 ? $data['pages'] : null;
        $payload['slug']         = uniqueSlug($data['title'], 'publications', $isEdit ? $id : 0);

        try {
            if ($isEdit) {
                unset($payload['slug']);
                // Slug hanya diperbarui bila judul berubah
                $current = fetch("SELECT title, slug FROM publications WHERE id = ?", [$id]);
                if ($current && $current['title'] !== $data['title']) {
                    $payload['slug'] = uniqueSlug($data['title'], 'publications', $id);
                }
                update('publications', $payload, 'id = ?', [$id]);
                flash('success', 'Judul berhasil diperbarui.');
            } else {
                insert('publications', $payload);
                flash('success', 'Judul baru ditambahkan ke katalog.');
            }
            redirect('index.php?page=publikasi');
        } catch (Exception $e) {
            $errors[] = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }

    $item['cover'] = $coverFile;
}

$csrf = csrf_token();

require_once ADMIN_PATH . '/includes/header.php';
require_once ADMIN_PATH . '/includes/sidebar.php';
?>

<div class="admin-content">

    <div class="page-header">
        <div class="page-header-left">
            <h2><?= $isEdit ? 'Ubah Judul' : 'Tambah Judul' ?></h2>
            <p>Data yang diisi di sini tampil sebagai kartu katalog dan halaman detail buku.</p>
        </div>
        <div class="page-header-actions">
            <a href="index.php?page=publikasi" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>Kembali
            </a>
            <?php if ($isEdit && !empty($item['slug'])): ?>
            <a href="../publikasi/<?= htmlspecialchars($item['slug']) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>Lihat di Situs
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" data-loading>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="admin-card-body">
                        <div class="form-section-title"><i class="fa-solid fa-heading"></i>Identitas Buku</div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="p_title">Judul buku <span class="required">*</span></label>
                                <input type="text" class="form-control" id="p_title" name="title" required
                                       value="<?= htmlspecialchars($item['title']) ?>"
                                       placeholder="Judul utama buku">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="p_subtitle">Anak judul</label>
                                <input type="text" class="form-control" id="p_subtitle" name="subtitle"
                                       value="<?= htmlspecialchars($item['subtitle'] ?? '') ?>"
                                       placeholder="Sub-judul bila ada">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label" for="p_authors">Penulis <span class="required">*</span></label>
                                <input type="text" class="form-control" id="p_authors" name="authors" required
                                       value="<?= htmlspecialchars($item['authors']) ?>"
                                       placeholder="Nama lengkap beserta gelar; pisahkan dengan koma bila lebih dari satu">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" for="p_editor">Editor</label>
                                <input type="text" class="form-control" id="p_editor" name="editor"
                                       value="<?= htmlspecialchars($item['editor'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="p_category">Kategori <span class="required">*</span></label>
                                <select class="form-select" id="p_category" name="category">
                                    <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $item['category'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="p_subject">Bidang ilmu</label>
                                <input type="text" class="form-control" id="p_subject" name="subject"
                                       value="<?= htmlspecialchars($item['subject'] ?? '') ?>"
                                       placeholder="Mis. Pendidikan, Teknik Sipil, Hukum">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-card mb-4">
                    <div class="admin-card-body">
                        <div class="form-section-title"><i class="fa-solid fa-align-left"></i>Sinopsis</div>
                        <textarea class="form-control" name="synopsis" rows="9"
                                  placeholder="Ringkasan isi buku, sasaran pembaca, dan keunggulan pembahasan."><?= htmlspecialchars($item['synopsis'] ?? '') ?></textarea>
                        <div class="form-hint">Ditampilkan pada halaman detail buku. Ditulis dalam paragraf biasa.</div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-body">
                        <div class="form-section-title"><i class="fa-solid fa-list"></i>Data Bibliografi</div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="p_year">Tahun terbit</label>
                                <input type="number" class="form-control" id="p_year" name="publish_year"
                                       min="1900" max="<?= (int) date('Y') + 1 ?>"
                                       value="<?= htmlspecialchars((string) ($item['publish_year'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="p_edition">Cetakan / Edisi</label>
                                <input type="text" class="form-control" id="p_edition" name="edition"
                                       value="<?= htmlspecialchars($item['edition'] ?? '') ?>"
                                       placeholder="Cetakan Pertama">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="p_pages">Jumlah halaman</label>
                                <input type="number" class="form-control" id="p_pages" name="pages" min="0"
                                       value="<?= htmlspecialchars((string) ($item['pages'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="p_dim">Ukuran buku</label>
                                <input type="text" class="form-control" id="p_dim" name="dimensions"
                                       value="<?= htmlspecialchars($item['dimensions'] ?? '') ?>"
                                       placeholder="15,5 × 23 cm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="p_lang">Bahasa</label>
                                <input type="text" class="form-control" id="p_lang" name="language"
                                       value="<?= htmlspecialchars($item['language'] ?? 'Indonesia') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="p_isbn">ISBN</label>
                                <input type="text" class="form-control" id="p_isbn" name="isbn"
                                       value="<?= htmlspecialchars($item['isbn'] ?? '') ?>"
                                       placeholder="978-XXX-XXXX-XX-X">
                                <div class="form-hint">
                                    Isi hanya bila nomor sudah terbit. Dicatat sebagai data bibliografi terbitan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="p_price">Harga cetak (Rp)</label>
                                <input type="text" class="form-control" id="p_price" name="price" inputmode="numeric"
                                       value="<?= (int) ($item['price'] ?? 0) ?>">
                                <div class="form-hint">Isi 0 bila tidak ingin menampilkan harga.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="p_url">Tautan pemesanan</label>
                                <input type="url" class="form-control" id="p_url" name="purchase_url"
                                       value="<?= htmlspecialchars($item['purchase_url'] ?? '') ?>"
                                       placeholder="https://…">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <div class="admin-card-body">
                        <div class="form-section-title"><i class="fa-solid fa-paper-plane"></i>Publikasi</div>

                        <div class="mb-3">
                            <label class="form-label" for="p_status">Status</label>
                            <select class="form-select" id="p_status" name="status">
                                <option value="published" <?= $item['status'] === 'published' ? 'selected' : '' ?>>Tayang di situs</option>
                                <option value="draft"     <?= $item['status'] === 'draft' ? 'selected' : '' ?>>Draf (belum tampil)</option>
                            </select>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="p_featured" name="is_featured" value="1"
                                   <?= (int) $item['is_featured'] === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="p_featured">
                                Tandai sebagai judul unggulan
                            </label>
                            <div class="form-hint">Judul unggulan tampil lebih dulu di katalog dan beranda.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-floppy-disk"></i><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Judul' ?>
                        </button>
                        <a href="index.php?page=publikasi" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-body">
                        <div class="form-section-title"><i class="fa-regular fa-image"></i>Sampul Buku</div>

                        <div class="img-preview-wrap mb-3" style="<?= empty($item['cover']) ? 'display:none;' : '' ?>">
                            <img id="coverPreview" class="img-preview img-preview-cover"
                                 src="<?= !empty($item['cover']) ? '../assets/uploads/publications/' . htmlspecialchars($item['cover']) : '' ?>"
                                 alt="Pratinjau sampul">
                        </div>

                        <input type="file" class="form-control" name="cover" accept="image/jpeg,image/png,image/webp,image/gif"
                               onchange="previewImage(this, 'coverPreview')">
                        <div class="form-hint">Format JPG, PNG, WEBP, atau GIF. Maksimal 2MB. Perbandingan ideal 3:4.</div>

                        <?php if (!empty($item['cover'])): ?>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="p_remove_cover" name="remove_cover" value="1">
                            <label class="form-check-label" for="p_remove_cover">Hapus sampul saat menyimpan</label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>
