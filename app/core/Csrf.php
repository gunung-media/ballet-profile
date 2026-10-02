<?php
declare(strict_types=1);

/** Perlindungan CSRF: setiap form POST wajib menyertakan csrf_field(). */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    // Jika ukuran kiriman melebihi post_max_size, PHP mengosongkan $_POST & $_FILES
    if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        flash('error', 'Ukuran file terlalu besar untuk server. Coba foto yang lebih kecil.');
        redirect($_SERVER['REQUEST_URI'] ?? 'index.php');
    }

    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        abort(419, 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}
