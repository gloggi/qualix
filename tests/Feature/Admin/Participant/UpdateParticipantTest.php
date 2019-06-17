<?php

namespace Tests\Feature\Admin\Participant;

use App\Models\Course;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateParticipantTest extends TestCaseWithCourse {

    private $payload;
    private $participantId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Qualm');

        $this->payload = ['scout_name' => 'Räuchli'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldUpdateParticipant() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
        $response->assertDontSee('Qualm');
    }

    public function test_shouldValidateNewParticipantData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['scout_name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewParticipantData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . ($this->participantId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
