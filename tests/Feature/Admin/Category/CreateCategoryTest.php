<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateCategoryTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Kategorie 1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayCategory() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/category');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldValidateNewCategoryData_noCategoryName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoCategoryInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/category');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Kategorien erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeCategoryInCourse() {
        // given
        $this->createCategory();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/category');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Kategorien erfasst.');
    }
}
