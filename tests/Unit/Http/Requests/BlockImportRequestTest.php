<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\BlockImportRequest;
use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewImporter;
use Mockery;
use Tests\TestCase;

class BlockImportRequestTest extends TestCase {

    public function test_shouldReturnCorrectImporter_whenTypeIsECamp2() {
        // given
        /** @var BlockImportRequest $request */
        $request = Mockery::mock(BlockImportRequest::class, function ($mock) {
            $mock->shouldReceive('input')->with('source')->andReturn('eCamp2BlockOverview');
        })->makePartial();

        // when
        $result = $request->getImporter();

        // then
        $this->assertTrue($result instanceof ECamp2BlockOverviewImporter);
    }
}
