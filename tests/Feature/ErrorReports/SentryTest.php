<?php

namespace Tests\Feature\ErrorReports;

use App\Http\Requests\UserRequest;
use Illuminate\Validation\ValidationException;
use Mockery;
use Sentry\EventId;
use Sentry\SentrySdk;
use Tests\TestCase;

class SentryTest extends TestCase {

    public function test_shouldReportErrorToSentry_andDisplayErrorForm_when500ErrorOccurs() {
        // given
        $sentryMock = Mockery::mock(app('sentry'));
        // Stupid fix because mockery ->passthru() doesn't work here for some reason
        $sentryMock->shouldReceive('captureException')->once()->andReturnUsing(function(...$args) use($sentryMock) {
            SentrySdk::getCurrentHub()->captureException(...$args);
            $sentryMock->shouldReceive('getLastEventId')->andReturn(new EventId('12341234123412341234123412341234'));
        });
        $this->instance('sentry', $sentryMock);

        // Force an exception
        $requestMock = $this->createPartialMock(UserRequest::class, [ 'validated', 'file' ]);
        $requestMock->expects(self::once())->method('validated')->willThrowException(new \Exception('exception thrown by test'));
        $requestMock->expects(self::any())->method('file');
        $this->instance(UserRequest::class, $requestMock);

        // when
        $response = $this->post('/user');

        // then
        $response->assertStatus(500);
        $response->assertSeeText('Es sieht so aus als hätten wir ein Problem.');
    }

    public function test_shouldNotReportErrorToSentry_orDisplayErrorForm_whenValidationErrorOccurs() {
        // given
        $sentryMock = Mockery::mock(app('sentry'));
        $sentryMock->shouldNotReceive('captureException');
        $this->instance('sentry', $sentryMock);

        // Force an exception
        $requestMock = $this->createPartialMock(UserRequest::class, [ 'validated', 'file' ]);
        $requestMock->expects(self::once())->method('validated')->willThrowException(ValidationException::withMessages(['file' => 'exception thrown by test']));
        $requestMock->expects(self::any())->method('file');
        $this->instance(UserRequest::class, $requestMock);

        // when
        $response = $this->post('/user');

        // then
        $response->assertStatus(302);
        $response->assertDontSeeText('Es sieht so aus als hätten wir ein Problem.');
    }

    public function test_shouldNotReportErrorToSentry_orDisplayErrorForm_whenNoErrorOccurs() {
        // given
        $sentryMock = Mockery::mock(app('sentry'));
        $sentryMock->shouldNotReceive('captureException');
        $this->instance('sentry', $sentryMock);

        // when
        $response = $this->get('/');

        // then
        $response->assertOk();
        $response->assertDontSeeText('Es sieht so aus als hätten wir ein Problem.');
    }
}
