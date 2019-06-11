<?php

namespace Tests\Feature\TN;

use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadTNTest extends TestCaseWithBasicData {

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayTN() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSee('Pflock');
    }

    public function test_shouldNotDisplayTN_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/tn/' . $this->participantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayTN_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherTNId = Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock'])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/tn/' . $otherTNId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoBeobachtungForTN() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId);

        // then
        $response->assertStatus(200);
        $response->assertSee('Keine Beobachtungen gefunden.');
    }

    public function test_shouldNotShowMessage_whenSomeBeobachtungForTN() {
        // given
        $this->createBeobachtung();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId);

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Keine Beobachtungen gefunden.');
    }
}
