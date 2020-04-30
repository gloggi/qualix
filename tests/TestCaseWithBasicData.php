<?php

namespace Tests;

use App\Models\Observation;
use Illuminate\Support\Arr;

abstract class TestCaseWithBasicData extends TestCaseWithCourse
{
    protected $participantId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pflock');
        $this->blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null);
    }

    protected function createObservation($content = 'hat gut mitgemacht', $impression = 1, $requirementId = [], $categoryIds = [], $blockId = null, $participantIds = null, $userId = null) {
        $observation = Observation::create(['user_id' => ($userId !== null ? $userId : $this->user()->id), 'block' => ($blockId !== null ? $blockId : $this->blockId), 'content' => $content, 'impression' => $impression]);
        $observation->requirements()->attach($requirementId);
        $observation->categories()->attach($categoryIds);
        $observation->participants()->attach(($participantIds !== null ? Arr::wrap($participantIds) : [$this->participantId]));
        return $observation->id;
    }
}
