<?php

namespace Tests\Feature\Admin\ParticipantGroup;

use App\Models\Course;
use App\Models\ParticipantGroup;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateParticipantGroupTest extends TestCaseWithCourse
{

    private $payload;
    private $participantId;

    public function setUp(): void
    {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pflock');
        $this->payload = ['group_name' => 'Unternehmungsgruppe 1', 'participants' => '' . $this->participantId];
    }

    public function test_shouldRequireLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse()
    {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldCreateAndDisplayParticipantGroup()
    {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Teilnehmergruppe wurde erfolgreich erstellt.');
    }

    public function test_shouldValidateNewParticipantGroup_noParticipantIds()
    {
        // given
        $payload = $this->payload;
        unset($payload['participants']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Teilnehmer muss ausgefüllt sein.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroup_invalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $payload['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Teilnehmer Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroup_oneValidParticipantId()
    {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups');
        $this->assertEquals([$participantId], ParticipantGroup::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewParticipantGroup_multipleValidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups');
        $this->assertEquals($participantIds, ParticipantGroup::latest()->first()->participants->pluck('id')->all());
    }

    public function test_shouldValidateNewParticipantGroup_someNonexistentParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), '999999', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Teilnehmer ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroup_someInvalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Teilnehmer Format ist ungültig.', $exception->validator->errors()->first('participants'));
    }

    public function test_shouldValidateNewParticipantGroup_multipleValidParticipantIds_shouldWork()
    {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Teilnehmergruppe wurde erfolgreich erstellt.');
    }

    public function test_createParticipantGroupWitMultipleParticipantIds_shouldLinkTheParticipantGroup()
    {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participants'] = $participantIds;
        $payload['group_name'] = 'visible on both participants';

        // when
        $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);
        $response->assertSee('visible on both participants');
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $participantId2);
        $response->assertSee('visible on both participants');
    }

    public function test_shouldValidateNewParticipantGroup_noGroupName()
    {
        // given
        $payload = $this->payload;
        unset($payload['group_name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gruppe muss ausgefüllt sein.', $exception->validator->errors()->first('group_name'));
    }

    public function test_shouldValidateNewParticipantGroup_longContent()
    {
        // given
        $payload = $this->payload;
        $payload['group_name'] = 'Unglaublich langer Gruppenname. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gruppe darf maximal 1023 Zeichen haben.', $exception->validator->errors()->first('group_name'));
    }


    public function test_shouldShowEscapedNotice_afterCreatingParticipantGroup()
    {
        // given
        $participantName = '<b>Participant name</b> with \'some" formatting';
        $payload = $this->payload;
        $payload['participants'] = $this->createParticipant($participantName);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload)->followRedirects();

        // then
        $response->assertDontSee($participantName, false);
        $response->assertSee(htmlspecialchars($participantName, ENT_QUOTES), false);
    }

    public function test_shouldNotAllowCreatingParticipantGroup_withParticipantFromADifferentCourse()
    {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $participantFromDifferentCourse = $this->createParticipant('Foreign', $differentCourse);
        $payload = $this->payload;
        $payload['participants'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Teilnehmer ist ungültig.', $exception->validator->errors()->first('participants'));
    }

}
