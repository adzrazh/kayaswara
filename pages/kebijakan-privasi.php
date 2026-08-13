<?php
$pageTitle       = 'Kebijakan Privasi – ' . htmlspecialchars(getSetting('site_name', 'Kayaswara'));
$metaDesc = 'Kebijakan privasi dan perlindungan data pengguna layanan Kayaswara Publishing.';
?>

<section class="page-hero" style="padding:60px 0 40px;background:linear-gradient(135deg,var(--primary) 0%,#2d6a4f 100%);">
    <div class="container text-center text-white">
        <h1 style="font-size:2rem;font-weight:800;margin-bottom:8px;">Kebijakan Privasi</h1>
        <p style="opacity:.8;font-size:15px;">Terakhir diperbarui: <?= date('d F Y') ?></p>
    </div>
</section>

<section style="padding:60px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 2px 16px rgba(0,0,0,0.06);">

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin-bottom:12px;">1. Informasi yang Kami Kumpulkan</h2>
                    <p style="color:#64748b;line-height:1.8;">Kami mengumpulkan informasi yang Anda berikan secara langsung, meliputi: nama lengkap, alamat email, nomor telepon/WhatsApp, nama institusi/universitas, dan detail kebutuhan penerbitan buku Anda. Informasi ini digunakan semata-mata untuk keperluan pelayanan dan komunikasi.</p>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">2. Penggunaan Informasi</h2>
                    <p style="color:#64748b;line-height:1.8;">Informasi Anda digunakan untuk:</p>
                    <ul style="color:#64748b;line-height:2;padding-left:20px;">
                        <li>Memproses dan mengelola pesanan layanan Anda</li>
                        <li>Mengirimkan notifikasi progress pekerjaan via email dan WhatsApp</li>
                        <li>Memberikan dukungan teknis dan konsultasi</li>
                        <li>Mengirimkan invoice dan informasi pembayaran</li>
                        <li>Meningkatkan kualitas layanan kami</li>
                    </ul>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">3. Perlindungan Data</h2>
                    <p style="color:#64748b;line-height:1.8;">Kami berkomitmen menjaga keamanan data Anda. Data disimpan di server dengan enkripsi dan tidak pernah dijual, disewakan, atau dibagikan kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh hukum.</p>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">4. Cookie</h2>
                    <p style="color:#64748b;line-height:1.8;">Website ini menggunakan cookie sesi untuk menjaga keamanan login dan preferensi pengguna. Cookie tidak digunakan untuk melacak aktivitas di luar website kami.</p>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">5. Hak Anda</h2>
                    <p style="color:#64748b;line-height:1.8;">Anda berhak untuk meminta akses, koreksi, atau penghapusan data pribadi Anda yang tersimpan di sistem kami. Hubungi kami melalui email untuk mengajukan permintaan tersebut.</p>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">6. Perubahan Kebijakan</h2>
                    <p style="color:#64748b;line-height:1.8;">Kami dapat memperbarui kebijakan privasi ini sewaktu-waktu. Perubahan signifikan akan diberitahukan melalui email atau notifikasi di website.</p>

                    <div style="margin-top:32px;padding:20px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;">
                        <p style="margin:0;color:#374151;font-size:14px;">
                            <strong>Pertanyaan tentang privasi?</strong><br>
                            Hubungi kami di <a href="mailto:<?= htmlspecialchars(getSetting('contact_email', 'kayaswara.jurnal@gmail.com')) ?>" style="color:var(--primary);"><?= htmlspecialchars(getSetting('contact_email', 'kayaswara.jurnal@gmail.com')) ?></a>
                            atau WhatsApp <a href="https://wa.me/<?= preg_replace('/\D/', '', getSetting('whatsapp_number', '081398191394')) ?>" style="color:var(--primary);"><?= htmlspecialchars(getSetting('whatsapp_number', '081398191394')) ?></a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

