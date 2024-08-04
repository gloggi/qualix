<?php

namespace Tests\Feature\EvaluationGrid;

use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridTemplate;
use App\Models\Observation;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateEvaluationGridTest extends TestCaseWithBasicData {

    private $evaluationGridTemplateId;
    private $evaluationGridTemplate;
    private $evaluationGridId;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate();
        $this->evaluationGridTemplate = EvaluationGridTemplate::find($this->evaluationGridTemplateId);
        $evaluationGridRowTemplates = $this->evaluationGridTemplate->evaluationGridRowTemplates()->get();
        $this->evaluationGridId = $this->createEvaluationGrid($this->evaluationGridTemplateId);

        $blockId2 = $this->createBlock();
        EvaluationGridTemplate::find($this->evaluationGridTemplateId)->blocks()->sync([$this->blockId, $blockId2]);

        $this->payload = [
            'participants' => '' . $this->participantId,
            'block' => '' . $blockId2,
            'rows' => $evaluationGridRowTemplates->mapWithKeys(function ($rowTemplate) {
                return [$rowTemplate->id => [
                    'value' => 6,
                    'notes' => "Changed for {$rowTemplate->id}",
                ]];
            })->all(),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayAllBlocksInChronologicalOrder() {
        // given
        $this->evaluationGridTemplate->blocks()->attach([
            $this->createBlock('old block', 1.1, Carbon::now()->subDays(2)->format('d.m.Y')),
            $this->createBlock('yesterday', 1.1, Carbon::now()->subDay()->format('d.m.Y')),
            $this->createBlock('today', 1.1, Carbon::now()->format('d.m.Y')),
            $this->createBlock('tomorrow', 1.1, Carbon::now()->addDay()->format('d.m.Y')),
        ]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId);

        // then
        $response->assertSeeInOrder(['old block', 'yesterday', 'today', 'tomorrow']);
    }

    public function test_shouldUpdateEvaluationGrid() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateNewEvaluationGridData_noParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = '';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_invalidParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_oneValidParticipantId() {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $participantId);
        $this->assertEquals([$participantId], EvaluationGrid::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewEvaluationGridData_oneParticipantIdFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $participantId = $this->createParticipant('Pflock', $course2);
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_multipleValidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $participantIds[0]);
        $this->assertEquals($participantIds, EvaluationGrid::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewEvaluationGridData_someParticipantIdsFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $foreignParticipant = $this->createParticipant('Pflock', $course2);
        $participantIds = [$this->createParticipant(), $foreignParticipant, $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_someNonexistentParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_someInvalidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block muss ausgefüllt sein.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewEvaluationGridData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block'] = '*';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block Format ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewEvaluationGridData_oneValidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block'] = $this->blockId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $this->assertEquals($this->blockId, EvaluationGrid::find($this->evaluationGridId)->block->id);
    }

    public function test_shouldValidateNewEvaluationGridData_oneBlockIdFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $payload['block'] = $this->createBlock('Block 1', '1.1', '01.01.2019', null, $course2);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Block ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewEvaluationGridData_oneBlockIdNotAllowedInEvaluationGridTemplate() {
        // given
        $payload = $this->payload;
        $payload['block'] = $this->createBlock('Block not allowlisted in the evaluation grid template');

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Block ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewEvaluationGridData_multipleValidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), $this->blockId];
        $payload['block'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block Format ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewEvaluationGridData_someInvalidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), 'abc'];
        $payload['block'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Block Format ist ungültig.', $exception->validator->errors()->first('block'));
    }

    public function test_shouldValidateNewEvaluationGridRowData_noValue() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        unset($payload['rows'][$rowTemplateId]['value']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_nullValue() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['value'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_invalidValue() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['value'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Wert muss eine Zahl sein.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowData_noNotes() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        unset($payload['rows'][$rowTemplateId]['notes']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_nullNotes() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['notes'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_longNotes() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['notes'] = 'Unglaublich lange Bemerkung. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Bemerkungen darf maximal '.Observation::CHAR_LIMIT.' Zeichen haben.', $exception->validator->errors()->first());
    }

    public function test_shouldValidateNewEvaluationGridRowData_invalidRowTemplateId_isIgnored() {
        // given
        $payload = $this->payload;
        $rowTemplateId = '0';
        $payload['rows'][$rowTemplateId]['notes'] = 'Bemerkung 1234';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_noRows_isIgnored() {
        // given
        $payload = $this->payload;
        unset($payload['rows']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster aktualisiert.');
    }

    public function test_shouldValidateUpdatedEvaluationGridURL_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . ($this->evaluationGridId + 1), $payload);

        // then
        $response->assertStatus(404);
    }

    public function test_shouldRedirectBackToParticipantPage() {
        // given
        // visiting the edit evaluationGrid form from the participant detail view
        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId;
        $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, [], ['referer' => $previous]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToParticipantPage_evenWhenValidationErrorsOccur() {
        // given
        // visiting the edit evaluationGrid form from the participant detail view
        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId;
        $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, [], ['referer' => $previous]);

        // send something which will trigger validation errors
        $payload = $this->payload;
        $payload['content'] = '';
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $response->followRedirects();

        // when
        // Try again with a correct request
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect($previous);
    }

    public function test_shouldRedirectBackToOneOfTheAssignedParticipants_whenPreviouslyAssignedParticipantIsUnassigned() {
        // given
        $participantId2 = $this->createParticipant('Bari');

        $previous = '/course/' . $this->courseId . '/participants/' . $this->participantId;
        $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, [], ['referer' => $previous]);
        $payload = $this->payload;
        $payload['participants'] = $participantId2;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/' . $this->evaluationGridId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/participants/' . $participantId2);
    }
}
