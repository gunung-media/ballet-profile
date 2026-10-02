<?php
declare(strict_types=1);
require __DIR__ . '/app/requirements.php';
require __DIR__ . '/app/bootstrap.php';

if (!is_installed()) {
    http_response_code(503);
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Angelique Ballet</title><body style="font-family:Georgia,serif;background:#FBF7F1;color:#2F2A2B;display:grid;place-items:center;min-height:100vh;margin:0;text-align:center;padding:1rem">'
        . '<div><h1 style="font-weight:400">Angelique Ballet</h1><p>Website sedang disiapkan. Admin: buka <a href="admin/setup.php">admin/setup.php</a>.</p></div></body></html>';
    exit;
}

$s = settings();
$classes = active_rows('classes');
$features = active_rows('features');
$gallery = active_rows('gallery');
$branches = active_rows('branches');
$testimonials = active_rows('testimonials');

$trialMsg = $s['wa_trial_message'];
$siteUrl = 'https://angeliqueballet.com/';
$stats = array_filter([
    [$s['stat1_label'], $s['stat1_value']],
    [$s['stat2_label'], $s['stat2_value']],
    [$s['stat3_label'], $s['stat3_value']],
], fn ($st) => $st[0] !== '' && $st[1] !== '');

$nav = array_filter([
    'tentang'   => 'Tentang',
    'kelas'     => $classes ? 'Kelas' : null,
    'galeri'    => $gallery ? 'Galeri' : null,
    'lokasi'    => $branches ? 'Lokasi' : null,
    'testimoni' => $testimonials ? 'Testimoni' : null,
    'kontak'    => 'Kontak',
]);

