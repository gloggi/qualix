<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateCourseTest extends TestCase {

    private $payload;
    private $kursId;

    public function setUp(): void {
        parent::setUp();

        // Create Kurs to test on
        $this->post('/neuerkurs', ['name' => 'Kursname', 'kursnummer' => 'CH 123-00']);

        /** @var User $user */
        $user = Auth::user();
        $this->kursId = $user->lastAccessedKurs->id;
        $this->payload = ['name' => 'Geänderter Kursname', 'kursnummer' => 'CH 999-99'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateCourse() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$this->payload['name']}</option>%", $response->content());
    }

    public function test_shouldValidateNewCourseData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCourseData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . ($this->kursId+1) . '/admin', $payload);

        // then
        $response->assertStatus(404);
    }

    public function test_shouldValidateNewCourseData_noId() {
        // given
        $payload = $this->payload;
        unset($payload['id']);

        // when
        $response = $this->post('/admin/kurs', $payload);

        // then
        $response->assertStatus(404);
    }
}
