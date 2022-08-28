<?php

namespace Tests\Feature\Admin\RequirementStatus;

use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateRequirementStatusTest extends TestCaseWithCourse {

    private $payload;
    private $requirementStatusId;

    public function setUp(): void {
        parent::setUp();

        $this->requirementStatusId = $this->createRequirementStatus('Gespraech ausstehend');

        $this->payload = ['name' => 'Geaenderter Status-Name', 'color' => 'red', 'icon' => 'binoculars'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateRequirementStatus() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement_status');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
        $response->assertDontSee('Gespraech ausstehend');
    }

    public function test_shouldValidateNewRequirementStatusData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Name muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewRequirementStatusData_longName() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Kategorienname 1Extrem langer Kategorienname 2Extrem langer Kategorienname 3Extrem langer Kategorienname 4Extrem langer Kategorienname 5Extrem langer Kategorienname 6Extrem langer Kategorienname 7Extrem langer Kategorienname 8Extrem langer Kategorienname 9Extrem langer Kategorienname 10Extrem langer Kategorienname 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . $this->requirementStatusId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Icon ist ungültig.', $exception->validator->errors()->first('icon'));
    }

    public function test_shouldValidateNewRequirementStatusData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement_status/' . ($this->requirementStatusId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
