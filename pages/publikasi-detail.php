<?php
/**
 * Detail publikasi — satu judul dalam katalog penerbit.
 */
$siteUrl  = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$slug     = preg_replace('/[^a-z0-9\-]/i', '', (string) ($_GET['slug'] ?? ''));

$book = null;
try {
    $book = fetch("SELECT * FROM publications WHERE slug = ? AND status = 'published'", [$slug]);
} catch (Exception $e) {}

if (!$book) {
    http_response_code(404);
    $pageTitle = 'Judul tidak ditemukan — ' . $siteName;
    ?>
    <div class="section">
        <div class="container">
            <div class="empty-state" style="max-width:620px;margin:0 auto;">
                <i class="fa-solid fa-book-open"></i>
                <h4>Judul tidak ditemukan</h4>
                <p>Buku yang Anda cari tidak tersedia atau telah dikeluarkan dari katalog.</p>
                <a href="<?= $siteUrl ?>/publikasi" class="btn btn-primary btn-sm">Kembali ke Katalog</a>
            </div>
        </div>
    </div>
    <?php
    return;
}

// Pencatatan kunjungan (best effort)
try { query("UPDATE publications SET views = views + 1 WHERE id = ?", [$book['id']]); } catch (Exception $e) {}

$pageTitle = $book['title'] . ' — Katalog ' . $siteName;
$metaDesc  = truncate($book['synopsis'] ?: ($book['title'] . ' karya ' . $book['authors']), 175);
$cover     = publicationCoverUrl($book['cover'] ?? '');
$catLabel  = getPublicationCategoryLabel($book['category']);

$related = [];
try {
    $related = fetchAll(
        "SELECT title, slug, authors, cover, category, publish_year
         FROM publications
         WHERE status='published' AND category = ? AND id <> ?
         ORDER BY publish_year DESC, id DESC LIMIT 4",
        [$book['category'], $book['id']]
    );
} catch (Exception $e) {}

