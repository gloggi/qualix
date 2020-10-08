<?php

namespace Tests\Feature\Admin\ObservationAssignment;

use App\Models\Course;
use App\Models\ObservationAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class CreateObservationAssignmentTest extends TestCaseWithBasicData
{

    private $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->payload = ['name' => 'Auftrag 1', 'participants' => '' . $this->participantId, 'users' => '' . Auth::id(), 'blocks' => '' . $this->blockId];
    }

    public function test_shouldRequireLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse()
    {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldCreateAndDisplayObservationAssignment()
    {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtungsauftrag wurde erfolgreich erstellt.');
    }

    public function test_shouldValidateNewObservationAssignment_noName()
    {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beobachtungsauftrag muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewObservationAssignment_longContent()
    {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Unglaublich langer Auftragsname. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Beobachtungsauftrag darf maximal 1023 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewObservationAssignment_noParticipantIds()
    {
        // given
        $payload = $this->payload;
        unset($payload['participants']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationAssignment_invalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationAssignment_oneValidParticipantId()
    {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        $this->assertEquals([$participantId], ObservationAssignment::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationAssignment_multipleValidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        $this->assertEquals($participantIds, ObservationAssignment::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationAssignment_someNonexistentParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationAssignment_someInvalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldShowEscapedNotice_afterCreatingObservationAssignment()
    {
        // given
        $participantName = '<b>Participant name</b> with \'some" formatting';
        $payload = $this->payload;
        $payload['participants'] = $this->createParticipant($participantName);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload)->followRedirects();

        // then
        $response->assertDontSee($participantName, false);
        $response->assertSee('&lt;b&gt;Participant name&lt;\/b&gt; with &#039;some\&quot; formatting', false);
    }

    public function test_shouldNotAllowCreatingObservationAssignment_withParticipantFromADifferentCourse()
    {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $participantFromDifferentCourse = $this->createParticipant('Foreign', $differentCourse);
        $payload = $this->payload;
        $payload['participants'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewObservationAssignment_noUserIds()
    {
        // given
        $payload = $this->payload;
        unset($payload['users']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Equipe muss ausgefüllt sein.', $exception->validator->errors()->first('users'));
    }

    public function test_shouldValidateNewObservationAssignment_invalidUserIds()
    {
        // given
        $payload = $this->payload;
        $payload['users'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Equipe Format ist ungültig.', $exception->validator->errors()->first('users'));
    }

    public function test_shouldValidateNewObservationAssignment_oneValidUserId()
    {
        // given
        $payload = $this->payload;
        $userId = $this->createUser()->id;
        Course::find($this->courseId)->users()->attach($userId);
        $payload['users'] = $userId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        $this->assertEquals([$userId], ObservationAssignment::latest()->first()->users->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationAssignment_multipleValidUserIds()
    {
        // given
        $payload = $this->payload;
        $userIds = [$this->createUser()->id, $this->createUser()->id];
        Course::find($this->courseId)->users()->attach($userIds);
        $payload['users'] = implode(',', $userIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        $this->assertEquals($userIds, ObservationAssignment::latest()->first()->users->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationAssignment_someNonexistentUserIds()
    {
        // given
        $payload = $this->payload;
        $userIds = [$this->createUser()->id, '999999', $this->createUser()->id];
        Course::find($this->courseId)->users()->attach([$userIds[0], $userIds[2]]);
        $payload['users'] = implode(',', $userIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Equipe ist ungültig.', $exception->validator->errors()->first('users'));
    }

    public function test_shouldValidateNewObservationAssignment_someInvalidUserIds()
    {
        // given
        $payload = $this->payload;
        $userIds = [$this->createUser()->id, 'abc', $this->createUser()->id];
        Course::find($this->courseId)->users()->attach([$userIds[0], $userIds[2]]);
        $payload['users'] = implode(',', $userIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Equipe Format ist ungültig.', $exception->validator->errors()->first('users'));
    }

    public function test_shouldNotAllowCreatingObservationAssignment_withUserFromADifferentCourse()
    {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $userFromDifferentCourse = $this->createUser(['name' => 'Foreign'])->id;
        Course::find($differentCourse)->users()->attach($userFromDifferentCourse);
        $payload = $this->payload;
        $payload['users'] = Auth::id() . ',' . $userFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Equipe ist ungültig.', $exception->validator->errors()->first('users'));
    }

    public function test_shouldValidateNewObservationAssignment_noBlockIds()
    {
        // given
        $payload = $this->payload;
        unset($payload['blocks']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blöcke muss ausgefüllt sein.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewObservationAssignment_invalidBlockIds()
    {
        // given
        $payload = $this->payload;
        $payload['blocks'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blöcke Format ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewObservationAssignment_oneValidBlockId()
    {
        // given
        $payload = $this->payload;
        $blockId = $this->createBlock();
        $payload['blocks'] = $blockId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        $this->assertEquals([$blockId], ObservationAssignment::latest()->first()->blocks->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationAssignment_multipleValidBlockIds()
    {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/observationAssignments');
        $this->assertEquals($blockIds, ObservationAssignment::latest()->first()->blocks->pluck('id')->all());
    }

    public function test_shouldValidateNewObservationAssignment_someNonexistentBlockIds()
    {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), '999999', $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Blöcke ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewObservationAssignment_someInvalidBlockIds()
    {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), 'abc', $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blöcke Format ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldNotAllowCreatingObservationAssignment_withBlockFromADifferentCourse()
    {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $blockFromDifferentCourse = $this->createBlock('Foreign', '1.1', '01.01.2019', null, $differentCourse);
        $payload = $this->payload;
        $payload['blocks'] = $this->blockId . ',' . $blockFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/observationAssignments', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Blöcke ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

}
