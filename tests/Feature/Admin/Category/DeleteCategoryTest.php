<?php

namespace Tests\Feature\Admin\Category;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithKurs;

class DeleteCategoryTest extends TestCaseWithKurs {

    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->qkId = $this->createCategory('Qualikategorie 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteQK() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Qualikategorie 1');
    }

    public function test_shouldValidateDeletedQKUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/qk/' . ($this->qkId + 1));

        // then
        $response->assertStatus(404);
    }
}
