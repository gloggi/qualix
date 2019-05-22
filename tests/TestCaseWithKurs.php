<?php

namespace Tests;

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
}
