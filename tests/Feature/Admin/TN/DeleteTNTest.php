<?php

namespace Tests\Feature\Admin\TN;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithKurs;

class DeleteTNTest extends TestCaseWithKurs {

    private $tnId;

    public function setUp(): void {
        parent::setUp();

        $this->tnId = $this->createTN('Pföschtli');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/tn/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteTN() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/tn/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/tn');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Pföschtli');
    }

    public function test_shouldValidateDeletedTNUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/tn/' . ($this->tnId + 1));

        // then
        $response->assertStatus(404);
    }
}
