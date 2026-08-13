<?php
/**
 * Layanan Penerbitan — pekerjaan redaksi & produksi yang dikerjakan Kayaswara.
 */
$siteUrl   = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$siteName  = html_entity_decode(getSetting('site_name', 'Kayaswara'), ENT_QUOTES, 'UTF-8');
$pageTitle = 'Layanan Penerbitan — ' . $siteName;
$metaDesc  = 'Telaah naskah, penyuntingan, tata letak, desain sampul, produksi cetak, dan distribusi buku akademik oleh ' . $siteName . '.';

$services = [
    [
        'id'    => 'telaah',
        'icon'  => 'fa-magnifying-glass-chart',
        'title' => 'Telaah & Penilaian Naskah',
        'desc'  => 'Setiap naskah dibaca redaksi sebelum keputusan terbit diambil. Penilaian mencakup keaslian, kedalaman kajian, struktur, dan kelayakan bagi pembaca akademik.',
        'items' => ['Pemeriksaan keaslian dan kutipan', 'Penilaian struktur & kelengkapan bab', 'Catatan perbaikan tertulis untuk penulis', 'Keputusan: diterima, revisi, atau belum layak'],
    ],
    [
        'id'    => 'penyuntingan',
        'icon'  => 'fa-pen-fancy',
        'title' => 'Penyuntingan Naskah',
        'desc'  => 'Penyuntingan dikerjakan berjenjang: substansi lebih dahulu, kemudian bahasa dan konsistensi. Seluruh perubahan dikembalikan kepada penulis untuk disetujui.',
        'items' => ['Penyuntingan substansi & alur argumen', 'Penyuntingan bahasa, ejaan, dan istilah', 'Konsistensi sitasi dan daftar pustaka', 'Dua putaran revisi bersama penulis'],
    ],
    [
        'id'    => 'tataletak',
        'icon'  => 'fa-table-columns',
        'title' => 'Tata Letak & Pracetak',
        'desc'  => 'Naskah ditata sesuai standar buku akademik: ukuran, margin, hierarki judul, tabel, gambar, catatan kaki, indeks, hingga berkas siap cetak.',
        'items' => ['Tata letak isi dan penomoran', 'Penyusunan daftar isi, tabel, dan gambar', 'Pemeriksaan pracetak (proofread akhir)', 'Berkas siap cetak dan berkas digital'],
    ],
    [
        'id'    => 'sampul',
        'icon'  => 'fa-palette',
        'title' => 'Desain Sampul',
        'desc'  => 'Perancangan sampul yang mewakili isi buku dan tetap tampil pantas di rak akademik, dikerjakan bersama penulis hingga disepakati.',
        'items' => ['Alternatif rancangan sampul', 'Penyusunan teks punggung & sampul belakang', 'Penyesuaian atas masukan penulis', 'Berkas sampul resolusi cetak'],
    ],
    [
        'id'    => 'produksi',
        'icon'  => 'fa-print',
        'title' => 'Produksi & Cetak',
        'desc'  => 'Pencetakan buku sesuai spesifikasi yang disepakati, dengan contoh cetak (proof) untuk diperiksa sebelum produksi penuh berjalan.',
        'items' => ['Pemilihan kertas, jilid, dan laminasi', 'Contoh cetak sebelum produksi penuh', 'Cetak sesuai jumlah kesepakatan', 'Pengiriman ke alamat penulis atau institusi'],
    ],
    [
        'id'    => 'katalog',
        'icon'  => 'fa-boxes-stacked',
        'title' => 'Katalogisasi & Distribusi',
        'desc'  => 'Terbitan dicatat pada katalog penerbit lengkap dengan data bibliografinya, lalu disebarkan sesuai kanal yang disepakati bersama penulis.',
        'items' => ['Pencatatan pada katalog penerbit', 'Penyusunan data bibliografi terbitan', 'Distribusi daring dan luring sesuai kesepakatan', 'Laporan penyebaran berkala'],
    ],
    [
        'id'    => 'konversi',
        'icon'  => 'fa-arrows-turn-to-dots',
        'title' => 'Konversi Karya Tulis Ilmiah',
        'desc'  => 'Disertasi, tesis, atau laporan penelitian disusun ulang menjadi naskah buku: struktur, sudut pandang, dan gaya bahasa disesuaikan bagi pembaca yang lebih luas.',
        'items' => ['Penataan ulang kerangka bab', 'Penyesuaian gaya bahasa akademik populer', 'Penyuntingan lampiran dan data pendukung', 'Pendampingan penulis selama proses'],
    ],
    [
        'id'    => 'pendampingan',
        'icon'  => 'fa-comments',
        'title' => 'Pendampingan Penulisan',
        'desc'  => 'Bagi penulis yang naskahnya masih berbentuk kerangka, redaksi mendampingi penyusunan hingga naskah siap ditelaah.',
        'items' => ['Diskusi kerangka dan ruang lingkup', 'Target penulisan bertahap', 'Umpan balik per bab', 'Persiapan naskah sebelum telaah'],
    ],
];
?>

