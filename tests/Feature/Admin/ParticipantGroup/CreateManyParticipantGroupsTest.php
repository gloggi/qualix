<?php

namespace Tests\Feature\Admin\ParticipantGroup;

use App\Models\Course;
use App\Models\ParticipantGroup;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateManyParticipantGroupsTest extends TestCaseWithCourse
{

    private $payload;
    private $participantId;
    private $participantId2;

    public function setUp(): void
    {
        parent::setUp();

        $this->participantId = $this->createParticipant('Pflock');
        $this->participantId2 = $this->createParticipant('Pfnörch');
        $this->payload = ['participantGroups' => [[
            ['group_name' => 'Unternehmungsgruppe 1', 'participants' => '' . $this->participantId],
            ['group_name' => 'Unternehmungsgruppe 2', 'participants' => '' . $this->participantId2],
        ]]];
    }

    public function test_shouldRequireLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse()
    {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldCreateAndDisplayParticipantGroups()
    {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('TN-Gruppen wurden erfolgreich erstellt.');
        $response->assertSee('Unternehmungsgruppe 1');
        $response->assertSee('Unternehmungsgruppe 2');
    }

    public function test_shouldValidateNewParticipantGroup_noParticipantIds()
    {
        // given
        $payload = $this->payload;
        unset($payload['participantGroups'][0][0]['participants']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN muss ausgefüllt sein.', $exception->validator->errors()->first('participantGroups.0.0.participants'));
    }

    public function test_shouldValidateNewParticipantGroup_invalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $payload['participantGroups'][0][0]['participants'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participantGroups.0.0.participants'));
    }

    public function test_shouldValidateNewParticipantGroup_oneValidParticipantId()
    {
        // given
        $payload = $this->payload;
        $participantId = $this->createParticipant();
        $payload['participantGroups'][0][0]['participants'] = $participantId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

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
        $payload['participantGroups'][0][0]['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

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
        $payload['participantGroups'][0][0]['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participantGroups.0.0.participants'));
    }

    public function test_shouldValidateNewParticipantGroup_someInvalidParticipantIds()
    {
        // given
        $payload = $this->payload;
        $participantIds = [$this->createParticipant(), 'abc', $this->createParticipant()];
        $payload['participantGroups'][0][0]['participants'] = implode(',', $participantIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('TN Format ist ungültig.', $exception->validator->errors()->first('participantGroups.0.0.participants'));
    }

    public function test_shouldValidateNewParticipantGroup_multipleValidParticipantIds_shouldWork()
    {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participantGroups'][0][0]['participants'] = $participantIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participantGroups');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('TN-Gruppen wurden erfolgreich erstellt.');
    }

    public function test_createParticipantGroupWitMultipleParticipantIds_shouldLinkTheParticipantGroup()
    {
        // given
        $participantId2 = $this->createParticipant('Pfnörch');
        $participantIds = $this->participantId . ',' . $participantId2;
        $payload = $this->payload;
        $payload['participantGroups'][0][0]['participants'] = $participantIds;
        $payload['participantGroups'][0][0]['group_name'] = 'visible on both participants';

        // when
        $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

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
        unset($payload['participantGroups'][0][0]['group_name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gruppenname muss ausgefüllt sein.', $exception->validator->errors()->first('participantGroups.0.0.group_name'));
    }

    public function test_shouldValidateNewParticipantGroup_longGroupName()
    {
        // given
        $payload = $this->payload;
        $payload['participantGroups'][0][0]['group_name'] = 'Unglaublich langer Gruppenname. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr. Und noch etwas mehr.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gruppenname darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('participantGroups.0.0.group_name'));
    }

    public function test_shouldShowEscapedNotice_afterCreatingParticipantGroup()
    {
        // given
        $participantName = '<b>Participant name</b> with \'some" formatting';
        $groupName = '<b>Group name</b> with \'some" formatting';
        $payload = $this->payload;
        $payload['participantGroups'][0][0]['participants'] = $this->createParticipant($participantName);
        $payload['participantGroups'][0][0]['group_name'] = $groupName;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload)->followRedirects();

        // then
        $response->assertDontSee($participantName, false);
        $response->assertSee('&lt;b&gt;Participant name&lt;\/b&gt; with &#039;some\&quot; formatting', false);
        $response->assertSee('&lt;b&gt;Group name&lt;\/b&gt; with &#039;some\&quot; formatting', false);
    }

    public function test_shouldNotAllowCreatingParticipantGroup_withParticipantFromADifferentCourse()
    {
        // given
        $differentCourse = $this->createCourse('Other course', '', false);
        $participantFromDifferentCourse = $this->createParticipant('Foreign', $differentCourse);
        $payload = $this->payload;
        $payload['participantGroups'][0][0]['participants'] = $this->participantId . ',' . $participantFromDifferentCourse;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participantGroups/storeMany', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für TN ist ungültig.', $exception->validator->errors()->first('participantGroups.0.0.participants'));
    }

}
