<?php

namespace Tests\Feature\FeedbackList;

use App\Models\Course;
use App\Models\Feedback;
use App\Models\FeedbackData;
use App\Models\Participant;
use Illuminate\Support\Arr;
use Tests\TestCaseWithBasicData;

class ReadFeedbackProgressTest extends TestCaseWithBasicData {

    protected $payload;
    /** @var Feedback|Feedback[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null */
    protected $feedback;
    protected $feedbackId;
    protected $feedbackDataId;
    protected $requirementId;

    public function setUp(): void {
        parent::setUp();

        $this->feedbackId = $this->createFeedback('Zwischenquali');
        $this->feedback = Feedback::find($this->feedbackId);
        $this->feedbackDataId = $this->feedback->feedback_data_id;
        $this->requirementId = $this->createRequirement();
        $requirementStatus = $this->createRequirementStatus();
        $this->feedback->feedback_requirements()->create(['requirement_id' => $this->requirementId, 'requirement_status_id' => $requirementStatus, 'order' => 10]);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldWork() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertOk();
        $response->assertSee('Anforderungs-Matrix Zwischenquali');
        $response->assertSee('<requirements-matrix', false);
    }

    public function test_shouldNotShowPerspectiveSelection_whenNoFeedbackIsAssigned() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertDontSee('Aus Sicht von');
    }

    public function test_shouldShowMessage_whenNoFeedbackIsAssignedToSelectedUser() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId . '?view=' . $this->user()->id);

        // then
        $response->assertSee('ist für keine TN verantwortlich. Du kannst oben die Perspektive wechseln, oder die');
    }

    public function test_shouldShowPerspectiveSelection_whenSomeFeedbackIsAssigned() {
        // given
        $this->feedback->users()->attach($this->user());

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertSee('Aus Sicht von');
    }

    public function test_shouldNotShowMatrix_whenNoRequirementsInFeedback() {
        // given
        $this->feedback->requirements()->delete();

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertOk();
        $response->assertDontSee('Anforderungs-Matrix');
        $response->assertSee($this->feedback->name);
    }

    public function test_shouldOrderFeedbacksByParticipantScoutNameInBackend_whenNoRequirementsInFeedback() {
        // given
        $feedbackData = $this->feedback->feedback_data;
        /** @var Participant $participant2 */
        $participant2 = $this->createParticipant('Aal');
        $feedbackData->feedbacks()->create(['participant_id' => $participant2]);
        /** @var Participant $participant3 */
        $participant3 = $this->createParticipant('Zyglrox');
        $feedbackData->feedbacks()->create(['participant_id' => $participant3]);
        $this->feedback->requirements()->delete();

        // when
        $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $this->assertSeeAllInOrder('b-list-group-item a strong', ['Aal', 'Pflock', 'Zyglrox']);
    }
}
