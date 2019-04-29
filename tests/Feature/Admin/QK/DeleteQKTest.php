<?php

namespace Tests\Feature\Admin\QK;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class DeleteQKTest extends TestCaseWithKurs {

    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/qk', ['quali_kategorie' => 'Qualikategorie 1']);
        /** @var User $user */
        $user = Auth::user();
        $this->qkId = $user->lastAccessedKurs->qks()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteQK() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/qk');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Qualikategorie 1');
    }

    public function test_shouldValidateDeletedQKUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/qk/' . ($this->qkId + 1));

        // then
        $response->assertStatus(404);
    }
}
