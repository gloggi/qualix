<?php

namespace Tests;

use App\Models\Course;
use App\Models\NativeUser;
use App\Models\RequirementStatus;
use App\Models\User;
use Dotenv\Dotenv;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\DomCrawler\Crawler;

abstract class TestCase extends BaseTestCase {
    use DatabaseTransactions;

    /** @var Crawler $crawler */
    protected $crawler;

    public function setUp(): void {
        Dotenv::createImmutable(__DIR__.'/../', '.env.testing')->load();
        ini_set('memory_limit', '-1');

        parent::setUp();

        $this->createUser(['name' => 'Bari', 'password' => bcrypt('87654321'), 'email' => 'bari@example.com'], true);

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
    public function assertSeeAllInOrder($selector, array $contents, $extractor = null) {
        if ($extractor === null) $extractor = function($domElement) { return trim($domElement->textContent); };
        $matches = $this->crawler->filter($selector);

        if ($matches->count() !== count($contents)) {
            $this->fail('Expected to find ' . count($contents) . ' matching elements, but found ' . $matches->count() . ' in ' . $this->crawler->html());
        }

        foreach ($matches as $index => $domElement) {
            $needle = array_values($contents)[$index];
            $haystack = $extractor($domElement);
            try {
                $this->assertStringContainsString($needle, $haystack);
            } catch (ExpectationFailedException $e) {
                $this->fail('Failed asserting that the element at index ' . $index . ' contains the string "' . $contents[$index] . '", was "' . $haystack . '" instead.');
            }
        }

        return $this;
    }

    /**
     * Given a CSS selector string, check that none of the elements matching the query
     * contain any of the values provided.
     *
     * @param string $selector
     * @param string|array $contents
     * @return $this
     */
    public function assertSeeNone($selector, $contents) {
        $matches = $this->crawler->filter($selector);

        foreach ($matches as $domElement) {
            foreach (Arr::wrap($contents) as $needle) {
                $haystack = trim($domElement->textContent);
                try {
                    $this->assertStringNotContainsString($needle, $haystack);
                } catch (ExpectationFailedException $e) {
                    $this->fail('Expected to find no element matching "' . $selector . '" that contains "' . $needle . '", but found "' . $domElement . '"');
                }
            }
        }

        return $this;
    }

    protected function user(): ?User {
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

    /**
     * @param array $attrs
     * @param bool $actAsNewUser
     * @return User
     */
    protected function createUser($attrs = [], $actAsNewUser = false) {
        $user = NativeUser::factory()->create($attrs);
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
        $this->createRequirementStatus('unter Beobachtung', 'gray-500', 'binoculars', $id);
        $this->createRequirementStatus('erfüllt', 'green', 'circle-check', $id);
        $this->createRequirementStatus('nicht erfüllt', 'red', 'circle-xmark', $id);
        return $id;
    }

    protected function createRequirementStatus($name = 'erfüllt', $color = 'green', $icon = 'circle-check', $courseId = null) {
        return RequirementStatus::create(['course_id' => $courseId ?? $this->courseId, 'name' => $name, 'color' => $color, 'icon' => $icon])->id;
    }
}
