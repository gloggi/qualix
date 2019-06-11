<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class CreateCategoryTest extends TestCaseWithKurs {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Qualikategorie 1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayQK() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldValidateNewQKData_noQualikategorieName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/qk', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoQKInCourse() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/qk');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Qualikategorien erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeQKInCourse() {
        // given
        $this->createCategory();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/qk');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Qualikategorien erfasst.');
    }
}
