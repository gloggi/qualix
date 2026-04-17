<?php

namespace Tests\Feature\Participant;

use App\Models\Course;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCaseWithBasicData;

class ReadParticipantTest extends TestCaseWithBasicData {

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayParticipant() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSee('Pflock');
    }

    public function test_shouldNotDisplayParticipant_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/participants/' . $this->participantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayParticipant_fromOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $otherParticipantId = Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/participants/' . $otherParticipantId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoObservationForParticipant() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertSee('Keine Beobachtungen gefunden.');
    }

    public function test_shouldNotShowMessage_whenSomeObservationForParticipant() {
        // given
        $this->createObservation();

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId);

        // then
        $response->assertOk();
        $response->assertDontSee('Keine Beobachtungen gefunden.');
    }

    public function test_shouldEscapeHTML_whenDisplayingParticipant() {
        // given
        $participantName = '<b>Bar</b>i\'"';
        $participantId = $this->createParticipant($participantName);
        $userName = 'Co<i>si</i>nus\'"';
        $this->createUser(['name' => $userName])->courses()->attach($this->courseId);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $participantId);

        // then
        $response->assertOk();
        $response->assertDontSee($participantName, false);
        $response->assertSee('&quot;&lt;b&gt;Bar&lt;\/b&gt;i&#039;\&quot;&quot;', false);
        $response->assertDontSee($userName, false);
        $response->assertSee('&quot;Co&lt;i&gt;si&lt;\/i&gt;nus&#039;\&quot;&quot;', false);
    }

    public function test_shouldRequireLogin_whenReadingParticipantImage() {
        // given
        Storage::fake();

        $participant = Participant::findOrFail($this->participantId);
        $participant->update([
            'image_url' => UploadedFile::fake()->image('participant.png')->store('participant-images'),
        ]);

        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/image');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayParticipantImage_forTrainerOfCourse() {
        // given
        Storage::fake();

        $participant = Participant::findOrFail($this->participantId);
        $path = UploadedFile::fake()->image('participant.png')->store('participant-images');
        $participant->update(['image_url' => $path]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/image');

        // then
        $response->assertOk();
        $response->assertHeader('Content-Type', Storage::mimeType($path) ?? 'application/octet-stream');
    }

    public function test_shouldReturn404_whenParticipantHasNoImage() {
        // given
        $participant = Participant::findOrFail($this->participantId);
        $participant->update(['image_url' => null]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/image');

        // then
        $response->assertNotFound();
    }

    public function test_shouldNotDisplayParticipantImage_fromOtherCourseOfSameUser() {
        // given
        Storage::fake();

        $participant = Participant::findOrFail($this->participantId);
        $participant->update([
            'image_url' => UploadedFile::fake()->image('participant.png')->store('participant-images'),
        ]);

        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/participants/' . $this->participantId . '/image');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayParticipantImage_fromOtherUser() {
        // given
        Storage::fake();

        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $otherParticipantId = Participant::create([
            'course_id' => $otherKursId,
            'scout_name' => 'Pflock',
            'image_url' => UploadedFile::fake()->image('participant.png')->store('participant-images'),
        ])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/participants/' . $otherParticipantId . '/image');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldReturn404_whenImageFileIsMissing() {
        // given
        Storage::fake();

        $participant = Participant::findOrFail($this->participantId);
        $participant->update([
            'image_url' => 'participant-images/missing-file.png',
        ]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/participants/' . $this->participantId . '/image');

        // then
        $response->assertNotFound();
    }
}
