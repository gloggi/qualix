<?php

namespace Tests\Feature\Observation;

use App\Models\Course;
use App\Models\Observation;
use Illuminate\Testing\TestResponse;
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
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationData_invalidParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $participantId . '&block=' . $this->blockId);
        $this->assertEquals([$participantId], Observation::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_multipleValidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . urlencode($payload['participants']) . '&block=' . $this->blockId);
        $this->assertEquals($participantIds, Observation::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_someNonexistentParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beobachtung muss ausgefüllt sein.', $exception->validator->errors()->first('content'));
    }

    public function test_shouldValidateNewObservationData_longContent() {
        // given
        $payload = $this->payload;
        $payload['content'] = 'Unglaublich lange Beobachtung. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beobachtung darf maximal 1023 Zeichen haben.', $exception->validator->errors()->first('content'));
    }

    public function test_shouldValidateNewObservationData_noImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Eindruck muss ausgefüllt sein.', $exception->validator->errors()->first('impression'));
    }

    public function test_shouldValidateNewObservationData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals($this->blockId, Observation::latest()->first()->block->id);
    }

    public function test_shouldValidateNewObservationData_multipleValidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), $this->blockId];
        $payload['block'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals([], Observation::latest()->first()->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_invalidRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals([$requirementId], Observation::latest()->first()->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals($requirementIds, Observation::latest()->first()->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), '999999', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals([], Observation::latest()->first()->categories->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['categories'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals([$categoryId], Observation::latest()->first()->categories->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_multipleValidCategoryIds() {
        // given
        $payload = $this->payload;
        $categoryIds = [$this->createCategory(), $this->createCategory()];
        $payload['categories'] = implode(',', $categoryIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new?participant=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals($categoryIds, Observation::latest()->first()->categories->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationData_someNonexistentCategoryIds() {
        // given
        $payload = $this->payload;
        $categoryIds = [$this->createCategory(), '999999', $this->createCategory()];
        $payload['categories'] = implode(',', $categoryIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kategorien Format ist ungültig.', $exception->validator->errors()->first('categories'));
    }

    public function test_shouldShowEscapedNotice_afterCreatingObservation() {
        // given
        $participantName = '<b>Participant name</b> with \'some" formatting';
        $payload = $this->payload;
        $payload['participants'] = $this->createParticipant($participantName);

        // when
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $payload)->followRedirects();

        // then
        $response->assertDontSee($participantName, false);
        $response->assertSee(htmlspecialchars($participantName, ENT_QUOTES), false);
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
