<?php
/** @var array $module @var array $rows */
$key = $module['key'];

/** Tampilan sel sesuai tipe field */
$cell = function (array $row, string $col) use ($module): string {
    $f = $module['fields'][$col] ?? ['type' => 'text'];
    $v = $row[$col] ?? '';
    return match ($f['type']) {
        'checkbox' => (int) $v
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[12px] text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Tampil</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-charcoal/5 px-2.5 py-0.5 text-[12px] text-charcoal/60"><span class="h-1.5 w-1.5 rounded-full bg-charcoal/30"></span>Disembunyikan</span>',
        'image'    => '<img src="' . e(asset_url($v, '../')) . '" alt="" loading="lazy" class="h-14 w-14 rounded-md object-cover sm:h-12 sm:w-16">',
        'select'   => e($f['options'][$v] ?? $v),
        default    => e(excerpt((string) $v, 70)),
    };
};
$imageCol = array_key_first(array_filter($module['list'], fn ($label, $col) => ($module['fields'][$col]['type'] ?? '') === 'image', ARRAY_FILTER_USE_BOTH));
$textCols = array_diff_key($module['list'], [$imageCol => true]);
$primaryCol = array_key_first($textCols);
?>

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <p class="max-w-xl text-[14px] text-charcoal/60"><?= e($module['description'] ?? '') ?></p>
  <a href="index.php?m=<?= e($key) ?>&a=create"
     class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-rose-deep px-5 py-2.5 text-[12px] uppercase tracking-[.15em] text-ivory transition hover:bg-charcoal">
    + Tambah <?= e($module['singular']) ?>
  </a>
</div>

<?php if (!$rows): ?>
  <div class="mt-8 rounded-xl border border-dashed border-charcoal/20 bg-white px-6 py-16 text-center">
    <p class="font-serif text-2xl">Belum ada data</p>
    <p class="mt-1 text-[14px] text-charcoal/60">Klik "Tambah <?= e($module['singular']) ?>" untuk memulai.</p>
  </div>
<?php else: ?>

  <!-- Mobile: kartu -->
  <ul class="mt-6 space-y-3 md:hidden">
    <?php foreach ($rows as $row): ?>
      <li class="rounded-xl border border-charcoal/10 bg-white p-4">
        <div class="flex gap-4">
          <?php if ($imageCol): ?><div class="shrink-0"><?= $cell($row, $imageCol) ?></div><?php endif; ?>
          <div class="min-w-0 flex-1 space-y-1.5">
            <p class="font-medium"><?= $cell($row, $primaryCol) ?></p>
            <?php foreach (array_slice($textCols, 1, null, true) as $col => $label): ?>
              <div class="text-[13px] text-charcoal/65"><?= $cell($row, $col) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="mt-4 flex gap-2 border-t border-charcoal/5 pt-3">
          <a href="index.php?m=<?= e($key) ?>&a=edit&id=<?= (int) $row['id'] ?>" class="flex-1 rounded-lg border border-charcoal/15 py-2 text-center text-[13px] transition hover:border-rose-deep hover:text-rose-deep">Edit</a>
          <form method="post" action="index.php?m=<?= e($key) ?>&a=delete&id=<?= (int) $row['id'] ?>" class="flex-1" data-confirm="Hapus <?= e($module['singular']) ?> ini? Tindakan ini tidak dapat dibatalkan.">
            <?= csrf_field() ?>
            <button class="w-full rounded-lg border border-red-200 py-2 text-[13px] text-red-700 transition hover:bg-red-50">Hapus</button>
          </form>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <!-- Desktop: tabel -->
  <div class="mt-6 hidden overflow-hidden rounded-xl border border-charcoal/10 bg-white md:block">
    <table class="w-full text-left text-[14px]">
      <thead class="border-b border-charcoal/10 bg-cream/50 text-[11px] uppercase tracking-[.15em] text-charcoal/55">
        <tr>
          <?php foreach ($module['list'] as $label): ?><th class="px-5 py-3 font-medium"><?= e($label) ?></th><?php endforeach; ?>
          <th class="px-5 py-3 text-right font-medium">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-charcoal/5">
        <?php foreach ($rows as $row): ?>
          <tr class="align-middle transition hover:bg-ivory">
            <?php foreach ($module['list'] as $col => $label): ?>
              <td class="px-5 py-3 <?= $col === $primaryCol ? 'font-medium' : 'text-charcoal/70' ?>"><?= $cell($row, $col) ?></td>
            <?php endforeach; ?>
            <td class="whitespace-nowrap px-5 py-3 text-right">
              <a href="index.php?m=<?= e($key) ?>&a=edit&id=<?= (int) $row['id'] ?>" class="rounded-md px-3 py-1.5 text-[13px] text-charcoal/70 transition hover:bg-blush/60 hover:text-rose-deep">Edit</a>
              <form method="post" action="index.php?m=<?= e($key) ?>&a=delete&id=<?= (int) $row['id'] ?>" class="inline" data-confirm="Hapus <?= e($module['singular']) ?> ini? Tindakan ini tidak dapat dibatalkan.">
                <?= csrf_field() ?>
                <button class="rounded-md px-3 py-1.5 text-[13px] text-red-700/80 transition hover:bg-red-50 hover:text-red-700">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
