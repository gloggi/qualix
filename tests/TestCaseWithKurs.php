<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class TestCaseWithKurs extends TestCase
{
    protected $kursId;

    public function setUp(): void {
        parent::setUp();


        // Create Kurs to test on
        $this->post('/neuerkurs', ['name' => 'Kursname', 'kursnummer' => 'CH 123-00']);

        /** @var User $user */
        $user = Auth::user();
        $this->kursId = $user->lastAccessedKurs->id;
    }
}
