/* Angelique Ballet — interaksi kecil panel admin (vanilla JS) */

/* ---------- Sidebar mobile ---------- */
const sidebar = document.querySelector('[data-sidebar]');
const overlay = document.querySelector('[data-sidebar-overlay]');

const setSidebar = (open) => {
  if (!sidebar) return;
  sidebar.classList.toggle('-translate-x-full', !open);
  overlay.classList.toggle('hidden', !open);
  document.body.style.overflow = open ? 'hidden' : '';
};

document.querySelector('[data-sidebar-open]')?.addEventListener('click', () => setSidebar(true));
document.querySelector('[data-sidebar-close]')?.addEventListener('click', () => setSidebar(false));
overlay?.addEventListener('click', () => setSidebar(false));
document.addEventListener('keydown', (e) => e.key === 'Escape' && setSidebar(false));

/* ---------- Konfirmasi sebelum menghapus ---------- */
document.querySelectorAll('form[data-confirm]').forEach((form) => {
  form.addEventListener('submit', (e) => {
    if (!confirm(form.dataset.confirm)) e.preventDefault();
  });
});

/* ---------- Pratinjau foto sebelum diunggah ---------- */
document.querySelectorAll('[data-image-field]').forEach((field) => {
  const input = field.querySelector('[data-preview-input]');
  const img = field.querySelector('[data-preview-img]');
  const empty = field.querySelector('[data-preview-empty]');
  const name = field.querySelector('[data-preview-name]');

  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    img.src = URL.createObjectURL(file);
    img.classList.remove('hidden');
    empty?.remove();
    name.textContent = `${file.name} · ${(file.size / 1048576).toFixed(1)} MB`;
  });
});

/* ---------- Tampilkan / sembunyikan password ---------- */
document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
  const input = document.getElementById(btn.dataset.togglePassword);
  btn.addEventListener('click', () => {
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.textContent = show ? 'Sembunyikan' : 'Lihat';
  });
});

/* ---------- Tutup pesan notifikasi ---------- */
document.querySelectorAll('[data-dismiss]').forEach((btn) =>
  btn.addEventListener('click', () => btn.closest('[data-dismissible]').remove())
);

/* ---------- Cegah klik ganda saat menyimpan ---------- */
document.querySelectorAll('form').forEach((form) => {
  form.addEventListener('submit', (e) => {
    if (e.defaultPrevented) return;
    form.querySelectorAll('button:not([type="button"])').forEach((b) => {
      b.disabled = true;
      b.classList.add('opacity-60');
    });
  });
});
