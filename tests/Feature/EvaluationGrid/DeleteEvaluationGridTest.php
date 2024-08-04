<?php

namespace Tests\Feature\EvaluationGrid;

use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridTemplate;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteEvaluationGridTest extends TestCaseWithBasicData {

    private $evaluationGridTemplateId;

    private $evaluationGridId;

    public function setUp(): void {
        parent::setUp();

        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate();
        $this->evaluationGridId = $this->createEvaluationGrid($this->evaluationGridTemplateId);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDeleteEvaluationGrid() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateDeletedEvaluationGridUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . ($this->evaluationGridId + 1));

        // then
        $response->assertStatus(404);
    }
}
