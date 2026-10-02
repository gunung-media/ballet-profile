<?php
declare(strict_types=1);

/**
 * Koneksi PDO (SQLite atau MySQL) + helper query.
 * Semua query memakai prepared statement — jangan pernah menyambung input pengguna ke SQL.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if (self::driver() === 'mysql') {
            $c = config('db.mysql');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $c['host'], $c['port'], $c['database'], $c['charset']);
            $options[PDO::ATTR_EMULATE_PREPARES] = false;
            self::$pdo = new PDO($dsn, $c['username'], $c['password'], $options);
        } else {
            $path = config('db.sqlite_path');
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            self::$pdo = new PDO('sqlite:' . $path, null, null, $options);
            self::$pdo->exec('PRAGMA journal_mode = WAL');
        }

        return self::$pdo;
    }

    public static function driver(): string
    {
        return config('db.driver') === 'mysql' ? 'mysql' : 'sqlite';
    }
}

function db(): PDO
{
    return Database::pdo();
}

/** Quote nama tabel/kolom. Nama hanya berasal dari definisi modul, tetap divalidasi. */
function qi(string $identifier): string
{
    if (!preg_match('/^[a-z_][a-z0-9_]*$/', $identifier)) {
        throw new InvalidArgumentException("Nama kolom/tabel tidak valid: $identifier");
    }
    return Database::driver() === 'mysql' ? "`$identifier`" : "\"$identifier\"";
}

function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() ?: null;
}

function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

function db_insert(string $table, array $data): int
{
    $columns = implode(', ', array_map('qi', array_keys($data)));
    $holders = implode(', ', array_fill(0, count($data), '?'));
    db_exec('INSERT INTO ' . qi($table) . " ($columns) VALUES ($holders)", array_values($data));
    return (int) db()->lastInsertId();
}

function db_update(string $table, int $id, array $data): void
{
    $sets = implode(', ', array_map(fn ($col) => qi($col) . ' = ?', array_keys($data)));
    db_exec('UPDATE ' . qi($table) . " SET $sets WHERE id = ?", [...array_values($data), $id]);
}

function db_find(string $table, int $id): ?array
{
    return db_one('SELECT * FROM ' . qi($table) . ' WHERE id = ?', [$id]);
}

function db_delete(string $table, int $id): void
{
    db_exec('DELETE FROM ' . qi($table) . ' WHERE id = ?', [$id]);
}

function db_count(string $table, string $where = '1=1', array $params = []): int
{
    return (int) (db_one('SELECT COUNT(*) AS c FROM ' . qi($table) . " WHERE $where", $params)['c'] ?? 0);
}

/** Baris aktif suatu modul, terurut — dipakai halaman publik. */
function active_rows(string $moduleKey): array
{
    $m = module($moduleKey);
    $where = isset($m['fields']['is_active']) ? 'WHERE is_active = 1' : '';
    return db_all('SELECT * FROM ' . qi($m['table']) . " $where ORDER BY " . ($m['order'] ?? 'id ASC'));
}
