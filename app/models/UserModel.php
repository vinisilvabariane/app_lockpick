<?php

namespace App\models;

use PDO;
use PDOException;
use App\exceptions\RegisterDbException;

class UserModel
{
    private PDO $db;
    private string $dbPath;

    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../../database/db';

        try {
            $this->db = new PDO("sqlite:{$this->dbPath}");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RegisterDbException(
                'Falha ao conectar no SQLite.',
                500,
                [
                    'db_path' => $this->dbPath,
                    'pdo_error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    public function listRegisteredDatabases(): array
    {
        $this->ensureConfigTableExists();

        $sql = "
            SELECT
                id,
                database_name,
                table_name,
                created_at
            FROM database_table_registry
            ORDER BY database_name ASC
        ";
        $stmt = $this->db->query($sql);

        return $stmt ? $stmt->fetchAll() : [];
    }

    public function findSchemasByDatabaseNames(array $databaseNames): array
    {
        $this->ensureConfigTableExists();

        $cleanNames = [];
        foreach ($databaseNames as $name) {
            $value = trim((string)$name);
            if ($value !== '') {
                $cleanNames[] = $value;
            }
        }
        $cleanNames = array_values(array_unique($cleanNames));

        if ($cleanNames === []) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($cleanNames as $index => $name) {
            $key = ":name_{$index}";
            $placeholders[] = $key;
            $params[$key] = $name;
        }

        $sql = "
            SELECT
                id,
                database_name,
                table_name,
                schema_json,
                created_at
            FROM database_table_registry
            WHERE database_name IN (" . implode(',', $placeholders) . ")
            ORDER BY database_name ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $decodedSchema = null;
            if (isset($row['schema_json'])) {
                $json = json_decode((string)$row['schema_json'], true);
                if (is_array($json)) {
                    $decodedSchema = $json;
                }
            }

            $result[] = [
                'id' => (int)($row['id'] ?? 0),
                'database_name' => (string)($row['database_name'] ?? ''),
                'table_name' => (string)($row['table_name'] ?? ''),
                'schema_json' => $decodedSchema,
                'created_at' => (string)($row['created_at'] ?? '')
            ];
        }

        return $result;
    }

    private function ensureConfigTableExists(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS database_table_registry (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                database_name TEXT NOT NULL,
                table_name TEXT NOT NULL,
                schema_json JSON,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        if (!$this->hasColumn('database_table_registry', 'schema_json')) {
            $this->db->exec(
                'ALTER TABLE database_table_registry ADD COLUMN schema_json JSON'
            );
        }
    }

    private function hasColumn(string $tableName, string $columnName): bool
    {
        $stmt = $this->db->query("PRAGMA table_info({$tableName})");
        $columns = $stmt ? $stmt->fetchAll() : [];
        foreach ($columns as $column) {
            if (
                isset($column['name']) &&
                (string)$column['name'] === $columnName
            ) {
                return true;
            }
        }

        return false;
    }
}
