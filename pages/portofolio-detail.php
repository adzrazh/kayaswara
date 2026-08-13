<?php
/**
 * Detail kerja sama institusi.
 */
$siteUrl  = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$slug     = preg_replace('/[^a-z0-9\-]/i', '', (string) ($_GET['slug'] ?? ''));

$item = null;
try {
    $item = fetch("SELECT * FROM portfolio WHERE slug = ? AND status = 'published'", [$slug]);
} catch (Exception $e) {}

if (!$item) {
    http_response_code(404);
    $pageTitle = 'Data tidak ditemukan — ' . $siteName;
    ?>
    <div class="section">
        <div class="container">
            <div class="empty-state" style="max-width:620px;margin:0 auto;">
                <i class="fa-solid fa-handshake"></i>
                <h4>Data tidak ditemukan</h4>
                <p>Dokumentasi kerja sama yang Anda cari tidak tersedia.</p>
                <a href="<?= $siteUrl ?>/portofolio" class="btn btn-primary btn-sm">Kembali</a>
            </div>
        </div>
    </div>
    <?php
    return;
}

$pageTitle = $item['title'] . ' — ' . $siteName;
$metaDesc  = truncate($item['description'] ?? $item['title'], 175);

$others = [];
try {
    $others = fetchAll("SELECT title, slug, image, client_institution FROM portfolio WHERE status='published' AND id <> ? ORDER BY created_at DESC LIMIT 3", [$item['id']]);
} catch (Exception $e) {}

$facts = array_filter([
    'Jenis mitra'  => getPartnerCategoryLabel($item['category']),
    'Institusi'    => $item['client_institution'] ?? '',
    'Penanggung jawab' => $item['client_name'] ?? '',
    'Tahun'        => !empty($item['created_at']) ? date('Y', strtotime($item['created_at'])) : '',
], static fn($v) => trim((string) $v) !== '');
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li><a href="<?= $siteUrl ?>/portofolio">Kerja Sama</a></li>
            <li><?= htmlspecialchars(truncate($item['title'], 44)) ?></li>
        </ol>
        <span class="eyebrow"><?= htmlspecialchars(getPartnerCategoryLabel($item['category'])) ?></span>
        <h1><?= htmlspecialchars($item['title']) ?></h1>
        <?php if (!empty($item['client_institution'])): ?>
        <p><i class="fa-solid fa-building-columns me-1"></i><?= htmlspecialchars($item['client_institution']) ?></p>
        <?php endif; ?>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8 reveal">
                <?php if (!empty($item['image'])): ?>
                <figure class="mb-4">
                    <img src="<?= $siteUrl ?>/assets/uploads/portfolio/<?= htmlspecialchars($item['image']) ?>"
                         alt="<?= htmlspecialchars($item['title']) ?>" class="w-100 rounded-md" style="border:1px solid var(--line);">
                </figure>
                <?php endif; ?>

                <h2 class="h3">Ringkasan Kerja Sama</h2>
                <div class="rule-accent"></div>
                <div class="prose">
                    <?= !empty($item['description']) ? nl2br(htmlspecialchars($item['description'])) : '<p class="text-muted">Keterangan belum tersedia.</p>' ?>
                </div>

                <div class="mt-4">
                    <a href="<?= $siteUrl ?>/portofolio" class="btn-link-arrow"><i class="fa-solid fa-arrow-left"></i>Kembali ke daftar kerja sama</a>
                </div>
            </div>

            <div class="col-lg-4 reveal" data-reveal-delay="0.08">
                <div class="side-panel">
                    <h4>Keterangan</h4>
                    <table class="biblio-table">
                        <tbody>
                        <?php foreach ($facts as $k => $v): ?>
                        <tr><th scope="row"><?= htmlspecialchars($k) ?></th><td><?= htmlspecialchars((string) $v) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (!empty($item['website_url'])): ?>
                    <a href="<?= htmlspecialchars($item['website_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary w-100 mt-3">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>Kunjungi Situs Mitra
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($others)): ?>
                <div class="side-panel">
                    <h4>Kerja Sama Lainnya</h4>
                    <?php foreach ($others as $o): ?>
                    <div class="contact-row">
                        <span class="ic"><i class="fa-solid fa-handshake"></i></span>
                        <div>
                            <div class="lb"><?= htmlspecialchars($o['client_institution'] ?: 'Mitra') ?></div>
                            <div class="vl"><a href="<?= $siteUrl ?>/portofolio/<?= htmlspecialchars($o['slug']) ?>" style="color:var(--ink);"><?= htmlspecialchars(truncate($o['title'], 60)) ?></a></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
