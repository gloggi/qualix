<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateCourseTest extends TestCase {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Kursname', 'kursnummer' => 'CH 123-00'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/neuerkurs', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndAutoselectCourse() {
        // given

        // when
        $response = $this->post('/neuerkurs', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertRegExp("%<option value=\"[^\"]*\" selected>{$this->payload['name']}</option>%", $response->content());
    }

    public function test_shouldValidateNewCourseData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/neuerkurs', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }
}
