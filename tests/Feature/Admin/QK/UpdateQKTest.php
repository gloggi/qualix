<?php

namespace Tests\Feature\Admin\QK;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class UpdateQKTest extends TestCaseWithKurs {

    private $payload;
    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->qkId = $this->createQK('Qualikategorie 1');

        $this->payload = ['quali_kategorie' => 'Geänderter QK-Titel'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateQK() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['quali_kategorie']);
        $response->assertDontSee('Qualikategorie 1');
    }

    public function test_shouldValidateNewQKData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['quali_kategorie']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewQKData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/qk/' . ($this->qkId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
