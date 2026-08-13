<?php
/**
 * Wawasan — kumpulan tulisan redaksi untuk penulis.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Wawasan — ' . $siteName;
$metaDesc  = 'Catatan redaksi ' . $siteName . ' seputar penulisan dan penerbitan karya akademik: penyusunan buku ajar, penyuntingan, sitasi, dan tata letak.';

$q      = trim((string) ($_GET['q'] ?? ''));
$cat    = trim((string) ($_GET['kategori'] ?? ''));
$pageNo = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 9;

$where  = ["status = 'published'"];
$params = [];
if ($q !== '') {
    $where[]  = '(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)';
    $like     = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($cat !== '') { $where[] = 'category = ?'; $params[] = $cat; }
$whereSql = implode(' AND ', $where);

$posts = [];
$total = 0;
$cats  = [];
$featured = null;

try {
    $total = (int) (fetch("SELECT COUNT(*) c FROM blog_posts WHERE {$whereSql}", $params)['c'] ?? 0);
    $pg    = getPagination($total, $perPage, $pageNo);
    $limit = (int) $pg['per_page'];
    $off   = (int) $pg['offset'];
    $posts = fetchAll("SELECT * FROM blog_posts WHERE {$whereSql} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$off}", $params);

    foreach (fetchAll("SELECT category, COUNT(*) c FROM blog_posts WHERE status='published' AND category <> '' GROUP BY category ORDER BY c DESC") as $r) {
        $cats[$r['category']] = (int) $r['c'];
    }

    if ($pageNo === 1 && $q === '' && $cat === '' && !empty($posts)) {
        $featured = array_shift($posts);
    }
} catch (Exception $e) {
    $pg = getPagination(0, $perPage, 1);
}

function blogUrl(array $override = []): string {
    $base = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/wawasan';
    $qs = array_filter(array_merge([
        'q'        => $_GET['q']        ?? '',
        'kategori' => $_GET['kategori'] ?? '',
        'p'        => $_GET['p']        ?? '',
    ], $override), static fn($v) => $v !== '' && $v !== null);
    return $qs ? $base . '?' . http_build_query($qs) : $base;
}
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Wawasan</li>
        </ol>
        <span class="eyebrow">Catatan Redaksi</span>
        <h1>Wawasan</h1>
        <p>Panduan praktis dari meja redaksi: menyusun naskah akademik, menyunting, mengelola sitasi, dan mempersiapkan buku sampai siap terbit.</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="catalog-toolbar reveal">
            <div class="row g-3 align-items-center">
                <div class="col-lg-7">
                    <div class="filter-pills">
                        <a href="<?= htmlspecialchars(blogUrl(['kategori' => '', 'p' => ''])) ?>" class="filter-pill <?= $cat === '' ? 'active' : '' ?>">Semua</a>
                        <?php foreach ($cats as $c => $n): ?>
                        <a href="<?= htmlspecialchars(blogUrl(['kategori' => $c, 'p' => ''])) ?>" class="filter-pill <?= $cat === $c ? 'active' : '' ?>">
                            <?= htmlspecialchars($c) ?> <span class="cnt"><?= $n ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <form method="get" action="<?= $siteUrl ?>/wawasan" class="input-group">
                        <?php if ($cat !== ''): ?><input type="hidden" name="kategori" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" name="q" class="form-control" value="<?= htmlspecialchars($q) ?>"
                               placeholder="Cari tulisan…" aria-label="Cari tulisan"
                               style="border-top-left-radius:0;border-bottom-left-radius:0;">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($featured): ?>
        <article class="card-plain mb-4 reveal" style="padding:0;overflow:hidden;">
            <div class="row g-0 align-items-stretch">
                <div class="col-md-5">
                    <a class="article-thumb h-100 d-block" href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($featured['slug']) ?>" style="aspect-ratio:auto;min-height:240px;">
                        <?php if (!empty($featured['image'])): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/blog/<?= htmlspecialchars($featured['image']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
                        <?php else: ?>
                            <span class="d-grid h-100 w-100" style="place-items:center;"><i class="fa-regular fa-file-lines" style="font-size:2rem;color:var(--line-strong);"></i></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="col-md-7">
                    <div class="p-4 p-lg-5">
                        <span class="chip chip-gold mb-3">Tulisan Terbaru</span>
                        <div class="article-meta">
                            <?php if (!empty($featured['category'])): ?><span><i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($featured['category']) ?></span><?php endif; ?>
                            <span><i class="fa-regular fa-calendar me-1"></i><?= formatDate($featured['created_at']) ?></span>
                            <span><i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($featured['author'] ?: 'Redaksi') ?></span>
                        </div>
                        <h2 class="h3 mb-2">
                            <a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($featured['slug']) ?>" style="color:var(--ink);"><?= htmlspecialchars($featured['title']) ?></a>
                        </h2>
                        <p class="mb-3"><?= htmlspecialchars(truncate($featured['excerpt'] ?: strip_tags($featured['content'] ?? ''), 220)) ?></p>
                        <a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($featured['slug']) ?>" class="btn-link-arrow">Baca selengkapnya<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </article>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
        <div class="row g-4">
            <?php foreach ($posts as $i => $p): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= ($i % 3) * 0.06 ?>">
                <article class="article-card">
                    <a class="article-thumb" href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($p['slug']) ?>">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/blog/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <i class="fa-regular fa-file-lines"></i>
                        <?php endif; ?>
                    </a>
                    <div class="article-body">
                        <div class="article-meta">
                            <?php if (!empty($p['category'])): ?><span><i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($p['category']) ?></span><?php endif; ?>
                            <span><i class="fa-regular fa-calendar me-1"></i><?= formatDate($p['created_at']) ?></span>
                        </div>
                        <h2 class="article-title"><a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a></h2>
                        <p class="article-excerpt"><?= htmlspecialchars(truncate($p['excerpt'] ?: strip_tags($p['content'] ?? ''), 140)) ?></p>
                        <a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($p['slug']) ?>" class="btn-link-arrow">Baca<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (($pg['total_pages'] ?? 1) > 1): ?>
        <nav class="mt-5" aria-label="Navigasi halaman tulisan">
            <ul class="pagination justify-content-center">
                <?php if ($pg['has_prev']): ?>
                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(blogUrl(['p' => $pg['prev']])) ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $pg['total_pages']; $p++): ?>
                <li class="page-item <?= $p === $pg['current'] ? 'active' : '' ?>"><a class="page-link" href="<?= htmlspecialchars(blogUrl(['p' => $p])) ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <?php if ($pg['has_next']): ?>
                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(blogUrl(['p' => $pg['next']])) ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php elseif (!$featured): ?>
        <div class="empty-state reveal">
            <i class="fa-regular fa-file-lines"></i>
            <h4><?= ($q !== '' || $cat !== '') ? 'Tidak ada tulisan yang cocok' : 'Belum ada tulisan' ?></h4>
            <p><?= ($q !== '' || $cat !== '') ? 'Coba kata kunci lain atau lepaskan penyaring.' : 'Catatan redaksi akan tayang di halaman ini.' ?></p>
            <?php if ($q !== '' || $cat !== ''): ?>
            <a href="<?= $siteUrl ?>/wawasan" class="btn btn-outline-primary btn-sm">Tampilkan Semua Tulisan</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
