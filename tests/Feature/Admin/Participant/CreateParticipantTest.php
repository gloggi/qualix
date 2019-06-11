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
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayTN() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/tn');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
    }

    public function test_shouldValidateNewTNData_noPfadiname() {
        // given
        $payload = $this->payload;
        unset($payload['scout_name']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/tn', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoTNInCourse() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/tn');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Teilnehmende erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeTNInCourse() {
        // given
        $this->createParticipant();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/tn');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Teilnehmende erfasst.');
    }
}
