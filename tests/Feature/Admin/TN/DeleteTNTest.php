<?php

namespace Tests\Feature\Admin\TN;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class DeleteTNTest extends TestCaseWithKurs {

    private $tnId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/tn', ['pfadiname' => 'Pföschtli']);
        /** @var User $user */
        $user = Auth::user();
        $this->tnId = $user->lastAccessedKurs->tns()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteTN() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/tn/' . $this->tnId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/tn');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Pföschtli');
    }

    public function test_shouldValidateDeletedTNUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/tn/' . ($this->tnId + 1));

        // then
        $response->assertStatus(404);
    }
}
