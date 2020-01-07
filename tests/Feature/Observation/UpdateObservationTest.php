<?php

namespace Tests\Feature\Observation;

use App\Models\Course;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateObservationTest extends TestCaseWithBasicData {

    private $observationId;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->observationId = $this->createObservation('hat gut mitgemacht', 1, [], [], $this->blockId);

        $blockId2 = $this->createBlock();
        $requirementId = $this->createRequirement('Mindestanforderung 1', true);
        $categoryId = $this->createCategory('Kategorie 1');

        $this->payload = ['participant_ids' => '' . $this->participantId, 'content' => 'kein Wort gesagt', 'impression' => '0', 'block_id' => '' . $blockId2, 'requirement_ids' => '' . $requirementId, 'category_ids' => '' . $categoryId];
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

    public function test_shouldUpdateBeobachtung() {
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

    public function test_shouldValidateNewBeobachtungData_noKommentar() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block_id']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block_id'] = '*';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['requirement_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['category_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateUpdatedBeobachtungURL_wrongId() {
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
}
