<?php

namespace Tests\Feature\Admin\EvaluationGridTemplate;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteEvaluationGridTemplateTest extends TestCaseWithBasicData {

    private $evaluationGridTemplateId;

    public function setUp(): void {
        parent::setUp();

        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate('Unternehmungsplanung');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteEvaluationGridTemplate() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Das Beurteilungsraster "Unternehmungsplanung" wurde erfolgreich gelöscht.');

        $response = $this->get('/course/' . $this->courseId . '/admin/evaluation_grids');
        $response->assertDontSee('Unternehmungsplanung');
    }

    public function test_shouldWorkInArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Das Beurteilungsraster "Unternehmungsplanung" wurde erfolgreich gelöscht.');

        $response = $this->get('/course/' . $this->courseId . '/admin/evaluation_grids');
        $response->assertDontSee('Unternehmungsplanung');
    }

    public function test_shouldValidateDeletedEvaluationGridUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/evaluation_grids/' . ($this->evaluationGridTemplateId + 1));

        // then
        $response->assertStatus(404);
    }
}
