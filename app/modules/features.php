<?php
return [
    'title'       => 'Keunggulan',
    'singular'    => 'Keunggulan',
    'description' => 'Poin-poin di section "Mengapa Angelique Ballet". Idealnya 3–4 poin.',
    'icon'        => 'M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.4 6.8 19.1l1-5.8L3.5 9.2l5.9-.9L12 3Z',
    'menu_order'  => 3,
    'table'       => 'features',
    'order'       => 'sort_order ASC, id ASC',
    'list'        => ['title' => 'Judul', 'description' => 'Deskripsi', 'is_active' => 'Status'],

    'fields' => [
        'title'       => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'max' => 80],
        'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
        'is_active'   => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'default' => 1, 'width' => 'half'],
        'sort_order'  => ['label' => 'Urutan', 'type' => 'number', 'default' => 0, 'width' => 'half'],
    ],

    'seed' => [
        ['title' => 'Kurikulum Klasik Terstruktur', 'sort_order' => 1, 'is_active' => 1,
         'description' => 'Materi bertingkat sesuai usia dan kemampuan, mengacu pada metode balet klasik internasional.'],
        ['title' => 'Pengajar Berpengalaman', 'sort_order' => 2, 'is_active' => 1,
         'description' => 'Guru yang terlatih secara profesional, sabar, dan memahami tahap tumbuh kembang anak.'],
        ['title' => 'Kelas Kecil & Personal', 'sort_order' => 3, 'is_active' => 1,
         'description' => 'Jumlah murid per kelas dibatasi agar setiap penari mendapat koreksi dan perhatian.'],
        ['title' => 'Panggung untuk Bersinar', 'sort_order' => 4, 'is_active' => 1,
         'description' => 'Recital tahunan dan kesempatan tampil yang membangun rasa percaya diri murid.'],
    ],
];
