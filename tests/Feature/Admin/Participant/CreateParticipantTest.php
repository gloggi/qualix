<?php

namespace Tests\Feature\Admin\Participant;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateParticipantTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['scout_name' => 'Pflock'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldCreateAndDisplayParticipant() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
    }

    public function test_shouldValidateNewParticipantData_noScoutName() {
        // given
        $payload = $this->payload;
        unset($payload['scout_name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Pfadiname muss ausgefüllt sein.', $exception->validator->errors()->first('scout_name'));
    }

    public function test_shouldValidateNewParticipantData_longScoutName() {
        // given
        $payload = $this->payload;
        $payload['scout_name'] = 'Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Pfadiname darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('scout_name'));
    }

    public function test_shouldValidateNewParticipantData_longGroup() {
        // given
        $payload = $this->payload;
        $payload['group'] = 'Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname Unglaublich langer Gruppenname';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Abteilung darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('group'));
    }

    public function test_shouldShowMessage_whenNoParticipantInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants');

        // then
        $response->assertOk();
        $response->assertSee('Bisher sind keine Teilnehmende erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeParticipantInCourse() {
        // given
        $this->createParticipant();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants');

        // then
        $response->assertOk();
        $response->assertDontSee('Bisher sind keine Teilnehmende erfasst.');
    }
}
