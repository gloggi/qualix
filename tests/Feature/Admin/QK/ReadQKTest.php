<?php

namespace Tests\Feature\Admin\QK;

use App\Models\QK;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithKurs;

class ReadQKTest extends TestCaseWithKurs {

    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->qkId = $this->createQK('Qualikategorie 1');
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
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/qk/' . $this->qkId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayQK_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherQKId = QK::create(['kurs_id' => $otherKursId, 'quali_kategorie' => 'Qualikategorie 1'])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/qk/' . $otherQKId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
