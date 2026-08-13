<?php
/**
 * Katalog Publikasi — daftar buku yang telah diterbitkan Kayaswara.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Katalog Publikasi — ' . $siteName;
$metaDesc  = 'Katalog buku terbitan ' . $siteName . ': buku ajar, buku referensi, monograf, bunga rampai, dan prosiding karya akademisi Indonesia.';

$categories = getPublicationCategories();

$q       = trim((string) ($_GET['q'] ?? ''));
$cat     = (string) ($_GET['kategori'] ?? '');
$year    = (int) ($_GET['tahun'] ?? 0);
$sort    = (string) ($_GET['urut'] ?? 'terbaru');
if (!isset($categories[$cat])) $cat = '';

$perPage = 12;
$pageNo  = max(1, (int) ($_GET['p'] ?? 1));

$where  = ["status = 'published'"];
$params = [];
if ($q !== '') {
    $where[]  = '(title LIKE ? OR subtitle LIKE ? OR authors LIKE ? OR subject LIKE ?)';
    $like     = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($cat !== '') { $where[] = 'category = ?';     $params[] = $cat; }
if ($year > 0)   { $where[] = 'publish_year = ?'; $params[] = $year; }
$whereSql = implode(' AND ', $where);

$orderSql = 'is_featured DESC, publish_year DESC, id DESC';
if ($sort === 'judul')  $orderSql = 'title ASC';
if ($sort === 'lama')   $orderSql = 'publish_year ASC, id ASC';

$items = [];
$total = 0;
$years = [];
$catCounts = [];
$tableReady = true;

try {
    $total = (int) (fetch("SELECT COUNT(*) c FROM publications WHERE {$whereSql}", $params)['c'] ?? 0);
    $pg    = getPagination($total, $perPage, $pageNo);
    $limit = (int) $pg['per_page'];
    $off   = (int) $pg['offset'];
    $items = fetchAll("SELECT * FROM publications WHERE {$whereSql} ORDER BY {$orderSql} LIMIT {$limit} OFFSET {$off}", $params);

    foreach (fetchAll("SELECT publish_year y, COUNT(*) c FROM publications WHERE status='published' AND publish_year IS NOT NULL AND publish_year > 0 GROUP BY publish_year ORDER BY publish_year DESC") as $r) {
        $years[(int) $r['y']] = (int) $r['c'];
    }
    foreach (fetchAll("SELECT category, COUNT(*) c FROM publications WHERE status='published' GROUP BY category") as $r) {
        $catCounts[$r['category']] = (int) $r['c'];
    }
    $totalAll = (int) (fetch("SELECT COUNT(*) c FROM publications WHERE status='published'")['c'] ?? 0);
} catch (Exception $e) {
    $tableReady = false;
    $pg = getPagination(0, $perPage, 1);
    $totalAll = 0;
}

/** Preserve active filters when building a link. */
function catalogUrl(array $override = []): string {
    $base = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/publikasi';
    $qs   = array_filter(array_merge([
        'q'        => $_GET['q']        ?? '',
        'kategori' => $_GET['kategori'] ?? '',
        'tahun'    => $_GET['tahun']    ?? '',
        'urut'     => $_GET['urut']     ?? '',
        'p'        => $_GET['p']        ?? '',
    ], $override), static fn($v) => $v !== '' && $v !== null);
    return $qs ? $base . '?' . http_build_query($qs) : $base;
}
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Publikasi</li>
        </ol>
        <span class="eyebrow">Katalog Penerbit</span>
        <h1>Katalog Publikasi</h1>
        <p>Daftar buku yang telah diterbitkan <?= htmlspecialchars($siteName) ?> beserta data bibliografinya. Gunakan pencarian dan penyaring untuk menemukan judul yang Anda butuhkan.</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <!-- Toolbar -->
        <div class="catalog-toolbar reveal">
            <form method="get" action="<?= $siteUrl ?>/publikasi" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <label class="visually-hidden" for="catq">Cari judul, penulis, atau bidang</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" class="form-control" id="catq" name="q"
                               value="<?= htmlspecialchars($q) ?>"
                               placeholder="Cari judul, penulis, atau bidang ilmu…"
                               style="border-top-left-radius:0;border-bottom-left-radius:0;">
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <label class="visually-hidden" for="cattahun">Tahun terbit</label>
                    <select class="form-select" id="cattahun" name="tahun">
                        <option value="">Semua tahun</option>
                        <?php foreach ($years as $y => $c): ?>
                        <option value="<?= $y ?>" <?= $year === $y ? 'selected' : '' ?>><?= $y ?> (<?= $c ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="visually-hidden" for="caturut">Urutkan</label>
                    <select class="form-select" id="caturut" name="urut">
                        <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                        <option value="lama"    <?= $sort === 'lama'    ? 'selected' : '' ?>>Terlama</option>
                        <option value="judul"   <?= $sort === 'judul'   ? 'selected' : '' ?>>Judul A–Z</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <?php if ($cat !== ''): ?><input type="hidden" name="kategori" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>

            <hr class="hr-soft my-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="filter-pills">
                    <a href="<?= htmlspecialchars(catalogUrl(['kategori' => '', 'p' => ''])) ?>" class="filter-pill <?= $cat === '' ? 'active' : '' ?>">
                        Semua <span class="cnt"><?= $totalAll ?></span>
                    </a>
                    <?php foreach ($categories as $key => $label):
                        $c = $catCounts[$key] ?? 0;
                        if ($c === 0 && $cat !== $key) continue;
                    ?>
                    <a href="<?= htmlspecialchars(catalogUrl(['kategori' => $key, 'p' => ''])) ?>" class="filter-pill <?= $cat === $key ? 'active' : '' ?>">
                        <?= htmlspecialchars($label) ?> <span class="cnt"><?= $c ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <span class="result-count">
                    <?= $total ?> judul ditemukan<?= $q !== '' ? ' untuk “' . htmlspecialchars($q) . '”' : '' ?>
                </span>
            </div>
        </div>

        <!-- Grid -->
        <?php if (!empty($items)): ?>
        <div class="row g-4">
            <?php foreach ($items as $i => $b):
                $cover = publicationCoverUrl($b['cover'] ?? '');
                $url   = $siteUrl . '/publikasi/' . htmlspecialchars($b['slug']);
            ?>
            <div class="col-6 col-md-4 col-lg-3 reveal" data-reveal-delay="<?= ($i % 4) * 0.05 ?>">
                <article class="book-card">
                    <a class="book-cover" href="<?= $url ?>" aria-label="<?= htmlspecialchars($b['title']) ?>">
                        <span class="book-cover-tag chip chip-gold"><?= htmlspecialchars(getPublicationCategoryLabel($b['category'])) ?></span>
                        <?php if ($cover): ?>
                            <img src="<?= htmlspecialchars($cover) ?>" alt="Sampul <?= htmlspecialchars($b['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="book-cover-fallback">
                                <span class="bcf-mark"><?= htmlspecialchars($siteName) ?></span>
                                <span class="bcf-title"><?= htmlspecialchars($b['title']) ?></span>
                                <span class="bcf-mark"><?= htmlspecialchars((string) ($b['publish_year'] ?: '')) ?></span>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="book-body">
                        <h2 class="book-title"><a href="<?= $url ?>"><?= htmlspecialchars($b['title']) ?></a></h2>
                        <p class="book-author"><?= htmlspecialchars($b['authors']) ?></p>
                        <div class="book-foot">
                            <span><?= htmlspecialchars((string) ($b['publish_year'] ?: '—')) ?></span>
                            <span><?= $b['pages'] ? (int) $b['pages'] . ' hlm.' : '' ?></span>
                        </div>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (($pg['total_pages'] ?? 1) > 1): ?>
        <nav class="mt-5" aria-label="Navigasi halaman katalog">
            <ul class="pagination justify-content-center">
                <?php if ($pg['has_prev']): ?>
                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(catalogUrl(['p' => $pg['prev']])) ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $pg['total_pages']; $p++): ?>
                <li class="page-item <?= $p === $pg['current'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars(catalogUrl(['p' => $p])) ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
                <?php if ($pg['has_next']): ?>
                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(catalogUrl(['p' => $pg['next']])) ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state reveal">
            <i class="fa-solid fa-book-open"></i>
            <?php if (!$tableReady): ?>
                <h4>Katalog belum aktif</h4>
                <p>Tabel katalog belum tersedia pada basis data. Jalankan <code>migrate.php</code> untuk mengaktifkannya.</p>
            <?php elseif ($q !== '' || $cat !== '' || $year > 0): ?>
                <h4>Tidak ada judul yang cocok</h4>
                <p>Coba ubah kata kunci atau lepaskan penyaring yang sedang aktif.</p>
                <a href="<?= $siteUrl ?>/publikasi" class="btn btn-outline-primary btn-sm">Tampilkan Semua Judul</a>
            <?php else: ?>
                <h4>Katalog sedang disiapkan</h4>
                <p>Daftar terbitan akan ditampilkan di halaman ini begitu tersedia.</p>
                <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-outline-primary btn-sm">Kirim Naskah Anda</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Ingin karya Anda masuk katalog ini?</h2>
                    <p>Kirimkan naskah beserta deskripsi singkatnya untuk ditelaah oleh redaksi.</p>
                </div>
                <div class="col-lg-4">
                    <div class="cta-actions justify-content-lg-end">
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-accent btn-lg"><i class="fa-regular fa-paper-plane"></i>Kirim Naskah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
