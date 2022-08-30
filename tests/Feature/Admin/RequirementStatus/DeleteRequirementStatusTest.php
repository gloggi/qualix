<?php

namespace Tests\Feature\Admin\RequirementStatus;

use App\Models\Course;
use Tests\TestCaseWithCourse;

class DeleteRequirementStatusTest extends TestCaseWithCourse {

    private $requirementStatusId;

    public function setUp(): void {
        parent::setUp();

        $this->requirementStatusId = $this->createRequirementStatus('Gespraech ausstehend');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteRequirementStatus() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement_status');
        $response->followRedirects();
        $this->assertSeeNone('td', 'Gespraech ausstehend');
    }

    public function test_shouldValidateDeletedRequirementStatusUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/requirement_status/' . ($this->requirementStatusId + 1));

        // then
        $response->assertStatus(404);
    }

    public function test_shouldValidateDeletedRequirementStatusUrl_deletingLastIsNotAllowed() {
        // given
        Course::find($this->courseId)->requirement_statuses()->delete();
        $requirementStatusId = $this->createRequirementStatus('test requirement status');

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/requirement_status/' . $requirementStatusId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement_status');
        $response = $response->followRedirects();
        $response->assertSee('test requirement status');
        $response->assertSee('Der letzte verbleibende Status im Kurs kann nicht gelöscht werden.');
    }
}
