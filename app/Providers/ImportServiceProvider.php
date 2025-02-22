<?php

namespace App\Providers;

use App\Services\Import\Blocks\BlockListParser;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;
use App\Services\Import\Blocks\ECamp3\ECamp3BlockOverviewImporter;
use App\Services\Import\Blocks\ECamp3\ECamp3BlockOverviewParser;
use App\Services\Import\Participants\MiData\MiDataParticipantListImporter;
use App\Services\Import\Participants\MiData\MiDataParticipantListParser;
use App\Services\Import\Participants\ParticipantListParser;
use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ImportServiceProvider extends ServiceProvider {
    public static $BLOCK_IMPORTER_MAP = [
        'eCamp2BlockOverview' => ECamp2BlockOverviewImporter::class,
        'eCamp3BlockOverview' => ECamp3BlockOverviewImporter::class,

    ];
    public static $PARTICIPANT_IMPORTER_MAP = [
        'MiDataParticipantList' => MiDataParticipantListImporter::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void {
        // eCamp2 Block import
        $this->app
            ->when(ECamp2BlockOverviewImporter::class)
            ->needs(BlockListParser::class)
            ->give(ECamp2BlockOverviewParser::class);

        $this->app->bind(ECamp2BlockOverviewParser::class);
        $this->app->bind(ECamp3BlockOverviewImporter::class);

        // eCamp3 Block import
        $this->app
            ->when(ECamp3BlockOverviewImporter::class)
            ->needs(BlockListParser::class)
            ->give(ECamp3BlockOverviewParser::class);

        $this->app->bind(ECamp3BlockOverviewParser::class);
        $this->app->bind(ECamp3BlockOverviewImporter::class);

        //MiData Participant import
        $this->app
            ->when(MiDataParticipantListImporter::class)
            ->needs(ParticipantListParser::class)
            ->give(MiDataParticipantListParser::class);
        $this->app
            ->extend(Csv::class, function (Csv $csvReader) {
                return $csvReader->setInputEncoding('ISO-8859-1');
            });

        $this->app->bind(MiDataParticipantListParser::class);
        $this->app->bind(MiDataParticipantListImporter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
    }
}
