<?php

namespace Tests\Unit\Providers;

use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;
use App\Services\Import\Participants\MiData\MiDataParticipantListImporter;
use App\Services\Import\Participants\MiData\MiDataParticipantListParser;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Tests\TestCase;

class ImportServiceProviderTest extends TestCase {

    public function test_shouldUseCorrectParserForECamp2BlockOverviewImporter() {
        // given

        // when
        /** @var ECamp2BlockOverviewImporter $resolved */
        $resolved = $this->app->make(ECamp2BlockOverviewImporter::class);

        // then
        $getParser = function () { return $this->parser; };
        $this->assertInstanceOf(ECamp2BlockOverviewParser::class, $getParser->call($resolved));
    }

    public function test_shouldUseCorrectParserForMiDataParticipantListImporter() {
        // given

        // when
        /** @var MiDataParticipantListImporter $resolved */
        $resolved = $this->app->make(MiDataParticipantListImporter::class);

        // then
        $getParser = function () { return $this->parser; };
        $this->assertInstanceOf(MiDataParticipantListParser::class, $getParser->call($resolved));
    }

    /**
     * MiData participant import needs ISO-8859-1 encoding
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function test_shouldSetCsvEncodingToISO88591() {
        // given

        // when
        /** @var Csv $resolved */
        $resolved = $this->app->make(Csv::class);

        // then
        $this->assertEquals('ISO-8859-1', $resolved->getInputEncoding());
    }

}
