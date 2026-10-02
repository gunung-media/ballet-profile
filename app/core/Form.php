<?php
declare(strict_types=1);

/**
 * Validasi & render field berdasarkan definisi modul.
 *
 * Tipe field: text, textarea, url, number, checkbox, select, image, password
 * Opsi umum : label, required, max, help, default, placeholder, width ('half'),
 *             group, pattern + pattern_message, unique, options (select), rows (textarea)
 */

/**
 * Ambil & validasi nilai dari $_POST/$_FILES.
 * Foto baru diunggah hanya jika field lain sudah valid, agar tidak ada file yatim.
 *
 * @return array{0: array, 1: array} [data, errors]
 */
function validate_fields(array $fields, ?array $existing = null, ?string $table = null): array
{
    $data = [];
    $errors = [];

    foreach ($fields as $name => $f) {
        $label = $f['label'];
        $required = !empty($f['required']);

        switch ($f['type']) {
            case 'checkbox':
                $data[$name] = isset($_POST[$name]) ? 1 : 0;
                continue 2;

            case 'number':
                $raw = trim((string) ($_POST[$name] ?? ''));
                if ($raw === '') {
                    $data[$name] = (int) ($f['default'] ?? 0);
                } elseif (filter_var($raw, FILTER_VALIDATE_INT) === false) {
                    $errors[$name] = "$label harus berupa angka bulat.";
                } else {
                    $data[$name] = (int) $raw;
                }
                continue 2;

            case 'image':
                continue 2; // diproses setelah field lain valid

            case 'password':
                $pass = (string) ($_POST[$name] ?? '');
                $confirm = (string) ($_POST[$name . '_confirmation'] ?? '');
                if ($pass === '') {
                    if (!$existing) {
                        $errors[$name] = "$label wajib diisi.";
                    }
                    continue 2; // kosong saat edit = password tidak diubah
                }
                if (mb_strlen($pass) < 8) {
                    $errors[$name] = "$label minimal 8 karakter.";
                } elseif ($pass !== $confirm) {
                    $errors[$name] = 'Konfirmasi password tidak sama.';
                } else {
                    $data[$name] = password_hash($pass, PASSWORD_DEFAULT);
                }
                continue 2;
        }

        // text, textarea, url, select
        $value = $_POST[$name] ?? '';
        $value = is_string($value) ? trim(str_replace("\r\n", "\n", $value)) : '';
        $data[$name] = $value;

        if ($value === '') {
            if ($required) {
                $errors[$name] = "$label wajib diisi.";
            }
            continue;
        }

        $max = $f['max'] ?? ($f['type'] === 'textarea' ? 5000 : 255);
        if (mb_strlen($value) > $max) {
            $errors[$name] = "$label maksimal $max karakter.";
        } elseif ($f['type'] === 'url' && !is_safe_url($value)) {
            $errors[$name] = "$label harus berupa link yang diawali https://";
        } elseif ($f['type'] === 'select' && !array_key_exists($value, $f['options'])) {
            $errors[$name] = "Pilihan $label tidak valid.";
        } elseif (!empty($f['pattern']) && !preg_match($f['pattern'], $value)) {
            $errors[$name] = $f['pattern_message'] ?? "Format $label tidak valid.";
        } elseif (!empty($f['unique']) && $table) {
            $dupe = db_one('SELECT id FROM ' . qi($table) . ' WHERE ' . qi($name) . ' = ? AND id <> ?',
                [$value, (int) ($existing['id'] ?? 0)]);
            if ($dupe) {
                $errors[$name] = "$label sudah dipakai.";
            }
        }
    }

    // Foto: unggah hanya jika semua field lain valid
    foreach ($fields as $name => $f) {
        if ($f['type'] !== 'image') {
            continue;
        }
        $file = $_FILES[$name] ?? null;
        $hasFile = $file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $current = $existing[$name] ?? '';

        if (!$hasFile) {
            if (!empty($f['required']) && $current === '') {
                $errors[$name] = "{$f['label']} wajib diunggah.";
            }
            continue;
        }
        if ($errors) {
            $errors[$name] ??= 'Pilih ulang foto setelah memperbaiki isian lain.';
            continue;
        }
        try {
            $data[$name] = upload_image($file);
        } catch (RuntimeException $ex) {
            $errors[$name] = $ex->getMessage();
        }
    }

    return [$data, $errors];
}

function is_safe_url(string $url): bool
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false
        && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
}

/** Kelompokkan field berdasarkan opsi 'group' untuk ditampilkan per kartu. */
function group_fields(array $fields, string $defaultGroup = 'Informasi'): array
{
    $groups = [];
    foreach ($fields as $name => $f) {
        $groups[$f['group'] ?? $defaultGroup][$name] = $f;
    }
    return $groups;
}

/* ================= Render ================= */

const INPUT_CLASS = 'block w-full rounded-lg border border-charcoal/15 bg-white px-3.5 py-2.5 text-[15px] text-charcoal shadow-sm outline-none transition placeholder:text-charcoal/35 focus:border-rose focus:ring-2 focus:ring-rose/20';

