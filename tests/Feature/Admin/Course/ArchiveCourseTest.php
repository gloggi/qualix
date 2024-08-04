<?php

namespace Tests\Feature\Admin\Course;

use App\Models\Block;
use App\Models\Category;
use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridRow;
use App\Models\EvaluationGridRowTemplate;
use App\Models\EvaluationGridTemplate;
use App\Models\Invitation;
use App\Models\Observation;
use App\Models\ObservationAssignment;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Models\Requirement;
use App\Models\RequirementDetail;
use App\Models\Trainer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class ArchiveCourseTest extends TestCaseWithBasicData {

    public function setUp(): void {
        parent::setUp();
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/archive');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/archive');

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldArchiveCourse() {
        // given
        $courseName = '000 Test archivation of course';
        $courseId = $this->createCourse($courseName);

        // when
        $response = $this->post('/course/' . $courseId . '/admin/archive');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        // Laravel bug: The Auth::user used in the application is cached and will not get the updated course list in this test, unless we refresh it manually
        $this->refreshUser();
        $response = $this->get('/course/' . $courseId . '/admin');
        $this->assertSeeAllInOrder('b-form-select#global-course-select b-form-select-option', ['Kursname', $courseName]);
        $this->assertSeeAllInOrder('b-form-select#global-course-select b-form-select-option-group[label="Archiviert"] b-form-select-option', [$courseName]);
        $response->assertDontSee('Überblick');
    }

    public function test_shouldDeleteRelatedData() {
        // given
        $categoryId = $this->createCategory();
        $requirementId = $this->createRequirement();
        Block::find($this->blockId)->requirements()->attach($requirementId);
        $this->createObservation('Beobachtung', 1, $requirementId, $categoryId);
        $this->createObservationAssignment();
        $this->createParticipantGroup();
        $this->post('/course/' . $this->courseId . '/admin/invitation', ['email' => 'invited@test.com']);
        $numEvaluationGridRows = EvaluationGridRow::all()->count();
        $numEvaluationGridsParticipants = DB::table('evaluation_grids_participants')->count();
        $this->createEvaluationGrid();
        $numBlocks = Block::all()->count();
        $numCategories = Category::all()->count();
        $numCourses = Course::all()->count();
        $numInvitations = Invitation::all()->count();
        $numObservations = Observation::all()->count();
        $numParticipants = Participant::all()->count();
        $numRequirements = Requirement::all()->count();
        $numRequirementDetails = RequirementDetail::all()->count();
        $numObservationAssignments = ObservationAssignment::all()->count();
        $numParticipantGroups = ParticipantGroup::all()->count();
        $numTrainers = Trainer::all()->count();
        $numEvaluationGridTemplates = EvaluationGridTemplate::all()->count();
        $numEvaluationGridRowTemplates = EvaluationGridRowTemplate::all()->count();
        $numEvaluationGrids = EvaluationGrid::all()->count();
        $numBlocksRequirements = DB::table('blocks_requirements')->count();
        $numObservationsCategories = DB::table('observations_categories')->count();
        $numObservationsRequirements = DB::table('observations_requirements')->count();
        $numObservationAssignmentBlocks = DB::table('observation_assignment_blocks')->count();
        $numObservationAssignmentParticipants = DB::table('observation_assignment_participants')->count();
        $numObservationAssignmentUsers = DB::table('observation_assignment_users')->count();
        $numParticipantGroupsParticipants = DB::table('participant_groups_participants')->count();
        $numEvaluationGridTemplatesBlocks = DB::table('evaluation_grid_templates_blocks')->count();
        $numEvaluationGridTemplatesRequirements = DB::table('evaluation_grid_templates_requirements')->count();

        // when
        $this->post('/course/' . $this->courseId . '/admin/archive');

        // then
        $this->assertEquals($numBlocks, Block::all()->count(), 'All blocks should have remained in course');
        $this->assertEquals($numCategories, Category::all()->count(), 'All categories should have remained in course');
        $this->assertEquals($numCourses, Course::all()->count(), 'Course should have remained in DB');
        $this->assertEquals($numInvitations, Invitation::all()->count(), 'All invitations should have remained in course');
        $this->assertEquals($numObservations - 1, Observation::all()->count(), 'All observations of course should have been removed from DB');
        $this->assertEquals($numParticipants - 1, Participant::all()->count(), 'All participants of course should have been removed from DB');
        $this->assertEquals($numRequirements, Requirement::all()->count(), 'All requirements should have remained in course');
        $this->assertEquals($numRequirementDetails, RequirementDetail::all()->count(), 'All requirement details should have remained in course');
        $this->assertEquals($numObservationAssignments - 1, ObservationAssignment::all()->count(), 'All observation assignments should have been removed from DB');
        $this->assertEquals($numParticipantGroups - 1, ParticipantGroup::all()->count(), 'All participant groups should have been removed from DB');
        $this->assertEquals($numTrainers, Trainer::all()->count(), 'All trainers should have remained in course');
        $this->assertEquals($numEvaluationGridTemplates, EvaluationGridTemplate::all()->count(), 'All evaluation grid templates should have remained in course');
        $this->assertEquals($numEvaluationGridRowTemplates, EvaluationGridRowTemplate::all()->count(), 'All evaluation grid row templates should have remained in course');
        $this->assertEquals($numEvaluationGrids - 1, EvaluationGrid::all()->count(), 'All evaluation grids should have been removed from DB');
        $this->assertEquals($numEvaluationGridRows, EvaluationGridRow::all()->count(), 'All evaluation grid rows should have been removed from DB');
        $this->assertEquals($numBlocksRequirements, DB::table('blocks_requirements')->count(), 'All blocks_requirements should have remained in course');
        $this->assertEquals($numObservationsCategories - 1, DB::table('observations_categories')->count(), 'All observations_categories entries of course should have been removed from DB');
        $this->assertEquals($numObservationsRequirements - 1, DB::table('observations_requirements')->count(), 'All observations_requirements entries of course should have been removed from DB');
        $this->assertEquals($numObservationAssignmentBlocks - 1, DB::table('observation_assignment_blocks')->count(), 'All observation_assignment_blocks entries of course should have been removed from DB');
        $this->assertEquals($numObservationAssignmentParticipants - 1, DB::table('observation_assignment_participants')->count(), 'All observation_assignment_participants entries of course should have been removed from DB');
        $this->assertEquals($numObservationAssignmentUsers - 1, DB::table('observation_assignment_users')->count(), 'All observation_assignment_users entries of course should have been removed from DB');
        $this->assertEquals($numParticipantGroupsParticipants - 1, DB::table('participant_groups_participants')->count(), 'All participant_groups_participants entries of course should have been removed from DB');
        $this->assertEquals($numEvaluationGridsParticipants, DB::table('evaluation_grids_participants')->count(), 'All evaluation_grids_participants entries of course should have been removed from DB');
        $this->assertEquals($numEvaluationGridTemplatesBlocks, DB::table('evaluation_grid_templates_blocks')->count(), 'All evaluation_grid_templates_blocks entries should have remained in course');
        $this->assertEquals($numEvaluationGridTemplatesRequirements, DB::table('evaluation_grid_templates_requirements')->count(), 'All evaluation_grid_templates_requirements entries should have remained in course');
    }

    public function test_shouldDeleteImagesOfParticipantsFromStorage() {
        // given
        $imageUrl = '/some/public/asset/url.jpg';
        Participant::find($this->participantId)->update(['image_url' => $imageUrl]);
        Storage::fake();
        Storage::shouldReceive('delete')->with($imageUrl)->once();

        // when
        $this->post('/course/' . $this->courseId . '/admin/archive');

        // then
    }

    public function test_shouldHandleNullParticipantImageGracefully() {
        // given
        Participant::find($this->participantId)->update(['image_url' => null]);
        Storage::fake();
        Storage::shouldReceive('delete')->never();

        // when
        $this->post('/course/' . $this->courseId . '/admin/archive');

        // then
    }

    public function test_shouldShowEscapedNotice_afterArchivingCourse() {
        // given
        $courseName = '<b>Course name</b> with \'some" formatting';
        $courseId = $this->createCourse($courseName);

        // when
        /** @var TestResponse $response */
        $response = $this->post('/course/' . $courseId . '/admin/archive')->followRedirects();

        // then
        $response->assertDontSee($courseName, false);
        $response->assertSee(htmlspecialchars($courseName, ENT_QUOTES), false);
    }
}
