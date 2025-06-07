<?php

namespace Tests\Feature\EvaluationGrid;

use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridTemplate;
use App\Models\Observation;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class CreateEvaluationGridTest extends TestCaseWithBasicData {

    private $payload;

    private $evaluationGridTemplateId;
    private $evaluationGridTemplate;

    public function setUp(): void {
        parent::setUp();

        $this->evaluationGridTemplateId = $this->createEvaluationGridTemplate();
        $this->evaluationGridTemplate = EvaluationGridTemplate::find($this->evaluationGridTemplateId);
        $evaluationGridRowTemplates = $this->evaluationGridTemplate->evaluationGridRowTemplates()->get();

        $this->payload = [
            'participants' => '' . $this->participantId,
            'block' => '' . $this->blockId,
            'rows' => $evaluationGridRowTemplates->mapWithKeys(function ($rowTemplate) {
                return [$rowTemplate->id => [
                    'value' => 0,
                    'notes' => "Notes for row template {$rowTemplate->id}",
                ]];
            })->all(),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayOldBlocksOnBottom() {
        // given
        $this->evaluationGridTemplate->blocks()->attach([
            $this->createBlock('old block', 1.1, Carbon::now()->subDays(2)->format('d.m.Y')),
            $this->createBlock('yesterday', 1.1, Carbon::now()->subDay()->format('d.m.Y')),
            $this->createBlock('today', 1.1, Carbon::now()->format('d.m.Y')),
            $this->createBlock('tomorrow', 1.1, Carbon::now()->addDay()->format('d.m.Y')),
        ]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId);

        // then
        $response->assertSeeInOrder(['yesterday', 'today', 'tomorrow', 'old block']);
    }

    public function test_shouldCreateAndDisplayEvaluationGrid() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldShowLinksToObservedParticipants() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);
        $participants = Participant::whereIn('id', $participantIds)->get();

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . urlencode($payload['participants']) . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        foreach ($participants as $participant) {
            $response->assertSee('Zu '. $participant->scout_name);
        }
    }

    public function test_shouldValidateNewEvaluationGridData_noParticipantIds() {
        // given
        $payload = $this->payload;
        unset($payload['participants']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $participantId . '&block=' . $this->blockId);
        $this->assertEquals([$participantId], EvaluationGrid::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewEvaluationGridData_oneParticipantIdFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $participantId = $this->createParticipant('Pflock', $course2);
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . urlencode($payload['participants']) . '&block=' . $this->blockId);
        $this->assertEquals($participantIds, EvaluationGrid::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewEvaluationGridData_someNonexistentParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_someParticipantIdsFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $foreignParticipant = $this->createParticipant('Pflock', $course2);
        $participantIds = [$this->createParticipant(), $foreignParticipant, $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewEvaluationGridData_multipleValidParticipantIds_shouldWork() {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . urlencode($participantIds) . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_createEvaluationGridWitMultipleParticipantIds_shouldLinkTheEvaluationGrids() {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantId3 = $this->createParticipant('Schnuppi');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;

        // when
        $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $response->assertSee($this->evaluationGridTemplate->name);
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $participantId2);
        $response->assertSee($this->evaluationGridTemplate->name);
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $participantId3);
        $response->assertDontSee($this->evaluationGridTemplate->name);
    }

    public function test_shouldValidateNewEvaluationGridData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        $this->assertEquals($this->blockId, EvaluationGrid::latest()->first()->block->id);
    }

    public function test_shouldValidateNewEvaluationGridData_oneBlockIdFromOtherCourse() {
        // given
        $payload = $this->payload;
        $course2 = $this->createCourse();
        $payload['block'] = $this->createBlock('Block 1', '1.1', '01.01.2019', null, $course2);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_nullValue() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['value'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_invalidValue() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['value'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_nullNotes() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['notes'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_longNotes() {
        // given
        $payload = $this->payload;
        $rowTemplateId = array_key_first($payload['rows']);
        $payload['rows'][$rowTemplateId]['notes'] = 'Unglaublich lange Bemerkung. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldValidateNewEvaluationGridRowData_noRows_isIgnored() {
        // given
        $payload = $this->payload;
        unset($payload['rows']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new?participants=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beurteilungsraster erfasst.');
    }

    public function test_shouldShowEscapedNotice_afterCreatingEvaluationGrid() {
        // given
        $participantName = '<b>Participant name</b> with \'some" formatting';
        $payload = $this->payload;
        $payload['participants'] = $this->createParticipant($participantName);

        // when
        $response = $this->post('/course/' . $this->courseId . '/evaluation_grid/' . $this->evaluationGridTemplateId . '/new', $payload)->followRedirects();

        // then
        $response->assertDontSee($participantName, false);
        $response->assertSee(htmlspecialchars($participantName, ENT_QUOTES), false);
    }
}
