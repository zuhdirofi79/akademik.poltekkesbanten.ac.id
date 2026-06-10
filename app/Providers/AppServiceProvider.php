<?php

namespace App\Providers;

use App\Models\ServiceType;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('pagination.simple');
        Paginator::defaultSimpleView('pagination.simple');

        View::composer('layouts.app', function ($view) {
            $view->with('navServices', ServiceType::active()->orderBy('sort_order')->orderBy('id')->get());
        });
    }
}
