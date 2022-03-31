<?php

namespace Tests\Feature\Admin\Participant;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateParticipantTest extends TestCaseWithCourse {

    private $payload;
    private $participantId;

    public function setUp(): void {
        parent::setUp();

        $this->participantId = $this->createParticipant('Qualm');

        $this->payload = ['scout_name' => 'Räuchli', 'freetext' => 'No nie het de Räuchli eppis gseit, da mümmer gnauer anneluege o_O'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldUpdateParticipant() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
        $response->assertDontSee('Qualm');
    }

    public function test_shouldValidateNewParticipantData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['scout_name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Pfadiname muss ausgefüllt sein.', $exception->validator->errors()->first('scout_name'));
    }

    public function test_shouldValidateNewParticipantData_noFreetext() {
        // given
        $payload = $this->payload;
        unset($payload['freetext']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['scout_name']);
        $response->assertDontSee('Qualm');
    }

    public function test_shouldValidateNewParticipantData_longScoutName() {
        // given
        $payload = $this->payload;
        $payload['scout_name'] = 'Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname Unglaublich langer Pfadiname';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . $this->participantId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Abteilung darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('group'));
    }

    public function test_shouldValidateNewParticipantData_longFreetext() {
        // given
        $payload = $this->payload;
        $tooLong = 'a';
        for ($i = 0; $i < 16; $i++) {
            $tooLong = $tooLong . $tooLong;
        }
        // $tooLong is now 65536 characters long
        $payload['freetext'] = $tooLong;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Freitext darf maximal 65535 Zeichen haben.', $exception->validator->errors()->first('freetext'));
    }

    public function test_shouldValidateNewParticipantData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/' . ($this->participantId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
