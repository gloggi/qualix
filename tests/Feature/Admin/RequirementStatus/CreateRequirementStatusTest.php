<?php

namespace Tests\Feature\Admin\RequirementStatus;

use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateRequirementStatusTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = [
            'name' => 'Gespräch ausstehend',
            'color' => 'blue',
            'icon' => 'binoculars',
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayRequirementStatus() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement_status');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
    }

    public function test_shouldValidateNewRequirementStatusData_noRequirementStatusName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Name muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewRequirementStatusData_longRequirementStatusName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Kategorienname 1Extrem langer Kategorienname 2Extrem langer Kategorienname 3Extrem langer Kategorienname 4Extrem langer Kategorienname 5Extrem langer Kategorienname 6Extrem langer Kategorienname 7Extrem langer Kategorienname 8Extrem langer Kategorienname 9Extrem langer Kategorienname 10Extrem langer Kategorienname 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Name darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewRequirementStatusData_noColor() {
        // given
        $payload = $this->payload;
        unset($payload['color']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Farbe muss ausgefüllt sein.', $exception->validator->errors()->first('color'));
    }

    public function test_shouldValidateNewRequirementStatusData_invalidColor() {
        // given
        $payload = $this->payload;
        $payload['color'] = '#ff00ff';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Farbe ist ungültig.', $exception->validator->errors()->first('color'));
    }

    public function test_shouldValidateNewRequirementStatusData_noIcon() {
        // given
        $payload = $this->payload;
        unset($payload['icon']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Icon muss ausgefüllt sein.', $exception->validator->errors()->first('icon'));
    }

    public function test_shouldValidateNewRequirementStatusData_invalidIcon() {
        // given
        $payload = $this->payload;
        $payload['icon'] = 'tree';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Icon ist ungültig.', $exception->validator->errors()->first('icon'));
    }
}
