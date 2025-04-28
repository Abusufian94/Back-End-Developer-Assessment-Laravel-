<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;

class CategoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService($app->make(CategoryRepository::class));
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
