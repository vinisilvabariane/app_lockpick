<?php

namespace App\config;

class ErrorResponse
{
    /**
     * @param string $error - Mensagem de erro, vazio por default
     * @param int $statusCode - Status de retorno da requisição
     * @param array $details - Passado um message, um objeto data, json etc, retorno genérico
     * @param bool $success - True ou False para sucesso da requisição
     */
    public static function send(string $error = '', int $statusCode = 500, array $details = [], bool $success = false): void
    {
        http_response_code($statusCode);
        header('Content-type: application/json');
        echo json_encode([
            'success' => $success,
            'error' => $error,
            'status_code' => $statusCode,
            'details' => $details
        ]);
    }
}
