<?php

namespace Tests\Feature\Admin\Requirement;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateRequirementTest extends TestCaseWithCourse {

    private $payload;
    private $maId;

    public function setUp(): void {
        parent::setUp();

        $this->maId = $this->createRequirement('Mindestanforderung 1', true);

        $this->payload = ['content' => 'Geänderte Anforderung', 'mandatory' => '1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . $this->maId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateMA() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . $this->maId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['content']);
        $response->assertDontSee('Mindestanforderung 1');
        $response->assertSee('Ja');
        $response->assertDontSee('Nein');
    }

    public function test_shouldValidateNewMAData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . $this->maId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewMAData_killerNotSet_shouldNotChangeKiller() {
        // given
        $payload = $this->payload;
        unset($payload['mandatory']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . $this->maId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Ja<');
        $response->assertDontSee('>Nein<');
    }

    public function test_shouldValidateNewMAData_killerFalse_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['mandatory'] = '0';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . $this->maId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/ma');
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
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . $this->maId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Ja<');
        $response->assertDontSee('>Nein<');
    }

    public function test_shouldValidateNewMAData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/ma/' . ($this->maId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
