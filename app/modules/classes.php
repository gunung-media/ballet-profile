<?php
return [
    'title'       => 'Kelas',
    'singular'    => 'Kelas',
    'description' => 'Program kelas yang tampil di section "Kelas". Nomor romawi (I, II, ...) mengikuti urutan.',
    'icon'        => 'M4 19V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13M4 19h16M9 8h6M9 12h6',
    'menu_order'  => 2,
    'table'       => 'classes',
    'order'       => 'sort_order ASC, id ASC',
    'list'        => ['title' => 'Nama Kelas', 'age_range' => 'Usia', 'is_active' => 'Status'],

    'fields' => [
        'title'        => ['label' => 'Nama kelas', 'type' => 'text', 'required' => true, 'max' => 80, 'width' => 'half', 'placeholder' => 'mis. Pre-Ballet'],
        'age_range'    => ['label' => 'Rentang usia', 'type' => 'text', 'required' => true, 'max' => 60, 'width' => 'half', 'placeholder' => 'mis. Usia 3 – 5 tahun'],
        'description'  => ['label' => 'Deskripsi singkat', 'type' => 'textarea', 'required' => true, 'max' => 400, 'rows' => 4],
        'is_highlight' => ['label' => 'Tonjolkan kartu (latar blush)', 'type' => 'checkbox', 'width' => 'half'],
        'is_active'    => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'default' => 1, 'width' => 'half'],
        'sort_order'   => ['label' => 'Urutan', 'type' => 'number', 'default' => 0, 'width' => 'half', 'help' => 'Angka kecil tampil lebih dulu.'],
    ],

    'seed' => [
        ['title' => 'Pre-Ballet', 'age_range' => 'Usia 3 – 5 tahun', 'sort_order' => 1, 'is_active' => 1, 'is_highlight' => 0,
         'description' => 'Mengenal musik, ritme, dan gerak dasar melalui permainan imajinatif. Melatih koordinasi, keseimbangan, dan keberanian tampil.'],
        ['title' => 'Junior', 'age_range' => 'Usia 6 – 9 tahun', 'sort_order' => 2, 'is_active' => 1, 'is_highlight' => 0,
         'description' => 'Fondasi teknik balet klasik: posisi kaki, barre dasar, dan postur yang benar. Disiplin dibangun dengan cara yang menyenangkan.'],
        ['title' => 'Teen', 'age_range' => 'Usia 10 – 16 tahun', 'sort_order' => 3, 'is_active' => 1, 'is_highlight' => 0,
         'description' => 'Teknik lanjutan, kekuatan, dan ekspresi artistik. Persiapan pertunjukan, ujian grade, serta pengenalan repertoar klasik.'],
        ['title' => 'Adult Ballet', 'age_range' => 'Usia 17 tahun ke atas', 'sort_order' => 4, 'is_active' => 1, 'is_highlight' => 1,
         'description' => 'Tidak ada kata terlambat. Kelas untuk pemula maupun yang ingin kembali menari — memperbaiki postur, kelenturan, dan menemukan ketenangan.'],
    ],
];
