<?php

namespace Tests\Feature\Admin\EvaluationGridTemplate;

use App\Models\Course;
use App\Models\EvaluationGridTemplate;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateEvaluationGridTemplateTest extends TestCaseWithBasicData {

    private $course;
    private $payload;
    private $evaluationGridTemplateId;

    public function setUp(): void {
        parent::setUp();

        $this->course = Course::find($this->courseId);
        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate('Unternehmungsplanung');

        $requirement = $this->createRequirement();
        $this->payload = [
            'name' => 'Unternehmungsplanung',
            'blocks' => $this->course->blocks()->pluck('id')->first(),
            'requirements' => $requirement,
            'row_templates' => [
                [
                    'criterion' => 'Routenwahl',
                    'control_type' => 'heading',
                    'order' => 1,
                ],
                [
                    'criterion' => 'Stufengerecht für Wolfsstufe',
                    'control_type' => 'radiobuttons',
                    'order' => 2,
                ],
                [
                    'criterion' => 'Höhepunkt eingebaut',
                    'control_type' => 'checkbox',
                    'order' => 3,
                ],
            ],
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldUpdateAndDisplayEvaluationGridTemplate() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldValidateNewEvaluationGridTemplate_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Titel muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Beurteilungsraster, welches im Grunde genommen auch ohne Qualix erstellt werden könnte, obwohl man natürlich hoffen würde dass Qualix einen bei der Kursführung unterstützen sollte, was wohl auch der Grund ist dass nun in diesem Kurs das endlich verfügbare Beurteilungsraster-Feature eingesetzt werden soll... Tja.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Titel darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_noBlockIds() {
        // given
        $payload = $this->payload;
        $payload['blocks'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Leistungszeitpunkte muss ausgefüllt sein.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_invalidBlockIds() {
        // given
        $payload = $this->payload;
        $payload['blocks'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Leistungszeitpunkte Format ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_oneValidBlockId() {
        // given
        $payload = $this->payload;
        $blockId = $this->createBlock();
        $payload['blocks'] = $blockId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals([$blockId], EvaluationGridTemplate::with('blocks')->find($this->evaluationGridTemplateId)->blocks->map->id->all());
    }

    public function test_shouldValidateNewEvaluationGridTemplate_oneBlockIdFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null, $course2);
        $payload['blocks'] = $blockId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Leistungszeitpunkte ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_multipleValidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEqualsCanonicalizing($blockIds, EvaluationGridTemplate::with('blocks')->find($this->evaluationGridTemplateId)->blocks->map->id->all());
    }

    public function test_shouldValidateNewEvaluationGridTemplate_someNonexistentBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), '999999', $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Leistungszeitpunkte ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_someBlockIdsFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $foreignBlockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null, $course2);
        $blockIds = [$this->createBlock(), $foreignBlockId, $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Leistungszeitpunkte ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_someInvalidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), 'abc', $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Leistungszeitpunkte Format ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_noRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beurteilte Anforderungen muss ausgefüllt sein.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_invalidRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beurteilte Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals([$requirementId], EvaluationGridTemplate::with('requirements')->find($this->evaluationGridTemplateId)->requirements->map->id->all());
    }

    public function test_shouldValidateNewEvaluationGridTemplate_oneRequirementIdFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $requirementId = $this->createRequirement('Mindestanforderung 1', true, $course2);
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Beurteilte Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals($requirementIds, EvaluationGridTemplate::with('requirements')->find($this->evaluationGridTemplateId)->requirements->map->id->all());
    }

    public function test_shouldValidateNewEvaluationGridTemplate_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 999999, $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Beurteilte Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_someRequirementIdsFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $foreignRequirementId = $this->createRequirement('Mindestanforderung 1', true, $course2);
        $requirementIds = [$this->createRequirement(), $foreignRequirementId, $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Beurteilte Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_someInvalidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 'abc', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beurteilte Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewEvaluationGridTemplate_tooManyValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = array_map(function () { return $this->createRequirement(); }, range(1, 41));
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beurteilte Anforderungen darf nicht mehr als 40 ausgewählte Elemente haben.', $exception->validator->errors()->first('requirements'));
    }
}
