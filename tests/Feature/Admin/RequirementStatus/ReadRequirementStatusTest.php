<?php

namespace Tests\Feature\Admin\RequirementStatus;

use App\Models\RequirementStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithCourse;

class ReadRequirementStatusTest extends TestCaseWithCourse {

    private $requirementStatusId;

    public function setUp(): void {
        parent::setUp();

        $this->requirementStatusId = $this->createRequirementStatus('Gespräch ausstehend');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayRequirementStatus() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId);

        // then
        $response->assertOk();
        $response->assertSee('Gespräch ausstehend');
    }

    public function test_shouldNotDisplayRequirementStatus_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/requirement_status/' . $this->requirementStatusId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayRequirementStatus_fromOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $otherRequirementStatusId = RequirementStatus::create(['course_id' => $otherKursId, 'name' => 'all done', 'color' => 'green', 'icon' => 'check-double'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/requirement_status/' . $otherRequirementStatusId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
