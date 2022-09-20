<?php

namespace Tests\Feature\FeedbackList;

use App\Models\Feedback;
use App\Models\FeedbackData;
use App\Models\Participant;
use Illuminate\Support\Arr;
use Tests\TestCaseWithBasicData;

class ReadRequirementsMatrixTest extends TestCaseWithBasicData {

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

    public function test_shouldWork() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertOk();
        $response->assertSee('Anforderungs-Matrix Zwischenquali');
        $response->assertSee('<table-feedback-requirements-matrix', false);
    }
}
