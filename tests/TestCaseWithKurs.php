<?php

namespace Tests;

use App\Models\Block;
use App\Models\TN;

abstract class TestCaseWithKurs extends TestCase
{
    protected $kursId;

    public function setUp(): void {
        parent::setUp();

        $this->kursId = $this->createKurs();
    }

    protected function createTN($pfadiname = 'Pflock', $kursId = null) {
        return TN::create(['kurs_id' => ($kursId !== null ? $kursId : $this->kursId), 'pfadiname' => $pfadiname])->id;
    }

    protected function createBlock($blockname = 'Block 1', $fullBlockNumber = '1.1', $datum = '01.01.2019', $ma_ids = null, $kursId = null) {
        return Block::create(['kurs_id' => ($kursId !== null ? $kursId : $this->kursId), 'full_block_number' => $fullBlockNumber, 'blockname' => $blockname, 'datum' => $datum, 'ma_ids' => $ma_ids])->id;
    }
}
