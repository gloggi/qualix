<?php

namespace Tests\Feature\Admin\Participant;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateParticipantTest extends TestCaseWithCourse {

    private $payload;
    private $tnId;

    public function setUp(): void {
        parent::setUp();

        $this->tnId = $this->createParticipant('Qualm');

        $this->payload = ['scout_name' => 'Räuchli'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn/' . $this->tnId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateTN() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn/' . $this->tnId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/tn');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
        $response->assertDontSee('Qualm');
    }

    public function test_shouldValidateNewTNData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['scout_name']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn/' . $this->tnId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewTNData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn/' . ($this->tnId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
