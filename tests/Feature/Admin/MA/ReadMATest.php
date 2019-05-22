<?php

namespace Tests\Feature\Admin\MA;

use App\Models\MA;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithKurs;

class ReadMATest extends TestCaseWithKurs {

    private $maId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/ma', ['anforderung' => 'Mindestanforderung 1', 'killer' => '1']);
        $this->maId = $this->user()->lastAccessedKurs->mas()->first()->id;
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
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/ma/' . $this->maId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayMA_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherMAId = MA::create(['kurs_id' => $otherKursId, 'anforderung' => 'Mindestanforderung 1', 'killer' => '1'])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/ma/' . $otherMAId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
