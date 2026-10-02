<?php
/**
 * Router panel admin.
 *   index.php                     → dashboard
 *   index.php?m=classes           → daftar data modul
 *   index.php?m=classes&a=create  → tambah
 *   index.php?m=classes&a=edit&id=3
 *   index.php?m=classes&a=delete&id=3   (POST)
 *   index.php?m=settings          → pengaturan
 */
declare(strict_types=1);
require dirname(__DIR__) . '/app/requirements.php';
require dirname(__DIR__) . '/app/bootstrap.php';

if (!is_installed()) {
    redirect('setup.php');
}

$user = require_login();
$m = (string) ($_GET['m'] ?? 'dashboard');
$a = (string) ($_GET['a'] ?? 'index');

function render(string $view, array $vars = []): void
{
    extract($vars);
    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    $content = ob_get_clean();
    require __DIR__ . '/views/layout.php';
    exit;
}

/* ---------- Dashboard ---------- */
if ($m === 'dashboard') {
    $stats = [];
    foreach (modules() as $key => $mod) {
        if ($mod['type'] === 'crud' && $key !== 'users') {
            $stats[$key] = [
                'total'  => db_count($mod['table']),
                'active' => isset($mod['fields']['is_active']) ? db_count($mod['table'], 'is_active = 1') : null,
            ];
        }
    }
    render('dashboard', ['title' => 'Dashboard', 'user' => $user, 'stats' => $stats, 'active' => 'dashboard']);
}

$module = module($m) ?? abort(404);

/* ---------- Pengaturan (key-value) ---------- */
if ($module['type'] === 'settings') {
    $values = settings();
    $errors = [];

    if (is_post()) {
        csrf_verify();
        [$data, $errors] = validate_fields($module['fields'], $values);
        if (!$errors) {
            foreach ($data as $key => $value) {
                if ($module['fields'][$key]['type'] === 'image' && $values[$key] !== $value) {
                    delete_upload($values[$key]);
                }
                save_setting($key, (string) $value);
            }
            flash('success', 'Pengaturan berhasil disimpan.');
            redirect('index.php?m=' . $m);
        }
        $values = array_merge($values, $data);
    }

    render('form', [
        'title' => $module['title'], 'module' => $module, 'values' => $values, 'errors' => $errors,
        'action' => 'index.php?m=' . $m, 'isEdit' => true, 'active' => $m, 'user' => $user,
    ]);
}

/* ---------- CRUD ---------- */
$table = $module['table'];
$base = 'index.php?m=' . $m;
$id = (int) ($_GET['id'] ?? 0);

switch ($a) {
    case 'index':
        $rows = db_all('SELECT * FROM ' . qi($table) . ' ORDER BY ' . ($module['order'] ?? 'id DESC'));
        render('list', ['title' => $module['title'], 'module' => $module, 'rows' => $rows, 'active' => $m, 'user' => $user]);

    case 'create':
    case 'edit':
        $existing = $a === 'edit' ? (db_find($table, $id) ?? abort(404, 'Data tidak ditemukan.')) : null;
        $values = $existing ?? array_map(fn ($f) => $f['default'] ?? '', $module['fields']);
        $errors = [];

        if (is_post()) {
            csrf_verify();
            [$data, $errors] = validate_fields($module['fields'], $existing, $table);

            if (!$errors) {
                $data['updated_at'] = now();
                if ($existing) {
                    foreach ($module['fields'] as $name => $f) {
                        if ($f['type'] === 'image' && isset($data[$name]) && $data[$name] !== $existing[$name]) {
                            delete_upload($existing[$name]);
                        }
                    }
                    db_update($table, $id, $data);
                    flash('success', $module['singular'] . ' berhasil diperbarui.');
                } else {
                    $data['created_at'] = now();
                    db_insert($table, $data);
                    flash('success', $module['singular'] . ' berhasil ditambahkan.');
                }
                redirect($base);
            }
            // tampilkan kembali isian (tanpa hash password)
            $values = array_merge($values, array_diff_key($data, array_filter($module['fields'], fn ($f) => $f['type'] === 'password')));
        }

        render('form', [
            'title'  => ($existing ? 'Edit ' : 'Tambah ') . $module['singular'],
            'module' => $module, 'values' => $values, 'errors' => $errors, 'isEdit' => (bool) $existing,
            'action' => $base . '&a=' . $a . ($existing ? '&id=' . $id : ''), 'active' => $m, 'user' => $user,
        ]);

    case 'delete':
        if (!is_post()) {
            abort(405, 'Metode tidak diizinkan.');
        }
        csrf_verify();
        $row = db_find($table, $id) ?? abort(404, 'Data tidak ditemukan.');

        if (isset($module['guard_delete']) && ($reason = $module['guard_delete']($row))) {
            flash('error', $reason);
            redirect($base);
        }

        db_delete($table, $id);
        foreach ($module['fields'] as $name => $f) {
            if ($f['type'] === 'image') {
                delete_upload($row[$name] ?? null);
            }
        }
        flash('success', $module['singular'] . ' berhasil dihapus.');
        redirect($base);

    default:
        abort(404);
}
