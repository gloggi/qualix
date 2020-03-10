<?php

namespace Tests\Feature\Observation;

use App\Models\Course;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class CreateObservationTest extends TestCaseWithBasicData {

    private $payload;
    private $requirementId;
    private $categoryId;

    public function setUp(): void {
        parent::setUp();

        $this->requirementId = $this->createRequirement('Mindestanforderung 1', true);
        $this->categoryId = $this->createCategory('Kategorie 1');

        $this->payload = ['participants' => '' . $this->participantId, 'content' => 'hat gut mitgemacht', 'impression' => '1',
            'block' => '' . $this->blockId, 'requirements' => '' . $this->requirementId, 'categories' => '' . $this->categoryId];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldCreateAndDisplayObservation() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtung erfasst.');
    }

    public function test_shouldValidateNewObservationData_noParticipantIds() {
        // given
        $payload = $this->payload;
        unset($payload['participants']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_multipleParticipantIds_shouldWork() {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . urlencode($participantIds) . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtung erfasst.');
    }

    public function test_createObservationWitMultipleParticipantIds_shouldLinkTheObservations() {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;
        $payload['content'] = 'visible on both participants';

        // when
        $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $response->assertSee('visible on both participants');
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $participantId2);
        $response->assertSee('visible on both participants');
    }

    public function test_shouldValidateNewObservationData_noContent() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_longContent() {
        // given
        $payload = $this->payload;
        $payload['content'] = 'Unglaublich lange Beobachtung. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_noImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block'] = '*';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['requirement_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewObservationData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['categories'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowEscapedNotice_afterCreatingObservation() {
        // given
        $participantName = '<b>Participant name</b> with \'some" formatting';
        $payload = $this->payload;
        $payload['participants'] = $this->createParticipant($participantName);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload)->followRedirects();

        // then
        $response->assertDontSee($participantName);
        $response->assertSee(htmlspecialchars($participantName, ENT_QUOTES));
    }

    public function test_shouldNotAllowCreatingObservation_withParticipantFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $participantFromDifferentCourse = $this->createParticipant('Foreign', $differentCourse);
        $payload = $this->payload;
        $payload['participants'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldNotAllowCreatingObservation_withRequirementFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $requirementFromDifferentCourse = $this->createRequirement('Must not be a bad person', true, $differentCourse);
        $payload = $this->payload;
        $payload['requirements'] = $this->requirementId . ',' . $requirementFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldNotAllowCreatingObservation_withCategoryFromADifferentCourse() {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $categoryFromDifferentCourse = $this->createCategory('Early observations', $differentCourse);
        $payload = $this->payload;
        $payload['categories'] = $this->categoryId . ',' . $categoryFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Kategorien ist ungültig.', $exception->validator->errors()->first('categories'));
    }
}
