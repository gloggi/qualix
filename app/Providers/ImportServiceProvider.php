<?php

namespace App\Providers;

use App\Services\Import\Blocks\BlockListImporter;
use App\Services\Import\Blocks\BlockListParser;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;

use App\Services\Import\Participants\ParticipantListParser;
use App\Services\Import\Participants\MiData\MiDataParticipantListImporter;
use App\Services\Import\Participants\MiData\MiDataParticipantListParser;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    public static $BLOCK_IMPORTER_MAP = [
       'eCamp2BlockOverview' => ECamp2BlockOverviewImporter::class,
    ];
    public static $PARTICIPANT_IMPORTER_MAP = [
        'MiDataParticipantList' => MiDataParticipantListImporter::class,
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Ecamp2 Block import
        $this->app
            ->when(ECamp2BlockOverviewImporter::class)
            ->needs(BlockListParser::class)
            ->give(ECamp2BlockOverviewParser::class);

        $this->app->bind(ECamp2BlockOverviewParser::class);
        $this->app->bind(ECamp2BlockOverviewImporter::class);

        //MiData Participant import
        $this->app
            ->when(MiDataParticipantListImporter::class)
            ->needs(ParticipantListParser::class)
            ->give(MiDataParticipantListParser::class);

        $this->app->bind(MiDataParticipantListParser::class);
        $this->app->bind(MiDataParticipantListImporter::class);


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
