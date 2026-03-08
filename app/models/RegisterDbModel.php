<?php

namespace App\models;

use PDO;
use PDOException;
use App\exceptions\RegisterDbException;

class RegisterDbModel
{

    private PDO $db;
    private string $dbPath;

    /**
     * Inicializa a conexão com o banco SQLite.
     *
     * @throws RegisterDbException Quando a conexão falha
     */
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
                ], $e
            );
        }
    }

    /**
     * Salva a configuração de uma tabela e seu schema JSON.
     *
     * @param array $payload Dados da configuração (database_name, table_name, schema_json)
     * @return array Dados persistidos e metadados
     * @throws RegisterDbException Quando os dados são inválidos ou ocorre erro no banco
     */
    public function saveDatabaseConfig(array $payload): array
    {
        $databaseName = trim((string)($payload['database_name'] ?? ''));
        $tableName = trim((string)($payload['table_name'] ?? ''));
        $schemaJson = (string)($payload['schema_json'] ?? '');

        if ($databaseName === '') {
            throw new RegisterDbException(
                'database_name e obrigatorio.',
                422,
                ['field' => 'database_name']
            );
        }
        if ($tableName === '') {
            throw new RegisterDbException(
                'table_name e obrigatorio.',
                422,
                ['field' => 'table_name']
            );
        }
        if ($schemaJson === '') {
            throw new RegisterDbException(
                'schema_json e obrigatorio.',
                422,
                ['field' => 'schema_json']
            );
        }
        $decodedJson = json_decode($schemaJson, true);
        if (!is_array($decodedJson)) {
            throw new RegisterDbException(
                'schema_json invalido.',
                422,
                ['field' => 'schema_json']
            );
        }
        try {
            $this->ensureConfigTableExists();
            $this->assertDatabaseNotRegistered($databaseName);
            $registrySql = "
                INSERT INTO database_table_registry (
                    database_name,
                    table_name,
                    schema_json
                ) VALUES (
                    :database_name,
                    :table_name,
                    :schema_json
                )
            ";
            $registryStmt = $this->db->prepare($registrySql);
            $registryStmt->execute([
                ':database_name' => $databaseName,
                ':table_name' => $tableName,
                ':schema_json' => $schemaJson
            ]);
            $registryId = (int)$this->db->lastInsertId();
            return [
                'id' => $registryId,
                'database_name' => $databaseName,
                'table_name' => $tableName,
                'schema_json' => $decodedJson,
                'db_path' => $this->dbPath,
                'db_realpath' => realpath($this->dbPath) ?: null
            ];
        } catch (PDOException $e) {
            throw new RegisterDbException(
                'Falha ao salvar configuracao do banco.',
                500,
                [
                    'operation' => 'save_database_config',
                    'pdo_error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Impede cadastro duplicado de database_name no registry.
     *
     * @param string $databaseName Nome do banco informado pelo usuário
     * @return void
     * @throws RegisterDbException Quando o banco já estiver cadastrado
     */
    private function assertDatabaseNotRegistered(string $databaseName): void
    {
        $sql = "
            SELECT id
            FROM database_table_registry
            WHERE LOWER(TRIM(database_name)) = LOWER(TRIM(:database_name))
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':database_name' => $databaseName
        ]);
        $existing = $stmt->fetch();
        if ($existing !== false) {
            throw new RegisterDbException(
                'Esse banco de dados ja foi cadastrado.',
                409,
                [
                    'field' => 'database_name',
                    'database_name' => $databaseName
                ]
            );
        }
    }

    /**
     * Garante que a tabela de registry exista no banco.
     * Cria a tabela e adiciona colunas faltantes se necessário.
     *
     * @return void
     */
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

    /**
     * Verifica se uma tabela possui uma coluna específica.
     *
     * @param string $tableName Nome da tabela
     * @param string $columnName Nome da coluna
     * @return bool True se existir, false caso contrário
     */
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