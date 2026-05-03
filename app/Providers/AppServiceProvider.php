<?php

namespace App\Providers;

use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\TicketRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Repositories\TicketRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
