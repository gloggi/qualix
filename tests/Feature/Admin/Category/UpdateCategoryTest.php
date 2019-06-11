<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class UpdateCategoryTest extends TestCaseWithKurs {

    private $payload;
    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->qkId = $this->createCategory('Qualikategorie 1');

        $this->payload = ['name' => 'Geänderter QK-Titel'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateQK() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
        $response->assertDontSee('Qualikategorie 1');
    }

    public function test_shouldValidateNewQKData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewQKData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk/' . ($this->qkId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
