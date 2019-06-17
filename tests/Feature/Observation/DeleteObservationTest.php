<?php

namespace Tests\Feature\Observation;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteObservationTest extends TestCaseWithBasicData {

    private $observationId;

    public function setUp(): void {
        parent::setUp();

        $this->observationId = $this->createObservation('hat gut mitgemacht');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/observation/' . $this->observationId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteBeobachtung() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/observation/' . $this->observationId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateDeletedBeobachtungUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/observation/' . ($this->observationId + 1));

        // then
        $response->assertStatus(404);
    }
}
