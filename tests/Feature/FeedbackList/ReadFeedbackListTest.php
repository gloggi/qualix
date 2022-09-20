<?php

namespace Tests\Feature\FeedbackList;

use App\Models\Feedback;
use App\Models\FeedbackData;
use App\Models\Participant;
use Illuminate\Support\Arr;
use Tests\TestCaseWithBasicData;

class ReadFeedbackListTest extends TestCaseWithBasicData {

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
        $response = $this->get('/course/' . $this->courseId . '/feedbacks');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldWork() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks');

        // then
        $response->assertOk();
        $requirementMatrixHref = '/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId;
        $this->assertSeeAllInOrder('[href$="'.$requirementMatrixHref.'"]', ['Anforderungs-Matrix']);
        $feedbackContentHref = '/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId . '/edit';
        $this->assertSeeAllInOrder('[href$="'.$feedbackContentHref.'"]', ['']);
        $this->assertEquals($this->feedbackId, $response->viewData('feedbackDatas')[0]->feedbacks->first()->id);
    }

    public function test_shouldNotShowPerspectiveSelection_whenNoFeedbackIsAssigned() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks');

        // then
        $response->assertDontSee('Aus Sicht von');
    }

    public function test_shouldShowMessage_whenNoFeedbackIsAssignedToSelectedUser() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks?view=' . $this->user()->id);

        // then
        $response->assertSee('ist für keine TN verantwortlich. Du kannst oben die Perspektive wechseln, oder die');
    }

    public function test_shouldShowPerspectiveSelection_whenSomeFeedbackIsAssigned() {
        // given
        $this->feedback->users()->attach($this->user());

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks');

        // then
        $response->assertSee('Aus Sicht von');
    }

    public function test_shouldOrderFeedbacksByParticipantScoutName() {
        // given
        $feedbackData = $this->feedback->feedback_data;
        /** @var Participant $participant2 */
        $participant2 = $this->createParticipant('Aal');
        $feedbackData->feedbacks()->create(['participant_id' => $participant2]);
        /** @var Participant $participant3 */
        $participant3 = $this->createParticipant('Zyglrox');
        $feedbackData->feedbacks()->create(['participant_id' => $participant3]);

        // when
        $this->get('/course/' . $this->courseId . '/feedbacks');

        // then
        $this->assertSeeAllInOrder('b-collapse b-list-group-item h5', ['Aal', 'Pflock', 'Zyglrox']);
    }
}
