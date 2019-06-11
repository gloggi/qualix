<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithKurs;

class DeleteCategoryTest extends TestCaseWithKurs {

    private $categoryId;

    public function setUp(): void {
        parent::setUp();

        $this->categoryId = $this->createCategory('Kategorie 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/qk/' . $this->categoryId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteCategory() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/qk/' . $this->categoryId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Kategorie 1');
    }

    public function test_shouldValidateDeletedCategoryUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/qk/' . ($this->categoryId + 1));

        // then
        $response->assertStatus(404);
    }
}
