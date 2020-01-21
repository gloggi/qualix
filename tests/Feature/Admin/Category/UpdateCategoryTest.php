<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateCategoryTest extends TestCaseWithCourse {

    private $payload;
    private $categoryId;

    public function setUp(): void {
        parent::setUp();

        $this->categoryId = $this->createCategory('Kategorie 1');

        $this->payload = ['name' => 'Geänderter Kategorie-Titel'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category/' . $this->categoryId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateCategory() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category/' . $this->categoryId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/category');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
        $response->assertDontSee('Kategorie 1');
    }

    public function test_shouldValidateNewCategoryData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category/' . $this->categoryId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCategoryData_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Kategorienname 1Extrem langer Kategorienname 2Extrem langer Kategorienname 3Extrem langer Kategorienname 4Extrem langer Kategorienname 5Extrem langer Kategorienname 6Extrem langer Kategorienname 7Extrem langer Kategorienname 8Extrem langer Kategorienname 9Extrem langer Kategorienname 10Extrem langer Kategorienname 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category/' . $this->categoryId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCategoryData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/category/' . ($this->categoryId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
