<?php /** @var array $user @var array $stats */ ?>
<section class="rounded-2xl border border-champagne/30 bg-blush/50 p-6 sm:p-8">
  <p class="text-[11px] uppercase tracking-[.25em] text-rose-deep">Halo,</p>
  <h2 class="mt-1 font-serif text-3xl sm:text-4xl"><?= e($user['name']) ?></h2>
  <p class="mt-2 max-w-lg text-[14px] text-charcoal/70">Perubahan yang Anda simpan di sini langsung tampil di website. Pilih bagian yang ingin diperbarui.</p>
  <div class="mt-6 flex flex-wrap gap-3">
    <a href="index.php?m=gallery&a=create" class="rounded-full bg-rose-deep px-5 py-2.5 text-[12px] uppercase tracking-[.15em] text-ivory transition hover:bg-charcoal">+ Tambah Foto</a>
    <a href="index.php?m=settings" class="rounded-full border border-charcoal/20 px-5 py-2.5 text-[12px] uppercase tracking-[.15em] transition hover:border-rose-deep hover:text-rose-deep">Ubah Kontak</a>
  </div>
</section>

<div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-3">
  <?php foreach ($stats as $key => $s): $mod = module($key); ?>
    <a href="index.php?m=<?= e($key) ?>" class="group rounded-xl border border-charcoal/10 bg-white p-5 transition hover:border-champagne/60 hover:shadow-[0_14px_30px_-20px_rgba(142,94,102,.5)]">
      <div class="flex items-center justify-between text-rose">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="<?= e($mod['icon']) ?>"/></svg>
        <svg class="h-4 w-4 text-charcoal/30 transition group-hover:translate-x-0.5 group-hover:text-rose-deep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
      <p class="mt-4 font-serif text-4xl"><?= (int) $s['total'] ?></p>
      <p class="text-[13px] text-charcoal/70"><?= e($mod['title']) ?></p>
      <?php if ($s['active'] !== null && $s['active'] !== $s['total']): ?>
        <p class="mt-1 text-[12px] text-charcoal/45"><?= (int) $s['active'] ?> tampil di website</p>
      <?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>
