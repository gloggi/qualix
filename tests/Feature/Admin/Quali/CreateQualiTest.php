<?php

namespace Tests\Feature\Admin\Quali;

use App\Models\Course;
use App\Models\Quali;
use App\Models\QualiData;
use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCaseWithBasicData;

class CreateQualiTest extends TestCaseWithBasicData {

    private $course;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->course = Course::find($this->courseId);
        $this->payload = [
            'name' => 'Zwischenquali',
            'participants' => $this->course->participants()->pluck('id')->implode(','),
            'requirements' => $this->course->requirements()->pluck('id')->implode(','),
            'quali_contents_template' => json_encode(['type' => 'doc', 'content' => [['type' => 'paragraph']]]),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayQuali() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldValidateNewQualiData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Titel muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewQualiData_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Zwischenquali, welches im Grunde genommen auch ohne Qualix geführt werden könnte, obwohl man natürlich hoffen würde dass Qualix einen bei der Kursführung unterstützen sollte, was wohl auch der Grund ist dass nun in diesem Kurs das endlich verfügbare Quali-Feature eingesetzt werden soll... Tja.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Titel darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewQualiData_noParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewQualiData_invalidParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewQualiData_oneValidParticipantId() {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        $this->assertEquals([$participantId], QualiData::latest()->first()->qualis()->pluck('participant_id')->all());
    }

    public function test_shouldValidateNewQualiData_multipleValidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        $this->assertEquals($participantIds, QualiData::latest()->first()->qualis()->pluck('participant_id')->all());
    }

    public function test_shouldValidateNewQualiData_someNonexistentParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewQualiData_someInvalidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewQualiData_noRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        $this->assertEquals([], Quali::latest()->first()->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewQualiData_invalidRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewQualiData_requirementsMismatch() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Die Anforderungen in der Vorlage stimmten nicht mit den relevanten Anforderungen überein. Wir haben das automatisch korrigiert. Kontrolliere ob jetzt alles stimmt und speichere erneut.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewQualiData_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;
        $payload['quali_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementId, 'passed' => 1]],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        $this->assertEquals([$requirementId], Quali::latest()->first()->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewQualiData_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);
        $payload['quali_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[0], 'passed' => null]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[1], 'passed' => 0]],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        $this->assertEquals($requirementIds, Quali::latest()->first()->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewQualiData_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 999999, $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);
        $payload['quali_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[0], 'passed' => null]],
            ['type' => 'requirement', 'attrs' => ['id' => 999999, 'passed' => null]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[1], 'passed' => 0]],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Relevante Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewQualiData_someInvalidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 'abc', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);
        $payload['quali_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[0], 'passed' => null]],
            ['type' => 'requirement', 'attrs' => ['id' => 'abc', 'passed' => null]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[1], 'passed' => 0]],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewQualiData_noQualiNotesTemplate() {
        // given
        $payload = $this->payload;
        unset($payload['quali_contents_template']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Vorlage für Quali-Text muss ausgefüllt sein.', $exception->validator->errors()->first('quali_contents_template'));
    }

    public function test_shouldValidateNewQualiData_usesTiptapFormatter_forValidationOfQualiNotesTemplate() {
        // given
        $payload = $this->payload;
        $this->instance(TiptapFormatter::class, Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('isValid')
                ->once()
                ->with(
                    json_decode($this->payload['quali_contents_template'], true),
                    Mockery::type(Collection::class),
                    Mockery::on(function ($observations) { return $observations instanceof Collection && $observations->isEmpty(); })
                )
                ->andReturnFalse();
        })->makePartial());

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Vorlage für Quali-Text ist ungültig.', $exception->validator->errors()->first('quali_contents_template'));
    }

    public function test_shouldValidateNewQualiData_invalidTrainerAssignment() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['qualis'][$participantId]['user'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Zuständig für Pflock Format ist ungültig.', $exception->validator->errors()->first("qualis.$participantId.user"));
    }

    public function test_shouldValidateNewQualiData_nonexistentTrainerAssignment() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['qualis'][$participantId]['user'] = 999999;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("qualis.$participantId.user"));
    }

    public function test_shouldValidateNewQualiData_assigningTrainerFromOtherCourse() {
        // given
        $user2 = $this->createUser();
        $user2->courses()->attach($this->createCourse('Zweiter Kurs'));

        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['qualis'][$participantId]['user'] = $user2->id;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("qualis.$participantId.user"));
    }

    public function test_shouldValidateNewQualiData_existingTrainerAssignment_shouldWork() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userId = $this->course->users()->pluck('id')->first();
        $payload['qualis'][$participantId]['user'] = $userId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/qualis', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        $this->assertEquals($userId, Quali::latest()->pluck('user_id')->first());
    }

    public function test_shouldShowMessage_whenNoQualisInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/qualis');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Qualis erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeQualisInCourse() {
        // given
        $this->createQuali();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/qualis');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Qualis erfasst.');
    }
}
