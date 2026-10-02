<?php
declare(strict_types=1);

/** Escape output HTML. Gunakan untuk SETIAP data dari database / input pengguna. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function abort(int $code, string $message = 'Halaman tidak ditemukan.'): never
{
    http_response_code($code);
    echo '<!doctype html><meta charset="utf-8"><title>' . $code . '</title>'
        . '<p style="font-family:system-ui;padding:2rem">' . e($message) . '</p>';
    exit;
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/* ---------- Flash message (pesan sekali tampil setelah redirect) ---------- */

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/* ---------- Modul ---------- */

/**
 * Semua modul konten di app/modules/*.php.
 * Menambah bagian baru di admin = menambah satu file modul, lalu buka admin/setup.php.
 */
function modules(): array
{
    static $modules = null;
    if ($modules !== null) {
        return $modules;
    }

    $modules = [];
    foreach (glob(ROOT . '/app/modules/*.php') as $file) {
        $key = basename($file, '.php');
        if (!preg_match('/^[a-z_]+$/', $key)) {
            continue;
        }
        $module = require $file;
        $module['key'] = $key;
        $module['type'] ??= 'crud';
        $modules[$key] = $module;
    }
    uasort($modules, fn ($a, $b) => ($a['menu_order'] ?? 99) <=> ($b['menu_order'] ?? 99));

    return $modules;
}

function module(string $key): ?array
{
    return modules()[$key] ?? null;
}

/* ---------- Pengaturan situs (tabel key-value) ---------- */

function settings(): array
{
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $settings = [];
    foreach (module('settings')['fields'] as $name => $field) {
        $settings[$name] = $field['default'] ?? '';
    }
    foreach (db_all('SELECT setting_key, setting_value FROM settings') as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

function save_setting(string $key, string $value): void
{
    $exists = db_one('SELECT 1 FROM settings WHERE setting_key = ?', [$key]);
    if ($exists) {
        db_exec('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
    } else {
        db_exec('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
    }
}

/* ---------- Format teks untuk halaman publik ---------- */

/** Path gambar dari database → URL. $prefix dipakai admin ("../"). */
function asset_url(?string $path, string $prefix = ''): string
{
    $path = (string) $path;
    if ($path === '' || preg_match('#^https?://#', $path)) {
        return $path;
    }
    return $prefix . ltrim($path, '/');
}

/** "*kata*" → <em>kata</em>, baris baru → <br>. Aman: teks di-escape terlebih dulu. */
function emphasize(string $text, string $emClass = 'text-rose-deep'): string
{
    $html = e($text);
    $html = preg_replace('/\*(.+?)\*/u', '<em class="' . e($emClass) . '">$1</em>', $html);
    return nl2br($html, false);
}

/** Pisahkan teks menjadi paragraf berdasarkan baris kosong. */
function paragraphs(string $text): array
{
    $parts = preg_split('/\R\s*\R/u', trim($text)) ?: [];
    return array_values(array_filter(array_map('trim', $parts)));
}

function roman(int $number): string
{
    $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90,
            'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
    $result = '';
    foreach ($map as $symbol => $value) {
        while ($number >= $value) {
            $result .= $symbol;
            $number -= $value;
        }
    }
    return $result;
}

function excerpt(string $text, int $length = 80): string
{
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length - 1) . '…' : $text;
}
