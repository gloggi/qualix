<?php

namespace Tests\Feature\Admin\TN;

use App\Models\TN;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class ReadTNTest extends TestCaseWithKurs {

    private $tnId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/tn', ['pfadiname' => 'Pflock']);
        /** @var User $user */
        $user = Auth::user();
        $this->tnId = $user->lastAccessedKurs->tns()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayTN() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId);

        // then
        $response->assertOk();
        $response->assertSee('Pflock');
    }

    public function test_shouldNotDisplayTN_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/tn/' . $this->tnId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayTN_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherTNId = TN::create(['kurs_id' => $otherKursId, 'pfadiname' => 'Pflock'])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/tn/' . $otherTNId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
