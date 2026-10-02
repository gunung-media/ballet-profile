<?php
declare(strict_types=1);

function auth_user(): ?array
{
    static $user = false;
    if ($user !== false) {
        return $user;
    }

    $user = null;
    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return null;
    }

    if (time() - ($_SESSION['last_seen'] ?? 0) > config('auth.idle_timeout')) {
        logout();
        flash('info', 'Sesi berakhir karena tidak aktif. Silakan masuk kembali.');
        return null;
    }

    $_SESSION['last_seen'] = time();
    $user = db_one('SELECT id, name, username FROM users WHERE id = ?', [(int) $id]);
    if (!$user) {
        logout();
    }
    return $user;
}

function require_login(): array
{
    $user = auth_user();
    if (!$user) {
        redirect('login.php');
    }
    return $user;
}

function login_locked(): bool
{
    $since = time() - config('auth.lockout_window');
    db_exec('DELETE FROM login_attempts WHERE attempted_at < ?', [$since]);
    return db_count('login_attempts', 'ip = ?', [client_ip()]) >= config('auth.max_attempts');
}

/** true jika berhasil. Pesan error sengaja umum agar username tidak bisa ditebak. */
function attempt_login(string $username, string $password): bool
{
    $user = db_one('SELECT * FROM users WHERE username = ?', [$username]);

    // Tetap jalankan password_verify meski user tidak ada, supaya waktu respons sama
    $hash = $user['password'] ?? '$2y$10$Wb13PGV6HwBNKDHivDFJw.RrNw3O.OhNRIOs7IdH1xiz18fmuSrJy';
    if (!password_verify($password, $hash) || !$user) {
        db_insert('login_attempts', ['ip' => client_ip(), 'attempted_at' => time()]);
        return false;
    }

    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        db_update('users', (int) $user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    db_exec('DELETE FROM login_attempts WHERE ip = ?', [client_ip()]);
    login_as((int) $user['id']);
    return true;
}

function login_as(int $userId): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['last_seen'] = time();
    unset($_SESSION['_csrf']);
}

function logout(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}
