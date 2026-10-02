<?php
/**
 * Setup awal & pembaruan database.
 * - Pertama kali: membuat tabel, mengisi konten awal, dan membuat akun admin pertama.
 * - Setelah ada admin: hanya memperbarui struktur tabel (mis. setelah menambah field di modul).
 */
declare(strict_types=1);
require dirname(__DIR__) . '/app/requirements.php';
require dirname(__DIR__) . '/app/bootstrap.php';

try {
    migrate();
    seed();
} catch (PDOException $ex) {
    abort(500, 'Gagal terhubung ke database. Periksa config/config.php. ' . (config('debug') ? $ex->getMessage() : ''));
}

$hasAdmin = db_count('users') > 0;
$errors = [];
$values = ['name' => '', 'username' => ''];

if (!$hasAdmin && is_post()) {
    csrf_verify();
    $fields = module('users')['fields'];
    [$data, $errors] = validate_fields($fields, null, 'users');
    if (!$errors) {
        $id = db_insert('users', $data + ['created_at' => now(), 'updated_at' => now()]);
        login_as($id);
        flash('success', 'Akun admin dibuat. Selamat datang!');
        redirect('index.php');
    }
    $values = array_merge($values, array_intersect_key($data, $values));
}

$pageTitle = 'Setup';
require __DIR__ . '/views/auth-layout.php';
?>
<?php if ($hasAdmin): ?>
    <h1 class="font-serif text-3xl">Database terbaru</h1>
    <p class="mt-3 text-[14px] text-charcoal/70">Struktur database sudah diperbarui. Setup awal sudah pernah dilakukan, jadi akun admin baru hanya bisa dibuat dari panel admin.</p>
    <a href="login.php" class="mt-8 block w-full rounded-full bg-rose-deep py-3.5 text-center text-[12px] uppercase tracking-[.2em] text-ivory transition hover:bg-charcoal">Ke Halaman Masuk</a>
<?php else: ?>
    <h1 class="font-serif text-3xl">Setup awal</h1>
    <p class="mt-1 text-[14px] text-charcoal/60">Database dan konten awal sudah siap. Buat akun admin pertama.</p>

    <form method="post" class="mt-8 grid gap-5" novalidate>
      <?= csrf_field() ?>
      <?php foreach (module('users')['fields'] as $name => $f): ?>
        <?= render_field($name, $f, $values[$name] ?? '', $errors[$name] ?? null) ?>
      <?php endforeach; ?>
      <button class="w-full rounded-full bg-rose-deep py-3.5 text-[12px] uppercase tracking-[.2em] text-ivory transition hover:bg-charcoal">Buat Akun Admin</button>
    </form>
<?php endif; ?>
<?php require __DIR__ . '/views/auth-layout-end.php'; ?>
