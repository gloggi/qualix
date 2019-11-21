<?php

namespace Tests\Feature\Admin\Block;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithCourse;

class DeleteBlockTest extends TestCaseWithCourse {

    private $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->blockId = $this->createBlock('Block 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteBlock() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        $response->followRedirects();
        $this->assertSeeNone('td', 'Block 1');
    }

    public function test_shouldValidateDeletedBlockUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/blocks/' . ($this->blockId + 1));

        // then
        $response->assertStatus(404);
    }
}
