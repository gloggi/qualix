<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class DeleteMATest extends TestCaseWithKurs {

    private $maId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/ma', ['anforderung' => 'Mindestanforderung 1', 'killer' => '1']);
        /** @var User $user */
        $user = Auth::user();
        $this->maId = $user->lastAccessedKurs->mas()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteMA() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/ma/' . $this->maId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/ma');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Mindestanforderung 1');
    }

    public function test_shouldValidateDeletedMAUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/ma/' . ($this->maId + 1));

        // then
        $response->assertStatus(404);
    }
}
