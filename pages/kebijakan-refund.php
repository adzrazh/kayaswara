<?php
$pageTitle       = 'Kebijakan Refund – ' . htmlspecialchars(getSetting('site_name', 'Kayaswara'));
$metaDesc = 'Kebijakan pengembalian dana dan pembayaran layanan Kayaswara Publishing.';
?>

<section class="page-hero" style="padding:60px 0 40px;background:linear-gradient(135deg,var(--primary) 0%,#2d6a4f 100%);">
    <div class="container text-center text-white">
        <h1 style="font-size:2rem;font-weight:800;margin-bottom:8px;">Kebijakan Refund & Pembayaran</h1>
        <p style="opacity:.8;font-size:15px;">Terakhir diperbarui: <?= date('d F Y') ?></p>
    </div>
</section>

<section style="padding:60px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 2px 16px rgba(0,0,0,0.06);">

                    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:16px 20px;margin-bottom:28px;">
                        <p style="margin:0;color:#92400e;font-size:14px;font-weight:600;">
                            <i class="fas fa-info-circle me-2"></i>
                            Kami berkomitmen memberikan layanan berkualitas. Harap baca kebijakan ini sebelum melakukan pembayaran.
                        </p>
                    </div>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin-bottom:12px;">1. Ketentuan Pembayaran</h2>
                    <ul style="color:#64748b;line-height:2.2;padding-left:20px;">
                        <li><strong>Uang Muka (DP) minimal 50%</strong> dari total invoice sebelum pengerjaan dimulai</li>
                        <li>Pelunasan 50% sisanya dilakukan sebelum file, akses, atau hasil pekerjaan diserahkan</li>
                        <li>Pembayaran dapat dilakukan melalui transfer bank atau QRIS</li>
                        <li>Invoice berlaku maksimal <strong>3 hari kerja</strong> sejak diterbitkan</li>
                    </ul>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">2. Kebijakan Refund</h2>

                    <h3 style="font-size:1rem;font-weight:700;color:#374151;margin:16px 0 8px;">✅ Refund Penuh (100%)</h3>
                    <p style="color:#64748b;line-height:1.8;">Refund penuh diberikan jika:</p>
                    <ul style="color:#64748b;line-height:2;padding-left:20px;">
                        <li>Kami belum memulai pengerjaan sama sekali</li>
                        <li>Pembatalan dilakukan dalam 24 jam setelah DP diterima</li>
                    </ul>

                    <h3 style="font-size:1rem;font-weight:700;color:#374151;margin:16px 0 8px;">⚠️ Refund Sebagian</h3>
                    <p style="color:#64748b;line-height:1.8;">Refund sebagian diberikan berdasarkan progres pekerjaan:</p>
                    <ul style="color:#64748b;line-height:2;padding-left:20px;">
                        <li>Progres 0–30%: Refund 70% dari DP yang dibayarkan</li>
                        <li>Progres 31–60%: Refund 40% dari DP yang dibayarkan</li>
                        <li>Progres >60%: Tidak ada refund (pekerjaan sudah berjalan signifikan)</li>
                    </ul>

                    <h3 style="font-size:1rem;font-weight:700;color:#374151;margin:16px 0 8px;">❌ Tidak Ada Refund</h3>
                    <ul style="color:#64748b;line-height:2;padding-left:20px;">
                        <li>Pekerjaan sudah selesai dan diserahkan</li>
                        <li>Pembatalan karena keterlambatan informasi/akses dari pihak klien</li>
                        <li>Klien mengubah spesifikasi pekerjaan di tengah jalan</li>
                    </ul>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">3. Proses Pengajuan Refund</h2>
                    <ol style="color:#64748b;line-height:2.2;padding-left:20px;">
                        <li>Hubungi kami via email atau WhatsApp dengan menyertakan kode tracking pesanan</li>
                        <li>Jelaskan alasan pembatalan/refund</li>
                        <li>Kami akan merespons dalam 1×24 jam kerja</li>
                        <li>Jika disetujui, dana dikembalikan dalam 3–7 hari kerja ke rekening asal</li>
                    </ol>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">4. Pajak (PPh 23)</h2>
                    <p style="color:#64748b;line-height:1.8;">
                        Untuk klien berbadan usaha (perusahaan/instansi), berlaku pemotongan PPh 23 sebesar <strong>2%</strong> dari nilai bruto jasa sesuai ketentuan perpajakan Indonesia.
                        Bukti potong PPh 23 wajib diserahkan kepada kami paling lambat 30 hari setelah pembayaran.
                    </p>

                    <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary);margin:24px 0 12px;">5. Garansi Pekerjaan</h2>
                    <p style="color:#64748b;line-height:1.8;">Kami memberikan garansi revisi <strong>7 hari</strong> setelah pekerjaan diserahkan untuk perbaikan bug atau ketidaksesuaian dengan spesifikasi awal. Garansi tidak mencakup penambahan fitur atau perubahan spesifikasi baru.</p>

                    <div style="margin-top:32px;padding:20px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;">
                        <p style="margin:0;color:#374151;font-size:14px;">
                            <strong>Ada pertanyaan atau ingin mengajukan refund?</strong><br>
                            Email: <a href="mailto:<?= htmlspecialchars(getSetting('contact_email', 'kayaswara.jurnal@gmail.com')) ?>" style="color:var(--primary);"><?= htmlspecialchars(getSetting('contact_email', 'kayaswara.jurnal@gmail.com')) ?></a>
                            &nbsp;|&nbsp; WhatsApp: <a href="https://wa.me/<?= preg_replace('/\D/', '', getSetting('whatsapp_number', '081398191394')) ?>" style="color:var(--primary);"><?= htmlspecialchars(getSetting('whatsapp_number', '081398191394')) ?></a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

