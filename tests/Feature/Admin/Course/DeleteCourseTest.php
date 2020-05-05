<?php

namespace Tests\Feature\Admin\Course;

use App\Models\Block;
use App\Models\Category;
use App\Models\Course;
use App\Models\Invitation;
use App\Models\Observation;
use App\Models\Participant;
use App\Models\Requirement;
use App\Models\RequirementDetail;
use App\Models\Trainer;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCaseWithBasicData;

class DeleteCourseTest extends TestCaseWithBasicData {

    public function setUp(): void {
        parent::setUp();
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteCourse() {
        // given
        $courseName = 'Test deletion of course';
        $courseId = $this->createCourse($courseName);

        // when
        $response = $this->delete('/course/' . $courseId . '/admin');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response->followRedirects();
        $this->assertSeeAllInOrder('b-form-select#global-course-select b-form-select-option', ['Kursname']);
    }

    public function test_shouldDeleteRelatedData() {
        // given
        $categoryId = $this->createCategory();
        $requirementId = $this->createRequirement();
        Block::find($this->blockId)->requirements()->attach($requirementId);
        $this->createObservation('Beobachtung', 1, $requirementId, $categoryId);
        $this->post('/course/' . $this->courseId . '/admin/invitation', ['email' => 'invited@test.com']);
        $numBlocks = Block::all()->count();
        $numCategories = Category::all()->count();
        $numCourses = Course::all()->count();
        $numInvitations = Invitation::all()->count();
        $numObservations = Observation::all()->count();
        $numParticipants = Participant::all()->count();
        $numRequirements = Requirement::all()->count();
        $numTrainers = Trainer::all()->count();
        $numBlocksRequirements = DB::table('blocks_requirements')->count();
        $numObservationsCategories = DB::table('observations_categories')->count();
        $numObservationsRequirements = DB::table('observations_requirements')->count();

        // when
        $this->delete('/course/' . $this->courseId . '/admin');

        // then
        $this->assertEquals($numBlocks - 1, Block::all()->count(), 'All blocks of course should have been removed from DB');
        $this->assertEquals($numCategories - 1, Category::all()->count(), 'All categories of course should have been removed from DB');
        $this->assertEquals($numCourses - 1, Course::all()->count(), 'Course should have been removed from DB');
        $this->assertEquals($numInvitations - 1, Invitation::all()->count(), 'All invitations of course should have been removed from DB');
        $this->assertEquals($numObservations - 1, Observation::all()->count(), 'All observations of course should have been removed from DB');
        $this->assertEquals($numParticipants - 1, Participant::all()->count(), 'All participants of course should have been removed from DB');
        $this->assertEquals($numRequirements - 1, Requirement::all()->count(), 'All requirements of course should have been removed from DB');
        $this->assertEquals(0, RequirementDetail::all()->count(), 'All requirement details of course should have been removed from DB');
        $this->assertEquals($numTrainers - 1, Trainer::all()->count(), 'All trainers should have been removed from course in DB');
        $this->assertEquals($numBlocksRequirements - 1, DB::table('blocks_requirements')->count(), 'All blocks_requirements entries of course should have been removed from DB');
        $this->assertEquals($numObservationsCategories - 1, DB::table('observations_categories')->count(), 'All observations_categories entries of course should have been removed from DB');
        $this->assertEquals($numObservationsRequirements - 1, DB::table('observations_requirements')->count(), 'All observations_requirements entries of course should have been removed from DB');
    }

    public function test_shouldDeleteImagesOfParticipantsFromStorage() {
        // given
        $imageUrl = '/some/public/asset/url.jpg';
        Participant::find($this->participantId)->update(['image_url' => $imageUrl]);
        Storage::fake();
        Storage::shouldReceive('delete')->with($imageUrl)->once();

        // when
        $this->delete('/course/' . $this->courseId . '/admin');

        // then
    }

    public function test_shouldShowEscapedNotice_afterDeletingCourse() {
        // given
        $courseName = '<b>Course name</b> with \'some" formatting';
        $courseId = $this->createCourse($courseName);

        // when
        $response = $this->delete('/course/' . $courseId . '/admin')->followRedirects();

        // then
        $response->assertDontSee($courseName);
        $response->assertSee(htmlspecialchars($courseName, ENT_QUOTES));
    }
}
