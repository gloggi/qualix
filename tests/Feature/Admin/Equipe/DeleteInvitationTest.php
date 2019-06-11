<?php

namespace Tests\Feature\Admin\Equipe;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCaseWithCourse;

class DeleteInvitationTest extends TestCaseWithCourse {

    protected $email = 'o-m-g@dahätsdi.ch';

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->courseId . '/admin/invitation', ['email' => $this->email]);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/invitation/' . $this->email);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteInvitation() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/invitation/' . $this->email);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee($this->email);
    }

    public function test_shouldValidateDeletedInvitationUrl_wrongEmail() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->courseId . '/admin/invitation/some-wrong@email.com');

        // then
        $response->assertStatus(404);
    }
}
