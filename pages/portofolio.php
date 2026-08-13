<?php
/**
 * Kerja Sama Institusi — dokumentasi kolaborasi penerbitan.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Kerja Sama Institusi — ' . $siteName;
$metaDesc  = 'Dokumentasi kerja sama penerbitan ' . $siteName . ' bersama perguruan tinggi, lembaga penelitian, dan komunitas akademik.';

$partnerCats = getPartnerCategories();

$cat    = (string) ($_GET['kategori'] ?? '');
if (!isset($partnerCats[$cat])) $cat = '';
$pageNo  = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 9;

$where  = ["status = 'published'"];
$params = [];
if ($cat !== '') { $where[] = 'category = ?'; $params[] = $cat; }
$whereSql = implode(' AND ', $where);

$items = [];
$total = 0;
try {
    $total = (int) (fetch("SELECT COUNT(*) c FROM portfolio WHERE {$whereSql}", $params)['c'] ?? 0);
    $pg    = getPagination($total, $perPage, $pageNo);
    $limit = (int) $pg['per_page'];
    $off   = (int) $pg['offset'];
    $items = fetchAll("SELECT * FROM portfolio WHERE {$whereSql} ORDER BY is_featured DESC, created_at DESC LIMIT {$limit} OFFSET {$off}", $params);
} catch (Exception $e) {
    $pg = getPagination(0, $perPage, 1);
}

function partnerUrl(array $override = []): string {
    $base = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/portofolio';
    $qs = array_filter(array_merge([
        'kategori' => $_GET['kategori'] ?? '',
        'p'        => $_GET['p'] ?? '',
    ], $override), static fn($v) => $v !== '' && $v !== null);
    return $qs ? $base . '?' . http_build_query($qs) : $base;
}
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Kerja Sama</li>
        </ol>
        <span class="eyebrow">Kolaborasi</span>
        <h1>Kerja Sama Institusi</h1>
        <p>Program penerbitan yang kami kerjakan bersama perguruan tinggi, lembaga penelitian, dan komunitas akademik di berbagai daerah.</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="catalog-toolbar reveal">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="filter-pills">
                    <a href="<?= htmlspecialchars(partnerUrl(['kategori' => '', 'p' => ''])) ?>" class="filter-pill <?= $cat === '' ? 'active' : '' ?>">Semua</a>
                    <?php foreach ($partnerCats as $key => $label): ?>
                    <a href="<?= htmlspecialchars(partnerUrl(['kategori' => $key, 'p' => ''])) ?>" class="filter-pill <?= $cat === $key ? 'active' : '' ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <span class="result-count"><?= $total ?> kerja sama tercatat</span>
            </div>
        </div>

        <?php if (!empty($items)): ?>
        <div class="row g-4">
            <?php foreach ($items as $i => $it): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= ($i % 3) * 0.06 ?>">
                <article class="article-card">
                    <a class="article-thumb" href="<?= $siteUrl ?>/portofolio/<?= htmlspecialchars($it['slug']) ?>">
                        <?php if (!empty($it['image'])): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/portfolio/<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <i class="fa-solid fa-building-columns"></i>
                        <?php endif; ?>
                    </a>
                    <div class="article-body">
                        <div class="article-meta">
                            <span class="chip chip-plain"><?= htmlspecialchars(getPartnerCategoryLabel($it['category'])) ?></span>
                            <?php if ($it['is_featured']): ?><span class="chip chip-gold">Unggulan</span><?php endif; ?>
                        </div>
                        <h2 class="article-title">
                            <a href="<?= $siteUrl ?>/portofolio/<?= htmlspecialchars($it['slug']) ?>"><?= htmlspecialchars($it['title']) ?></a>
                        </h2>
                        <?php if (!empty($it['client_institution'])): ?>
                        <p class="text-muted mb-2" style="font-size:.86rem;">
                            <i class="fa-solid fa-building-columns me-1"></i><?= htmlspecialchars($it['client_institution']) ?>
                        </p>
                        <?php endif; ?>
                        <p class="article-excerpt"><?= htmlspecialchars(truncate($it['description'] ?? '', 130)) ?></p>
                        <a href="<?= $siteUrl ?>/portofolio/<?= htmlspecialchars($it['slug']) ?>" class="btn-link-arrow">Lihat detail<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (($pg['total_pages'] ?? 1) > 1): ?>
        <nav class="mt-5" aria-label="Navigasi halaman kerja sama">
            <ul class="pagination justify-content-center">
                <?php if ($pg['has_prev']): ?>
                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(partnerUrl(['p' => $pg['prev']])) ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $pg['total_pages']; $p++): ?>
                <li class="page-item <?= $p === $pg['current'] ? 'active' : '' ?>"><a class="page-link" href="<?= htmlspecialchars(partnerUrl(['p' => $p])) ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <?php if ($pg['has_next']): ?>
                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(partnerUrl(['p' => $pg['next']])) ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state reveal">
            <i class="fa-solid fa-handshake"></i>
            <h4>Belum ada kerja sama yang ditampilkan</h4>
            <p>Dokumentasi kolaborasi penerbitan akan tayang di halaman ini.</p>
            <a href="<?= $siteUrl ?>/publikasi" class="btn btn-outline-primary btn-sm">Lihat Katalog Publikasi</a>
        </div>
        <?php endif; ?>

    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Merencanakan program penerbitan institusi?</h2>
                    <p>Kami dapat menyusun jadwal penerbitan bersama untuk beberapa judul sekaligus dalam satu perjanjian kerangka.</p>
                </div>
                <div class="col-lg-4">
                    <div class="cta-actions justify-content-lg-end">
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-accent btn-lg"><i class="fa-regular fa-envelope"></i>Ajukan Kerja Sama</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
