<?php

namespace Tests;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\DomCrawler\Crawler;

abstract class TestCase extends BaseTestCase {
    use CreatesApplication;
    use DatabaseTransactions;

    /** @var Crawler $crawler */
    protected $crawler;

    public function setUp(): void {
        parent::setUp();

        $this->createUser(['name' => 'Bari'], true);

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

        $this->crawler = new Crawler();
    }

    private function setUpCrawler(TestResponse $response) {
        $this->crawler->clear();
        $document = new \DOMDocument();
        $libxml_use_internal_errors = libxml_use_internal_errors(true);
        $document->loadHTML($response->content());
        libxml_use_internal_errors($libxml_use_internal_errors);
        $this->crawler->addDocument($document);
        return $response;
    }

    public function get($uri, array $data = [], array $headers = [], $injectCsrfToken = false) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'GET');
        return $this->setUpCrawler(parent::get($uri, $headers));
    }

    public function post($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'POST');
        return $this->setUpCrawler(parent::post($uri, $data, $headers));
    }

    public function put($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'PUT');
        return $this->setUpCrawler(parent::put($uri, $data, $headers));
    }

    public function patch($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'PATCH');
        return $this->setUpCrawler(parent::patch($uri, $data, $headers));
    }

    public function delete($uri, array $data = [], array $headers = [], $injectCsrfToken = true) {
        $this->setCSRFTokenAndMethod($data, $injectCsrfToken, 'DELETE');
        return $this->setUpCrawler(parent::delete($uri, $data, $headers));
    }

    private function setCSRFTokenAndMethod(&$data, $injectCsrfToken, $method) {
        if ($injectCsrfToken && !isset($data['_token'])) {
            $data['_token'] = csrf_token();
        }
        if (!isset($data['_method'])) {
            $data['_method'] = $method;
        }
    }

    /**
     * Given a CSS selector string, check if all the elements matching the query
     * contain all the values provided, in the order they are provided.
     *
     * @param string $selector
     * @param array $contents
     * @return $this
     */
    public function assertSeeAllInOrder($selector, Array $contents) {
        $matches = $this->crawler->filter($selector);

        if ($matches->count() !== count($contents)) {
            $this->fail('Expected to find ' . count($contents) . ' matching elements, but found ' . $matches->count() . ' in ' . $this->crawler->html());
        }

        foreach ($matches as $index => $domElement) {
            $needle = $contents[$index];
            $haystack = trim($domElement->textContent);
            try {
                $this->assertContains($needle, $haystack);
            } catch (ExpectationFailedException $e) {
                $this->fail('Failed asserting that the element at index ' . $index . ' contains the string "' . $contents[$index] . '", was "' . $haystack . '" instead.');
            }
        }

        return $this;
    }

    protected function user(): User {
        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            return User::find($user->id);
        }
        return null;
    }

    protected function refreshUser(): User {
        /** @var User $user */
        $user = $this->user();
        auth()->setUser($user);
        return $user;
    }

    protected function createUser($attrs = [], $actAsNewUser = false) {
        $user = factory(User::class)->create($attrs);
        if ($actAsNewUser) {
            $this->be($user);
        }
        return $user;
    }

    protected function createCourse($name = 'Kursname', $courseNumber = 'CH 123-00', $attachToUser = true) {
        $id = Course::create(['name' => $name, 'course_number' => $courseNumber])->id;
        if ($attachToUser) {
            $this->user()->courses()->attach($id, ['last_accessed' => Carbon::now()]);
            // Laravel bug: The Auth::user used in the application is cached and will not get the updated course list during the test, unless we refresh it manually
            $this->refreshUser();
        }
        return $id;
    }
}
