<?php

namespace Tests\Feature\Admin\EvaluationGridTemplate;

use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadEvaluationGridTemplateTest extends TestCaseWithBasicData {

    private $evaluationGridTemplateId;

    public function setUp(): void {
        parent::setUp();

        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate('Unternehmungsplanung');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayEvaluationGridTemplate() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $response->assertOk();
        $response->assertSee('Unternehmungsplanung');
    }

    public function test_shouldNotDisplayEvaluationGrid_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayEvaluationGrid_fromOtherUser() {
        // given
        $otherCourseId = $this->createCourse('Zweiter Kurs', '', false);
        $otherEvaluationGridTemplateId = $this->createEvaluationGridTemplate('Fremdes Beurteilungsraster', $otherCourseId);

        // when
        $response = $this->get('/course/' . $otherCourseId . '/admin/evaluation_grids/' . $otherEvaluationGridTemplateId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
