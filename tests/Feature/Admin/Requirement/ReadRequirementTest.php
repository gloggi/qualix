<?php

namespace Tests\Feature\Admin\Requirement;

use App\Models\Requirement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithCourse;

class ReadRequirementTest extends TestCaseWithCourse {

    private $maId;

    public function setUp(): void {
        parent::setUp();

        $this->maId = $this->createRequirement('Mindestanforderung 1', true);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement/' . $this->maId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayMA() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement/' . $this->maId);

        // then
        $response->assertOk();
        $response->assertSee('Mindestanforderung 1');
    }

    public function test_shouldNotDisplayMA_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/requirement/' . $this->maId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayMA_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherMAId = Requirement::create(['course_id' => $otherKursId, 'content' => 'Mindestanforderung 1', 'mandatory' => '1'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/requirement/' . $otherMAId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
