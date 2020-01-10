<?php

namespace App\Providers;

use App\Http\ViewComposers\CurrentCourseViewComposer;
use App\Services\ECamp2BlockOverviewImporter;
use App\Services\Translator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    public static $BLOCK_IMPORTER_MAP = [
       'eCamp2BlockOverview' => ECamp2BlockOverviewImporter::class,
    ];

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
        app()->singleton(ECamp2BlockOverviewImporter::class);
    }
}
