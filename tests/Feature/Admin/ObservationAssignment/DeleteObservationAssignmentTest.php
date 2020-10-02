<?php

namespace Tests\Feature\Admin\ObservationAssignment;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteObservationAssignmentTest extends TestCaseWithBasicData {

    private $observationAssignmentId;

    public function setUp(): void {
        parent::setUp();
        $this->observationAssignmentId = $this->createObservationAssignment('Beobachtungsauftrag 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDeleteObservationAssignment() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('UN Gruppe 1');
    }

    public function test_shouldValidateDeletedObservationAssignmentUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/observationAssignments/' . ($this->observationAssignmentId + 1));

        // then
        $response->assertStatus(404);
    }
}
