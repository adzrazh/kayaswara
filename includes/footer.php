<?php
/**
 * Global site footer — Kayaswara
 */
$siteUrl      = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName     = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$siteTagline  = html_entity_decode(getSetting('site_tagline', 'Penerbit Buku Akademik'), ENT_QUOTES, 'UTF-8');
$legalName    = html_entity_decode(getSetting('legal_name', 'CV. Kayaswara'), ENT_QUOTES, 'UTF-8');
$logoPath     = getSetting('logo_path', '');
$footerText   = getSetting('footer_text', '© ' . date('Y') . ' ' . $legalName . '. Seluruh hak cipta dilindungi.');
$whatsapp     = getSetting('whatsapp_number', '');
$waDigits     = $whatsapp ? preg_replace('/\D/', '', $whatsapp) : '';
$emailContact = getSetting('email_contact', '');
$address      = getSetting('address', '');

$socialIg = getSetting('social_instagram', '');
$socialFb = getSetting('social_facebook', '');
$socialYt = getSetting('social_youtube', '');
$socialLi = getSetting('social_linkedin', '');
?>
</main><!-- /#main -->

<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="row g-4 g-lg-5">

                <div class="col-lg-4 col-md-12">
                    <div class="footer-brand">
                        <span class="brand-mark">
                            <?php if (!empty($logoPath)): ?>
                                <img src="<?= $siteUrl ?>/assets/uploads/site/<?= htmlspecialchars($logoPath) ?>" alt="Logo <?= htmlspecialchars($siteName) ?>">
                            <?php else: ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 5.5C5.4 4.2 8.2 4.2 10.6 5.5v13c-2.4-1.3-5.2-1.3-7.6 0v-13Z" fill="currentColor" opacity=".9"/>
                                    <path d="M21 5.5c-2.4-1.3-5.2-1.3-7.6 0v13c2.4-1.3 5.2-1.3 7.6 0v-13Z" fill="currentColor" opacity=".55"/>
                                    <path d="M12 4v16" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
                                </svg>
                            <?php endif; ?>
                        </span>
                        <span>
                            <span class="brand-name"><?= htmlspecialchars($siteName) ?></span>
                            <span class="brand-sub"><?= htmlspecialchars($siteTagline) ?></span>
                        </span>
                    </div>
                    <p class="footer-about">
                        <?= htmlspecialchars($legalName) ?> menerbitkan buku ajar, buku referensi, monograf, dan
                        bunga rampai karya dosen serta peneliti Indonesia — melalui proses penelaahan,
                        penyuntingan, dan produksi yang terdokumentasi.
                    </p>
                    <?php if ($socialIg || $socialFb || $socialYt || $socialLi || $waDigits): ?>
                    <div class="footer-social">
                        <?php if ($socialIg): ?><a href="<?= htmlspecialchars($socialIg) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a><?php endif; ?>
                        <?php if ($socialFb): ?><a href="<?= htmlspecialchars($socialFb) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a><?php endif; ?>
                        <?php if ($socialYt): ?><a href="<?= htmlspecialchars($socialYt) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a><?php endif; ?>
                        <?php if ($socialLi): ?><a href="<?= htmlspecialchars($socialLi) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a><?php endif; ?>
                        <?php if ($waDigits): ?><a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <h6 class="footer-heading">Penerbit</h6>
                    <ul class="footer-links">
                        <li><a href="<?= $siteUrl ?>/tentang">Tentang Kami</a></li>
                        <li><a href="<?= $siteUrl ?>/publikasi">Katalog Publikasi</a></li>
                        <li><a href="<?= $siteUrl ?>/proses">Alur Penerbitan</a></li>
                        <li><a href="<?= $siteUrl ?>/portofolio">Kerja Sama Institusi</a></li>
                        <li><a href="<?= $siteUrl ?>/wawasan">Wawasan</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 col-6">
                    <h6 class="footer-heading">Untuk Penulis</h6>
                    <ul class="footer-links">
                        <li><a href="<?= $siteUrl ?>/kirim-naskah">Kirim Naskah</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan">Layanan Penerbitan</a></li>
                        <li><a href="<?= $siteUrl ?>/biaya">Biaya Penerbitan</a></li>
                        <li><a href="<?= $siteUrl ?>/proses#ketentuan">Ketentuan Naskah</a></li>
                        <li><a href="<?= $siteUrl ?>/lacak">Lacak Naskah</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h6 class="footer-heading">Kontak Redaksi</h6>
                    <ul class="footer-contact">
                        <?php if (!empty($address)): ?>
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            <span><?= nl2br(htmlspecialchars($address)) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($emailContact)): ?>
                        <li>
                            <i class="fa-regular fa-envelope"></i>
                            <a href="mailto:<?= htmlspecialchars($emailContact) ?>"><?= htmlspecialchars($emailContact) ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($whatsapp)): ?>
                        <li>
                            <i class="fa-brands fa-whatsapp"></i>
                            <a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($whatsapp) ?></a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <i class="fa-regular fa-clock"></i>
                            <span>Senin–Jumat, 08.00–17.00 WIB</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="inner">
                <p class="mb-0"><?= htmlspecialchars(html_entity_decode($footerText, ENT_QUOTES, 'UTF-8')) ?></p>
                <div class="footer-legal-links">
                    <a href="<?= $siteUrl ?>/kebijakan-privasi">Kebijakan Privasi</a>
                    <a href="<?= $siteUrl ?>/kebijakan-refund">Kebijakan Pembatalan</a>
                    <a href="<?= $siteUrl ?>/kirim-naskah">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="to-top" aria-label="Kembali ke atas"><i class="fa-solid fa-arrow-up"></i></a>

<?php if (!empty($waDigits)): ?>
<a href="https://wa.me/<?= htmlspecialchars($waDigits) ?>?text=<?= rawurlencode('Halo ' . $siteName . ', saya ingin bertanya tentang penerbitan naskah.') ?>"
   class="wa-float" target="_blank" rel="noopener" aria-label="Hubungi kami via WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $siteUrl ?>/assets/js/main.js"></script>
</body>
</html>
