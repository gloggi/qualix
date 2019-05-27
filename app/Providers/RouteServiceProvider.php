<?php

namespace App\Providers;

use App\Models\Beobachtung;
use App\Models\Kurs;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('kurs', function($id) {
            /** @var User $user */
            $user = Auth::user();
            return $user->kurse()->findOrFail($id);
        });
        Route::bind('qk', function($id, \Illuminate\Routing\Route $route) {
            /** @var Kurs $kurs */
            $kurs = $route->parameter('kurs');
            return $kurs->qks()->findOrFail($id);
        });
        Route::bind('ma', function($id, \Illuminate\Routing\Route $route) {
            /** @var Kurs $kurs */
            $kurs = $route->parameter('kurs');
            return $kurs->mas()->findOrFail($id);
        });
        Route::bind('block', function($id, \Illuminate\Routing\Route $route) {
            /** @var Kurs $kurs */
            $kurs = $route->parameter('kurs');
            return $kurs->bloecke()->findOrFail($id);
        });
        Route::bind('user', function($id, \Illuminate\Routing\Route $route) {
            /** @var Kurs $kurs */
            $kurs = $route->parameter('kurs');
            return $kurs->users()->findOrFail($id);
        });
        Route::bind('tn', function($id, \Illuminate\Routing\Route $route) {
            /** @var Kurs $kurs */
            $kurs = $route->parameter('kurs');
            return $kurs->tns()->findOrFail($id);
        });
        Route::bind('beobachtung', function($id, \Illuminate\Routing\Route $route) {
            /** @var Kurs $kurs */
            $kurs = $route->parameter('kurs');
            /** @var Beobachtung $beobachtung */
            $beobachtung = Beobachtung::findOrFail($id);
            if ($beobachtung->block->kurs->id !== $kurs->id) {
                throw (new ModelNotFoundException)->setModel(Beobachtung::class, $id);
            }
            return $beobachtung;
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
