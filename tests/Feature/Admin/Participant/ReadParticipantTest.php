<?php

namespace Tests\Feature\Admin\Participant;

use App\Models\Course;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithCourse;

class ReadParticipantTest extends TestCaseWithCourse {

    private $participantId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pflock');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayParticipant() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSee('Pflock');
    }

    public function test_shouldNotDisplayParticipant_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/participants/' . $this->participantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayParticipant_fromOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $otherParticipantId = Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/participants/' . $otherParticipantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
