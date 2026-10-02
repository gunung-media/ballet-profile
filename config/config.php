<?php
/**
 * Konfigurasi utama Angelique Ballet.
 * File ini TIDAK boleh bisa diakses dari browser (dilindungi .htaccess).
 */
return [
    'app_name' => 'Angelique Ballet',

    // Zona waktu untuk created_at / updated_at
    'timezone' => 'Asia/Jakarta',

    // Tampilkan detail error. WAJIB false saat website sudah online.
    'debug' => false,

    'db' => [
        // 'sqlite' (tanpa setup, cocok untuk mulai) atau 'mysql' (umum di hosting)
        'driver' => 'sqlite',

        'sqlite_path' => dirname(__DIR__) . '/storage/database.sqlite',

        // TODO: isi jika memakai MySQL / MariaDB
        'mysql' => [
            'host'     => 'localhost',
            'port'     => 3306,
            'database' => 'angelique_ballet',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8mb4',
        ],
    ],

    'upload' => [
        'dir'        => dirname(__DIR__) . '/assets/uploads',
        'url'        => 'assets/uploads',   // relatif terhadap root website
        'max_bytes'  => 5 * 1024 * 1024,    // 5 MB per foto
        'max_width'  => 1600,               // foto diperkecil otomatis & dikonversi ke WebP
        'quality'    => 82,
    ],

    'auth' => [
        'idle_timeout'   => 2 * 60 * 60,    // logout otomatis setelah 2 jam tidak aktif
        'max_attempts'   => 5,              // percobaan login gagal per IP...
        'lockout_window' => 15 * 60,        // ...dalam 15 menit
    ],
];
