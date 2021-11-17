<?php

namespace Tests\Feature\Admin\Equipe;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithCourse;

class DeleteTrainerTest extends TestCaseWithCourse {

    /** @var User */
    protected $otherUser;

    public function setUp(): void {
        parent::setUp();

        $this->otherUser = $this->createUser(['name' => 'Lindo']);
        $this->otherUser->courses()->attach($this->courseId);

        $this->get('/course/' . $this->courseId . '/admin/equipe')->assertSee($this->otherUser->name);
        $this->get('/course/' . $this->courseId . '/admin/equipe')->assertSee($this->user()->name);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/equipe/' . $this->otherUser->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteEquipeMember() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/equipe/' . $this->otherUser->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee("{$this->otherUser->name} wurde aus der Equipe entfernt.");
        $response->assertDontSee(">{$this->otherUser->name}</td>", false);
    }

    public function test_shouldValidateDeletedEquipeMemberUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/equipe/' . ($this->otherUser->id + 1));

        // then
        $response->assertStatus(404);
    }

    public function test_shouldPreventDeletingLastEquipeMember() {
        // given
        $this->delete('/course/' . $this->courseId . '/admin/equipe/' . $this->otherUser->id);

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/equipe/' . $this->user()->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Mindestens ein Equipenmitglied muss im Kurs bleiben.');
        $response->assertSee($this->user()->name);
    }

    public function test_shouldNotDeleteEquipeMember_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs');

        // when
        $response = $this->delete('/course/' . $otherKursId . '/admin/equipe/' . $this->otherUser->id);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDeleteEquipeMember_fromOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        $this->otherUser->courses()->attach($otherKursId);

        // when
        $response = $this->delete('/course/' . $otherKursId . '/admin/equipe/' . $this->otherUser->id);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldRedirectToOtherPage_whenRemovingSelf() {
        // given
        $this->get('/')->followRedirects()->assertSee('Kursname');

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/equipe/' . $this->user()->id);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('Kursname');
    }
}
