<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MessageController;

Route::apiResource('tickets', TicketController::class);

Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus']);

Route::apiResource('tickets.messages', MessageController::class)->only(['store', 'update', 'destroy']);

