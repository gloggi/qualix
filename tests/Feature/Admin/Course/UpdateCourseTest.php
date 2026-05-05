<?php

namespace Tests\Feature\Admin\Course;

use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateCourseTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'Geänderter Kursname', 'course_number' => 'CH 999-99', 'uses_impressions' => '1', 'observation_count_red_threshold' => 5, 'observation_count_green_threshold' => 10];
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
        $this->assertMatchesRegularExpression("%<b-form-select-option value=\"[^\"]*\" selected>{$this->payload['name']}</b-form-select-option>%", $response->content());
    }

    public function test_shouldValidateNewCourseData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Kursnummer darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('course_number'));
    }

    public function test_shouldValidateNewCourseData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['uses_impressions'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Eindruck auf Beobachtungen aktivieren muss entweder \'true\' oder \'false\' sein.', $exception->validator->errors()->first('uses_impressions'));
    }

    public function test_shouldValidateNewCourseData_noRedThreshold() {
        // given
        $payload = $this->payload;
        unset($payload['observation_count_red_threshold']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Mindestanzahl Beobachtungen muss ausgefüllt sein.', $exception->validator->errors()->first('observation_count_red_threshold'));
    }

    public function test_shouldValidateNewCourseData_nonIntegerRedThreshold() {
        // given
        $payload = $this->payload;
        $payload['observation_count_red_threshold'] = '1.1';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Mindestanzahl Beobachtungen muss eine ganze Zahl sein.', $exception->validator->errors()->first('observation_count_red_threshold'));
    }

    public function test_shouldValidateNewCourseData_negativeRedThreshold() {
        // given
        $payload = $this->payload;
        $payload['observation_count_red_threshold'] = '-10';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Mindestanzahl Beobachtungen muss mindestens 0 sein.', $exception->validator->errors()->first('observation_count_red_threshold'));
    }

    public function test_shouldValidateNewCourseData_noGreenThreshold() {
        // given
        $payload = $this->payload;
        unset($payload['observation_count_green_threshold']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gewünschte Anzahl Beobachtungen muss ausgefüllt sein.', $exception->validator->errors()->first('observation_count_green_threshold'));
    }

    public function test_shouldValidateNewCourseData_nonIntegerGreenThreshold() {
        // given
        $payload = $this->payload;
        $payload['observation_count_green_threshold'] = '1.1';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gewünschte Anzahl Beobachtungen muss eine ganze Zahl sein.', $exception->validator->errors()->first('observation_count_green_threshold'));
    }

    public function test_shouldValidateNewCourseData_negativeGreenThreshold() {
        // given
        $payload = $this->payload;
        $payload['observation_count_green_threshold'] = '-10';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gewünschte Anzahl Beobachtungen muss mindestens 0 sein.', $exception->validator->errors()->first('observation_count_green_threshold'));
    }

    public function test_shouldValidateNewCourseData_greenThresholdSmallerThanRedThreshold() {
        // given
        $payload = $this->payload;
        $payload['observation_count_green_threshold'] = '2';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Gewünschte Anzahl Beobachtungen muss mindestens 5 sein.', $exception->validator->errors()->first('observation_count_green_threshold'));
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
