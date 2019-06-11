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

    public function test_shouldValidateNewCourseData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . ($this->courseId+1) . '/admin', $payload);

        // then
        $response->assertStatus(404);
    }
}
