<?php
/*
 * Pemeriksa kebutuhan server Angelique Ballet.
 * Upload ke public_html, buka https://angeliqueballet.com/cek.php
 * HAPUS file ini setelah selesai (berisi info server).
 * Sengaja ditulis dengan sintaks PHP lama agar tetap jalan di versi PHP apa pun.
 */
header('Content-Type: text/plain; charset=utf-8');

function baris($label, $ok, $info) {
    echo str_pad($ok ? '[OK]   ' : '[GAGAL]', 8) . str_pad($label, 26) . $info . "\n";
}

echo "PEMERIKSAAN SERVER ANGELIQUE BALLET\n";
echo "===================================\n\n";

baris('Versi PHP (min 8.1)', version_compare(PHP_VERSION, '8.1.0', '>='), PHP_VERSION);
foreach (array('pdo_sqlite', 'mbstring', 'gd', 'fileinfo', 'session', 'json') as $ext) {
    baris('Ekstensi ' . $ext, extension_loaded($ext), extension_loaded($ext) ? 'aktif' : 'TIDAK AKTIF');
}
baris('WebP (gd)', function_exists('imagewebp'), function_exists('imagewebp') ? 'didukung' : 'tidak (foto disimpan tanpa konversi)');

$dir = dirname(__FILE__);
foreach (array('storage', 'assets/uploads') as $d) {
    $p = $dir . '/' . $d;
    baris('Folder ' . $d, is_dir($p) && is_writable($p), !is_dir($p) ? 'TIDAK ADA' : (is_writable($p) ? 'bisa ditulis' : 'TIDAK BISA DITULIS (ubah izin ke 755/775)'));
}
foreach (array('index.php', '.htaccess', 'app/bootstrap.php', 'config/config.php') as $f) {
    baris('File ' . $f, is_file($dir . '/' . $f), is_file($dir . '/' . $f) ? 'ada' : 'TIDAK ADA');
}

echo "\nupload_max_filesize = " . ini_get('upload_max_filesize');
echo "\npost_max_size       = " . ini_get('post_max_size');
echo "\nServer              = " . (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '-');
echo "\nFolder website      = " . $dir . "\n";

$log = $dir . '/error_log';
if (is_file($log)) {
    echo "\nERROR TERAKHIR (public_html/error_log):\n";
    $lines = file($log);
    echo implode('', array_slice($lines, -8));
}
