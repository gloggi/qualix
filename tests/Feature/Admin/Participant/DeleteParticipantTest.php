<?php

namespace Tests\Feature\Admin\Participant;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithCourse;

class DeleteParticipantTest extends TestCaseWithCourse {

    private $tnId;

    public function setUp(): void {
        parent::setUp();

        $this->tnId = $this->createParticipant('Pföschtli');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteTN() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Pföschtli');
    }

    public function test_shouldValidateDeletedTNUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participants/' . ($this->tnId + 1));

        // then
        $response->assertStatus(404);
    }
}
