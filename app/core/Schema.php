<?php
declare(strict_types=1);

/**
 * Membuat / memperbarui tabel berdasarkan definisi field di setiap modul.
 * Aman dijalankan berulang: tabel yang sudah ada hanya ditambah kolom yang belum ada.
 */
function migrate(): void
{
    $mysql = Database::driver() === 'mysql';
    $idCol = $mysql ? 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $tail  = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';

    db()->exec('CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
        setting_value TEXT
    )' . $tail);

    db()->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id $idCol,
        ip VARCHAR(45) NOT NULL,
        attempted_at INTEGER NOT NULL
    )" . $tail);

    foreach (modules() as $module) {
        if ($module['type'] !== 'crud') {
            continue;
        }

        $columns = [];
        foreach ($module['fields'] as $name => $field) {
            $columns[$name] = column_sql($field, $mysql);
        }
        $columns['created_at'] = 'VARCHAR(19)';
        $columns['updated_at'] = 'VARCHAR(19)';

        $table = $module['table'];
        $existing = table_columns($table);

        if (!$existing) {
            $defs = ['id ' . $idCol];
            foreach ($columns as $name => $sql) {
                $defs[] = qi($name) . ' ' . $sql;
            }
            db()->exec('CREATE TABLE ' . qi($table) . " (\n  " . implode(",\n  ", $defs) . "\n)" . $tail);
        } else {
            foreach (array_diff_key($columns, array_flip($existing)) as $name => $sql) {
                // Kolom baru tanpa UNIQUE agar ALTER aman pada tabel yang sudah berisi data
                db()->exec('ALTER TABLE ' . qi($table) . ' ADD COLUMN ' . qi($name) . ' ' . str_replace(' UNIQUE', '', $sql));
            }
        }
    }
}

function column_sql(array $field, bool $mysql): string
{
    $sql = match ($field['type']) {
        'textarea'          => 'TEXT',
        'number', 'checkbox' => 'INTEGER NOT NULL DEFAULT ' . (int) ($field['default'] ?? 0),
        default             => !empty($field['unique']) ? 'VARCHAR(191)' : 'VARCHAR(255)',
    };
    return !empty($field['unique']) ? $sql . ' UNIQUE' : $sql;
}

function table_columns(string $table): array
{
    if (Database::driver() === 'mysql') {
        $exists = db_one('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?', [$table]);
        return $exists ? array_column(db_all('SHOW COLUMNS FROM ' . qi($table)), 'Field') : [];
    }
    return array_column(db_all('PRAGMA table_info(' . qi($table) . ')'), 'name');
}

/** Isi data awal (konten website saat ini) ke tabel yang masih kosong. */
function seed(): void
{
    foreach (modules() as $module) {
        if ($module['type'] !== 'crud' || empty($module['seed'])) {
            continue;
        }
        if (db_count($module['table']) > 0) {
            continue;
        }
        foreach ($module['seed'] as $row) {
            db_insert($module['table'], $row + ['created_at' => now(), 'updated_at' => now()]);
        }
    }
}

function is_installed(): bool
{
    try {
        return table_columns('users') !== [];
    } catch (PDOException) {
        return false;
    }
}
