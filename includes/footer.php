<?php
/**
 * Global site footer.
 */
$siteUrl        = defined('SITE_URL') ? SITE_URL : '';
$siteName       = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$siteTagline    = html_entity_decode(getSetting('site_tagline', 'Jasa Penerbitan & Pencetakan Buku Akademik Profesional'), ENT_QUOTES, 'UTF-8');
$logoPath       = getSetting('logo_path', '');
$footerText     = getSetting('footer_text', '&copy; ' . date('Y') . ' Kayaswara. All rights reserved.');
$whatsappNumber = getSetting('whatsapp_number', '');
$emailContact   = getSetting('email_contact', '');
$address        = getSetting('address', '');
?>
</main><!-- /mainContent -->

<!-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ -->
<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3 d-flex align-items-center gap-2">
                        <?php if (!empty($logoPath)): ?>
                            <img src="<?= $siteUrl ?>/assets/uploads/site/<?= htmlspecialchars($logoPath) ?>"
                                 alt="<?= htmlspecialchars($siteName) ?> logo"
                                 style="max-height:40px; width:auto; max-width:100px; object-fit:contain; flex-shrink:0;">
                        <?php else: ?>
                            <svg width="34" height="34" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
                                <rect width="38" height="38" rx="9" fill="rgba(255,255,255,0.1)"/>
                                <path d="M10 28V12l9-4 9 4v16H22v-6h-6v6z" fill="rgba(255,255,255,0.9)"/>
                                <rect x="15" y="14" width="8" height="2" rx="1" fill="#0B7A6E"/>
                            </svg>
                        <?php endif; ?>
                        <div class="d-flex flex-column gap-1">
                            <span class="fw-700 lh-1"><?= htmlspecialchars($siteName) ?></span>
                            <span style="font-size:0.7rem; opacity:0.65; font-weight:400; line-height:1.2;"><?= htmlspecialchars($siteTagline) ?></span>
                        </div>
                    </div>
                    <p class="footer-desc">
                        Mitra terpercaya dosen, peneliti, dan akademisi Indonesia dalam penerbitan buku ajar, buku referensi, monograf, dan karya ilmiah lainnya secara profesional.
                    </p>
                    <!-- Social Media -->
                    <div class="footer-social mt-3">
                        <a href="#" class="footer-social-link" aria-label="Instagram" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Facebook" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="YouTube" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="LinkedIn" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php if (!empty($whatsappNumber)): ?>
                        <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', $whatsappNumber)) ?>"
                           class="footer-social-link" aria-label="WhatsApp" title="WhatsApp" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <h6 class="footer-heading">Navigasi</h6>
                    <ul class="footer-links">
                        <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan">Layanan</a></li>
                        <li><a href="<?= $siteUrl ?>/portofolio">Portofolio</a></li>
                        <li><a href="<?= $siteUrl ?>/blog">Blog</a></li>
                        <li><a href="<?= $siteUrl ?>/harga">Harga</a></li>
                        <li><a href="<?= $siteUrl ?>/tentang">Tentang Kami</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <h6 class="footer-heading">Layanan Kami</h6>
                    <ul class="footer-links">
                        <li><a href="<?= $siteUrl ?>/layanan#setup">Penerbitan Buku</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan#konversi">Konversi KTI ke Buku</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan#editing">Editing & Layout</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan#desain">Desain Cover Buku</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan#isbn">ISBN & Distribusi</a></li>
                        <li><a href="<?= $siteUrl ?>/layanan#konsultasi-pendampingan">Konsultasi & Pendampingan</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading">Hubungi Kami</h6>
                    <ul class="footer-contact-list">
                        <?php if (!empty($emailContact)): ?>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= htmlspecialchars($emailContact) ?>"><?= htmlspecialchars($emailContact) ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($whatsappNumber)): ?>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', $whatsappNumber)) ?>"
                               target="_blank" rel="noopener"><?= htmlspecialchars($whatsappNumber) ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($address)): ?>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= nl2br(htmlspecialchars($address)) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if (empty($emailContact) && empty($whatsappNumber) && empty($address)): ?>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>kayaswara.jurnal@gmail.com</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Senin – Jumat, 08.00 – 17.00 WIB</span>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <!-- CTA -->
                    <a href="<?= $siteUrl ?>/konsultasi" class="btn btn-accent btn-sm mt-3">
                        <i class="fas fa-comments me-1"></i> Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                <p class="mb-0 small"><?= htmlspecialchars($footerText) ?></p>
                <p class="mb-0 small opacity-75" style="display:flex;gap:16px;flex-wrap:wrap;">
                    <a href="<?= $siteUrl ?>/kebijakan-privasi" style="color:inherit;opacity:.8;text-decoration:none;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.8">Kebijakan Privasi</a>
                    <a href="<?= $siteUrl ?>/kebijakan-refund" style="color:inherit;opacity:.8;text-decoration:none;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.8">Kebijakan Refund</a>
                    <a href="<?= $siteUrl ?>/konsultasi" style="color:inherit;opacity:.8;text-decoration:none;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.8">Hubungi Kami</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<?php
$_waNum  = !empty($whatsappNumber) ? preg_replace('/\D/', '', $whatsappNumber) : '';
$_waHref = !empty($_waNum)
    ? 'https://wa.me/' . htmlspecialchars($_waNum) . '?text=Halo%2C%20saya%20ingin%20konsultasi%20tentang%20penerbitan%20buku'
    : (defined('SITE_URL') ? SITE_URL : '') . '/konsultasi';
?>
<a href="<?= $_waHref ?>"
   class="wa-float" target="_blank" rel="noopener" aria-label="Chat WhatsApp" title="Chat via WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="wa-float-tooltip">Chat WhatsApp</span>
</a>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= $siteUrl ?>/assets/js/main.js"></script>

</body>
</html>
