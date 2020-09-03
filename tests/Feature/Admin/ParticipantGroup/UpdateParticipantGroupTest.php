<?php

namespace Tests\Feature\Admin\ParticipantGroup;

use App\Models\Course;
use App\Models\ParticipantGroup;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateParticipantGroupTest extends TestCaseWithBasicData
{


    private $participantGroupId;
    private $payload;

    public function setUp(): void
    {
        parent::setUp();
        $this->participantGroupId = $this->createParticipantGroup("UN Gruppe 1");


        $this->payload = ['participants' => '' . $this->participantId, 'group_name' => 'Bessere Gruppe'];
    }

    public function test_shouldRequireLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse()
    {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldUpdateParticipantGroup()
    {
        // given

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups/' );
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['group_name']);
        $response->assertDontSee('UN Gruppe 1');
    }

    public function test_shouldValidateParticipantGroupData_noParticipantIds()
    {
        // given
        $payload = $this->payload;
        $payload['participants'] = '';

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Teilnehmer muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroupData_invalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Teilnehmer Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroupData_oneValidParticipantId()
    {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups/');
        $this->assertEquals([$participantId], ParticipantGroup::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewParticipantGroupData_multipleValidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups/');
        $this->assertEquals($participantIds, ParticipantGroup::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewParticipantGroupData_someNonexistentParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Teilnehmer ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroupData_someInvalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Teilnehmer Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroupData_multipleValidParticipantIds_shouldWork()
    {
        // given
        $participantId2 = $this->createParticipant('Cozy');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Teilnehmergruppe wurde erfolgreich gespeichert.');
    }

    public function test_shouldValidateNewParticipantGroupData_noName()
    {
        // given
        $payload = $this->payload;
        unset($payload['group_name']);

        // when
        $response = $this->put('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gruppe muss ausgefüllt sein.', $exception->validator->errors()->first('group_name'));
    }

    public function test_shouldValidateNewParticipantGroupData_longName()
    {
        // given
        $payload = $this->payload;
        $payload['group_name'] = 'Unglaublich langer Name. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gruppe darf maximal 1023 Zeichen haben.', $exception->validator->errors()->first('group_name'));
    }

    public function test_shouldNotAllowChangingParticipantToSomeoneFromADifferentCourse()
    {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $participantFromDifferentCourse = $this->createParticipant('Foreign', $differentCourse);
        $payload = $this->payload;
        $payload['participants'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->patch('/course/' . $this->courseId . '/admin/participantGroups/' . $this->participantGroupId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Teilnehmer ist ungültig.', $exception->validator->errors()->first('participants'));
    }

 }
