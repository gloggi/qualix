<?php

namespace Tests;

use App\Models\Observation;

abstract class TestCaseWithBasicData extends TestCaseWithKurs
{
    protected $participantId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createTN('Pflock');
        $this->blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null);
    }

    protected function createBeobachtung($kommentar = 'hat gut mitgemacht', $impression = 1, $requirementId = [], $categoryIds = [], $blockId = null, $tnId = null, $userId = null) {
        $observation = Observation::create(['user_id' => ($userId !== null ? $userId : $this->user()->id), 'participant_id' => ($tnId !== null ? $tnId : $this->participantId), 'block_id' => ($blockId !== null ? $blockId : $this->blockId), 'content' => $kommentar, 'impression' => $impression]);
        $observation->requirements()->attach($requirementId);
        $observation->categories()->attach($categoryIds);
        return $observation->id;
    }
}
