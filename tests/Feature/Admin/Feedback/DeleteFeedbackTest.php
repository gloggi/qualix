<?php

namespace Tests\Feature\Admin\Feedback;

use App\Models\Feedback;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteFeedbackTest extends TestCaseWithBasicData {

    private $feedbackDataId;

    public function setUp(): void {
        parent::setUp();

        $feedback = Feedback::find($this->createFeedback('Zwischenquali'));
        $this->feedbackDataId = $feedback->feedback_data->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteFeedback() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Die Rückmeldung "Zwischenquali" wurde erfolgreich gelöscht.');

        $response = $this->get('/course/' . $this->courseId . '/admin/feedbacks');
        $response->assertDontSee('Zwischenquali');
    }

    public function test_shouldValidateDeletedFeedbackUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/feedbacks/' . ($this->feedbackDataId + 1));

        // then
        $response->assertStatus(404);
    }
}
