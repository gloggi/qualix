<?php

namespace Tests\Feature\Admin\TN;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class UpdateTNTest extends TestCaseWithKurs {

    private $payload;
    private $tnId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/tn', ['pfadiname' => 'Qualm']);
        /** @var User $user */
        $user = Auth::user();
        $this->tnId = $user->lastAccessedKurs->tns()->first()->id;

        $this->payload = ['pfadiname' => 'Räuchli'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateTN() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/tn');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['pfadiname']);
        $response->assertDontSee('Qualm');
    }

    public function test_shouldValidateNewTNData_noName() {
        // given
        $payload = $this->payload;
        unset($payload['pfadiname']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewTNData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/admin/tn/' . ($this->tnId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
