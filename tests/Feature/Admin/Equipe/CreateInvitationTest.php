<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class CreateInvitationTest extends TestCaseWithKurs {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['email' => 'neues-mitglied@equipe.com'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/invitation', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayInvitation() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/invitation', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['email']);
    }

    public function test_shouldValidateNewInvitationData_noEmail() {
        // given
        $payload = $this->payload;
        unset($payload['email']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/invitation', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewInvitationData_invalidEmail() {
        // given
        $payload = $this->payload;
        $payload['email'] = 'so en chabis';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/invitation', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoInvitationInCourse() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/equipe', $this->payload);

        // then
        $response->assertStatus(200);
        $response->assertSee('Momentan sind keine Einladungen offen.');
    }

    public function test_shouldNotShowMessage_whenSomeQKInCourse() {
        // given
        $this->post('/kurs/' . $this->kursId . '/admin/invitation', $this->payload);

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/equipe', $this->payload);

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Momentan sind keine Einladungen offen.');
    }
}
