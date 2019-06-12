<?php

namespace Tests\Feature\Admin\Participant;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateParticipantTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['scout_name' => 'Pflock'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayParticipant() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
    }

    public function test_shouldValidateNewParticipantData_noScoutName() {
        // given
        $payload = $this->payload;
        unset($payload['scout_name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoParticipantInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Teilnehmende erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeParticipantInCourse() {
        // given
        $this->createParticipant();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Teilnehmende erfasst.');
    }
}
