<?php
return [
    'title'       => 'Cabang',
    'singular'    => 'Cabang',
    'description' => 'Lokasi studio beserta peta & tombol "Petunjuk Arah".',
    'icon'        => 'M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21ZM12 12a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
    'menu_order'  => 5,
    'table'       => 'branches',
    'order'       => 'sort_order ASC, id ASC',
    'list'        => ['name' => 'Nama Studio', 'label' => 'Cabang', 'is_active' => 'Status'],

    'fields' => [
        'label'       => ['label' => 'Label cabang', 'type' => 'text', 'required' => true, 'max' => 60, 'width' => 'half', 'placeholder' => 'mis. Cabang Gejayan'],
        'name'        => ['label' => 'Nama studio', 'type' => 'text', 'required' => true, 'max' => 100, 'width' => 'half'],
        'address'     => ['label' => 'Alamat lengkap', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
        'maps_url'    => ['label' => 'Link Google Maps', 'type' => 'url', 'required' => true,
                          'help' => 'Untuk tombol "Petunjuk Arah". Salin dari Google Maps → Bagikan → Salin link.', 'placeholder' => 'https://maps.app.goo.gl/...'],
        'embed_query' => ['label' => 'Kata kunci peta (opsional)', 'type' => 'text', 'max' => 255,
                          'help' => 'Teks untuk mencari titik di peta. Kosongkan untuk memakai alamat. Bisa juga koordinat, mis. -7.7701,110.3895.'],
        'is_active'   => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'default' => 1, 'width' => 'half'],
        'sort_order'  => ['label' => 'Urutan', 'type' => 'number', 'default' => 0, 'width' => 'half'],
    ],

    'seed' => [
        ['label' => 'Cabang Gejayan', 'name' => 'Angelique Ballet', 'sort_order' => 1, 'is_active' => 1,
         'address' => 'Gg. Kantil Pelem Kecut No.9 CTX, Karang Gayam, Caturtunggal, Kec. Depok, Kab. Sleman, DIY 55281',
         'maps_url' => 'https://maps.app.goo.gl/TeZXYRcwqxsW3nx77',
         'embed_query' => 'Gg. Kantil Pelem Kecut No.9, Karang Gayam, Caturtunggal, Depok, Sleman, Yogyakarta 55281'],
        ['label' => 'Cabang Kranggan', 'name' => 'AB Studio by Angelique Ballet', 'sort_order' => 2, 'is_active' => 1,
         'address' => 'Jl. Kranggan No.74, Cokrodiningratan, Kec. Jetis, Kota Yogyakarta, DIY 55233',
         'maps_url' => 'https://maps.app.goo.gl/pPmynuPLdQqvJNXT6',
         'embed_query' => 'Jl. Kranggan No.74, Cokrodiningratan, Jetis, Kota Yogyakarta 55233'],
    ],
];
