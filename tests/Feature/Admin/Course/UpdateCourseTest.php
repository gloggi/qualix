<?php

namespace Tests\Feature\Admin\Course;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateCourseTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Geänderter Kursname', 'course_number' => 'CH 999-99'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateCourse() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$this->payload['name']}</option>%", $response->content());
    }

    public function test_shouldValidateNewCourseData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCourseData_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Kursname 1Extrem langer Kursname 2Extrem langer Kursname 3Extrem langer Kursname 4Extrem langer Kursname 5Extrem langer Kursname 6Extrem langer Kursname 7Extrem langer Kursname 8Extrem langer Kursname 9Extrem langer Kursname 10Extrem langer Kursname 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCourseData_longCourseNumber() {
        // given
        $payload = $this->payload;
        $payload['course_number'] = 'Extrem lange Kursnummer 1Extrem lange Kursnummer 2Extrem lange Kursnummer 3Extrem lange Kursnummer 4Extrem lange Kursnummer 5Extrem lange Kursnummer 6Extrem lange Kursnummer 7Extrem lange Kursnummer 8Extrem lange Kursnummer 9Extrem lange Kursnummer 10Extrem lange Kursnummer 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCourseData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . ($this->courseId+1) . '/admin', $payload);

        // then
        $response->assertStatus(404);
    }
}
