<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\ProductService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, function ($app) {
            return new UserRepository($app->make(User::class));
        });

        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService($app->make(ProductRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
