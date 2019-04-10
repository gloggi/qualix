<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class CreateQKTest extends TestCaseWithKurs {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['quali_kategorie' => 'Qualikategorie 1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayQK() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['quali_kategorie']);
    }

    public function test_shouldValidateNewQKData_noQualiKategorieName() {
        // given
        $payload = $this->payload;
        unset($payload['quali_kategorie']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }
}
