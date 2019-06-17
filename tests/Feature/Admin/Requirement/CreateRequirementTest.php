<?php

namespace Tests\Feature\Admin\Requirement;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateRequirementTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['content' => 'Mindestanforderung 1', 'mandatory' => '1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayMA() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['content']);
    }

    public function test_shouldValidateNewMAData_noAnforderungText() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewMAData_killerNotSet_shouldWork() {
        // given
        $payload = $this->payload;
        unset($payload['mandatory']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Nein<');
        $response->assertDontSee('>Ja<');
    }

    public function test_shouldValidateNewMAData_killerFalse_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['mandatory'] = '0';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Nein<');
        $response->assertDontSee('>Ja<');
    }

    public function test_shouldValidateNewMAData_killerTrue_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['mandatory'] = '1';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Ja<');
        $response->assertDontSee('>Nein<');
    }

    public function test_shouldShowMessage_whenNoMAInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Mindestanforderungen erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeMAInCourse() {
        // given
        $this->createRequirement();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Mindestanforderungen erfasst.');
    }
}
