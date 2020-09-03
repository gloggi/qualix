<?php

namespace Tests\Feature\Admin\ParticipantGroup;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteParticipantGroupTest extends TestCaseWithBasicData {

    private $participantGroupId;

    public function setUp(): void {
        parent::setUp();
        $this->participantGroupId = $this->createParticipantGroup('UN Gruppe 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDeleteParticipantGroup() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('UN Gruppe 1');
    }

    public function test_shouldValidateDeletedParticipantGroupUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/participantGroups/' . ($this->participantGroupId + 1));

        // then
        $response->assertStatus(404);
    }
}
