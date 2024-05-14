<?php

namespace Tests\Feature\ErrorReports;

use App\Http\Requests\UserRequest;
use Illuminate\Validation\ValidationException;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Tests\TestCase;

class SentryTest extends TestCase {

    public function test_shouldReportErrorToSentry_when500ErrorOccurs() {
        // given
        // Spy on Sentry to check the exception is reported
        $sentryHubMock = $this->createMock(Hub::class);
        $sentryHubMock->expects(self::once())->method('captureException')->willReturn(null);
        SentrySdk::setCurrentHub($sentryHubMock);

        // Force an exception
        $requestMock = $this->createPartialMock(UserRequest::class, [ 'validated', 'file' ]);
        $requestMock->expects(self::once())->method('validated')->willThrowException(new \Exception('exception thrown by test'));
        $requestMock->expects(self::any())->method('file');
        $this->instance(UserRequest::class, $requestMock);

        // when
        $response = $this->post('/user');

        // then
        $response->assertStatus(500);
        $response->assertSeeText('Bitte versuche es später nochmals.');
    }

    public function test_shouldNotReportErrorToSentry_orDisplayErrorForm_whenValidationErrorOccurs() {
        // given
        // Spy on Sentry to check the exception isn't reported
        $sentryHubMock = $this->createMock(Hub::class);
        $sentryHubMock->expects(self::never())->method('captureException');
        SentrySdk::setCurrentHub($sentryHubMock);

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
        // Spy on Sentry to check the exception isn't reported
        $sentryHubMock = $this->createMock(Hub::class);
        $sentryHubMock->expects(self::never())->method('captureException');
        SentrySdk::setCurrentHub($sentryHubMock);

        // when
        $response = $this->get('/');

        // then
        $response->assertOk();
        $response->assertDontSeeText('Es sieht so aus als hätten wir ein Problem.');
    }
}
