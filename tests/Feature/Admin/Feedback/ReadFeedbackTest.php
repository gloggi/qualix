<?php

namespace Tests\Feature\Admin\Feedback;

use App\Models\Course;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadFeedbackTest extends TestCaseWithBasicData {

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
        $response = $this->get('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayFeedbackData() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId);

        // then
        $response->assertOk();
        $response->assertSee('Zwischenquali');
    }

    public function test_shouldNotDisplayFeedback_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/feedbacks/' . $this->feedbackDataId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayFeedback_fromOtherUser() {
        // given
        $otherCourseId = $this->createCourse('Zweiter Kurs', '', false);
        $otherFeedbackDataId = Feedback::find($this->createFeedback('Fremde Rückmeldung', $otherCourseId))->feedback_data->id;

        // when
        $response = $this->get('/course/' . $otherCourseId . '/admin/feedbacks/' . $otherFeedbackDataId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
