<?php

namespace Tests\Feature\Admin\MA;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class CreateMATest extends TestCaseWithKurs {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['anforderung' => 'Mindestanforderung 1', 'killer' => '1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayMA() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['anforderung']);
    }

    public function test_shouldValidateNewMAData_noAnforderungText() {
        // given
        $payload = $this->payload;
        unset($payload['anforderung']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewMAData_killerNotSet_shouldWork() {
        // given
        $payload = $this->payload;
        unset($payload['killer']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Nein');
        $response->assertDontSee('Ja');
    }

    public function test_shouldValidateNewMAData_killerFalse_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['killer'] = '0';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Nein');
        $response->assertDontSee('Ja');
    }

    public function test_shouldValidateNewMAData_killerTrue_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['killer'] = '1';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Ja');
        $response->assertDontSee('Nein');
    }

    public function test_shouldShowMessage_whenNoMAInCourse() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/ma', $this->payload);

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Mindestanforderungen erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeMAInCourse() {
        // given
        $this->post('/kurs/' . $this->kursId . '/admin/ma', $this->payload);

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/ma', $this->payload);

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Mindestanforderungen erfasst.');
    }
}
