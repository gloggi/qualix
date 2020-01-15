<?php

namespace Tests\Feature\Observation;

use App\Models\Course;
use App\Models\Observation;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateObservationTest extends TestCaseWithBasicData {

    private $observationId;
    private $requirementId;
    private $categoryId;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->observationId = $this->createObservation('hat gut mitgemacht', 1, [], [], $this->blockId);

        $blockId2 = $this->createBlock();
        $this->requirementId = $this->createRequirement('Mindestanforderung 1', true);
        $this->categoryId = $this->createCategory('Kategorie 1');

        $this->payload = ['participant_ids' => '' . $this->participantId, 'content' => 'kein Wort gesagt', 'impression' => '0',
            'block_id' => '' . $blockId2, 'requirement_ids' => '' . $this->requirementId, 'category_ids' => '' . $this->categoryId];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldUpdateObservation() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['content']);
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateNewObservationData_noParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participant_ids'] = '';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_noComment() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_noImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block_id']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block_id'] = '*';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['requirement_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['category_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateUpdatedObservationURL_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . ($this->observationId + 1), $payload);

        // then
        $response->assertStatus(404);
    }

    public function test_shouldRedirectBackToParticipantPage_includingFilters() {
        // given
        // visiting the edit observation form from the participant detail view with filters activated
        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId . '?requirement=3';
        $this->get('/course/' . $this->courseId . '/observation/' . $this->observationId, [], ['referer' => $previous]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToParticipantPage_includingFilters_evenWhenValidationErrorsOccur() {
        // given
        // visiting the edit observation form from the participant detail view with filters activated
        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId . '?requirement=3';
        $this->get('/course/' . $this->courseId . '/observation/' . $this->observationId, [], ['referer' => $previous]);

        // send something which will trigger validation errors
        $payload = $this->payload;
        $payload['content'] = '';
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/' . $this->observationId);
        $response->followRedirects();

        // when
        // Try again with a correct request
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToPreviouslyViewedParticipantPage_whenMultipleParticipantsAreAssigned() {
        // given
        $participantId2 = $this->createParticipant('Bari');
        Observation::findOrFail($this->observationId)->participants()->attach($participantId2);

        $previous = '/course/' . $this->courseId . '/participants/' . $participantId2;
        $this->get('/course/' . $this->courseId . '/observation/' . $this->observationId, [], ['referer' => $previous]);
        $payload = $this->payload;
        $payload['participant_ids'] = $this->participantId . ',' . $participantId2;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToPreviouslyViewedParticipantPage_whenMultipleParticipantsAreAssigned_evenWhenValidationErrorsOccur() {
        // given
        $participantId2 = $this->createParticipant('Bari');
        Observation::findOrFail($this->observationId)->participants()->attach($participantId2);

        $previous = '/course/' . $this->courseId . '/participants/' . $participantId2;
        $this->get('/course/' . $this->courseId . '/observation/' . $this->observationId, [], ['referer' => $previous]);
        $payload = $this->payload;
        $payload['participant_ids'] = $this->participantId . ',' . $participantId2;

        // send something which will trigger validation errors
        $payloadWithErrors = $payload;
        $payloadWithErrors['content'] = '';
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payloadWithErrors);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/' . $this->observationId);
        $response->followRedirects();

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToOneOfTheAssignedParticipants_whenPreviouslyAssignedParticipantIsUnassigned() {
        // given
        $participantId2 = $this->createParticipant('Bari');

        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId;
        $this->get('/course/' . $this->courseId . '/observation/' . $this->observationId, [], ['referer' => $previous]);
        $payload = $this->payload;
        $payload['participant_ids'] = $participantId2;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $participantId2);
    }

    public function test_shouldNotAllowChangingParticipantToSomeoneFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $participantFromDifferentCourse = $this->createParticipant('Foreign', $differentCourse);
        $payload = $this->payload;
        $payload['participant_ids'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participant_ids'));
    }

    public function test_shouldNotAllowChangingRequirementToOneFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $requirementFromDifferentCourse = $this->createRequirement('Must not be a bad person', true, $differentCourse);
        $payload = $this->payload;
        $payload['requirement_ids'] = $this->requirementId . ',' . $requirementFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Mindestanforderungen ist ungültig.', $exception->validator->errors()->first('requirement_ids'));
    }

    public function test_shouldNotAllowChangingCategoryToOneFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $categoryFromDifferentCourse = $this->createCategory('Early observations', $differentCourse);
        $payload = $this->payload;
        $payload['category_ids'] = $this->categoryId . ',' . $categoryFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Kategorien ist ungültig.', $exception->validator->errors()->first('category_ids'));
    }
}
