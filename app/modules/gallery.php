<?php
return [
    'title'       => 'Galeri',
    'singular'    => 'Foto',
    'description' => 'Foto latihan & pertunjukan. Atur bentuk "Tinggi" / "Lebar" agar grid terlihat bervariasi.',
    'icon'        => 'M4 5h16v14H4zM4 15l4-4 4 4 3-3 5 5M15 9.5a1.5 1.5 0 1 0 0-.01',
    'menu_order'  => 4,
    'table'       => 'gallery',
    'order'       => 'sort_order ASC, id ASC',
    'list'        => ['image' => 'Foto', 'caption' => 'Keterangan', 'layout' => 'Bentuk', 'is_active' => 'Status'],

    'fields' => [
        'image'      => ['label' => 'Foto', 'type' => 'image', 'required' => true],
        'caption'    => ['label' => 'Keterangan foto', 'type' => 'text', 'required' => true, 'max' => 150,
                         'help' => 'Dipakai sebagai teks alternatif (penting untuk SEO & aksesibilitas).', 'placeholder' => 'mis. Murid Junior berlatih di barre'],
        'layout'     => ['label' => 'Bentuk di grid', 'type' => 'select', 'default' => 'normal', 'width' => 'half',
                         'options' => ['normal' => 'Normal', 'tall' => 'Tinggi (portrait)', 'wide' => 'Lebar (landscape, desktop)']],
        'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'default' => 0, 'width' => 'half'],
        'is_active'  => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'default' => 1],
    ],

    'seed' => [
        ['image' => 'assets/images/placeholder-portrait.svg',  'caption' => 'Murid Pre-Ballet berlatih di barre',     'layout' => 'tall',   'sort_order' => 1, 'is_active' => 1],
        ['image' => 'assets/images/placeholder-landscape.svg', 'caption' => 'Latihan kelas Junior',                   'layout' => 'normal', 'sort_order' => 2, 'is_active' => 1],
        ['image' => 'assets/images/placeholder-landscape.svg', 'caption' => 'Pertunjukan recital tahunan di panggung', 'layout' => 'wide',   'sort_order' => 3, 'is_active' => 1],
        ['image' => 'assets/images/placeholder-landscape.svg', 'caption' => 'Detail sepatu pointe',                   'layout' => 'normal', 'sort_order' => 4, 'is_active' => 1],
        ['image' => 'assets/images/placeholder-portrait.svg',  'caption' => 'Penari Teen dalam pose développé',       'layout' => 'tall',   'sort_order' => 5, 'is_active' => 1],
        ['image' => 'assets/images/placeholder-landscape.svg', 'caption' => 'Kelas Adult Ballet',                     'layout' => 'normal', 'sort_order' => 6, 'is_active' => 1],
        ['image' => 'assets/images/placeholder-landscape.svg', 'caption' => 'Murid dan pengajar setelah pertunjukan', 'layout' => 'normal', 'sort_order' => 7, 'is_active' => 1],
    ],
];
