<?php

namespace App\controllers;

use App\config\ErrorResponse;
use App\exceptions\RegisterDbException;
use App\models\ConfigurateModel;

class ConfigurateController
{
    /**
     * Responsavel por carregar a view de configuração.
     * @return void
     */
    public function index()
    {
        require_once __DIR__ . '/../views/configurate/index.php';
    }

    public function listTables(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            ErrorResponse::send('Metodo nao permitido.', 405, [
                'allowed_method' => 'GET'
            ]);
            return;
        }

        try {
            $model = new ConfigurateModel();
            $tables = $model->listRegisteredTables();

            ErrorResponse::send('', 200, [
                'message' => 'Tabelas cadastradas carregadas com sucesso.',
                'data' => $tables
            ], true);
        } catch (RegisterDbException $e) {
            ErrorResponse::send($e->getMessage(), $e->getStatusCode(), $e->getDetails());
        } catch (\Throwable $e) {
            ErrorResponse::send('Erro interno ao listar tabelas cadastradas.', 500, [
                'exception' => $e->getMessage()
            ]);
        }
    }

    public function loadConfiguration(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            ErrorResponse::send('Metodo nao permitido.', 405, [
                'allowed_method' => 'GET'
            ]);
            return;
        }

        $registryId = isset($_GET['registry_id']) ? (int)$_GET['registry_id'] : 0;
        if ($registryId <= 0) {
            ErrorResponse::send('registry_id invalido.', 422, [
                'field' => 'registry_id'
            ]);
            return;
        }

        try {
            $model = new ConfigurateModel();
            $configuration = $model->getConfigurationByRegistryId($registryId);

            ErrorResponse::send('', 200, [
                'message' => 'Configuracao carregada com sucesso.',
                'data' => $configuration
            ], true);
        } catch (RegisterDbException $e) {
            ErrorResponse::send($e->getMessage(), $e->getStatusCode(), $e->getDetails());
        } catch (\Throwable $e) {
            ErrorResponse::send('Erro interno ao carregar configuracao.', 500, [
                'exception' => $e->getMessage()
            ]);
        }
    }

    public function saveConfiguration(): void
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

        $registryId = isset($payload['registry_id']) ? (int)$payload['registry_id'] : 0;
        $configuration = $payload['configuration'] ?? null;
        if ($registryId <= 0) {
            ErrorResponse::send('registry_id invalido.', 422, [
                'field' => 'registry_id'
            ]);
            return;
        }
        if (!is_array($configuration)) {
            ErrorResponse::send('configuration deve ser um objeto JSON.', 422, [
                'field' => 'configuration'
            ]);
            return;
        }

        try {
            $model = new ConfigurateModel();
            $saved = $model->upsertConfiguration($registryId, $configuration);

            ErrorResponse::send('', 201, [
                'message' => 'Configuracao da tabela salva com sucesso.',
                'data' => $saved
            ], true);
        } catch (RegisterDbException $e) {
            ErrorResponse::send($e->getMessage(), $e->getStatusCode(), $e->getDetails());
        } catch (\Throwable $e) {
            ErrorResponse::send('Erro interno ao salvar configuracao.', 500, [
                'exception' => $e->getMessage()
            ]);
        }
    }
}
