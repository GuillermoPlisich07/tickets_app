<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Tickets API', version: '1.0.0', description: 'API REST para gestión de tickets de soporte')]
#[OA\Server(url: 'http://localhost:8000')]
abstract class Controller
{
    //
}
