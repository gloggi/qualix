<?php

namespace Tests\Unit\Providers;

use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;
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

}
