<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class E2EServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->environment('testing')) {
            Route::middleware('web')
                ->group(base_path('routes/e2e.php'));
        }
    }
}
