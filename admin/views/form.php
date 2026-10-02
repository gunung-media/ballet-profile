<?php
/** @var array $module @var array $values @var array $errors @var string $action @var bool $isEdit */
$isSettings = $module['type'] === 'settings';
$backUrl = $isSettings ? 'index.php' : 'index.php?m=' . $module['key'];
?>

<?php if ($isSettings): ?>
  <p class="mb-6 max-w-xl text-[14px] text-charcoal/60"><?= e($module['description']) ?></p>
<?php else: ?>
  <a href="<?= e($backUrl) ?>" class="mb-6 inline-flex items-center gap-1 text-[13px] text-charcoal/55 hover:text-rose-deep">&larr; Kembali ke daftar <?= e(mb_strtolower($module['title'])) ?></a>
<?php endif; ?>

<?php if ($errors): ?>
  <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[14px] text-red-800" role="alert">
    Periksa kembali isian yang ditandai merah.
  </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="space-y-6" novalidate>
  <?= csrf_field() ?>

  <?php foreach (group_fields($module['fields']) as $group => $fields): ?>
    <fieldset class="rounded-xl border border-charcoal/10 bg-white p-5 sm:p-7">
      <legend class="sr-only"><?= e($group) ?></legend>
      <h2 class="mb-5 font-serif text-xl"><?= e($group) ?></h2>
      <div class="grid gap-5 sm:grid-cols-2">
        <?php foreach ($fields as $name => $f):
          if ($f['type'] === 'password' && $isEdit) {
              $f['required'] = false;
              $f['label'] = 'Password baru';
              $f['help'] = 'Kosongkan jika tidak ingin mengganti password. Minimal 8 karakter.';
          } ?>
          <?= render_field($name, $f, $values[$name] ?? '', $errors[$name] ?? null) ?>
        <?php endforeach; ?>
      </div>
    </fieldset>
  <?php endforeach; ?>

  <!-- Tombol simpan: menempel di bawah layar pada HP -->
  <div class="fixed inset-x-0 bottom-0 z-10 border-t border-champagne/20 bg-ivory/95 px-4 py-3 backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 lg:pl-0">
    <div class="flex gap-3 sm:justify-end">
      <?php if (!$isSettings): ?>
        <a href="<?= e($backUrl) ?>" class="flex-1 rounded-full border border-charcoal/20 py-3 text-center text-[12px] uppercase tracking-[.15em] transition hover:border-charcoal/40 sm:flex-none sm:px-6">Batal</a>
      <?php endif; ?>
      <button class="flex-1 rounded-full bg-rose-deep py-3 text-[12px] uppercase tracking-[.15em] text-ivory transition hover:bg-charcoal sm:flex-none sm:px-8">Simpan</button>
    </div>
  </div>
</form>
