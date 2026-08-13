<?php
/**
 * Kayaswara — Data contoh (katalog publikasi & wawasan)
 *
 * Mengisi basis data dengan 5 judul katalog dan 5 tulisan wawasan supaya
 * tampilan situs dapat dinilai dengan data nyata, bukan contoh yang
 * ditanam di dalam template.
 *
 * Pemakaian (dari SSH pada direktori situs):
 *   php seed.php            → memasukkan data contoh (melewati yang sudah ada)
 *   php seed.php --remove   → menghapus kembali seluruh data contoh
 *
 * PENTING
 * - Data ini adalah CONTOH. Sebelum mengajukan situs ke Perpustakaan Nasional,
 *   jalankan `php seed.php --remove` lalu isi katalog dengan terbitan yang
 *   benar-benar sudah diterbitkan.
 * - Kolom ISBN sengaja dikosongkan. ISBN adalah nomor resmi; mengisinya dengan
 *   angka karangan justru menimbulkan masalah baru.
 * - Hapus berkas ini dari server setelah selesai dipakai.
 */

$isCli = PHP_SAPI === 'cli';
if (!$isCli && !in_array($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Akses ditolak. Jalankan script ini dari localhost atau CLI.');
}

if (!file_exists(__DIR__ . '/config.php')) {
    exit('config.php tidak ditemukan. Jalankan install.php terlebih dahulu.' . PHP_EOL);
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$remove = $isCli
    ? in_array('--remove', $argv ?? [], true)
    : isset($_GET['remove']);

// ──────────────────────────────────────────────────────────────
// DATA
// ──────────────────────────────────────────────────────────────
$publications = [
    [
        'title'        => 'Metodologi Penelitian Pendidikan',
        'subtitle'     => 'Pendekatan Kuantitatif, Kualitatif, dan Campuran',
        'authors'      => 'Dr. Rahmawati Nur Aisyah, M.Pd.',
        'editor'       => '',
        'category'     => 'buku_ajar',
        'subject'      => 'Pendidikan',
        'publish_year' => 2024,
        'pages'        => 286,
        'price'        => 135000,
        'is_featured'  => 1,
        'synopsis'     => "Buku ajar ini disusun untuk mata kuliah Metodologi Penelitian pada program sarjana dan magister kependidikan. Pembahasan dimulai dari cara merumuskan masalah penelitian yang layak diteliti, menyusun kerangka teori, sampai memilih rancangan yang sesuai dengan pertanyaan penelitian.\n\nTiga pendekatan dibahas berimbang: kuantitatif dengan penekanan pada validitas instrumen dan uji asumsi, kualitatif dengan penekanan pada keabsahan data dan pencatatan lapangan, serta pendekatan campuran beserta pertimbangan kapan ia benar-benar diperlukan.\n\nSetiap bab ditutup dengan contoh kasus dari penelitian pendidikan di Indonesia dan latihan yang dapat dikerjakan mahasiswa secara mandiri maupun berkelompok.",
    ],
    [
        'title'        => 'Manajemen Rantai Pasok Agroindustri',
        'subtitle'     => 'Dari Petani hingga Konsumen Akhir',
        'authors'      => 'Prof. Dr. Bambang Hermanto, S.P., M.Si.',
        'editor'       => '',
        'category'     => 'buku_referensi',
        'subject'      => 'Agribisnis',
        'publish_year' => 2024,
        'pages'        => 244,
        'price'        => 158000,
        'is_featured'  => 0,
        'synopsis'     => "Rantai pasok produk pertanian menghadapi persoalan yang tidak dijumpai pada industri lain: hasil panen mudah rusak, pasokan berfluktuasi mengikuti musim, dan pelakunya didominasi petani berskala kecil.\n\nBuku referensi ini menelaah persoalan tersebut secara berurutan — perencanaan pasokan, penanganan pascapanen, penyimpanan, distribusi, sampai penetapan harga di tingkat konsumen. Pembahasan disertai data lapangan dari sentra produksi hortikultura di Jawa dan Sumatera.\n\nBuku ini ditujukan bagi peneliti, mahasiswa pascasarjana, dan praktisi yang menangani perencanaan pasokan pada perusahaan agroindustri.",
    ],
    [
        'title'        => 'Ketahanan Struktur Beton pada Lingkungan Pesisir',
        'subtitle'     => '',
        'authors'      => 'Dr. Eng. Yusuf Maulana, S.T., M.T.',
        'editor'       => '',
        'category'     => 'monograf',
        'subject'      => 'Teknik Sipil',
        'publish_year' => 2025,
        'pages'        => 168,
        'price'        => 125000,
        'is_featured'  => 0,
        'synopsis'     => "Monograf ini melaporkan hasil penelitian lima tahun mengenai laju korosi tulangan beton pada bangunan yang berdiri kurang dari satu kilometer dari garis pantai.\n\nPengujian dilakukan terhadap benda uji yang dipapar langsung pada tiga lokasi pesisir dengan tingkat salinitas berbeda. Hasilnya dibandingkan dengan prediksi model difusi klorida yang lazim dipakai dalam perancangan.\n\nTemuan utama penelitian ini adalah perlunya penyesuaian tebal selimut beton pada perancangan bangunan pesisir, disertai usulan angka yang dapat langsung dipakai perencana.",
    ],
    [
        'title'        => 'Transformasi Digital Perguruan Tinggi',
        'subtitle'     => 'Bunga Rampai Pemikiran dan Praktik',
        'authors'      => 'Tim Penulis Konsorsium Pendidikan Tinggi',
        'editor'       => 'Dr. Siti Halimah, M.Kom.',
        'category'     => 'bunga_rampai',
        'subject'      => 'Manajemen Pendidikan',
        'publish_year' => 2025,
        'pages'        => 312,
        'price'        => 175000,
        'is_featured'  => 0,
        'synopsis'     => "Empat belas penulis dari sembilan perguruan tinggi menuliskan pengalaman masing-masing dalam menata ulang layanan akademik secara digital.\n\nTulisan dikelompokkan ke dalam empat bagian: tata kelola data akademik, pembelajaran daring dan bauran, layanan mahasiswa, serta kesiapan sumber daya manusia. Setiap tulisan memuat kendala nyata yang dihadapi, bukan hanya rencana yang berhasil.\n\nBunga rampai ini disunting agar dapat dibaca berurutan maupun per bagian, sesuai kebutuhan pembaca yang sedang menyusun peta jalan digitalisasi di kampusnya.",
    ],
    [
        'title'        => 'Prosiding Seminar Nasional Literasi Digital 2025',
        'subtitle'     => 'Membaca, Menulis, dan Menilai Informasi di Ruang Daring',
        'authors'      => 'Panitia Seminar Nasional Literasi Digital',
        'editor'       => 'Dr. Anwar Sanusi, M.Hum.',
        'category'     => 'prosiding',
        'subject'      => 'Ilmu Informasi',
        'publish_year' => 2025,
        'pages'        => 420,
        'price'        => 190000,
        'is_featured'  => 0,
        'synopsis'     => "Prosiding ini memuat 38 makalah yang dipresentasikan pada Seminar Nasional Literasi Digital 2025 dan telah melalui penelaahan oleh dewan penelaah seminar.\n\nMakalah dikelompokkan menjadi tiga tema: kemampuan menilai kesahihan informasi, literasi digital pada pendidikan dasar dan menengah, serta peran perpustakaan dalam pendampingan literasi.\n\nSeluruh makalah disunting untuk keseragaman format sitasi dan penulisan tabel, tanpa mengubah isi dan simpulan penulis.",
    ],
];

$posts = [
    [
        'title'    => 'Menyusun Kerangka Buku Ajar yang Runtut',
        'category' => 'Penulisan',
        'created'  => '-5 months',
        'excerpt'  => 'Kerangka yang disusun rapi sejak awal menghemat berminggu-minggu revisi. Ini cara redaksi kami menilai sebuah kerangka buku ajar sudah siap dikembangkan atau belum.',
        'content'  => "<p>Naskah buku ajar yang paling sering dikembalikan bukanlah naskah yang bahasanya buruk, melainkan naskah yang kerangkanya belum matang. Bahasa dapat disunting; struktur yang keliru menuntut penulisan ulang.</p>
<h2>Mulai dari capaian pembelajaran</h2>
<p>Buku ajar berbeda dari buku referensi karena terikat pada capaian pembelajaran mata kuliah. Sebelum menulis satu paragraf pun, tuliskan lebih dahulu: setelah membaca bab ini, mahasiswa mampu melakukan apa? Kalimat itulah yang menentukan isi bab, bukan sebaliknya.</p>
<h2>Satu bab, satu gagasan utama</h2>
<p>Bab yang memuat tiga gagasan besar sekaligus hampir selalu melelahkan untuk dibaca. Pecah menjadi beberapa bab yang lebih pendek. Buku dengan dua belas bab ringkas lebih mudah dipakai mengajar daripada enam bab yang padat.</p>
<h2>Susun urutan berdasarkan ketergantungan</h2>
<p>Periksa apakah setiap bab hanya memerlukan pengetahuan dari bab sebelumnya. Bila bab 3 menuntut pemahaman bab 7, urutannya perlu ditata ulang. Cara sederhana memeriksanya: tuliskan tiap bab pada satu kartu, lalu susun sampai tidak ada panah yang mundur.</p>
<h2>Perkirakan bobot halaman sejak awal</h2>
<p>Tetapkan perkiraan jumlah halaman per bab pada tahap kerangka. Bab yang direncanakan 15 halaman tetapi tumbuh menjadi 60 halaman biasanya menandakan ada dua bab yang tersamar di dalamnya.</p>
<h2>Yang kami periksa saat telaah</h2>
<ul>
<li>Kesesuaian isi bab dengan capaian pembelajaran yang dinyatakan.</li>
<li>Keseimbangan bobot antarbab.</li>
<li>Keberadaan latihan atau contoh penerapan pada tiap bab.</li>
<li>Kelengkapan rujukan mutakhir pada bidang yang dibahas.</li>
</ul>
<p>Kerangka yang lolos empat pemeriksaan itu biasanya berkembang menjadi naskah utuh tanpa perombakan besar.</p>",
    ],
    [
        'title'    => 'Mengubah Disertasi Menjadi Buku yang Enak Dibaca',
        'category' => 'Penulisan',
        'created'  => '-4 months',
        'excerpt'  => 'Disertasi ditulis untuk meyakinkan penguji; buku ditulis untuk pembaca yang tidak wajib menyelesaikannya. Perbedaan itu menuntut penataan ulang, bukan sekadar penggantian sampul.',
        'content'  => "<p>Banyak penulis mengira naskah disertasi tinggal dicetak ulang menjadi buku. Padahal keduanya ditulis untuk pembaca yang berbeda, dengan kewajiban membaca yang berbeda pula.</p>
<h2>Pindahkan simpulan ke depan</h2>
<p>Disertasi menahan temuan sampai bab akhir agar alur pembuktiannya utuh. Pembaca buku tidak sesabar itu. Sampaikan temuan pokok pada bagian awal, lalu gunakan bab berikutnya untuk menunjukkan bagaimana temuan itu diperoleh.</p>
<h2>Rampingkan bab metodologi</h2>
<p>Uraian metodologi sepanjang empat puluh halaman diperlukan di hadapan penguji, tidak di hadapan pembaca umum. Ringkas menjadi bab pendek yang menjelaskan cara kerja secukupnya, dan pindahkan rinciannya ke lampiran.</p>
<h2>Kurangi kutipan yang bersifat penegasan</h2>
<p>Kutipan pada disertasi sering berfungsi menunjukkan bahwa penulis sudah membaca banyak sumber. Pada buku, kutipan sebaiknya hanya muncul ketika benar-benar menopang argumen yang sedang dibangun.</p>
<h2>Ganti nada penulisan</h2>
<p>Kalimat pasif berlapis yang lazim pada disertasi membuat buku terasa berjarak. Ubah menjadi kalimat aktif bila memungkinkan, dan panggil pembaca secara wajar tanpa kehilangan ketelitian ilmiah.</p>
<h2>Perkirakan penyusutan</h2>
<p>Pengalaman kami, disertasi 300 halaman umumnya menjadi buku 180–220 halaman setelah ditata ulang. Penyusutan itu pertanda baik: yang hilang biasanya pengulangan, bukan substansi.</p>",
    ],
    [
        'title'    => 'Kesalahan Sitasi yang Paling Sering Kami Temukan',
        'category' => 'Penyuntingan',
        'created'  => '-3 months',
        'excerpt'  => 'Dari ratusan naskah yang melewati meja penyuntingan, kesalahan sitasi cenderung berulang pada pola yang sama. Lima di antaranya dapat Anda periksa sendiri sebelum mengirim naskah.',
        'content'  => "<p>Sitasi yang berantakan jarang menggagalkan kelayakan naskah, tetapi hampir selalu menambah satu putaran revisi. Berikut pola yang paling sering kami jumpai.</p>
<h2>1. Kutipan dalam teks tidak ada di daftar pustaka</h2>
<p>Ini kesalahan nomor satu, biasanya muncul karena naskah disunting berkali-kali dan sebagian rujukan terhapus. Periksa dengan mencocokkan satu per satu, atau gunakan pengelola rujukan sejak awal penulisan.</p>
<h2>2. Daftar pustaka memuat sumber yang tidak pernah dikutip</h2>
<p>Kebalikannya juga sering terjadi. Daftar pustaka bukan daftar bacaan penulis, melainkan daftar sumber yang benar-benar dirujuk di dalam teks.</p>
<h2>3. Gaya sitasi tercampur</h2>
<p>Satu naskah memakai APA pada bab awal dan gaya nomor pada bab akhir. Biasanya ini terjadi pada naskah yang ditulis beberapa orang. Sepakati satu gaya sejak awal dan sebutkan pada catatan penulis.</p>
<h2>4. Mengutip dari kutipan tanpa menyebutkan</h2>
<p>Bila Anda membaca pendapat A di dalam tulisan B, sumbernya adalah B — kecuali Anda memang membaca A secara langsung. Menyebutkan A saja membuat rantai rujukan menjadi tidak dapat ditelusuri.</p>
<h2>5. Rujukan tanpa data terbit yang lengkap</h2>
<p>Nama penulis dan judul tanpa tahun, penerbit, atau tautan yang stabil membuat pembaca kesulitan menelusuri sumber. Untuk sumber daring, cantumkan tanggal akses.</p>
<p>Memeriksa lima hal ini sebelum mengirim naskah biasanya memangkas satu putaran revisi penuh.</p>",
    ],
    [
        'title'    => 'Apa yang Dinilai Redaksi Saat Menelaah Naskah',
        'category' => 'Penerbitan',
        'created'  => '-2 months',
        'excerpt'  => 'Telaah naskah bukan proses tertutup. Ini kriteria yang kami pakai, supaya penulis tahu persis apa yang sedang dinilai dan mengapa sebuah naskah dikembalikan.',
        'content'  => "<p>Setiap naskah yang masuk dibaca lebih dahulu sebelum keputusan terbit diambil. Agar prosesnya tidak terasa seperti kotak hitam, berikut hal-hal yang kami nilai.</p>
<h2>Keaslian</h2>
<p>Naskah harus merupakan karya penulis sendiri dan belum pernah diterbitkan pihak lain. Materi milik pihak ketiga — gambar, tabel, kutipan panjang — perlu disertai izin penggunaan.</p>
<h2>Kedalaman kajian</h2>
<p>Kami menilai apakah pembahasan menambah sesuatu bagi pembacanya. Naskah yang hanya merangkum sumber lain tanpa sudut pandang penulis biasanya kami kembalikan untuk diperdalam.</p>
<h2>Struktur</h2>
<p>Urutan bab harus dapat diikuti tanpa melompat. Kami memeriksa apakah setiap bab berdiri di atas bab sebelumnya dan apakah bobot antarbab wajar.</p>
<h2>Kesesuaian dengan lini terbitan</h2>
<p>Naskah yang baik bisa saja tidak sesuai dengan lini terbitan kami. Dalam hal ini kami menyampaikannya secara terbuka agar penulis dapat menawarkannya ke penerbit yang lebih cocok.</p>
<h2>Kesiapan naskah</h2>
<p>Kelengkapan halaman judul, daftar isi, daftar pustaka, dan biodata penulis menentukan berapa lama tahap penyuntingan akan berjalan.</p>
<h2>Tiga kemungkinan hasil</h2>
<ul>
<li><strong>Diterima</strong> — naskah lanjut ke penyusunan penawaran dan perjanjian penerbitan.</li>
<li><strong>Diterima dengan revisi</strong> — naskah layak, disertai catatan yang perlu diperbaiki lebih dahulu.</li>
<li><strong>Belum layak terbit</strong> — disertai alasan dan saran perbaikan; penulis dipersilakan mengirim ulang.</li>
</ul>
<p>Ketiganya disampaikan tertulis. Tidak ada naskah yang ditolak tanpa penjelasan.</p>",
    ],
    [
        'title'    => 'Menyiapkan Tabel dan Gambar agar Layak Cetak',
        'category' => 'Produksi',
        'created'  => '-1 month',
        'excerpt'  => 'Gambar yang tampak tajam di layar sering pecah ketika dicetak. Beberapa penyesuaian sederhana pada tahap penulisan menghemat banyak waktu di tahap pracetak.',
        'content'  => "<p>Tahap pracetak paling sering tertunda bukan karena naskahnya, melainkan karena gambar dan tabel yang perlu dibuat ulang. Persiapan berikut mencegahnya.</p>
<h2>Perhatikan resolusi, bukan ukuran layar</h2>
<p>Gambar untuk cetak sebaiknya beresolusi minimal 300 dpi pada ukuran tampilnya di halaman. Tangkapan layar dari peramban umumnya hanya 72–96 dpi dan akan terlihat pecah ketika dicetak.</p>
<h2>Kirim berkas asli, bukan hasil tempel</h2>
<p>Grafik yang ditempel ke dalam dokumen pengolah kata kehilangan sebagian ketajamannya. Sertakan berkas aslinya secara terpisah — hasil ekspor dari perangkat lunak pengolah data, atau berkas vektor bila tersedia.</p>
<h2>Pertimbangkan cetak hitam putih</h2>
<p>Sebagian besar buku akademik dicetak satu warna. Grafik yang membedakan kelompok data hanya lewat warna akan kehilangan makna. Bedakan pula dengan pola, jenis garis, atau penanda titik.</p>
<h2>Tabel yang terlalu lebar</h2>
<p>Tabel dengan dua belas kolom tidak akan muat pada halaman buku berukuran 15,5 × 23 cm. Pecah menjadi beberapa tabel, putar orientasinya, atau pindahkan ke lampiran.</p>
<h2>Nomor, judul, dan sumber</h2>
<p>Setiap tabel dan gambar memerlukan nomor, judul, dan sumber bila dikutip dari karya lain. Rujuk juga di dalam teks — pembaca perlu tahu kapan harus melihatnya.</p>
<p>Naskah yang gambarnya sudah siap sejak awal umumnya melewati tahap pracetak satu hingga dua minggu lebih cepat.</p>",
    ],
];

// ──────────────────────────────────────────────────────────────
// EKSEKUSI
// ──────────────────────────────────────────────────────────────
$log = [];
$note = function (string $type, string $msg) use (&$log) {
    $log[] = ['type' => $type, 'msg' => $msg];
};

/** Slug tetap, supaya data contoh dapat dikenali dan dihapus kembali. */
$pubSlugs  = array_map(static fn($p) => slugify($p['title']), $publications);
$postSlugs = array_map(static fn($p) => slugify($p['title']), $posts);

if ($remove) {
    foreach ([['publications', $pubSlugs, 'Judul katalog'], ['blog_posts', $postSlugs, 'Tulisan wawasan']] as [$table, $slugs, $label]) {
        $deleted = 0;
        foreach ($slugs as $slug) {
            try {
                $deleted += delete($table, 'slug = ?', [$slug]);
            } catch (Throwable $e) {
                $note('error', $label . ' — gagal menghapus: ' . $e->getMessage());
            }
        }
        $note($deleted > 0 ? 'ok' : 'skip', $label . ' — ' . $deleted . ' data contoh dihapus.');
    }
} else {
    // Katalog publikasi
    foreach ($publications as $i => $p) {
        $slug = $pubSlugs[$i];
        try {
            if (fetch("SELECT id FROM publications WHERE slug = ?", [$slug])) {
                $note('skip', 'Katalog: "' . $p['title'] . '" sudah ada.');
                continue;
            }
            insert('publications', [
                'title'        => $p['title'],
                'subtitle'     => $p['subtitle'],
                'slug'         => $slug,
                'authors'      => $p['authors'],
                'editor'       => $p['editor'],
                'category'     => $p['category'],
                'subject'      => $p['subject'],
                'synopsis'     => $p['synopsis'],
                'cover'        => '',
                'isbn'         => '', // sengaja dikosongkan — lihat catatan di kepala berkas
                'publish_year' => $p['publish_year'],
                'edition'      => 'Cetakan Pertama',
                'pages'        => $p['pages'],
                'dimensions'   => '15,5 × 23 cm',
                'language'     => 'Indonesia',
                'price'        => $p['price'],
                'purchase_url' => '',
                'is_featured'  => $p['is_featured'],
                'status'       => 'published',
            ]);
            $note('ok', 'Katalog: "' . $p['title'] . '" ditambahkan.');
        } catch (Throwable $e) {
            $note('error', 'Katalog: "' . $p['title'] . '" gagal — ' . $e->getMessage());
        }
    }

    // Wawasan
    foreach ($posts as $i => $p) {
        $slug = $postSlugs[$i];
        try {
            if (fetch("SELECT id FROM blog_posts WHERE slug = ?", [$slug])) {
                $note('skip', 'Wawasan: "' . $p['title'] . '" sudah ada.');
                continue;
            }
            $created = date('Y-m-d H:i:s', strtotime($p['created'] . ' 09:00'));
            insert('blog_posts', [
                'title'      => $p['title'],
                'slug'       => $slug,
                'excerpt'    => $p['excerpt'],
                'content'    => $p['content'],
                'image'      => '',
                'author'     => 'Redaksi Kayaswara',
                'status'     => 'published',
                'category'   => $p['category'],
                'views'      => 0,
                'created_at' => $created,
                'updated_at' => $created,
            ]);
            $note('ok', 'Wawasan: "' . $p['title'] . '" ditambahkan.');
        } catch (Throwable $e) {
            $note('error', 'Wawasan: "' . $p['title'] . '" gagal — ' . $e->getMessage());
        }
    }
}

// ──────────────────────────────────────────────────────────────
// KELUARAN
// ──────────────────────────────────────────────────────────────
if ($isCli) {
    $mark = ['ok' => '  [ok]   ', 'skip' => '  [lewat]', 'error' => '  [GAGAL]'];
    echo PHP_EOL . ($remove ? 'Menghapus data contoh Kayaswara' : 'Mengisi data contoh Kayaswara') . PHP_EOL;
    echo str_repeat('-', 62) . PHP_EOL;
    foreach ($log as $l) {
        echo ($mark[$l['type']] ?? '  ') . ' ' . $l['msg'] . PHP_EOL;
    }
    echo str_repeat('-', 62) . PHP_EOL;
    if (!$remove) {
        echo 'Data contoh siap. Hapus kembali dengan: php seed.php --remove' . PHP_EOL;
    }
    echo 'Hapus seed.php dari server setelah selesai dipakai.' . PHP_EOL . PHP_EOL;
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Contoh — Kayaswara</title>
<style>
    body { font-family: system-ui, sans-serif; max-width: 760px; margin: 60px auto; padding: 0 20px; color:#41525F; background:#F7F9FB; }
    h1 { color:#1A3C5E; font-size:1.5rem; }
    ul { list-style:none; padding:0; }
    li { padding:.55rem .9rem; border:1px solid #DCE5ED; border-radius:8px; margin-bottom:.45rem; background:#fff; font-size:.93rem; }
    .ok { border-left:3px solid #2E6188; }
    .skip { border-left:3px solid #C0CCD8; color:#74838E; }
    .error { border-left:3px solid #B3392E; color:#8A2C23; font-weight:600; }
    .warn { background:#FAF2E0; border:1px solid #D8C79A; padding:14px 18px; border-radius:8px; margin-top:24px; font-size:.92rem; }
    code { background:#E2EAF1; padding:.1em .35em; border-radius:4px; }
</style>
</head>
<body>
<h1><?= $remove ? 'Data contoh dihapus' : 'Data contoh dimasukkan' ?></h1>
<ul>
<?php foreach ($log as $l): ?>
    <li class="<?= htmlspecialchars($l['type']) ?>"><?= htmlspecialchars($l['msg']) ?></li>
<?php endforeach; ?>
</ul>
<div class="warn">
    <strong>Sebelum mengajukan situs ke Perpustakaan Nasional:</strong> hapus data contoh ini
    dengan <code>php seed.php --remove</code> (atau <code>seed.php?remove=1</code>), lalu isi
    katalog dengan terbitan yang benar-benar sudah diterbitkan. Setelah itu hapus
    <code>seed.php</code> dari server.
</div>
</body>
</html>