function render_field(string $name, array $f, mixed $value, ?string $error): string
{
    $id = 'f-' . $name;
    $req = !empty($f['required']) && $f['type'] !== 'password' ? ' <span class="text-rose">*</span>' : '';
    $label = '<label for="' . $id . '" class="mb-1.5 block text-[13px] font-medium text-charcoal/80">' . e($f['label']) . $req . '</label>';
    $help = !empty($f['help']) ? '<p class="mt-1.5 text-[12px] text-charcoal/50">' . e($f['help']) . '</p>' : '';
    $err = $error ? '<p class="mt-1.5 text-[12px] text-red-700">' . e($error) . '</p>' : '';
    $cls = INPUT_CLASS . ($error ? ' !border-red-400' : '');
    $ph = isset($f['placeholder']) ? ' placeholder="' . e($f['placeholder']) . '"' : '';
    $value = (string) ($value ?? '');

    $input = match ($f['type']) {
        'textarea' => '<textarea id="' . $id . '" name="' . e($name) . '" rows="' . (int) ($f['rows'] ?? 4) . '" class="' . $cls . '"' . $ph . '>' . e($value) . '</textarea>',

        'select' => '<select id="' . $id . '" name="' . e($name) . '" class="' . $cls . '">'
            . implode('', array_map(
                fn ($k, $v) => '<option value="' . e($k) . '"' . ((string) $k === $value ? ' selected' : '') . '>' . e($v) . '</option>',
                array_keys($f['options']), $f['options']))
            . '</select>',

        'checkbox' => '<label class="flex cursor-pointer items-center justify-between gap-4 rounded-lg border border-charcoal/10 bg-white px-4 py-3">'
            . '<span class="text-[14px] text-charcoal/80">' . e($f['label']) . '</span>'
            . '<span class="relative inline-flex">'
            . '<input type="checkbox" id="' . $id . '" name="' . e($name) . '" value="1" class="peer sr-only"' . ($value === '1' ? ' checked' : '') . '>'
            . '<span class="h-6 w-11 rounded-full bg-charcoal/15 transition peer-checked:bg-rose-deep peer-focus-visible:ring-2 peer-focus-visible:ring-rose/40"></span>'
            . '<span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>'
            . '</span></label>',

        'image' => render_image_input($id, $name, $value, $error),

        'password' => '<input type="password" id="' . $id . '" name="' . e($name) . '" autocomplete="new-password" class="' . $cls . '">'
            . '<input type="password" name="' . e($name) . '_confirmation" autocomplete="new-password" placeholder="Ulangi password" class="' . $cls . ' mt-2">',

        'number' => '<input type="number" inputmode="numeric" id="' . $id . '" name="' . e($name) . '" value="' . e($value) . '" class="' . $cls . '"' . $ph . '>',

        default => '<input type="' . ($f['type'] === 'url' ? 'url' : 'text') . '" id="' . $id . '" name="' . e($name) . '" value="' . e($value) . '"'
            . ' maxlength="' . (int) ($f['max'] ?? 255) . '" class="' . $cls . '"' . $ph
            . (isset($f['inputmode']) ? ' inputmode="' . e($f['inputmode']) . '"' : '') . '>',
    };

    $span = ($f['width'] ?? '') === 'half' ? 'sm:col-span-1' : 'sm:col-span-2';
    $showLabel = $f['type'] !== 'checkbox';

    return '<div class="' . $span . '">' . ($showLabel ? $label : '') . $input . $help . $err . '</div>';
}

function render_image_input(string $id, string $name, string $value, ?string $error): string
{
    $preview = $value !== ''
        ? '<img src="' . e(asset_url($value, '../')) . '" alt="" class="h-full w-full object-cover" data-preview-img>'
        : '<img src="" alt="" class="hidden h-full w-full object-cover" data-preview-img>'
          . '<span class="text-[12px] text-charcoal/40" data-preview-empty>Belum ada foto</span>';

    return '<div class="flex flex-col gap-4 sm:flex-row sm:items-center" data-image-field>'
        . '<div class="flex aspect-[4/3] w-full shrink-0 items-center justify-center overflow-hidden rounded-lg border border-dashed border-charcoal/20 bg-cream/60 sm:w-40">' . $preview . '</div>'
        . '<div class="flex-1">'
        . '<label class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-rose-deep/40 px-5 py-2.5 text-[12px] uppercase tracking-[.15em] text-rose-deep transition hover:bg-rose-deep hover:text-ivory">'
        . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 16V4m0 0-4 4m4-4 4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>'
        . ($value !== '' ? 'Ganti Foto' : 'Pilih Foto')
        . '<input type="file" id="' . $id . '" name="' . e($name) . '" accept="image/jpeg,image/png,image/webp" class="sr-only" data-preview-input>'
        . '</label>'
        . '<p class="mt-2 text-[12px] text-charcoal/50" data-preview-name>JPG, PNG, atau WebP · maks ' . round(config('upload.max_bytes') / 1048576) . ' MB · otomatis dioptimasi</p>'
        . '</div></div>';
}
