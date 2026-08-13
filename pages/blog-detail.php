<?php
/**
 * Detail tulisan Wawasan.
 */
$siteUrl  = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$slug     = preg_replace('/[^a-z0-9\-]/i', '', (string) ($_GET['slug'] ?? ''));

$post = null;
try {
    $post = fetch("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'", [$slug]);
} catch (Exception $e) {}

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Tulisan tidak ditemukan — ' . $siteName;
    ?>
    <div class="section">
        <div class="container">
            <div class="empty-state" style="max-width:620px;margin:0 auto;">
                <i class="fa-regular fa-file-lines"></i>
                <h4>Tulisan tidak ditemukan</h4>
                <p>Tulisan yang Anda cari tidak tersedia atau telah dipindahkan.</p>
                <a href="<?= $siteUrl ?>/wawasan" class="btn btn-primary btn-sm">Kembali ke Wawasan</a>
            </div>
        </div>
    </div>
    <?php
    return;
}

try { query("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]); } catch (Exception $e) {}

$pageTitle = $post['title'] . ' — ' . $siteName;
$metaDesc  = truncate($post['excerpt'] ?: strip_tags($post['content'] ?? ''), 175);
$shareUrl  = $siteUrl . '/wawasan/' . $post['slug'];

$related = [];
try {
    $related = fetchAll(
        "SELECT title, slug, image, created_at, category FROM blog_posts
         WHERE status='published' AND id <> ? " . (!empty($post['category']) ? "AND category = ? " : '') .
        "ORDER BY created_at DESC LIMIT 3",
        !empty($post['category']) ? [$post['id'], $post['category']] : [$post['id']]
    );
    if (count($related) < 3) {
        $related = fetchAll("SELECT title, slug, image, created_at, category FROM blog_posts WHERE status='published' AND id <> ? ORDER BY created_at DESC LIMIT 3", [$post['id']]);
    }
} catch (Exception $e) {}

// preg_split, bukan str_word_count(), agar kata ber-UTF-8 tetap terhitung
$wordCount   = count(preg_split('/\s+/u', trim(strip_tags($post['content'] ?? '')), -1, PREG_SPLIT_NO_EMPTY));
$readMinutes = max(1, (int) ceil($wordCount / 200));
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li><a href="<?= $siteUrl ?>/wawasan">Wawasan</a></li>
            <li><?= htmlspecialchars(truncate($post['title'], 44)) ?></li>
        </ol>
        <?php if (!empty($post['category'])): ?>
        <span class="eyebrow"><?= htmlspecialchars($post['category']) ?></span>
        <?php endif; ?>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p>
            <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($post['author'] ?: 'Redaksi') ?>
            <span class="mx-2">·</span>
            <i class="fa-regular fa-calendar me-1"></i><?= formatDate($post['created_at'], 'd F Y') ?>
            <span class="mx-2">·</span>
            <i class="fa-regular fa-clock me-1"></i><?= $readMinutes ?> menit baca
        </p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-5 justify-content-center">
            <div class="col-lg-8">
                <article class="reveal">
                    <?php if (!empty($post['image'])): ?>
                    <figure class="mb-4">
                        <img src="<?= $siteUrl ?>/assets/uploads/blog/<?= htmlspecialchars($post['image']) ?>"
                             alt="<?= htmlspecialchars($post['title']) ?>"
                             class="w-100 rounded-md" style="border:1px solid var(--line);">
                    </figure>
                    <?php endif; ?>

                    <?php if (!empty($post['excerpt'])): ?>
                    <p class="lead serif" style="font-size:1.15rem;color:var(--ink);"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <hr class="hr-soft">
                    <?php endif; ?>

                    <div class="prose">
                        <?= sanitizeHtml($post['content'] ?? '') ?>
                    </div>

                    <hr class="hr-soft">

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <a href="<?= $siteUrl ?>/wawasan" class="btn-link-arrow">
                            <i class="fa-solid fa-arrow-left"></i>Kembali ke Wawasan
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Bagikan:</span>
                            <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener"
                               href="https://api.whatsapp.com/send?text=<?= rawurlencode($post['title'] . ' — ' . $shareUrl) ?>" aria-label="Bagikan via WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                            <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener"
                               href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($shareUrl) ?>" aria-label="Bagikan via Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener"
                               href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($shareUrl) ?>" aria-label="Bagikan via LinkedIn">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-lg-4">
                <div class="reveal" data-reveal-delay="0.08" style="position:sticky;top:110px;">
                    <div class="side-panel">
                        <h4>Kirim Naskah Anda</h4>
                        <p style="font-size:.93rem;">Sudah punya naskah yang siap ditelaah redaksi? Kirimkan berkasnya hari ini.</p>
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary w-100">
                            <i class="fa-regular fa-paper-plane"></i>Kirim Naskah
                        </a>
                    </div>

                    <?php if (!empty($related)): ?>
                    <div class="side-panel">
                        <h4>Tulisan Lain</h4>
                        <?php foreach ($related as $r): ?>
                        <div class="contact-row">
                            <span class="ic"><i class="fa-regular fa-file-lines"></i></span>
                            <div>
                                <div class="lb"><?= formatDate($r['created_at']) ?></div>
                                <div class="vl">
                                    <a href="<?= $siteUrl ?>/wawasan/<?= htmlspecialchars($r['slug']) ?>" style="color:var(--ink);">
                                        <?= htmlspecialchars(truncate($r['title'], 62)) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
