<?php

namespace App\Providers;

use App\Services\Translator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->extend('translator', function($laravelTranslator) {
            return new Translator($laravelTranslator);
        });

        $this->app->singleton('csp-nonce', function () {
            return Str::random(32);
        });
        Blade::directive('nonce', function () {
            return '<?php echo "nonce=\"" . csp_nonce() . "\""; ?>';
        });
    }
}
