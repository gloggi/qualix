<?php

namespace Tests\Feature\TN;

use App\Models\Kurs;
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
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayTN() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId);

        // then
        $response->assertOk();
        $response->assertSee('Pflock');
    }

    public function test_shouldNotDisplayTN_fromOtherCourseOfSameUser() {
        // given
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => ''])->followRedirects();
        $otherKursId = Kurs::where('name', '=', 'Zweiter Kurs')->firstOrFail()->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/tn/' . $this->tnId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayTN_fromOtherUser() {
        // given
        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();
        $this->be($otherUser);
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => '']);

        // when
        $response = $this->get('/kurs/' . $otherUser->lastAccessedKurs->id . '/tn/' . $this->tnId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