// Baris data bibliografi — hanya yang terisi yang ditampilkan
$biblio = array_filter([
    'Judul'          => $book['title'],
    'Anak judul'     => $book['subtitle'] ?? '',
    'Penulis'        => $book['authors'],
    'Editor'         => $book['editor'] ?? '',
    'Kategori'       => $catLabel,
    'Bidang ilmu'    => $book['subject'] ?? '',
    'Penerbit'       => html_entity_decode(getSetting('legal_name', 'CV. Kayaswara'), ENT_QUOTES, 'UTF-8'),
    'Tahun terbit'   => $book['publish_year'] ? (string) $book['publish_year'] : '',
    'Cetakan/Edisi'  => $book['edition'] ?? '',
    'Jumlah halaman' => $book['pages'] ? $book['pages'] . ' halaman' : '',
    'Ukuran'         => $book['dimensions'] ?? '',
    'Bahasa'         => $book['language'] ?? '',
    'ISBN'           => $book['isbn'] ?? '',
], static fn($v) => trim((string) $v) !== '');
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li><a href="<?= $siteUrl ?>/publikasi">Publikasi</a></li>
            <li><?= htmlspecialchars(truncate($book['title'], 48)) ?></li>
        </ol>
        <span class="eyebrow"><?= htmlspecialchars($catLabel) ?></span>
        <h1><?= htmlspecialchars($book['title']) ?></h1>
        <?php if (!empty($book['subtitle'])): ?>
        <p><?= htmlspecialchars($book['subtitle']) ?></p>
        <?php endif; ?>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5">

            <!-- Sampul + tindakan -->
            <div class="col-lg-4">
                <div class="reveal">
                    <div class="detail-cover">
                        <?php if ($cover): ?>
                            <img src="<?= htmlspecialchars($cover) ?>" alt="Sampul <?= htmlspecialchars($book['title']) ?>">
                        <?php else: ?>
                            <div class="book-cover-fallback">
                                <span class="bcf-mark"><?= htmlspecialchars($siteName) ?></span>
                                <span class="bcf-title"><?= htmlspecialchars($book['title']) ?></span>
                                <span class="bcf-mark"><?= htmlspecialchars((string) ($book['publish_year'] ?: '')) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ((int) $book['price'] > 0 || !empty($book['purchase_url'])): ?>
                    <div class="side-panel mt-4">
                        <?php if ((int) $book['price'] > 0): ?>
                        <div class="mb-3">
                            <div class="lb text-muted" style="font-size:.78rem;">Harga cetak</div>
                            <div class="serif" style="font-size:1.5rem;font-weight:700;color:var(--primary);"><?= rupiah($book['price']) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($book['purchase_url'])): ?>
                        <a href="<?= htmlspecialchars($book['purchase_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary w-100">
                            <i class="fa-solid fa-cart-shopping"></i>Pesan Buku
                        </a>
                        <?php else: ?>
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-outline-primary w-100">
                            <i class="fa-regular fa-envelope"></i>Tanya Ketersediaan
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="side-panel mt-4">
                        <h4>Data Bibliografi</h4>
                        <table class="biblio-table">
                            <tbody>
                            <?php foreach ($biblio as $label => $value): ?>
                                <tr>
                                    <th scope="row"><?= htmlspecialchars($label) ?></th>
                                    <td><?= htmlspecialchars((string) $value) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sinopsis -->
            <div class="col-lg-8">
                <div class="reveal" data-reveal-delay="0.08">
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="chip"><?= htmlspecialchars($catLabel) ?></span>
                        <?php if (!empty($book['subject'])): ?><span class="chip chip-plain"><?= htmlspecialchars($book['subject']) ?></span><?php endif; ?>
                        <?php if (!empty($book['publish_year'])): ?><span class="chip chip-plain"><?= (int) $book['publish_year'] ?></span><?php endif; ?>
                    </div>

                    <h2 class="h3">Tentang Buku Ini</h2>
                    <div class="rule-accent"></div>

                    <?php if (!empty($book['synopsis'])): ?>
                        <div class="prose"><?= nl2br(htmlspecialchars($book['synopsis'])) ?></div>
                    <?php else: ?>
                        <p class="text-muted">Sinopsis untuk judul ini belum tersedia.</p>
                    <?php endif; ?>

                    <div class="row g-3 mt-4">
                        <div class="col-sm-6">
                            <div class="card-plain">
                                <div class="service-icon"><i class="fa-solid fa-feather-pointed"></i></div>
                                <h3 class="h6 mb-1">Penulis</h3>
                                <p class="mb-0" style="font-size:.93rem;"><?= htmlspecialchars($book['authors']) ?></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card-plain">
                                <div class="service-icon"><i class="fa-solid fa-building-columns"></i></div>
                                <h3 class="h6 mb-1">Penerbit</h3>
                                <p class="mb-0" style="font-size:.93rem;">
                                    <?= htmlspecialchars(html_entity_decode(getSetting('legal_name', 'CV. Kayaswara'), ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="notice mt-4">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>
                            Hak cipta atas isi buku berada pada penulis. Pengutipan wajib menyebutkan sumber
                            sesuai kaidah penulisan ilmiah yang berlaku.
                        </span>
                    </div>

                    <div class="mt-4">
                        <a href="<?= $siteUrl ?>/publikasi" class="btn-link-arrow">
                            <i class="fa-solid fa-arrow-left"></i>Kembali ke katalog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($related)): ?>
<section class="section paper-grain">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">Judul Terkait</span>
            <h2 class="h3">Terbitan lain pada lini <?= htmlspecialchars($catLabel) ?></h2>
        </div>
        <div class="row g-4">
            <?php foreach ($related as $i => $r):
                $rc = publicationCoverUrl($r['cover'] ?? '');
            ?>
            <div class="col-6 col-lg-3 reveal" data-reveal-delay="<?= $i * 0.06 ?>">
                <article class="book-card">
                    <a class="book-cover" href="<?= $siteUrl ?>/publikasi/<?= htmlspecialchars($r['slug']) ?>" aria-label="<?= htmlspecialchars($r['title']) ?>">
                        <?php if ($rc): ?>
                            <img src="<?= htmlspecialchars($rc) ?>" alt="Sampul <?= htmlspecialchars($r['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="book-cover-fallback">
                                <span class="bcf-mark"><?= htmlspecialchars($siteName) ?></span>
                                <span class="bcf-title"><?= htmlspecialchars($r['title']) ?></span>
                                <span class="bcf-mark"><?= htmlspecialchars((string) ($r['publish_year'] ?: '')) ?></span>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="book-body">
                        <h3 class="book-title"><a href="<?= $siteUrl ?>/publikasi/<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['title']) ?></a></h3>
                        <p class="book-author mb-0"><?= htmlspecialchars($r['authors']) ?></p>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
