<?php
/*
 * Cek kebutuhan server SEBELUM kode utama dimuat.
 * Ditulis dengan sintaks PHP lama agar pesan ini tetap tampil di server dengan PHP usang.
 */
$abMissing = array();
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    $abMissing[] = 'Versi PHP minimal 8.1 (server ini: ' . PHP_VERSION . '). Ubah di cPanel &rarr; Select PHP Version / MultiPHP Manager.';
}
foreach (array('pdo_sqlite', 'mbstring', 'fileinfo') as $abExt) {
    if (!extension_loaded($abExt)) {
        $abMissing[] = 'Ekstensi PHP <b>' . $abExt . '</b> belum aktif. Centang di cPanel &rarr; Select PHP Version &rarr; Extensions.';
    }
}
if ($abMissing) {
    http_response_code(500);
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Server belum siap</title><body style="font-family:system-ui;max-width:640px;margin:3rem auto;padding:0 1rem;line-height:1.6">'
        . '<h1 style="font-weight:500">Server belum memenuhi kebutuhan</h1><ul><li>' . implode('</li><li>', $abMissing) . '</li></ul></body>';
    exit;
}
