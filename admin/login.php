<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/requirements.php';
require dirname(__DIR__) . '/app/bootstrap.php';

if (!is_installed()) {
    redirect('setup.php');
}
if (auth_user()) {
    redirect('index.php');
}

$error = null;
$username = '';

if (is_post()) {
    csrf_verify();
    $username = strtolower(trim((string) ($_POST['username'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (login_locked()) {
        $error = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . round(config('auth.lockout_window') / 60) . ' menit.';
    } elseif ($username === '' || $password === '') {
        $error = 'Isi username dan password.';
    } elseif (attempt_login($username, $password)) {
        redirect('index.php');
    } else {
        $error = 'Username atau password salah.';
    }
}

$flashes = take_flashes();
$pageTitle = 'Masuk';
require __DIR__ . '/views/auth-layout.php';
?>
    <h1 class="font-serif text-3xl">Selamat datang</h1>
    <p class="mt-1 text-[14px] text-charcoal/60">Masuk untuk mengelola website.</p>

    <?php foreach ($flashes as $f): ?>
      <p class="mt-6 rounded-lg bg-cream px-4 py-3 text-[13px] text-charcoal/80"><?= e($f['message']) ?></p>
    <?php endforeach; ?>
    <?php if ($error): ?>
      <p class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[13px] text-red-800" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" class="mt-8 space-y-5" novalidate>
      <?= csrf_field() ?>
      <div>
        <label for="username" class="mb-1.5 block text-[13px] font-medium text-charcoal/80">Username</label>
        <input id="username" name="username" value="<?= e($username) ?>" autocomplete="username" autocapitalize="none" required autofocus
               class="<?= INPUT_CLASS ?>">
      </div>
      <div>
        <label for="password" class="mb-1.5 block text-[13px] font-medium text-charcoal/80">Password</label>
        <div class="relative">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="<?= INPUT_CLASS ?> pr-16">
          <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 px-4 text-[12px] text-charcoal/50 hover:text-rose-deep">Lihat</button>
        </div>
      </div>
      <button class="w-full rounded-full bg-rose-deep py-3.5 text-[12px] uppercase tracking-[.2em] text-ivory transition hover:bg-charcoal">Masuk</button>
    </form>
<?php require __DIR__ . '/views/auth-layout-end.php'; ?>
