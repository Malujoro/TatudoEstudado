<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Força todas as URLs geradas (Vite, assets, links de formulário) a usarem HTTPS em produção
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
