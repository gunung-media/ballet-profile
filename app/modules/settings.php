<?php
/**
 * Pengaturan umum (bukan tabel per baris, melainkan key-value di tabel "settings").
 * Nilai 'default' dipakai sampai admin menyimpan nilai baru.
 */
return [
    'type'        => 'settings',
    'title'       => 'Pengaturan',
    'description' => 'Kontak, teks utama, dan SEO website.',
    'icon'        => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z',
    'menu_order'  => 80,

    'fields' => [
        /* ----- Kontak ----- */
        'whatsapp' => ['group' => 'Kontak', 'label' => 'Nomor WhatsApp', 'type' => 'text', 'required' => true, 'width' => 'half',
            'default' => '6281234567890', 'inputmode' => 'numeric', 'max' => 15,
            'pattern' => '/^62\d{8,13}$/', 'pattern_message' => 'Gunakan format 628xxxxxxxxxx (tanpa +, spasi, atau 0 di depan).',
            'help' => 'Format 628xxxxxxxxxx.'],
        'instagram' => ['group' => 'Kontak', 'label' => 'Username Instagram', 'type' => 'text', 'required' => true, 'width' => 'half',
            'default' => 'angeliqueballet', 'max' => 30,
            'pattern' => '/^[A-Za-z0-9._]{1,30}$/', 'pattern_message' => 'Tulis username saja, tanpa @ atau link.',
            'help' => 'Tanpa @.'],
        'wa_trial_message' => ['group' => 'Kontak', 'label' => 'Pesan WhatsApp — tombol Trial Class', 'type' => 'text', 'max' => 200,
            'default' => 'Halo Angelique Ballet, saya ingin mendaftar Trial Class.'],
        'wa_default_message' => ['group' => 'Kontak', 'label' => 'Pesan WhatsApp — tombol melayang', 'type' => 'text', 'max' => 200,
            'default' => 'Halo Angelique Ballet, saya ingin bertanya tentang kelas balet.'],

        /* ----- Hero ----- */
        'hero_eyebrow' => ['group' => 'Hero (bagian paling atas)', 'label' => 'Teks kecil di atas judul', 'type' => 'text', 'max' => 80,
            'default' => 'Studio Balet Klasik · Yogyakarta'],
        'hero_title' => ['group' => 'Hero (bagian paling atas)', 'label' => 'Judul utama', 'type' => 'textarea', 'rows' => 2, 'max' => 120, 'required' => true,
            'default' => "Keanggunan yang\n*tumbuh* dari setiap langkah",
            'help' => 'Apit kata dengan *bintang* untuk huruf miring berwarna rose. Enter = baris baru.'],
        'hero_subtitle' => ['group' => 'Hero (bagian paling atas)', 'label' => 'Kalimat pendukung', 'type' => 'textarea', 'rows' => 3, 'max' => 250,
            'default' => 'Kelas balet klasik untuk anak, remaja, dan dewasa — diajarkan dengan disiplin, kehangatan, dan cinta pada seni.'],
        'hero_image' => ['group' => 'Hero (bagian paling atas)', 'label' => 'Foto utama (portrait 3:4)', 'type' => 'image',
            'default' => 'assets/images/placeholder-portrait.svg'],

        /* ----- Tentang ----- */
        'about_title' => ['group' => 'Tentang Kami', 'label' => 'Judul', 'type' => 'text', 'max' => 120,
            'default' => 'Disiplin yang melahirkan *keindahan*', 'help' => 'Apit kata dengan *bintang* untuk huruf miring.'],
        'about_body' => ['group' => 'Tentang Kami', 'label' => 'Isi', 'type' => 'textarea', 'rows' => 8, 'max' => 3000,
            'help' => 'Pisahkan paragraf dengan satu baris kosong.',
            'default' => "Angelique Ballet percaya bahwa balet klasik bukan sekadar tarian, melainkan pendidikan karakter. Di setiap plié dan port de bras, murid belajar kesabaran, fokus, dan rasa hormat — pada guru, teman, dan diri sendiri.\n\nKami mengajarkan teknik balet klasik secara bertahap dan terstruktur, dalam suasana yang hangat dan aman. Tujuan kami sederhana: setiap murid pulang dengan postur yang lebih baik, kepercayaan diri yang tumbuh, dan cinta yang mendalam pada seni tari."],
        'about_image' => ['group' => 'Tentang Kami', 'label' => 'Foto (portrait 4:5)', 'type' => 'image',
            'default' => 'assets/images/placeholder-portrait.svg'],
        'stat1_value' => ['group' => 'Tentang Kami', 'label' => 'Angka 1', 'type' => 'text', 'max' => 10, 'width' => 'half', 'default' => '10+'],
        'stat1_label' => ['group' => 'Tentang Kami', 'label' => 'Keterangan 1', 'type' => 'text', 'max' => 20, 'width' => 'half', 'default' => 'Tahun'],
        'stat2_value' => ['group' => 'Tentang Kami', 'label' => 'Angka 2', 'type' => 'text', 'max' => 10, 'width' => 'half', 'default' => '300+'],
        'stat2_label' => ['group' => 'Tentang Kami', 'label' => 'Keterangan 2', 'type' => 'text', 'max' => 20, 'width' => 'half', 'default' => 'Murid'],
        'stat3_value' => ['group' => 'Tentang Kami', 'label' => 'Angka 3', 'type' => 'text', 'max' => 10, 'width' => 'half', 'default' => '2'],
        'stat3_label' => ['group' => 'Tentang Kami', 'label' => 'Keterangan 3', 'type' => 'text', 'max' => 20, 'width' => 'half', 'default' => 'Cabang'],

        /* ----- CTA ----- */
        'cta_title' => ['group' => 'Ajakan (section Kontak)', 'label' => 'Judul', 'type' => 'text', 'max' => 120,
            'default' => 'Coba kelas pertama *bersama kami*'],
        'cta_body' => ['group' => 'Ajakan (section Kontak)', 'label' => 'Kalimat', 'type' => 'textarea', 'rows' => 3, 'max' => 300,
            'default' => 'Hubungi kami untuk jadwal Trial Class, informasi biaya, dan konsultasi kelas yang sesuai usia.'],

        /* ----- SEO ----- */
        'seo_title' => ['group' => 'SEO (Google & media sosial)', 'label' => 'Judul halaman', 'type' => 'text', 'max' => 70,
            'default' => 'Angelique Ballet — Studio Balet Klasik di Yogyakarta', 'help' => 'Ideal 50–60 karakter.'],
        'seo_description' => ['group' => 'SEO (Google & media sosial)', 'label' => 'Deskripsi', 'type' => 'textarea', 'rows' => 3, 'max' => 200,
            'default' => 'Angelique Ballet, studio balet klasik di Yogyakarta untuk anak, remaja, dan dewasa. Kelas Pre-Ballet, Junior, Teen, dan Adult Ballet di cabang Gejayan dan Kranggan. Daftar trial class sekarang.',
            'help' => 'Ideal 140–160 karakter.'],
    ],
];
