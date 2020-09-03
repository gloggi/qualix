<?php

namespace Tests\Feature\Observation;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
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

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/observation/' . $this->observationId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDeleteObservation() {
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

    public function test_shouldValidateDeletedObservationUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/observation/' . ($this->observationId + 1));

        // then
        $response->assertStatus(404);
    }
}