<div class="page-head">
    <div class="container">
        <ol class="crumbs">
            <li><a href="<?= $siteUrl ?>/">Beranda</a></li>
            <li>Layanan</li>
        </ol>
        <span class="eyebrow">Pekerjaan Redaksi</span>
        <h1>Layanan Penerbitan</h1>
        <p>Rangkaian pekerjaan yang kami kerjakan untuk mengantar sebuah naskah menjadi buku yang siap dibaca, dirujuk, dan disimpan di rak perpustakaan.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Ruang Lingkup</span>
            <h2>Dari telaah naskah sampai buku tersedia</h2>
            <p>Layanan dapat diambil sebagai satu rangkaian penuh, atau sebagian sesuai kondisi naskah Anda.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $i => $s): ?>
            <div class="col-md-6 col-lg-4 reveal" data-reveal-delay="<?= ($i % 3) * 0.07 ?>" id="<?= $s['id'] ?>">
                <article class="service-card">
                    <div class="service-icon"><i class="fa-solid <?= $s['icon'] ?>"></i></div>
                    <h3><?= $s['title'] ?></h3>
                    <p><?= $s['desc'] ?></p>
                    <ul class="service-list">
                        <?php foreach ($s['items'] as $it): ?>
                        <li><i class="fa-solid fa-check"></i><span><?= $it ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Yang perlu penulis ketahui -->
<section class="section paper-grain">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal">
                <span class="eyebrow">Ketentuan Kerja Sama</span>
                <h2>Hal-hal yang kami sepakati di awal</h2>
                <div class="rule-accent"></div>
                <p class="lead">
                    Agar tidak ada salah paham di tengah jalan, seluruh lingkup pekerjaan dituangkan tertulis
                    sebelum naskah masuk tahap produksi.
                </p>
                <ul class="check-list">
                    <li><i class="fa-solid fa-check"></i><span>Lingkup pekerjaan, jadwal, dan biaya tercantum dalam satu dokumen penawaran.</span></li>
                    <li><i class="fa-solid fa-check"></i><span>Hak cipta isi buku tetap berada pada penulis.</span></li>
                    <li><i class="fa-solid fa-check"></i><span>Penulis menjamin naskah adalah karya asli dan bebas dari sengketa hak cipta.</span></li>
                    <li><i class="fa-solid fa-check"></i><span>Perubahan besar di luar lingkup awal disepakati ulang secara tertulis.</span></li>
                </ul>
            </div>
            <div class="col-lg-6 reveal" data-reveal-delay="0.1">
                <div class="side-panel">
                    <h4>Yang tidak kami janjikan</h4>
                    <p style="font-size:.94rem;">
                        Kejujuran di awal lebih berguna daripada janji yang tak bisa ditepati. Karena itu ada
                        beberapa hal yang berada di luar kendali penerbit:
                    </p>
                    <ul class="check-list">
                        <li><i class="fa-solid fa-xmark" style="color:var(--danger);"></i><span>Kepastian bahwa setiap naskah akan lolos telaah redaksi.</span></li>
                        <li><i class="fa-solid fa-xmark" style="color:var(--danger);"></i><span>Keputusan lembaga eksternal atas terbitan, karena itu wewenang lembaga terkait.</span></li>
                        <li><i class="fa-solid fa-xmark" style="color:var(--danger);"></i><span>Angka penjualan atau jumlah sitasi tertentu.</span></li>
                    </ul>
                    <a href="<?= $siteUrl ?>/proses" class="btn btn-outline-primary w-100 mt-2">
                        <i class="fa-solid fa-diagram-project"></i>Lihat Alur & Ketentuan Naskah
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Untuk institusi -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="eyebrow eyebrow-center">Untuk Institusi</span>
            <h2>Kerja sama program studi &amp; lembaga penelitian</h2>
            <p>Program penerbitan terjadwal untuk beberapa judul sekaligus, dengan satu penanggung jawab dari pihak institusi.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 reveal">
                <div class="card-plain">
                    <div class="service-icon"><i class="fa-solid fa-people-group"></i></div>
                    <h3 class="h5">Penerbitan Kolektif</h3>
                    <p class="mb-0" style="font-size:.93rem;">Beberapa naskah dari satu program studi dikerjakan dalam satu gelombang dengan jadwal bersama.</p>
                </div>
            </div>
            <div class="col-md-4 reveal" data-reveal-delay="0.07">
                <div class="card-plain">
                    <div class="service-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <h3 class="h5">Lokakarya Penulisan</h3>
                    <p class="mb-0" style="font-size:.93rem;">Pendampingan penulisan buku ajar bagi dosen, dari penyusunan kerangka sampai naskah siap ditelaah.</p>
                </div>
            </div>
            <div class="col-md-4 reveal" data-reveal-delay="0.14">
                <div class="card-plain">
                    <div class="service-icon"><i class="fa-solid fa-file-contract"></i></div>
                    <h3 class="h5">Perjanjian Kerangka</h3>
                    <p class="mb-0" style="font-size:.93rem;">Satu perjanjian payung untuk kebutuhan penerbitan institusi dalam satu tahun akademik.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 reveal">
            <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-primary">
                <i class="fa-regular fa-envelope"></i>Ajukan Kerja Sama Institusi
            </a>
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>Belum yakin layanan mana yang Anda perlukan?</h2>
                    <p>Kirimkan naskah atau kerangkanya. Redaksi akan menyampaikan rekomendasi lingkup pekerjaan beserta perkiraan biayanya.</p>
                </div>
                <div class="col-lg-4">
                    <div class="cta-actions justify-content-lg-end">
                        <a href="<?= $siteUrl ?>/biaya" class="btn btn-outline-light btn-lg"><i class="fa-solid fa-receipt"></i>Lihat Biaya</a>
                        <a href="<?= $siteUrl ?>/kirim-naskah" class="btn btn-accent btn-lg"><i class="fa-regular fa-paper-plane"></i>Kirim Naskah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
