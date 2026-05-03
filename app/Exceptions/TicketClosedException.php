<?php

namespace App\Exceptions;

use Exception;

class TicketClosedException extends Exception
{
    public function __construct(string $message = "El ticket esta cerrado")
    {
        parent::__construct($message);
    }
}
