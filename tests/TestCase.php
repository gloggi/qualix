<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Session;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();

        $user = factory(User::class)->create();
        $this->be($user);

        Session::start();

        $test = $this;
        TestResponse::macro('followRedirects', function ($testCase = null) use ($test) {
            /** @var TestResponse $response */
            $response = $this;
            $testCase = $testCase ?: $test;

            while ($response->isRedirect()) {
                $response = $testCase->get($response->headers->get('Location'));
            }

            return $response;
        });

        $this->withExceptionHandling();
    }

    public function get($uri, array $data = [], array $headers = [], $injectCsrfToken = false) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'GET');
        return parent::get($uri, $headers);
    }

    public function post($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'POST');
        return parent::post($uri, $data, $headers);
    }

    public function put($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'PUT');
        return parent::put($uri, $data, $headers);
    }

    public function patch($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'PATCH');
        return parent::patch($uri, $data, $headers);
    }

    public function delete($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'DELETE');
        return parent::delete($uri, $data, $headers);
    }

    private function setCSRFTokenAndMethod(&$data, $injectCsrfToken, $method) {
        if ($injectCsrfToken && !isset($data['_token'])) {
            $data['_token'] = csrf_token();
        }
        if (!isset($data['_method'])) {
            $data['_method'] = $method;
        }
    }
}
