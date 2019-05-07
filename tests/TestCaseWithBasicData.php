<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class TestCaseWithBasicData extends TestCaseWithKurs
{
    protected $tnId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();


        // Create TN to work with
        $this->post('/kurs/' . $this->kursId . '/admin/tn', ['pfadiname' => 'Pflock']);

        // Create Block to work with
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 1', 'datum' => '01.01.2019', 'ma_ids' => null]);

        /** @var User $user */
        $user = Auth::user();
        $this->tnId = $user->lastAccessedKurs->tns()->first()->id;
        $this->blockId = $user->lastAccessedKurs->bloecke()->first()->id;
    }
}
