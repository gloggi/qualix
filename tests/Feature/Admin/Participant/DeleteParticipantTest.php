<?php

namespace Tests\Feature\Admin\Participant;

use App\Models\Course;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithCourse;

class DeleteParticipantTest extends TestCaseWithCourse {

    private $participantId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pföschtli');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDeleteParticipant() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Pföschtli');
    }

    public function test_shouldValidateDeletedParticipantUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . ($this->participantId + 1));

        // then
        $response->assertStatus(404);
    }
}
