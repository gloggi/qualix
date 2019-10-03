<?php

namespace Tests\Feature\Admin\Requirement;

use App\Models\Requirement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithCourse;

class ReadRequirementTest extends TestCaseWithCourse {

    private $requirementId;

    public function setUp(): void {
        parent::setUp();

        $this->requirementId = $this->createRequirement('Mindestanforderung 1', true);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement/' . $this->requirementId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayMA() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement/' . $this->requirementId);

        // then
        $response->assertOk();
        $response->assertSee('Mindestanforderung 1');
    }

    public function test_shouldNotDisplayMA_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/requirement/' . $this->requirementId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayMA_fromOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $otherRequirementId = Requirement::create(['course_id' => $otherKursId, 'content' => 'Mindestanforderung 1', 'mandatory' => '1'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/requirement/' . $otherRequirementId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
