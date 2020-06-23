<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\ParticipantImportRequest;
use App\Services\Import\Participants\MiData\MiDataParticipantListImporter;
use Mockery;
use Tests\TestCase;

class ParticipantImportRequestTest extends TestCase {

    public function test_shouldReturnCorrectImporter_whenTypeIsMiData() {
        // given
        /** @var ParticipantImportRequest $request */
        $request = Mockery::mock(ParticipantImportRequest::class, function ($mock) {
            $mock->shouldReceive('input')->with('source')->andReturn('MiDataParticipantList');
        })->makePartial();

        // when
        $result = $request->getImporter();

        // then
        $this->assertTrue($result instanceof MiDataParticipantListImporter);
    }
}
