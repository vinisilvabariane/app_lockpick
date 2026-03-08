<?php

namespace App\controllers;

use App\models\RegisterDbModel;
use App\exceptions\RegisterDbException;
use App\config\ErrorResponse;

class RegisterDbController
{
    /**
     * Responsável por carregar a view de cadastro/configuração.
     * @return void
     */
    public function index()
    {
        require_once __DIR__ . '/../views/register/index.php';
    }

    /**
     * Salva a configuração do banco de dados.
     * @return void
     */
    public function saveConfig()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            ErrorResponse::send('Metodo nao permitido.', 405, [
                'allowed_method' => 'POST'
            ]);
            return;
        }
        $rawBody = file_get_contents('php://input');
        $payload = json_decode((string)$rawBody, true);
        if (!is_array($payload)) {
            ErrorResponse::send('JSON invalido no body da requisicao.', 400, [
                'raw_body' => $rawBody
            ]);
            return;
        }
        try {
            $databaseName = trim((string)($payload['database_name'] ?? ''));
            $sqlDefinition = trim((string)($payload['sql_definition'] ?? ''));
            if ($databaseName === '') {
                ErrorResponse::send(
                    'database_name e obrigatorio.',
                    422, ['field' => 'database_name']);
                return;
            }
            if ($sqlDefinition === '') {
                ErrorResponse::send('sql_definition e obrigatorio.', 422, [
                    'field' => 'sql_definition'
                ]);
                return;
            }
            $parsed = $this->parseDllToJson($sqlDefinition);
            $jsonPayload = json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($jsonPayload === false) {
                ErrorResponse::send('Falha ao serializar JSON de configuracao.', 500);
                return;
            }
            $model = new RegisterDbModel();
            $result = $model->saveDatabaseConfig([
                'database_name' => $databaseName,
                'table_name' => $parsed['table_name'],
                'schema_json' => $jsonPayload
            ]);
            ErrorResponse::send('Sem registro de erros!', 201, [
                'message' => 'Configuração do banco salva com sucesso.',
                'data' => $result,
                'json' => $parsed
            ], true);
        } catch (RegisterDbException $e) {
            ErrorResponse::send($e->getMessage(), $e->getStatusCode(), $e->getDetails());
        } catch (\Throwable $e) {
            ErrorResponse::send('Erro interno ao salvar configuracao.', 500, [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Converte uma DDL (CREATE TABLE) em uma estrutura JSON.
     * @param string $sql SQL DDL no formato CREATE TABLE
     * @return array Estrutura normalizada contendo schema, tabela e colunas
     * @throws RegisterDbException Quando a DDL é inválida ou incompleta
     */
    private function parseDllToJson(string $sql): array
    {
        $tableRegex = '/CREATE\s+TABLE\s+`?(?:(?<schema>[A-Za-z0-9_]+)`?\.)?`?(?<table>[A-Za-z0-9_]+)`?\s*\(/i';
        if (!preg_match($tableRegex, $sql, $tableMatch)) {
            throw new RegisterDbException(
                'DDL invalida: nao foi possivel identificar CREATE TABLE.',
                422,
                ['hint' => 'Use um SQL iniciado com CREATE TABLE ...']
            );
        }
        $schemaName = isset($tableMatch['schema']) ? (string)$tableMatch['schema'] : null;
        $tableName = (string)$tableMatch['table'];
        $start = strpos($sql, '(');
        $end = strrpos($sql, ')');
        if ($start === false || $end === false || $end <= $start) {
            throw new RegisterDbException(
                'DDL invalida: bloco de colunas nao encontrado.',
                422
            );
        }
        $inside = substr($sql, $start + 1, $end - $start - 1);
        $lines = preg_split('/\r\n|\r|\n/', (string)$inside);
        $columns = [];
        foreach ($lines as $rawLine) {
            $line = trim((string)$rawLine);
            $line = rtrim($line, ',');
            if ($line === '') {
                continue;
            }
            if (preg_match('/^(PRIMARY\s+KEY|UNIQUE(?:\s+KEY|\s+INDEX)?|CONSTRAINT|FOREIGN\s+KEY|KEY|INDEX)\b/i', $line)) {
                continue;
            }
            if (
                preg_match('/^`(?<name>[^`]+)`\s+(?<type>[A-Za-z0-9_]+(?:\([^)]+\))?)(?<rest>.*)$/i', $line, $m)
                || preg_match('/^(?<name>[A-Za-z0-9_]+)\s+(?<type>[A-Za-z0-9_]+(?:\([^)]+\))?)(?<rest>.*)$/i', $line, $m)
            ) {
                $rest = (string)($m['rest'] ?? '');
                $defaultValue = null;
                if (preg_match('/DEFAULT\s+(.+?)(?:\s+COMMENT|\s*$)/i', $rest, $defaultMatch)) {
                    $defaultValue = trim((string)$defaultMatch[1], " '");
                }
                $isAutoIncrement = stripos($rest, 'AUTO_INCREMENT') !== false
                    || preg_match('/\bIDENTITY\s*\(\s*\d+\s*,\s*\d+\s*\)/i', $rest) === 1
                    || stripos($rest, 'IDENTITY') !== false;
                $columns[] = [
                    'name' => (string)$m['name'],
                    'type' => strtoupper((string)$m['type']),
                    'definition' => trim((string)$m['type'] . ' ' . trim($rest)),
                    'nullable' => stripos($rest, 'NOT NULL') === false,
                    'auto_increment' => $isAutoIncrement,
                    'default' => $defaultValue
                ];
            }
        }
        if ($columns === []) {
            throw new RegisterDbException(
                'DDL invalida: nenhuma coluna encontrada no CREATE TABLE.',
                422
            );
        }
        return [
            'schema_name' => $schemaName,
            'table_name' => $tableName,
            'columns_count' => count($columns),
            'columns' => $columns
        ];
    }
}
