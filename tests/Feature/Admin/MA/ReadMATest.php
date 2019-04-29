<?php

namespace Tests\Feature\Admin\MA;

use App\Models\Kurs;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class ReadMATest extends TestCaseWithKurs {

    private $maId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/ma', ['anforderung' => 'Mindestanforderung 1', 'killer' => '1']);
        /** @var User $user */
        $user = Auth::user();
        $this->maId = $user->lastAccessedKurs->mas()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId);

        // then
        $response->assertOk();
        $response->assertSee('Mindestanforderung 1');
    }

    public function test_shouldNotDisplayMA_fromOtherCourseOfSameUser() {
        // given
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => ''])->followRedirects();
        $otherKursId = Kurs::where('name', '=', 'Zweiter Kurs')->firstOrFail()->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/ma/' . $this->maId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayMA_fromOtherUser() {
        // given
        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();
        $this->be($otherUser);
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => '']);

        // when
        $response = $this->get('/kurs/' . $otherUser->lastAccessedKurs->id . '/admin/ma/' . $this->maId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
