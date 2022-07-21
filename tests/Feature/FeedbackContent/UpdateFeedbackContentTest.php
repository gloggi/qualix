<?php

namespace Tests\Feature\FeedbackContent;

use App\Models\Feedback;
use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCaseWithBasicData;

class UpdateFeedbackContentTest extends TestCaseWithBasicData {

    protected $payload;
    protected $feedbackId;
    protected $requirementId;

    public function setUp(): void {
        parent::setUp();

        $this->feedbackId = $this->createFeedback('Zwischenquali');
        $feedback = Feedback::find($this->feedbackId);
        $this->requirementId = $this->createRequirement();
        $feedback->requirements()->attach([$this->requirementId => ['passed' => null, 'order' => 10]]);

        $this->payload = [
            'feedback_contents' => json_encode(['type' => 'doc', 'content' => [
                ['type' => 'paragraph'],
                ['type' => 'requirement', 'attrs' => ['id' => $this->requirementId, 'passed' => null]]
            ]]),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldWork() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId, $this->payload);

        // then
        $response->assertOk();
    }

    public function test_shouldUseTiptapFormatterForUpdating() {
        // given
        $payload = $this->payload;
        $mock = Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('applyToFeedback')
                ->once()
                ->with(json_decode($this->payload['feedback_contents'], true));
        })->makePartial();
        $this->app->extend(TiptapFormatter::class, function() use ($mock) { return $mock; });

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId, $payload);

        // then
        $response->assertOk();
    }

    public function test_shouldValidateNewFeedbackData_requirementsMismatch() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['feedback_contents'] = json_encode(['type' => 'doc', 'content' => [['type' => 'paragraph']]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Die Änderungen konnten nicht gespeichert werden, weil die Anforderungen in der Rückmeldung inzwischen geändert wurden. Kontrolliere ob alles stimmt und speichere dann erneut.', $exception->validator->errors()->first('feedback_contents'));
    }

    public function test_shouldValidateNewFeedbackData_noFeedbackContent() {
        // given
        $payload = $this->payload;
        unset($payload['feedback_contents']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Rückmeldungs-Text muss ausgefüllt sein.', $exception->validator->errors()->first('feedback_contents'));
    }

    public function test_shouldValidateNewFeedbackData_usesTiptapFormatter_forValidationOfFeedbackNotesTemplate() {
        // given
        $payload = $this->payload;
        $this->instance(TiptapFormatter::class, Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('isValid')
                ->once()
                ->with(
                    json_decode($this->payload['feedback_contents'], true),
                    Mockery::type(Collection::class),
                    Mockery::on(function ($observations) { return $observations instanceof Collection && $observations->isEmpty(); })
                )
                ->andReturnFalse();
        })->makePartial());

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/feedbacks/' . $this->feedbackId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Rückmeldungs-Text ist ungültig.', $exception->validator->errors()->first('feedback_contents'));
    }
}
