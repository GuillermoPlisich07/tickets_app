<?php

namespace App\Exceptions;

use Exception;

class CannotDeleteRootMessageException extends Exception
{
    public function __construct(string $message = "No se puede eliminar el mensaje raiz")
    {
        parent::__construct($message);
    }
}
