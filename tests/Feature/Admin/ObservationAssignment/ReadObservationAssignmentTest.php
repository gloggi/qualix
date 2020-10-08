<?php

namespace Tests\Feature\Admin\ObservationAssignment;

use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadObservationAssignmentTest extends TestCaseWithBasicData
{

    private $observationAssignmentId;

    public function setUp(): void
    {
        parent::setUp();
        $this->observationAssignmentId = $this->createObservationAssignment('Auftrag 1');
    }


    public function test_shouldRequireLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse()
    {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayObservationAssignment()
    {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $response->assertOk();
        $response->assertSee('Auftrag 1');
    }

    public function test_shouldNotDisplayObservationAssignment_fromOtherCourseOfSameUser()
    {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/observationAssignments/' . $this->observationAssignmentId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
