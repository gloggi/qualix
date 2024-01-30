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

class UpdateFeedbackTest extends TestCaseWithBasicData {

    private $course;
    private $payload;
    private $feedbackId;
    private $feedbackDataId;

    public function setUp(): void {
        parent::setUp();

        $this->course = Course::find($this->courseId);
        $this->feedbackId = $this->createFeedback('Zwischenquali');
        $this->feedbackDataId = Feedback::find($this->feedbackId)->feedback_data_id;

        $this->payload = [
            'name' => 'Zwischenquali',
            'participants' => $this->course->participants()->pluck('id')->implode(','),
            'requirements' => $this->course->requirements()->pluck('id')->implode(','),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldUpdateAndDisplayFeedback() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldUpdateFeedback_usesTiptapFormatter_forUpdatingFeedbackContent_whenRequirementsChanged() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement('Zusätzliche Anforderung');
        $payload['requirements'] = implode(',', array_filter([$payload['requirements'], $requirementId]));
        $mock = Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('appendRequirementsToFeedback')
                ->once()
                ->with(Mockery::type(Collection::class));
        })->makePartial();
        $this->app->extend(TiptapFormatter::class, function() use ($mock) { return $mock; });

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
    }

    public function test_shouldValidateNewFeedbackData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals([$requirementId], Feedback::find($this->feedbackId)->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewFeedbackData_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals($requirementIds, Feedback::find($this->feedbackId)->requirements()->pluck('requirements.id')->all());
    }

    public function test_shouldValidateNewFeedbackData_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 999999, $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_tooManyValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = array_map(function () { return $this->createRequirement(); }, range(1, 41));
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Relevante Anforderungen darf nicht mehr als 40 ausgewählte Elemente haben.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewFeedbackData_invalidTrainerAssignment() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $payload['feedbacks'][$participantId]['users'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals($userId, Feedback::find($this->feedbackId)->users()->pluck('id')->join(','));
    }

    public function test_shouldValidateNewFeedbackData_multipleExistingTrainerAssignment_shouldWork() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $user2 = $this->createUser();
        $user2->courses()->attach($this->course);
        $userIds = $this->course->users()->pluck('id')->first();
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals($userIds, Feedback::find($this->feedbackId)->users()->pluck('id')->join(','));
    }

    public function test_shouldValidateNewFeedbackData_someInvalidTrainerAssignments() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userIds = $this->course->users()->pluck('id')->first() . ',a';
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Zuständig für Pflock Format ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_someNonexistentTrainerAssignments() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userIds = $this->course->users()->pluck('id')->first() . ',9999999';
        $payload['feedbacks'][$participantId]['users'] = $userIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
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
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Zuständig für Pflock ist ungültig.', $exception->validator->errors()->first("feedbacks.$participantId.users"));
    }

    public function test_shouldValidateNewFeedbackData_removePreviousTrainerAssignment_shouldWork() {
        // given
        $payload = $this->payload;
        $participantId = $this->course->participants()->pluck('id')->first();
        $userId = $this->course->users()->pluck('id')->first();
        Feedback::find($this->feedbackId)->users()->attach($userId);
        $payload['feedbacks'][$participantId]['users'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/feedbacks/' . $this->feedbackDataId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/feedbacks');
        $this->assertEquals(null, Feedback::find($this->feedbackId)->users()->pluck('id')->join(','));
    }
}
