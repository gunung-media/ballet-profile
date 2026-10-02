<?php
/** @var string $title @var string $content @var string $active @var array $user */
$flashes = take_flashes();
$icon = fn (string $d) => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="' . e($d) . '"/></svg>';
$navItem = function (string $href, string $label, string $iconPath, bool $isActive) use ($icon) {
    $cls = $isActive ? 'bg-white/10 text-ivory' : 'text-ivory/65 hover:bg-white/5 hover:text-ivory';
    return '<a href="' . e($href) . '" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[14px] transition ' . $cls . '"'
        . ($isActive ? ' aria-current="page"' : '') . '>' . $icon($iconPath) . e($label) . '</a>';
};
?>
<!doctype html>
<html lang="id">
<head>
  <title><?= e($title) ?> · Admin Angelique Ballet</title>
  <?php require __DIR__ . '/head.php'; ?>
</head>
<body class="bg-ivory font-sans text-charcoal">

  <!-- Overlay menu mobile -->
  <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-charcoal/40 lg:hidden"></div>

  <!-- ===== Sidebar ===== -->
  <aside data-sidebar class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-charcoal transition-transform duration-300 lg:translate-x-0">
    <div class="flex h-16 items-center justify-between px-6">
      <a href="index.php" class="font-serif text-2xl text-ivory">Angelique <span class="italic text-blush">Ballet</span></a>
      <button type="button" data-sidebar-close class="-mr-2 p-2 text-ivory/70 lg:hidden" aria-label="Tutup menu">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Menu admin">
      <?= $navItem('index.php', 'Dashboard', 'M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-16v4h6V4h-6Z', $active === 'dashboard') ?>
      <p class="px-3 pb-1 pt-5 text-[11px] uppercase tracking-[.2em] text-champagne/80">Konten</p>
      <?php foreach (modules() as $key => $mod): if ($mod['type'] !== 'crud' || $key === 'users') continue; ?>
        <?= $navItem('index.php?m=' . $key, $mod['title'], $mod['icon'], $active === $key) ?>
      <?php endforeach; ?>
      <p class="px-3 pb-1 pt-5 text-[11px] uppercase tracking-[.2em] text-champagne/80">Sistem</p>
      <?= $navItem('index.php?m=settings', module('settings')['title'], module('settings')['icon'], $active === 'settings') ?>
      <?= $navItem('index.php?m=users', module('users')['title'], module('users')['icon'], $active === 'users') ?>
    </nav>

    <div class="border-t border-white/10 p-4">
      <div class="flex items-center gap-3 px-2">
        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-rose/30 font-serif text-lg text-blush"><?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
        <div class="min-w-0 flex-1">
          <p class="truncate text-[14px] text-ivory"><?= e($user['name']) ?></p>
          <p class="truncate text-[12px] text-ivory/50">@<?= e($user['username']) ?></p>
        </div>
      </div>
      <form method="post" action="logout.php" class="mt-3">
        <?= csrf_field() ?>
        <button class="w-full rounded-lg px-3 py-2 text-left text-[13px] text-ivory/60 transition hover:bg-white/5 hover:text-ivory">Keluar</button>
      </form>
    </div>
  </aside>

  <!-- ===== Konten ===== -->
  <div class="lg:pl-72">
    <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-champagne/20 bg-ivory/90 px-4 backdrop-blur sm:px-6 lg:px-10">
      <button type="button" data-sidebar-open class="-ml-1 p-2 lg:hidden" aria-label="Buka menu">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M4 8h16M4 16h16"/></svg>
      </button>
      <h1 class="truncate font-serif text-2xl"><?= e($title) ?></h1>
      <a href="../" target="_blank" rel="noopener"
         class="ml-auto inline-flex shrink-0 items-center gap-2 rounded-full border border-charcoal/15 px-3 py-1.5 text-[12px] text-charcoal/70 transition hover:border-rose-deep hover:text-rose-deep sm:px-4">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></svg>
        <span class="hidden sm:inline">Lihat Website</span>
      </a>
    </header>

    <main class="mx-auto max-w-5xl px-4 pb-28 pt-6 sm:px-6 lg:px-10 lg:pt-10">
      <?php foreach ($flashes as $f):
        $style = ['success' => 'border-emerald-200 bg-emerald-50 text-emerald-900', 'error' => 'border-red-200 bg-red-50 text-red-800'][$f['type']] ?? 'border-champagne/40 bg-cream text-charcoal/80'; ?>
        <div class="mb-6 flex items-start justify-between gap-4 rounded-lg border px-4 py-3 text-[14px] <?= $style ?>" role="status" data-dismissible>
          <span><?= e($f['message']) ?></span>
          <button type="button" class="opacity-60 hover:opacity-100" aria-label="Tutup" data-dismiss>&times;</button>
        </div>
      <?php endforeach; ?>

      <?= $content ?>
    </main>
  </div>

  <script src="../assets/js/admin.js" defer></script>
</body>
</html>
