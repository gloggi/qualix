<?php

namespace Tests\Feature\Admin\Equipe;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class DeleteInvitationTest extends TestCaseWithKurs {

    protected $email = 'o-m-g@dahätsdi.ch';

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/invitation', ['email' => $this->email]);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/invitation/' . $this->email);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteInvitation() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/invitation/' . $this->email);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee($this->email);
    }

    public function test_shouldValidateDeletedInvitationUrl_wrongEmail() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/invitation/some-wrong@email.com');

        // then
        $response->assertStatus(404);
    }
}
