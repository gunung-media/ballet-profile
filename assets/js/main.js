/* ==========================================================
   Angelique Ballet — main.js (vanilla, tanpa dependensi)
   ========================================================== */

/* ---------- KONFIGURASI KONTAK ----------
   Diambil dari atribut <body> yang diisi server dari Admin → Pengaturan. */
const CONFIG = {
  whatsapp: document.body.dataset.waNumber || '',
  instagram: document.body.dataset.igUser || '',
  defaultMessage: document.body.dataset.waMessage || 'Halo Angelique Ballet, saya ingin bertanya tentang kelas balet.',
};

const waLink = (message = CONFIG.defaultMessage) =>
  `https://wa.me/${CONFIG.whatsapp}?text=${encodeURIComponent(message)}`;

/* Semua elemen <a data-wa="pesan"> otomatis diarahkan ke WhatsApp.
   Pesan bisa diatur per tombol lewat atribut data-wa. */
document.querySelectorAll('[data-wa]').forEach((el) => {
  el.href = waLink(el.dataset.wa || undefined);
  el.target = '_blank';
  el.rel = 'noopener';
});

document.querySelectorAll('[data-ig]').forEach((el) => {
  el.href = `https://instagram.com/${CONFIG.instagram}`;
  el.target = '_blank';
  el.rel = 'noopener';
  if (el.hasAttribute('data-ig-label')) el.textContent = `@${CONFIG.instagram}`;
});

/* ---------- Header: latar muncul saat discroll ---------- */
const header = document.querySelector('.site-header');
const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 24);
onScroll();
window.addEventListener('scroll', onScroll, { passive: true });

/* ---------- Menu mobile ---------- */
const toggle = document.querySelector('[data-menu-toggle]');
const menu = document.querySelector('[data-menu]');

const setMenu = (open) => {
  menu.classList.toggle('is-open', open);
  toggle.setAttribute('aria-expanded', String(open));
  toggle.querySelector('[data-icon-open]').classList.toggle('hidden', open);
  toggle.querySelector('[data-icon-close]').classList.toggle('hidden', !open);
  document.body.style.overflow = open ? 'hidden' : '';
};

toggle.addEventListener('click', () => setMenu(!menu.classList.contains('is-open')));
menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setMenu(false)));
document.addEventListener('keydown', (e) => e.key === 'Escape' && setMenu(false));

/* ---------- Reveal saat masuk viewport ---------- */
const revealEls = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window) {
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      });
    },
    { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
  );
  revealEls.forEach((el) => io.observe(el));
} else {
  revealEls.forEach((el) => el.classList.add('is-visible'));
}

/* ---------- Tandai link navigasi sesuai section aktif ---------- */
const navLinks = document.querySelectorAll('.nav-link');
const sections = [...navLinks]
  .map((link) => document.querySelector(link.getAttribute('href')))
  .filter(Boolean);

if ('IntersectionObserver' in window) {
  const navIo = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        navLinks.forEach((link) =>
          link.classList.toggle('is-active', link.getAttribute('href') === `#${entry.target.id}`)
        );
      });
    },
    { rootMargin: '-45% 0px -50% 0px' }
  );
  sections.forEach((s) => navIo.observe(s));
}

