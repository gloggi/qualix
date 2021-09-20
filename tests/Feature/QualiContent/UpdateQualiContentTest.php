<?php

namespace Tests\Feature\QualiContent;

use App\Models\Quali;
use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCaseWithBasicData;

class UpdateQualiContentTest extends TestCaseWithBasicData {

    protected $payload;
    protected $qualiId;
    protected $requirementId;

    public function setUp(): void {
        parent::setUp();

        $this->qualiId = $this->createQuali('Zwischequali');
        $quali = Quali::find($this->qualiId);
        $this->requirementId = $this->createRequirement();
        $quali->requirements()->attach([$this->requirementId => ['passed' => null, 'order' => 10]]);

        $this->payload = [
            'quali_contents' => json_encode(['type' => 'doc', 'content' => [
                ['type' => 'paragraph'],
                ['type' => 'requirement', 'attrs' => ['id' => $this->requirementId, 'passed' => null]]
            ]]),
        ];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldWork() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId, $this->payload);

        // then
        $response->assertOk();
    }

    public function test_shouldUseTiptapFormatterForUpdating() {
        // given
        $payload = $this->payload;
        $mock = Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('applyToQuali')
                ->once()
                ->with(json_decode($this->payload['quali_contents'], true));
        })->makePartial();
        $this->app->extend(TiptapFormatter::class, function() use ($mock) { return $mock; });

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId, $payload);

        // then
        $response->assertOk();
    }

    public function test_shouldValidateNewQualiData_requirementsMismatch() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['quali_contents'] = json_encode(['type' => 'doc', 'content' => [['type' => 'paragraph']]]);

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Die Änderungen konnten nicht gespeichert werden, weil die Anforderungen im Quali inzwischen geändert wurden. Kontrolliere ob alles stimmt und speichere dann erneut.', $exception->validator->errors()->first('quali_contents'));
    }

    public function test_shouldValidateNewQualiData_noQualiContent() {
        // given
        $payload = $this->payload;
        unset($payload['quali_contents']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Quali-Text muss ausgefüllt sein.', $exception->validator->errors()->first('quali_contents'));
    }

    public function test_shouldValidateNewQualiData_usesTiptapFormatter_forValidationOfQualiNotesTemplate() {
        // given
        $payload = $this->payload;
        $this->instance(TiptapFormatter::class, Mockery::mock(TiptapFormatter::class, function ($mock) {
            $mock->shouldReceive('isValid')
                ->once()
                ->with(
                    json_decode($this->payload['quali_contents'], true),
                    Mockery::type(Collection::class),
                    Mockery::on(function ($observations) { return $observations instanceof Collection && $observations->isEmpty(); })
                )
                ->andReturnFalse();
        })->makePartial());

        // when
        $response = $this->post('/course/' . $this->courseId . '/participants/' . $this->participantId . '/qualis/' . $this->qualiId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Quali-Text ist ungültig.', $exception->validator->errors()->first('quali_contents'));
    }
}
