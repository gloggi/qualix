<?php

namespace Tests\Feature\Admin\Feedback;

use App\Models\Course;
use App\Models\Feedback;
use App\Models\FeedbackData;
use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCaseWithBasicData;

class CreateFeedbackTest extends TestCaseWithBasicData {

    private $course;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->course = Course::find($this->courseId);
        $this->payload = [
            'name' => 'Zwischenquali',
            'participants' => $this->course->participants()->pluck('id')->implode(','),
            'requirements' => $this->course->requirements()->pluck('id')->implode(','),
            'feedback_contents_template' => json_encode(['type' => 'doc', 'content' => [['type' => 'paragraph']]]),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldCreateAndDisplayFeedback() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldCreateFeedback_usesTiptapFormatter_forSettingFeedbackContent() {
        // given
        $payload = $this->payload;
        $mock = Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('applyToFeedback')
                ->once()
                ->with(json_decode($this->payload['feedback_contents_template'], true));
        })->makePartial();
        $this->app->extend(TiptapFormatter::class, function() use ($mock) { return $mock; });

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
    }

    public function test_shouldValidateNewFeedbackData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Titel muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewFeedbackData_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Zwischenquali, welches im Grunde genommen auch ohne Qualix geführt werden könnte, obwohl man natürlich hoffen würde dass Qualix einen bei der Kursführung unterstützen sollte, was wohl auch der Grund ist dass nun in diesem Kurs das endlich verfügbare Feedback-Feature eingesetzt werden soll... Tja.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Titel darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewFeedbackData_noParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewFeedbackData_invalidParticipantIds() {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewFeedbackData_oneValidParticipantId() {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals([$participantId], FeedbackData::latest()->first()->feedbacks()->pluck('participant_id')->all());
    }

    public function test_shouldValidateNewFeedbackData_multipleValidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEqualsCanonicalizing($participantIds, FeedbackData::latest()->first()->feedbacks()->pluck('participant_id')->all());
    }

    public function test_shouldValidateNewFeedbackData_someNonexistentParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewFeedbackData_someInvalidParticipantIds() {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewFeedbackData_noRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals([], Feedback::latest()->first()->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewFeedbackData_invalidRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_requirementsMismatch() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Die Anforderungen in der Vorlage stimmten nicht mit den relevanten Anforderungen überein. Wir haben das automatisch korrigiert. Kontrolliere ob jetzt alles stimmt und speichere erneut.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $requirementStatusId = $this->createRequirementStatus();
        $payload['requirements'] = $requirementId;
        $payload['feedback_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementId, 'status_id' => $requirementStatusId, 'comment' => '']],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals([$requirementId], Feedback::latest()->first()->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewFeedbackData_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $requirementStatusId1 = $this->createRequirementStatus();
        $requirementStatusId2 = $this->createRequirementStatus();
        $payload['requirements'] = implode(',', $requirementIds);
        $payload['feedback_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[0], 'status_id' => $requirementStatusId1, 'comment' => 'something']],
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[1], 'status_id' => $requirementStatusId2, 'comment' => '']],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals($requirementIds, Feedback::latest()->first()->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewFeedbackData_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 999999, $this->createRequirement()];
        $requirementStatusId1 = $this->createRequirementStatus();
        $requirementStatusId2 = $this->createRequirementStatus();
        $payload['requirements'] = implode(',', $requirementIds);
        $payload['feedback_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[0], 'status_id' => $requirementStatusId1]],
            ['type' => 'requirement', 'attrs' => ['id' => 999999, 'status_id' => $requirementStatusId1]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[1], 'status_id' => $requirementStatusId2]],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Relevante Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_someInvalidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 'abc', $this->createRequirement()];
        $requirementStatusId1 = $this->createRequirementStatus();
        $requirementStatusId2 = $this->createRequirementStatus();
        $payload['requirements'] = implode(',', $requirementIds);
        $payload['feedback_contents_template'] = json_encode(['type' => 'doc', 'content' => [
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[0], 'status_id' => $requirementStatusId1]],
            ['type' => 'requirement', 'attrs' => ['id' => 'abc', 'status_id' => $requirementStatusId1]],
            ['type' => 'requirement', 'attrs' => ['id' => $requirementIds[1], 'status_id' => $requirementStatusId2]],
        ]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_noFeedbackNotesTemplate() {
        // given
        $payload = $this->payload;
        unset($payload['feedback_contents_template']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Vorlage für Rückmeldungs-Text muss ausgefüllt sein.', $exception->validator->errors()->first('feedback_contents_template'));
    }

    public function test_shouldValidateNewFeedbackData_usesTiptapFormatter_forValidationOfFeedbackNotesTemplate() {
        // given
        $payload = $this->payload;
        $this->instance(TiptapFormatter::class, Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('isValid')
                ->once()
                ->with(
                    json_decode($this->payload['feedback_contents_template'], true),
                    Mockery::type(Collection::class),
                    Mockery::on(function ($observations) { return $observations instanceof Collection && $observations->isEmpty(); }),
                    Mockery::type(Collection::class),
                )
                ->andReturnFalse();
        })->makePartial());

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Vorlage für Rückmeldungs-Text ist ungültig.', $exception->validator->errors()->first('feedback_contents_template'));
    }

    public function test_shouldValidateNewFeedbackData_invalidTrainerAssignment() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['feedbacks'][$participantId]['users'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Zuständig für Pflock Format ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_nonexistentTrainerAssignment() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['feedbacks'][$participantId]['users'] = 999999;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_assigningTrainerFromOtherCourse() {
        // given
        $user2 = $this->createUser();
        $user2->courses()->attach($this->createCourse('Zweiter Kurs'));

        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['feedbacks'][$participantId]['users'] = $user2->id;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_existingTrainerAssignment_shouldWork() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userId = $this->course->users()->pluck('id')->first();
        $payload['feedbacks'][$participantId]['users'] = $userId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals($userId, Feedback::latest()->first()->users()->pluck('id')->join(','));
    }

    public function test_shouldValidateNewFeedbackData_multipleExistingTrainerAssignments_shouldWork() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $user2 = $this->createUser();
        $user2->courses()->attach($this->course);
        $userIds = $this->course->users()->pluck('id')->join(',');
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals($userIds, Feedback::latest()->first()->users()->pluck('id')->join(','));
    }

    public function test_shouldValidateNewFeedbackData_someForeignTrainerAssignments() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $user2 = $this->createUser();
        $user2->courses()->attach($this->createCourse('Other course'));
        $userIds = $this->course->users()->pluck('id')->first() . ',' . $user2->id;
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_someNonexistentTrainerAssignments() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userIds = $this->course->users()->pluck('id')->first() . ',9999999';
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_someInvalidTrainerAssignments() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userIds = $this->course->users()->first() . ',a';
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Zuständig für Pflock Format ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldShowMessage_whenNoFeedbacksInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/feedbacks');

        // then
        $response->assertOk();
        $response->assertSee('Bisher sind keine Rückmeldungen erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeFeedbacksInCourse() {
        // given
        $this->createFeedback();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/feedbacks');

        // then
        $response->assertOk();
        $response->assertDontSee('Bisher sind keine Rückmeldungen erfasst.');
    }
}
