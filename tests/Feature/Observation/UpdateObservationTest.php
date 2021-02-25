<?php

namespace Tests\Feature\Observation;

use App\Models\Course;
use App\Models\Observation;
use Illuminate\Testing\TestResponse;
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

        $this->payload = ['participants' => '' . $this->participantId, 'content' => 'kein Wort gesagt', 'impression' => '0',
            'block' => '' . $blockId2, 'requirements' => '' . $this->requirementId, 'categories' => '' . $this->categoryId];
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
        $payload['participants'] = '';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationData_invalidParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationData_oneValidParticipantId() {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $participantId);
        $this->assertEquals([$participantId], Observation::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_multipleValidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $participantIds[0]);
        $this->assertEquals($participantIds, Observation::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_someNonexistentParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationData_someInvalidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationData_multipleValidParticipantIds_shouldWork() {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtung aktualisiert.');
    }

    public function test_shouldValidateNewObservationData_noComment() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beobachtung muss ausgefüllt sein.', $exception->validator->errors()->first('content'));
    }

    public function test_shouldValidateNewObservationData_longComment() {
        // given
        $payload = $this->payload;
        $payload['content'] = 'Unglaublich lange Beobachtung. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beobachtung darf maximal 1023 Zeichen haben.', $exception->validator->errors()->first('content'));
    }

    public function test_shouldValidateNewObservationData_noImpression_shouldLeavePreviousImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);
        Observation::find($this->observationId)->update(['impression' => 2]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertStatus(200);
        $this->assertNull($response->exception);
        $this->assertEquals(2, Observation::find($this->observationId)->toArray()['impression']);
    }

    public function test_shouldValidateNewObservationData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Eindruck ist ungültig.', $exception->validator->errors()->first('impression'));
    }

    public function test_shouldValidateNewObservationData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block muss ausgefüllt sein.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewObservationData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block'] = '*';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block Format ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewObservationData_oneValidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block'] = $this->blockId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals($this->blockId, Observation::find($this->observationId)->block->id);
    }

    public function test_shouldValidateNewObservationData_multipleValidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), $this->blockId];
        $payload['block'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block Format ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewObservationData_someInvalidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), 'abc'];
        $payload['block'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block Format ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewObservationData_noRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals([], Observation::find($this->observationId)->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_invalidRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewObservationData_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals([$requirementId], Observation::find($this->observationId)->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals($requirementIds, Observation::find($this->observationId)->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), '999999', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewObservationData_someInvalidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 'abc', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewObservationData_noCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['categories'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals([], Observation::find($this->observationId)->categories->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['categories'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kategorien Format ist ungültig.', $exception->validator->errors()->first('categories'));
    }

    public function test_shouldValidateNewObservationData_oneValidCategoryId() {
        // given
        $payload = $this->payload;
        $categoryId = $this->createCategory();
        $payload['categories'] = $categoryId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals([$categoryId], Observation::find($this->observationId)->categories->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_multipleValidCategoryIds() {
        // given
        $payload = $this->payload;
        $categoryIds = [$this->createCategory(), $this->createCategory()];
        $payload['categories'] = implode(',', $categoryIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals($categoryIds, Observation::find($this->observationId)->categories->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_someNonexistentCategoryIds() {
        // given
        $payload = $this->payload;
        $categoryIds = [$this->createCategory(), '999999', $this->createCategory()];
        $payload['categories'] = implode(',', $categoryIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Kategorien ist ungültig.', $exception->validator->errors()->first('categories'));
    }

    public function test_shouldValidateNewObservationData_someInvalidCategoryIds() {
        // given
        $payload = $this->payload;
        $categoryIds = [$this->createCategory(), 'abc', $this->createCategory()];
        $payload['categories'] = implode(',', $categoryIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kategorien Format ist ungültig.', $exception->validator->errors()->first('categories'));
    }

    public function test_shouldValidateUpdatedObservationURL_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . ($this->observationId + 1), $payload);

        // then
        $response->assertStatus(404);
    }

    public function test_shouldRedirectBackToParticipantPage() {
        // given
        // visiting the edit observation form from the participant detail view
        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId;
        $this->get('/course/' . $this->courseId . '/observation/' . $this->observationId, [], ['referer' => $previous]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToParticipantPage_evenWhenValidationErrorsOccur() {
        // given
        // visiting the edit observation form from the participant detail view
        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId;
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
        $payload['participants'] = $this->participantId . ',' . $participantId2;

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
        $payload['participants'] = $this->participantId . ',' . $participantId2;

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
        $payload['participants'] = $participantId2;

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
        $payload['participants'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldNotAllowChangingRequirementToOneFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $requirementFromDifferentCourse = $this->createRequirement('Must not be a bad person', true, $differentCourse);
        $payload = $this->payload;
        $payload['requirements'] = $this->requirementId . ',' . $requirementFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldNotAllowChangingCategoryToOneFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $categoryFromDifferentCourse = $this->createCategory('Early observations', $differentCourse);
        $payload = $this->payload;
        $payload['categories'] = $this->categoryId . ',' . $categoryFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Kategorien ist ungültig.', $exception->validator->errors()->first('categories'));
    }
}
