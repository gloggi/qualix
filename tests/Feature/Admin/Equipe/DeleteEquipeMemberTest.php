<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
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
}
