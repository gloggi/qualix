<?php

namespace Tests\Feature\Participant;

use App\Models\Course;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadParticipantTest extends TestCaseWithBasicData {

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayParticipant() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSee('Pflock');
    }

    public function test_shouldNotDisplayParticipant_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/participants/' . $this->participantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayParticipant_fromOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $otherParticipantId = Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/participants/' . $otherParticipantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoObservationForParticipant() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertStatus(200);
        $response->assertSee('Keine Beobachtungen gefunden.');
    }

    public function test_shouldNotShowMessage_whenSomeObservationForParticipant() {
        // given
        $this->createObservation();

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Keine Beobachtungen gefunden.');
    }

    public function test_shouldEscapeHTML_whenDisplayingParticipant() {
        // given
        $participantName = '<b>Bar</b>i\'"';
        $participantId = $this->createParticipant($participantName);
        $userName = 'Co<i>si</i>nus\'"';
        $this->createUser(['name' => $userName])->courses()->attach($this->courseId);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $participantId);

        // then
        $response->assertOk();
        $response->assertDontSee($participantName);
        $response->assertSee(htmlspecialchars($participantName, ENT_QUOTES));
        $response->assertDontSee($userName);
        $response->assertSee(htmlspecialchars($userName, ENT_QUOTES));
    }
}
