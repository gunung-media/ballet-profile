<?php
return [
    'title'       => 'Testimoni',
    'singular'    => 'Testimoni',
    'description' => 'Cerita orang tua & murid. Pastikan sudah mendapat izin untuk mencantumkan nama.',
    'icon'        => 'M7 8h10M7 12h6M5 19l2.5-3H19a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v13Z',
    'menu_order'  => 6,
    'table'       => 'testimonials',
    'order'       => 'sort_order ASC, id ASC',
    'list'        => ['name' => 'Nama', 'role' => 'Keterangan', 'quote' => 'Testimoni', 'is_active' => 'Status'],

    'fields' => [
        'name'       => ['label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 80, 'width' => 'half', 'placeholder' => 'mis. Ibu Rina'],
        'role'       => ['label' => 'Keterangan', 'type' => 'text', 'max' => 100, 'width' => 'half', 'placeholder' => 'mis. Orang tua murid Pre-Ballet'],
        'quote'      => ['label' => 'Isi testimoni', 'type' => 'textarea', 'required' => true, 'max' => 500, 'rows' => 5],
        'is_active'  => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'default' => 1, 'width' => 'half'],
        'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'default' => 0, 'width' => 'half'],
    ],

    // TODO: contoh — ganti dengan testimoni asli
    'seed' => [
        ['name' => 'Ibu Rina', 'role' => 'Orang tua murid Pre-Ballet', 'sort_order' => 1, 'is_active' => 1,
         'quote' => 'Anak saya yang tadinya pemalu sekarang berani tampil di panggung. Guru-gurunya sabar sekali dan selalu memberi perhatian pada setiap anak.'],
        ['name' => 'Bapak Andi', 'role' => 'Orang tua murid Junior', 'sort_order' => 2, 'is_active' => 1,
         'quote' => 'Postur putri saya membaik dan ia jadi lebih disiplin, bahkan di sekolah. Suasana studionya hangat tapi tetap serius belajar teknik.'],
        ['name' => 'Nadia', 'role' => 'Murid Adult Ballet', 'sort_order' => 3, 'is_active' => 1,
         'quote' => 'Saya mulai balet di usia 28 tanpa pengalaman sama sekali. Di sini saya tidak pernah merasa terlambat — justru jadi waktu paling tenang dalam seminggu.'],
    ],
];
