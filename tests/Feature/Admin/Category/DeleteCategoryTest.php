<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithCourse;

class DeleteCategoryTest extends TestCaseWithCourse {

    private $categoryId;

    public function setUp(): void {
        parent::setUp();

        $this->categoryId = $this->createCategory('Kategorie 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/category/' . $this->categoryId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteCategory() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/category/' . $this->categoryId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/category');
        $response->followRedirects();
        $this->assertSeeNone('td', 'Kategorie 1');
    }

    public function test_shouldValidateDeletedCategoryUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/category/' . ($this->categoryId + 1));

        // then
        $response->assertStatus(404);
    }
}