$galleryLayout = ['tall' => 'row-span-2', 'wide' => 'md:col-span-2', 'normal' => ''];
$featureCols = ['', 'lg:grid-cols-1', 'lg:grid-cols-2', 'lg:grid-cols-3', 'lg:grid-cols-4'][min(count($features), 4)];

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'DanceSchool',
    'name'     => 'Angelique Ballet',
    'url'      => $siteUrl,
    'sameAs'   => ['https://instagram.com/' . $s['instagram']],
    'location' => array_map(fn ($b) => [
        '@type'   => 'Place',
        'name'    => $b['name'] . ' — ' . $b['label'],
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => $b['address'], 'addressCountry' => 'ID'],
    ], $branches),
];
?>
<!doctype html>
<html lang="id" class="no-js">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script>document.documentElement.classList.replace('no-js', 'js');</script>

  <!-- ===== SEO dasar (diatur dari Admin → Pengaturan) ===== -->
  <title><?= e($s['seo_title']) ?></title>
  <meta name="description" content="<?= e($s['seo_description']) ?>" />
  <meta name="theme-color" content="#FBF7F1" />
  <link rel="canonical" href="<?= e($siteUrl) ?>" />
  <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml" />

  <!-- ===== Open Graph ===== -->
  <meta property="og:type" content="website" />
  <meta property="og:locale" content="id_ID" />
  <meta property="og:site_name" content="Angelique Ballet" />
  <meta property="og:title" content="<?= e($s['seo_title']) ?>" />
  <meta property="og:description" content="<?= e($s['seo_description']) ?>" />
  <meta property="og:url" content="<?= e($siteUrl) ?>" />
  <!-- TODO: buat og-image.jpg 1200x630 (SVG tidak didukung WhatsApp/Facebook) -->
  <meta property="og:image" content="<?= e($siteUrl) ?>assets/images/og-image.jpg" />
  <meta name="twitter:card" content="summary_large_image" />

  <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>

  <!-- ===== Font: preconnect + satu request, display=swap ===== -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Inter:wght@300;400;500&display=swap" />

  <!-- ===== Tailwind (Play CDN) =====
       TODO (produksi): ganti dengan Tailwind CLI build agar CSS lebih kecil -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="assets/js/tailwind-config.js"></script>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body class="font-sans font-light text-[15px] leading-relaxed md:text-base"
      data-wa-number="<?= e($s['whatsapp']) ?>"
      data-wa-message="<?= e($s['wa_default_message']) ?>"
      data-ig-user="<?= e($s['instagram']) ?>">

  <!-- Ikon yang dipakai berulang -->
  <svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
    <symbol id="i-wa" viewBox="0 0 24 24"><path fill="currentColor" d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.47-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.2-.24-.59-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.07c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.08-.13-.27-.2-.57-.35M12.05 21.8h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.22-3.74.99 1-3.65-.24-.38a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.83 9.83 0 0 1 2.89 7c0 5.45-4.44 9.88-9.88 9.88m8.41-18.3A11.82 11.82 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.69 1.45c6.55 0 11.89-5.34 11.89-11.9a11.82 11.82 0 0 0-3.48-8.4Z"/></symbol>
    <symbol id="i-ig" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r=".6" fill="currentColor"/></g></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></g></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.4" d="M5 12h14M13 6l6 6-6 6"/></symbol>
  </svg>

  <!-- ================= HEADER ================= -->
  <header class="site-header fixed inset-x-0 top-0 z-40">
    <nav class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5 md:h-20 md:px-8" aria-label="Navigasi utama">
      <a href="#beranda" class="font-serif text-2xl tracking-wide text-charcoal">
        Angelique <span class="italic text-rose-deep">Ballet</span>
      </a>

      <ul class="hidden items-center gap-8 text-[13px] uppercase tracking-[.18em] text-charcoal/80 lg:flex">
        <?php foreach ($nav as $id => $label): ?>
          <li><a class="nav-link" href="#<?= $id ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>

      <a data-wa="<?= e($trialMsg) ?>" href="#kontak"
         class="hidden rounded-full border border-rose-deep/40 px-5 py-2 text-[12px] uppercase tracking-[.18em] text-rose-deep transition-colors duration-500 hover:bg-rose-deep hover:text-ivory lg:inline-block">
        Trial Class
      </a>

      <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu"
              class="-mr-2 p-2 text-charcoal lg:hidden" aria-label="Buka menu">
        <svg data-icon-open class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M4 8h16M4 16h16"/></svg>
        <svg data-icon-close class="hidden h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </nav>

    <!-- Menu mobile -->
    <div id="mobile-menu" data-menu class="mobile-menu fixed inset-x-0 top-16 bottom-0 overflow-y-auto bg-ivory lg:hidden">
      <ul class="flex flex-col items-center gap-7 pt-14 font-serif text-3xl text-charcoal">
        <?php foreach ($nav as $id => $label): ?>
          <li><a href="#<?= $id ?>"><?= e($id === 'tentang' ? 'Tentang Kami' : $label) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <div class="ornament mt-12"><span></span></div>
      <div class="mt-10 pb-10 text-center">
        <a data-wa="<?= e($trialMsg) ?>" href="#kontak"
           class="inline-block rounded-full bg-rose-deep px-8 py-3 text-[12px] uppercase tracking-[.2em] text-ivory">
          Daftar Trial Class
        </a>
      </div>
    </div>
  </header>

  <main>
    <!-- ================= 1. HERO ================= -->
    <section id="beranda" class="relative overflow-hidden pt-16 md:pt-20">
      <div class="mx-auto grid max-w-6xl items-center gap-12 px-5 pb-20 pt-10 md:grid-cols-2 md:gap-16 md:px-8 md:pb-28 md:pt-16">
        <div class="order-2 text-center md:order-1 md:text-left">
          <p class="eyebrow hero-rise" style="--delay:100ms"><?= e($s['hero_eyebrow']) ?></p>
          <h1 class="hero-rise mt-5 font-serif text-[2.75rem] font-normal leading-[1.05] text-charcoal sm:text-6xl lg:text-7xl" style="--delay:250ms">
            <?= emphasize($s['hero_title']) ?>
          </h1>
          <p class="hero-rise mx-auto mt-6 max-w-md text-charcoal/70 md:mx-0" style="--delay:400ms"><?= e($s['hero_subtitle']) ?></p>
          <div class="hero-rise mt-9 flex flex-col items-center gap-4 sm:flex-row sm:justify-center md:justify-start" style="--delay:550ms">
            <a data-wa="<?= e($trialMsg) ?>" href="#kontak"
               class="inline-flex items-center gap-3 rounded-full bg-rose-deep px-8 py-3.5 text-[12px] uppercase tracking-[.2em] text-ivory transition-colors duration-500 hover:bg-charcoal">
              <svg class="h-4 w-4"><use href="#i-wa" /></svg>
              Daftar Trial Class
            </a>
            <?php if ($classes): ?>
              <a href="#kelas" class="group inline-flex items-center gap-2 text-[12px] uppercase tracking-[.2em] text-charcoal/80">
                Lihat Kelas
                <svg class="h-4 w-4 transition-transform duration-500 group-hover:translate-x-1"><use href="#i-arrow" /></svg>
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="hero-rise order-1 mx-auto w-full max-w-sm md:order-2 md:max-w-none" style="--delay:200ms">
          <div class="frame relative z-0 ml-0 mr-4 md:ml-10">
            <img src="<?= e(asset_url($s['hero_image'])) ?>"
                 alt="Penari balet Angelique Ballet"
                 width="600" height="800" fetchpriority="high"
                 class="aspect-[3/4] w-full object-cover" />
          </div>
        </div>
      </div>
      <div class="pointer-events-none absolute bottom-0 left-1/2 hidden h-16 w-px bg-gradient-to-b from-transparent to-champagne md:block"></div>
    </section>

    <!-- ================= 2. TENTANG KAMI ================= -->
    <section id="tentang" class="bg-cream/60 py-24 md:py-32">
      <div class="mx-auto grid max-w-6xl items-center gap-14 px-5 md:grid-cols-12 md:px-8">
        <div class="reveal md:col-span-5">
          <div class="frame relative z-0 mr-4">
            <img src="<?= e(asset_url($s['about_image'])) ?>" alt="Suasana latihan di studio Angelique Ballet"
                 width="600" height="800" loading="lazy" decoding="async"
                 class="aspect-[4/5] w-full object-cover" />
          </div>
        </div>
        <div class="md:col-span-6 md:col-start-7">
          <p class="eyebrow reveal">Tentang Kami</p>
          <h2 class="reveal mt-4 font-serif text-4xl leading-tight md:text-5xl" style="--delay:100ms"><?= emphasize($s['about_title']) ?></h2>
          <div class="reveal mt-6 h-px w-16 bg-champagne" style="--delay:150ms"></div>
          <div class="reveal mt-6 space-y-4 text-charcoal/75" style="--delay:200ms">
            <?php foreach (paragraphs($s['about_body']) as $p): ?>
              <p><?= nl2br(e($p), false) ?></p>
            <?php endforeach; ?>
          </div>
          <?php if ($stats): ?>
            <dl class="reveal mt-10 grid grid-cols-<?= count($stats) ?> gap-4 border-t border-champagne/40 pt-8 text-center md:text-left" style="--delay:300ms">
              <?php foreach ($stats as [$label, $value]): ?>
                <div><dt class="text-[11px] uppercase tracking-[.2em] text-charcoal/60"><?= e($label) ?></dt><dd class="font-serif text-4xl text-rose-deep"><?= e($value) ?></dd></div>
              <?php endforeach; ?>
            </dl>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ================= 3. KELAS ================= -->
    <?php if ($classes): ?>
    <section id="kelas" class="py-24 md:py-32">
      <div class="mx-auto max-w-6xl px-5 md:px-8">
        <header class="mx-auto max-w-xl text-center">
          <p class="eyebrow reveal">Program Kelas</p>
          <h2 class="reveal mt-4 font-serif text-4xl md:text-5xl" style="--delay:100ms">Langkah pertama untuk <em class="text-rose-deep">setiap usia</em></h2>
          <div class="ornament reveal mt-6" style="--delay:150ms"><span></span></div>
        </header>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <?php foreach ($classes as $i => $c): ?>
            <article class="card reveal flex flex-col border border-champagne/30 <?= $c['is_highlight'] ? 'bg-blush/50' : 'bg-ivory' ?> p-8" style="--delay:<?= ($i % 4) * 120 ?>ms">
              <p class="font-serif text-5xl italic text-champagne"><?= roman($i + 1) ?></p>
              <h3 class="mt-4 font-serif text-2xl"><?= e($c['title']) ?></h3>
              <p class="mt-1 text-[12px] uppercase tracking-[.2em] text-rose-deep"><?= e($c['age_range']) ?></p>
              <p class="mt-5 flex-1 text-[14px] text-charcoal/70"><?= e($c['description']) ?></p>
              <a data-wa="<?= e("Halo, saya ingin tahu tentang kelas {$c['title']} ({$c['age_range']}).") ?>" href="#kontak"
                 class="mt-8 inline-flex items-center gap-2 text-[12px] uppercase tracking-[.2em] text-charcoal/80 hover:text-rose-deep">Tanya Kelas <svg class="h-4 w-4"><use href="#i-arrow" /></svg></a>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= 4. MENGAPA ANGELIQUE BALLET ================= -->
    <?php if ($features): ?>
    <section id="keunggulan" class="bg-charcoal py-24 text-ivory md:py-32">
      <div class="mx-auto max-w-6xl px-5 md:px-8">
        <header class="mx-auto max-w-xl text-center">
          <p class="eyebrow reveal !text-champagne">Mengapa Kami</p>
          <h2 class="reveal mt-4 font-serif text-4xl md:text-5xl" style="--delay:100ms">Mengapa <em class="text-blush">Angelique Ballet</em></h2>
        </header>

        <div class="mt-16 grid gap-px bg-champagne/20 sm:grid-cols-2 <?= $featureCols ?>">
          <?php foreach ($features as $i => $f): ?>
            <div class="reveal bg-charcoal p-8 lg:p-10" style="--delay:<?= ($i % 4) * 120 ?>ms">
              <p class="font-serif text-sm italic text-champagne"><?= sprintf('%02d', $i + 1) ?></p>
              <h3 class="mt-3 font-serif text-2xl"><?= e($f['title']) ?></h3>
              <p class="mt-4 text-[14px] text-ivory/65"><?= e($f['description']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= 5. GALERI ================= -->
    <?php if ($gallery): ?>
    <section id="galeri" class="py-24 md:py-32">
      <div class="mx-auto max-w-6xl px-5 md:px-8">
        <header class="flex flex-col items-center gap-4 text-center md:flex-row md:items-end md:justify-between md:text-left">
          <div>
            <p class="eyebrow reveal">Galeri</p>
            <h2 class="reveal mt-4 font-serif text-4xl md:text-5xl" style="--delay:100ms">Momen di <em class="text-rose-deep">studio & panggung</em></h2>
          </div>
          <a data-ig href="#" class="reveal inline-flex items-center gap-2 text-[12px] uppercase tracking-[.2em] text-charcoal/80 hover:text-rose-deep" style="--delay:150ms">
            <svg class="h-4 w-4"><use href="#i-ig" /></svg> Lihat lebih banyak
          </a>
        </header>

        <div class="mt-14 grid grid-flow-dense auto-rows-[160px] grid-cols-2 gap-3 sm:auto-rows-[220px] md:grid-cols-4 md:gap-4">
          <?php foreach ($gallery as $i => $g): ?>
            <figure class="gallery-item reveal overflow-hidden <?= $galleryLayout[$g['layout']] ?? '' ?>" style="--delay:<?= ($i % 4) * 80 ?>ms">
              <img src="<?= e(asset_url($g['image'])) ?>" alt="<?= e($g['caption']) ?>" loading="lazy" decoding="async" class="h-full w-full object-cover" />
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= 6. LOKASI ================= -->
    <?php if ($branches): ?>
    <section id="lokasi" class="bg-cream/60 py-24 md:py-32">
      <div class="mx-auto max-w-6xl px-5 md:px-8">
        <header class="mx-auto max-w-xl text-center">
          <p class="eyebrow reveal">Lokasi Studio</p>
          <h2 class="reveal mt-4 font-serif text-4xl md:text-5xl" style="--delay:100ms">
            <?= count($branches) === 2 ? 'Dua studio di' : 'Studio kami di' ?> <em class="text-rose-deep">Yogyakarta</em>
          </h2>
          <div class="ornament reveal mt-6" style="--delay:150ms"><span></span></div>
        </header>

        <div class="mt-16 grid gap-8 <?= count($branches) > 1 ? 'md:grid-cols-2' : 'mx-auto max-w-xl' ?>">
          <?php foreach ($branches as $i => $b):
            $mapQuery = $b['embed_query'] !== '' && $b['embed_query'] !== null ? $b['embed_query'] : $b['address']; ?>
            <article class="reveal flex flex-col border border-champagne/30 bg-ivory" style="--delay:<?= ($i % 2) * 150 ?>ms">
              <div class="aspect-[4/3] w-full overflow-hidden bg-blush/40">
                <iframe title="Peta <?= e($b['name']) ?>"
                        src="https://www.google.com/maps?q=<?= e(urlencode($mapQuery)) ?>&amp;output=embed"
                        class="h-full w-full border-0 grayscale-[30%]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
              </div>
              <div class="flex flex-1 flex-col p-8">
                <p class="eyebrow"><?= e($b['label']) ?></p>
                <h3 class="mt-2 font-serif text-2xl"><?= e($b['name']) ?></h3>
                <p class="mt-4 flex flex-1 gap-3 text-[14px] text-charcoal/70">
                  <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose"><use href="#i-pin" /></svg>
                  <span><?= e($b['address']) ?></span>
                </p>
                <a href="<?= e($b['maps_url']) ?>" target="_blank" rel="noopener"
                   class="mt-8 inline-flex items-center justify-center gap-2 self-start rounded-full border border-rose-deep/40 px-6 py-3 text-[12px] uppercase tracking-[.2em] text-rose-deep transition-colors duration-500 hover:bg-rose-deep hover:text-ivory">
                  Petunjuk Arah <svg class="h-4 w-4"><use href="#i-arrow" /></svg>
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= 7. TESTIMONI ================= -->
    <?php if ($testimonials): ?>
    <section id="testimoni" class="py-24 md:py-32">
      <div class="mx-auto max-w-6xl px-5 md:px-8">
        <header class="mx-auto max-w-xl text-center">
          <p class="eyebrow reveal">Testimoni</p>
          <h2 class="reveal mt-4 font-serif text-4xl md:text-5xl" style="--delay:100ms">Cerita dari <em class="text-rose-deep">keluarga kami</em></h2>
        </header>

        <div class="snap-row -mx-5 mt-14 flex snap-x snap-mandatory gap-5 overflow-x-auto px-5 pb-4 md:mx-0 md:grid md:grid-cols-3 md:gap-8 md:overflow-visible md:px-0">
          <?php foreach ($testimonials as $i => $t): ?>
            <figure class="reveal w-[85%] shrink-0 snap-center border-t border-champagne/60 pt-8 md:w-auto" style="--delay:<?= ($i % 3) * 120 ?>ms">
              <p class="font-serif text-6xl leading-none text-champagne">&ldquo;</p>
              <blockquote class="-mt-4 font-serif text-xl italic leading-snug text-charcoal/85"><?= e($t['quote']) ?></blockquote>
              <figcaption class="mt-6 text-[12px] uppercase tracking-[.2em] text-rose-deep">
                <?= e($t['name']) ?><?php if ($t['role']): ?> <span class="text-charcoal/50">· <?= e($t['role']) ?></span><?php endif; ?>
              </figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ================= 8. KONTAK / CTA ================= -->
    <section id="kontak" class="relative overflow-hidden bg-blush/60 py-24 md:py-32">
      <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full border border-champagne/40"></div>
      <div class="pointer-events-none absolute -bottom-32 -right-20 h-96 w-96 rounded-full border border-champagne/30"></div>

      <div class="relative mx-auto max-w-2xl px-5 text-center md:px-8">
        <p class="eyebrow reveal">Mulai Menari</p>
        <h2 class="reveal mt-4 font-serif text-4xl leading-tight md:text-6xl" style="--delay:100ms"><?= emphasize($s['cta_title']) ?></h2>
        <p class="reveal mx-auto mt-6 max-w-md text-charcoal/70" style="--delay:200ms"><?= e($s['cta_body']) ?></p>
        <div class="reveal mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row" style="--delay:300ms">
          <a data-wa="<?= e($trialMsg) ?>" href="#"
             class="inline-flex w-full items-center justify-center gap-3 rounded-full bg-rose-deep px-8 py-4 text-[12px] uppercase tracking-[.2em] text-ivory transition-colors duration-500 hover:bg-charcoal sm:w-auto">
            <svg class="h-4 w-4"><use href="#i-wa" /></svg> Chat WhatsApp
          </a>
          <a data-ig href="#"
             class="inline-flex w-full items-center justify-center gap-3 rounded-full border border-charcoal/30 px-8 py-4 text-[12px] uppercase tracking-[.2em] text-charcoal transition-colors duration-500 hover:border-rose-deep hover:text-rose-deep sm:w-auto">
            <svg class="h-4 w-4"><use href="#i-ig" /></svg> Instagram
          </a>
        </div>
      </div>
    </section>
  </main>

  <!-- ================= FOOTER ================= -->
  <footer class="bg-ivory py-14">
    <div class="mx-auto max-w-6xl px-5 md:px-8">
      <div class="flex flex-col items-center gap-8 text-center md:flex-row md:items-start md:justify-between md:text-left">
        <div>
          <p class="font-serif text-2xl">Angelique <span class="italic text-rose-deep">Ballet</span></p>
          <p class="mt-2 text-[13px] text-charcoal/60">Studio balet klasik · Yogyakarta</p>
        </div>
        <?php if ($branches): ?>
          <div class="grid gap-6 text-[13px] text-charcoal/70 sm:grid-cols-2 sm:gap-12">
            <?php foreach ($branches as $b): ?>
              <div>
                <p class="eyebrow mb-2"><?= e(preg_replace('/^Cabang\s+/i', '', $b['label'])) ?></p>
                <p class="max-w-[16rem]"><?= e(excerpt($b['address'], 70)) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="flex items-center gap-5 text-charcoal/70">
          <a data-wa href="#" aria-label="WhatsApp" class="hover:text-rose-deep"><svg class="h-5 w-5"><use href="#i-wa" /></svg></a>
          <a data-ig href="#" aria-label="Instagram" class="hover:text-rose-deep"><svg class="h-5 w-5"><use href="#i-ig" /></svg></a>
        </div>
      </div>
      <div class="mt-12 border-t border-champagne/30 pt-6 text-center text-[12px] tracking-wide text-charcoal/50">
        &copy; <?= date('Y') ?> Angelique Ballet · angeliqueballet.com
      </div>
    </div>
  </footer>

  <!-- ================= FLOATING WHATSAPP ================= -->
  <a data-wa href="#kontak"
     class="wa-float fixed bottom-5 right-5 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-rose-deep text-ivory md:bottom-8 md:right-8"
     aria-label="Chat via WhatsApp">
    <svg class="relative h-6 w-6"><use href="#i-wa" /></svg>
  </a>

  <script src="assets/js/main.js" defer></script>
</body>
</html>
