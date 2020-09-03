<?php

namespace Tests\Feature\Admin\ParticipantGroup;

use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadParticipantGroupTest extends TestCaseWithBasicData
{

    private $participantGroupId;

    public function setUp(): void
    {
        parent::setUp();
        $this->participantGroupId = $this->createParticipantGroup('UN Gruppe 1');
    }


    public function test_shouldRequireLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId . '/edit');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse()
    {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId . '/edit');

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayParticipantGroup()
    {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId . '/edit');

        // then
        $response->assertOk();
        $response->assertSee('UN Gruppe 1');
    }

    public function test_shouldNotDisplayParticipantGroup_fromOtherCourseOfSameUser()
    {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/participantGroups/' . $this->participantGroupId . '/edit');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
