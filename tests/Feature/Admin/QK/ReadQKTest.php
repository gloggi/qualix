<?php

namespace Tests\Feature\Admin\QK;

use App\Models\Kurs;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class ReadQKTest extends TestCaseWithKurs {

    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/qk', ['quali_kategorie' => 'Qualikategorie 1']);
        /** @var User $user */
        $user = Auth::user();
        $this->qkId = $user->lastAccessedKurs->qks()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayQK() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertOk();
        $response->assertSee('Qualikategorie 1');
    }

    public function test_shouldNotDisplayQK_fromOtherCourseOfSameUser() {
        // given
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => ''])->followRedirects();
        $otherKursId = Kurs::where('name', '=', 'Zweiter Kurs')->firstOrFail()->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/qk/' . $this->qkId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayQK_fromOtherUser() {
        // given
        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();
        $this->be($otherUser);
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => '']);

        // when
        $response = $this->get('/kurs/' . $otherUser->lastAccessedKurs->id . '/admin/qk/' . $this->qkId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
