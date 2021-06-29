<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public $code;

    public function __construct($code, $status)
    {
        $this->code = $code;
        $this->status = $status;
    }

    /**
     * Retorna um array com detalhes da exceção para o handler de exceções.
     *
     * @return array
     */
    public function getErrorBody(): array
    {
        switch ($this->code) {
            case 'user_not_found' :
                return $this->buildResponseArray('Usuário não encontrado.');
            default :
                return $this->buildResponseArray('Erro desconhecido.');
        }
    }

    /**
     * Recupera o status da exceção lançada.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Constrói o array com os detalhes da exceção.
     *
     * @param string $message
     *
     * @return array
     */
    public function buildResponseArray($message): array
    {
        return [
            'error' => true,
            'status' => $this->status,
            'code' => $this->code,
            'message' => $message
        ];
    }
}
