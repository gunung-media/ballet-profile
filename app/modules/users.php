<?php
return [
    'title'       => 'Admin',
    'singular'    => 'Akun Admin',
    'description' => 'Akun yang bisa masuk ke panel admin. Kosongkan password saat edit jika tidak ingin menggantinya.',
    'icon'        => 'M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1M10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM20 19v-1a4 4 0 0 0-3-3.9M15 4.1a3 3 0 0 1 0 5.8',
    'menu_order'  => 90,
    'table'       => 'users',
    'order'       => 'name ASC',
    'list'        => ['name' => 'Nama', 'username' => 'Username'],

    'fields' => [
        'name'     => ['label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 80],
        'username' => ['label' => 'Username', 'type' => 'text', 'required' => true, 'max' => 40, 'unique' => true,
                       'pattern' => '/^[a-z0-9._]{3,40}$/', 'pattern_message' => 'Username 3–40 karakter: huruf kecil, angka, titik, atau garis bawah.'],
        'password' => ['label' => 'Password', 'type' => 'password', 'required' => true, 'help' => 'Minimal 8 karakter.'],
    ],

    /** Kembalikan pesan error untuk membatalkan penghapusan. */
    'guard_delete' => function (array $row): ?string {
        if ((int) $row['id'] === (int) auth_user()['id']) {
            return 'Anda tidak dapat menghapus akun yang sedang dipakai.';
        }
        if (db_count('users') <= 1) {
            return 'Harus ada minimal satu akun admin.';
        }
        return null;
    },
];
