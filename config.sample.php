<?php
/**
 * Contoh konfigurasi — Kayaswara
 *
 * Salin berkas ini menjadi config.php lalu sesuaikan nilainya,
 * atau biarkan install.php membuatnya secara otomatis.
 * config.php TIDAK boleh dimasukkan ke repositori.
 */

define('DB_HOST',    'localhost');
define('DB_NAME',    'kayaswara');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// URL situs tanpa garis miring di akhir
define('SITE_URL',   'http://localhost/kayaswara');

// Batas unggahan gambar (sampul buku, logo, gambar tulisan)
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
