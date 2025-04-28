<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderService;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OrderRepository::class),
                $app->make(ProductRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
