<?php

namespace Tests;

use App\Models\Observation;

abstract class TestCaseWithBasicData extends TestCaseWithKurs
{
    protected $tnId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->tnId = $this->createTN('Pflock');
        $this->blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null);
    }

    protected function createBeobachtung($kommentar = 'hat gut mitgemacht', $bewertung = 1, $maIds = [], $qkIds = [], $blockId = null, $tnId = null, $userId = null) {
        $observation = Observation::create(['user_id' => ($userId !== null ? $userId : $this->user()->id), 'participant_id' => ($tnId !== null ? $tnId : $this->tnId), 'block_id' => ($blockId !== null ? $blockId : $this->blockId), 'content' => $kommentar, 'impression' => $bewertung]);
        $observation->requirements()->attach($maIds);
        $observation->categories()->attach($qkIds);
        return $observation->id;
    }
}
