<?php

namespace Tests\Feature\Admin\MA;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class UpdateMATest extends TestCaseWithKurs {

    private $payload;
    private $maId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/ma', ['anforderung' => 'Mindestanforderung 1', 'killer' => '1']);
        /** @var User $user */
        $user = Auth::user();
        $this->maId = $user->lastAccessedKurs->mas()->first()->id;

        $this->payload = ['anforderung' => 'Geänderte Anforderung', 'killer' => '1'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateMA() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['anforderung']);
        $response->assertDontSee('Mindestanforderung 1');
        $response->assertSee('Ja');
        $response->assertDontSee('Nein');
    }

    public function test_shouldValidateNewMAData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['anforderung']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewMAData_killerNotSet_shouldNotChangeKiller() {
        // given
        $payload = $this->payload;
        unset($payload['killer']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Ja');
        $response->assertDontSee('Nein');
    }

    public function test_shouldValidateNewMAData_killerFalse_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['killer'] = '0';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId, $payload);

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
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Ja');
        $response->assertDontSee('Nein');
    }

    public function test_shouldValidateNewMAData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/ma/' . ($this->maId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
