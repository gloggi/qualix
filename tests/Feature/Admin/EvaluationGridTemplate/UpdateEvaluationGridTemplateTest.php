<?php

namespace Tests\Feature\Admin\EvaluationGridTemplate;

use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridRow;
use App\Models\EvaluationGridRowTemplate;
use App\Models\EvaluationGridTemplate;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateEvaluationGridTemplateTest extends TestCaseWithBasicData {

    private $course;
    private $payload;
    private $evaluationGridTemplateId;
    private $rowTemplateIds;

    public function setUp(): void {
        parent::setUp();

        $this->course = Course::find($this->courseId);
        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate('Unternehmungsplanung', 3);
        $this->rowTemplateIds = EvaluationGridTemplate::find($this->evaluationGridTemplateId)->evaluationGridRowTemplates()->pluck('id');

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
                    'id' => $this->rowTemplateIds[0],
                ],
                [
                    'criterion' => 'Stufengerecht für Wolfsstufe',
                    'control_type' => 'radiobuttons',
                    'order' => 2,
                    'id' => $this->rowTemplateIds[1],
                ],
                [
                    'criterion' => 'Höhepunkt eingebaut',
                    'control_type' => 'checkbox',
                    'order' => 3,
                    'id' => $this->rowTemplateIds[2],
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

    public function test_shouldValidateNewEvaluationGridRowTemplateData_noCriterion() {
        // given
        $payload = $this->payload;
        unset($payload['row_templates'][0]['criterion']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kriterium muss ausgefüllt sein.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowTemplateData_longCriterion() {
        // given
        $payload = $this->payload;
        $payload['row_templates'][0]['criterion'] = str_repeat('a', 65536);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kriterium darf maximal 65535 Zeichen haben.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowTemplateData_noControlType() {
        // given
        $payload = $this->payload;
        unset($payload['row_templates'][0]['control_type']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Typ muss ausgefüllt sein.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowTemplateData_invalidControlType() {
        // given
        $payload = $this->payload;
        $payload['row_templates'][0]['control_type'] = 'foobar';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Typ ist ungültig.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowTemplateData_invalidOrder() {
        // given
        $payload = $this->payload;
        $payload['row_templates'][0]['order'] = 'foobar';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Sortierreihenfolge muss eine Zahl sein.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowTemplateData_reassignsOrderDuringUpdate() {
        // given
        $payload = $this->payload;
        $payload['row_templates'][0]['order'] = 3;
        $payload['row_templates'][0]['criterion'] = 'Test criterion 3';
        $payload['row_templates'][1]['order'] = 5;
        $payload['row_templates'][1]['criterion'] = 'Test criterion 5';
        $payload['row_templates'][2]['order'] = -10;
        $payload['row_templates'][2]['criterion'] = 'Test criterion -10';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals(1, EvaluationGridRowTemplate::where(['criterion' => 'Test criterion -10'])->first()->order);
        $this->assertEquals(2, EvaluationGridRowTemplate::where(['criterion' => 'Test criterion 3'])->first()->order);
        $this->assertEquals(3, EvaluationGridRowTemplate::where(['criterion' => 'Test criterion 5'])->first()->order);
    }

    public function test_shouldProcessNewEvaluationGridRowTemplateData_updatesExistingRowTemplates_keepingExistingRows() {
        // given
        $this->createEvaluationGrid($this->evaluationGridTemplateId);
        $numRowTemplates = EvaluationGridRowTemplate::all()->count();
        $numRows = EvaluationGridRow::all()->count();
        $rowIds = EvaluationGridRowTemplate::whereIn('id', $this->rowTemplateIds)->get()->flatMap->evaluation_grid_rows->map->id;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals($numRowTemplates, EvaluationGridRowTemplate::all()->count(), 'should have kept the 3 evaluation grid row templates');
        foreach($this->rowTemplateIds as $index => $id) {
            $this->assertTrue(EvaluationGridRowTemplate::where(['id' => $id])->exists());
            $rowTemplate = EvaluationGridRowTemplate::find($id);
            $this->assertEquals($this->payload['row_templates'][$index]['criterion'], $rowTemplate->criterion);
            $this->assertEquals($this->payload['row_templates'][$index]['control_type'], $rowTemplate->control_type);
            $this->assertEquals($index + 1, $rowTemplate->order);
        }
        $this->assertEquals($numRows, EvaluationGridRow::all()->count(), 'should have kept the 3 evaluation grid rows');
        foreach($rowIds as $id) {
            $this->assertTrue(EvaluationGridRow::where(['id' => $id])->exists());
        }
    }

    public function test_shouldProcessNewEvaluationGridRowTemplateData_deletesExistingRowTemplates_deletesExistingRows() {
        // given
        $this->createEvaluationGrid($this->evaluationGridTemplateId);
        $numRowTemplates = EvaluationGridRowTemplate::all()->count();
        $numRows = EvaluationGridRow::all()->count();
        $rowIds = EvaluationGridRowTemplate::whereIn('id', $this->rowTemplateIds)->get()->flatMap->evaluation_grid_rows->map->id;
        $payload = $this->payload;
        unset($payload['row_templates'][0]['id']);
        unset($payload['row_templates'][1]);
        unset($payload['row_templates'][2]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals($numRowTemplates - 2, EvaluationGridRowTemplate::all()->count(), 'should have deleted 3 and created 1 evaluation grid row template');
        foreach($this->rowTemplateIds as $id) {
            $this->assertFalse(EvaluationGridRowTemplate::where(['id' => $id])->exists());
        }
        $this->assertEquals($numRows - 2, EvaluationGridRow::all()->count(), 'should have deleted 3 and created 1 evaluation grid row');
        foreach($rowIds as $id) {
            $this->assertFalse(EvaluationGridRow::where(['id' => $id])->exists());
        }
    }

    public function test_shouldProcessNewEvaluationGridRowTemplateData_createsNewRowTemplates_creatingNewRows() {
        // given
        $this->createEvaluationGrid($this->evaluationGridTemplateId);
        $numRowTemplates = EvaluationGridRowTemplate::all()->count();
        $numRows = EvaluationGridRow::all()->count();
        $rowIds = EvaluationGridRowTemplate::whereIn('id', $this->rowTemplateIds)->get()->flatMap->evaluation_grid_rows->map->id;
        $payload = $this->payload;
        $payload['row_templates'][] = [
            'criterion' => 'Neues Kriterium',
            'control_type' => 'radiobuttons',
            'order' => 0,
        ];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/evaluation_grids/' . $this->evaluationGridTemplateId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/evaluation_grids');
        $this->assertEquals($numRowTemplates + 1, EvaluationGridRowTemplate::all()->count(), 'should have added 1 new evaluation grid row template');
        foreach($this->rowTemplateIds as $index => $id) {
            $this->assertTrue(EvaluationGridRowTemplate::where(['id' => $id])->exists());
            $rowTemplate = EvaluationGridRowTemplate::find($id);
            $this->assertEquals($payload['row_templates'][$index]['criterion'], $rowTemplate->criterion);
            $this->assertEquals($payload['row_templates'][$index]['control_type'], $rowTemplate->control_type);
            $this->assertEquals($index + 2, $rowTemplate->order);
        }

        $newRowTemplate = EvaluationGridRowTemplate::find(EvaluationGridRowTemplate::max('id'));
        $this->assertEquals('Neues Kriterium', $newRowTemplate->criterion);
        $this->assertEquals('radiobuttons', $newRowTemplate->control_type);
        $this->assertEquals(1, $newRowTemplate->order);

        $this->assertEquals($numRows + 1, EvaluationGridRow::all()->count(), 'should have added 1 new evaluation grid row');
        foreach($rowIds as $id) {
            $this->assertTrue(EvaluationGridRow::where(['id' => $id])->exists());
        }

        $newRow = EvaluationGridRow::find(EvaluationGridRow::max('id'));
        $this->assertEquals($newRowTemplate->id, $newRow->evaluation_grid_row_template_id);
        $this->assertNull($newRow->value);
        $this->assertNull($newRow->notes);
    }
}
