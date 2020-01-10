<?php

namespace App\Providers;

use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
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
