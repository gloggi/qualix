<?php

namespace App\Providers;

use App\Services\Import\Blocks\BlockListImporter;
use App\Services\Import\Blocks\BlockListParser;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;
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
        $this->app
            ->when(ECamp2BlockOverviewImporter::class)
            ->needs(BlockListParser::class)
            ->give(ECamp2BlockOverviewParser::class);

        $this->app->bind(ECamp2BlockOverviewParser::class);
        $this->app->bind(ECamp2BlockOverviewImporter::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
