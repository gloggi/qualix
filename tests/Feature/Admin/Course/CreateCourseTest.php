<?php

namespace Tests\Feature\Admin\Course;

use App\Models\Course;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateCourseTest extends TestCase {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Kursname', 'course_number' => 'CH 123-00', 'uses_impressions' => '1', 'observation_count_red_threshold' => 5, 'observation_count_green_threshold' => 10];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/newcourse', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndAutoselectCourse() {
        // given

        // when
        $response = $this->post('/newcourse', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $this->assertMatchesRegularExpression("%<b-form-select-option value=\"[^\"]+\" selected>" . $this->payload['name'] . "</b-form-select-option>%s", $response->content());
    }

    public function test_shouldCreateDefaultRequirementStatuses() {
        // given

        // when
        $response = $this->post('/newcourse', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $course = Course::find($response->original->getData()['course']->id);

        $this->assertEquals(Course::query()->orderBy('id', 'DESC')->limit(1)->get()->first(), $course);
        $this->assertEquals(3, $course->requirement_statuses()->count());
        $this->assertEquals(collect([
            ['name' => 'unter Beobachtung', 'color' => 'gray-500', 'icon' => 'binoculars'],
            ['name' => 'erfüllt', 'color' => 'green', 'icon' => 'circle-check'],
            ['name' => 'nicht erfüllt', 'color' => 'red', 'icon' => 'circle-xmark'],
        ]), $course->requirement_statuses->map->only('name', 'color', 'icon'));
    }

    public function test_shouldValidateNewCourseData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/newcourse', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kursname muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewCourseData_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Kursname 1Extrem langer Kursname 2Extrem langer Kursname 3Extrem langer Kursname 4Extrem langer Kursname 5Extrem langer Kursname 6Extrem langer Kursname 7Extrem langer Kursname 8Extrem langer Kursname 9Extrem langer Kursname 10Extrem langer Kursname 11';

        // when
        $response = $this->post('/newcourse', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kursname darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewCourseData_longCourseNumber() {
        // given
        $payload = $this->payload;
        $payload['course_number'] = 'Extrem lange Kursnummer 1Extrem lange Kursnummer 2Extrem lange Kursnummer 3Extrem lange Kursnummer 4Extrem lange Kursnummer 5Extrem lange Kursnummer 6Extrem lange Kursnummer 7Extrem lange Kursnummer 8Extrem lange Kursnummer 9Extrem lange Kursnummer 10Extrem lange Kursnummer 11';

        // when
        $response = $this->post('/newcourse', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewCourseData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['uses_impressions'] = '3';

        // when
        $response = $this->post('/newcourse', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Eindruck auf Beobachtungen aktivieren muss entweder \'true\' oder \'false\' sein.', $exception->validator->errors()->first('uses_impressions'));
    }
}
