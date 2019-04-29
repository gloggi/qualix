<?php

namespace Tests\Feature\Admin\Equipe;

use App\Models\Kurs;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function test_shouldNotDeleteEquipeMember_fromOtherCourseOfSameUser() {
        // given
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => ''])->followRedirects();
        $otherKursId = Kurs::where('name', '=', 'Zweiter Kurs')->firstOrFail()->id;

        // when
        $response = $this->delete('/kurs/' . $otherKursId . '/admin/equipe/' . $this->user->id);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDeleteEquipeMember_fromOtherUser() {
        // given
        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();
        $this->be($otherUser);
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => '']);

        // when
        $response = $this->delete('/kurs/' . $otherUser->lastAccessedKurs->id . '/admin/equipe/' . $this->user->id);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
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
