<?php

namespace Tests;

use App\Models\Observation;

abstract class TestCaseWithBasicData extends TestCaseWithCourse
{
    protected $participantId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pflock');
        $this->blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null);
    }

    protected function createObservation($content = 'hat gut mitgemacht', $impression = 1, $requirementId = [], $categoryIds = [], $blockId = null, $participantId = null, $userId = null) {
        $observation = Observation::create(['user_id' => ($userId !== null ? $userId : $this->user()->id), 'participant_id' => ($participantId !== null ? $participantId : $this->participantId), 'block_id' => ($blockId !== null ? $blockId : $this->blockId), 'content' => $content, 'impression' => $impression]);
        $observation->requirements()->attach($requirementId);
        $observation->categories()->attach($categoryIds);
        return $observation->id;
    }
}
