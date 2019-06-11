<?php

namespace Tests\Feature\Beobachtung;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteBeobachtungTest extends TestCaseWithBasicData {

    private $observationId;

    public function setUp(): void {
        parent::setUp();

        $this->observationId = $this->createBeobachtung('hat gut mitgemacht');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteBeobachtung() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/tn/' . $this->tnId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateDeletedBeobachtungUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/beobachtungen/' . ($this->observationId + 1));

        // then
        $response->assertStatus(404);
    }
}
