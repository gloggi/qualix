<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class DeleteEquipeMemberTest extends TestCaseWithKurs {

    /** @var User */
    private $user;

    public function setUp(): void {
        parent::setUp();

        $this->user = factory(User::class)->create(['name' => 'Bari']);
        $this->user->kurse()->attach($this->kursId);

        $this->get('/kurs/' . $this->kursId . '/admin/equipe')->assertSee($this->user->name);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/equipe/' . $this->user->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteEquipeMember() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/equipe/' . $this->user->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee($this->user->name);
    }

    public function test_shouldValidateDeletedEquipeMemberUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/equipe/' . ($this->user->id + 1));

        // then
        $response->assertStatus(404);
    }

    public function test_shouldPreventDeletingLastEquipeMember() {
        // given
        $this->delete('/kurs/' . $this->kursId . '/admin/equipe/' . $this->user->id);
        /** @var User $me */
        $me = Auth::user();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/equipe/' . $me->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Mindestens ein Equipenmitglied muss im Kurs bleiben.');
        $response->assertSee($me->name);
    }

    public function test_shouldRedirectToOtherPage_whenRemovingSelf() {
        // given
        $this->get('/')->followRedirects()->assertSee('Kursname');

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/admin/equipe/' . Auth::user()->getAuthIdentifier());

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Kursname');
    }
}
