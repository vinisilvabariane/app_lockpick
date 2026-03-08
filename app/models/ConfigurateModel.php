<?php

namespace App\models;

use App\exceptions\RegisterDbException;
use PDO;
use PDOException;

class ConfigurateModel
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

    public function listRegisteredTables(): array
    {
        $this->ensureRegistryTableExists();
        $this->ensureConfigurationTableExists();

        $sql = "
            SELECT
                r.id,
                r.database_name,
                r.table_name,
                r.created_at,
                CASE WHEN c.id IS NULL THEN 0 ELSE 1 END AS has_configuration
            FROM database_table_registry r
            LEFT JOIN configuration_tables c ON c.registry_id = r.id
            ORDER BY r.database_name ASC, r.table_name ASC
        ";
        $stmt = $this->db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => (int)($row['id'] ?? 0),
                'database_name' => (string)($row['database_name'] ?? ''),
                'table_name' => (string)($row['table_name'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
                'has_configuration' => (int)($row['has_configuration'] ?? 0) === 1
            ];
        }

        return $result;
    }

    public function getConfigurationByRegistryId(int $registryId): array
    {
        $this->ensureRegistryTableExists();
        $this->ensureConfigurationTableExists();

        $sql = "
            SELECT
                r.id AS registry_id,
                r.database_name,
                r.table_name,
                r.schema_json,
                c.id AS configuration_id,
                c.config_json,
                c.updated_at AS configuration_updated_at
            FROM database_table_registry r
            LEFT JOIN configuration_tables c ON c.registry_id = r.id
            WHERE r.id = :registry_id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':registry_id' => $registryId
        ]);
        $row = $stmt->fetch();

        if ($row === false) {
            throw new RegisterDbException(
                'Tabela cadastrada nao encontrada.',
                404,
                [
                    'field' => 'registry_id',
                    'registry_id' => $registryId
                ]
            );
        }

        $schema = json_decode((string)($row['schema_json'] ?? ''), true);
        if (!is_array($schema)) {
            throw new RegisterDbException(
                'Schema JSON da tabela cadastrada esta invalido.',
                500,
                [
                    'registry_id' => $registryId
                ]
            );
        }

        $storedConfiguration = null;
        if (isset($row['config_json']) && (string)$row['config_json'] !== '') {
            $decodedConfig = json_decode((string)$row['config_json'], true);
            if (is_array($decodedConfig)) {
                $storedConfiguration = $decodedConfig;
            }
        }

        $effectiveConfiguration = $this->buildEffectiveConfiguration(
            $schema,
            $storedConfiguration
        );

        return [
            'registry' => [
                'id' => (int)($row['registry_id'] ?? 0),
                'database_name' => (string)($row['database_name'] ?? ''),
                'table_name' => (string)($row['table_name'] ?? '')
            ],
            'schema' => $schema,
            'configuration' => $effectiveConfiguration,
            'configuration_meta' => [
                'id' => isset($row['configuration_id']) ? (int)$row['configuration_id'] : null,
                'updated_at' => isset($row['configuration_updated_at'])
                    ? (string)$row['configuration_updated_at']
                    : null
            ]
        ];
    }

    public function upsertConfiguration(int $registryId, array $configuration): array
    {
        $loaded = $this->getConfigurationByRegistryId($registryId);
        $schema = is_array($loaded['schema'] ?? null) ? $loaded['schema'] : [];
        $normalizedConfig = $this->normalizeIncomingConfiguration($schema, $configuration);
        $configJson = json_encode(
            $normalizedConfig,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($configJson === false) {
            throw new RegisterDbException(
                'Falha ao serializar configuracao.',
                500
            );
        }

        try {
            $sql = "
                INSERT INTO configuration_tables (
                    registry_id,
                    database_name,
                    table_name,
                    config_json,
                    created_at,
                    updated_at
                ) VALUES (
                    :registry_id,
                    :database_name,
                    :table_name,
                    :config_json,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )
                ON CONFLICT(registry_id) DO UPDATE SET
                    database_name = excluded.database_name,
                    table_name = excluded.table_name,
                    config_json = excluded.config_json,
                    updated_at = CURRENT_TIMESTAMP
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':registry_id' => $registryId,
                ':database_name' => (string)$loaded['registry']['database_name'],
                ':table_name' => (string)$loaded['registry']['table_name'],
                ':config_json' => $configJson
            ]);
        } catch (PDOException $e) {
            throw new RegisterDbException(
                'Falha ao salvar configuracao da tabela.',
                500,
                [
                    'operation' => 'upsert_configuration',
                    'pdo_error' => $e->getMessage()
                ],
                $e
            );
        }

        return $this->getConfigurationByRegistryId($registryId);
    }

    private function buildEffectiveConfiguration(array $schema, ?array $storedConfiguration): array
    {
        if (is_array($storedConfiguration)) {
            return $this->normalizeIncomingConfiguration($schema, $storedConfiguration);
        }

        return $this->normalizeIncomingConfiguration($schema, []);
    }

    private function normalizeIncomingConfiguration(array $schema, array $incoming): array
    {
        $schemaColumns = [];
        if (
            isset($schema['columns']) &&
            is_array($schema['columns'])
        ) {
            $schemaColumns = $schema['columns'];
        }

        $incomingColumnsMap = [];
        if (isset($incoming['columns']) && is_array($incoming['columns'])) {
            foreach ($incoming['columns'] as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $name = trim((string)($item['name'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $incomingColumnsMap[$name] = $item;
            }
        }

        $incomingSettings = isset($incoming['settings']) && is_array($incoming['settings'])
            ? $incoming['settings']
            : [];

        $normalizedColumns = [];
        foreach ($schemaColumns as $column) {
            if (!is_array($column)) {
                continue;
            }

            $name = trim((string)($column['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            if ($this->isConstraintPseudoColumn($name)) {
                continue;
            }

            $type = (string)($column['type'] ?? 'TEXT');
            $nullable = isset($column['nullable']) ? (bool)$column['nullable'] : true;
            $autoIncrement = isset($column['auto_increment']) ? (bool)$column['auto_increment'] : false;
            $definition = strtoupper((string)($column['definition'] ?? ''));
            if (
                $autoIncrement === false &&
                (strpos($definition, 'IDENTITY') !== false || strpos($definition, 'AUTO_INCREMENT') !== false)
            ) {
                $autoIncrement = true;
            }
            $schemaDefault = isset($column['default']) ? (string)$column['default'] : '';
            $schemaDefault = trim($schemaDefault);
            if (strtoupper($schemaDefault) === 'NULL') {
                $schemaDefault = '';
            }

            $saved = $incomingColumnsMap[$name] ?? [];
            $normalizedDefaultValue = array_key_exists('default_value', $saved)
                ? (string)$saved['default_value']
                : $schemaDefault;

            $isAutomaticDefault = $autoIncrement || $schemaDefault !== '';
            $isManualDefault = !$isAutomaticDefault;

            $isManualInput = array_key_exists('is_manual_input', $saved)
                ? (bool)$saved['is_manual_input']
                : false;
            $isDefaultInput = array_key_exists('is_default_input', $saved)
                ? (bool)$saved['is_default_input']
                : (
                    array_key_exists('is_manual', $saved)
                        ? (bool)$saved['is_manual']
                        : (
                            array_key_exists('is_required', $saved)
                                ? (bool)$saved['is_required']
                                : $isManualDefault
                        )
                );
            $isAutomatic = array_key_exists('is_automatic', $saved)
                ? (bool)$saved['is_automatic']
                : $isAutomaticDefault;
            $insertAsNull = array_key_exists('insert_as_null', $saved)
                ? (bool)$saved['insert_as_null']
                : false;

            if (!$nullable) {
                $insertAsNull = false;
            }

            if ($insertAsNull) {
                $isAutomatic = true;
                $isDefaultInput = false;
                $isManualInput = false;
                $normalizedDefaultValue = '';
            }

            // Garante exclusividade entre os 3 modos.
            $selectedModes = [
                $isManualInput,
                $isDefaultInput,
                $isAutomatic
            ];
            $selectedCount = 0;
            foreach ($selectedModes as $selected) {
                if ($selected) {
                    $selectedCount++;
                }
            }
            if ($selectedCount === 0) {
                $isDefaultInput = true;
            } elseif ($selectedCount > 1) {
                if ($isManualInput) {
                    $isDefaultInput = false;
                    $isAutomatic = false;
                } elseif ($isDefaultInput) {
                    $isAutomatic = false;
                }
            }

            $autoSource = array_key_exists('auto_source', $saved)
                ? trim((string)$saved['auto_source'])
                : $this->inferAutoSource($name, $type);
            if ($autoSource === '') {
                $autoSource = 'none';
            }
            if ($insertAsNull) {
                $autoSource = 'none';
            }

            $normalizedColumns[] = [
                'name' => $name,
                'type' => $type,
                'is_manual_input' => $isManualInput,
                'is_default_input' => $isDefaultInput,
                'is_automatic' => $isAutomatic,
                'insert_as_null' => $insertAsNull,
                'default_value' => $normalizedDefaultValue,
                'auto_source' => $autoSource,
                'nullable' => $nullable
            ];
        }

        return [
            'version' => 1,
            'settings' => [
                'email_domain' => trim((string)($incomingSettings['email_domain'] ?? '')),
                'email_prefix_source' => trim((string)($incomingSettings['email_prefix_source'] ?? ''))
            ],
            'columns' => $normalizedColumns
        ];
    }

    private function inferAutoSource(string $columnName, string $columnType): string
    {
        $name = strtolower($columnName);
        $type = strtoupper($columnType);

        if (strpos($name, 'email') !== false) {
            return 'email_from_name';
        }
        if (strpos($name, 'login') !== false || strpos($name, 'usuario') !== false || strpos($name, 'username') !== false) {
            return 'login_from_name';
        }
        if ($name === 'nome' || strpos($name, 'nome_completo') !== false) {
            return 'name_input';
        }
        if (strpos($name, 'date') !== false || strpos($name, 'data') !== false || strpos($type, 'DATE') !== false || strpos($type, 'TIME') !== false) {
            return 'current_datetime';
        }
        if (strpos($name, 'status') !== false || strpos($name, 'situacao') !== false) {
            return 'static_value';
        }
        return 'none';
    }

    private function isConstraintPseudoColumn(string $name): bool
    {
        $upper = strtoupper(trim($name));
        return in_array($upper, [
            'PRIMARY',
            'UNIQUE',
            'KEY',
            'CONSTRAINT',
            'FOREIGN',
            'INDEX'
        ], true);
    }

    private function ensureRegistryTableExists(): void
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
    }

    private function ensureConfigurationTableExists(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS configuration_tables (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                registry_id INTEGER NOT NULL UNIQUE,
                database_name TEXT NOT NULL,
                table_name TEXT NOT NULL,
                config_json JSON NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        if (!$this->hasColumn('configuration_tables', 'updated_at')) {
            $this->db->exec(
                'ALTER TABLE configuration_tables ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP'
            );
        }
    }

    private function hasColumn(string $tableName, string $columnName): bool
    {
        $stmt = $this->db->query("PRAGMA table_info({$tableName})");
        $columns = $stmt ? $stmt->fetchAll() : [];
        foreach ($columns as $column) {
            if (isset($column['name']) && (string)$column['name'] === $columnName) {
                return true;
            }
        }

        return false;
    }
}
