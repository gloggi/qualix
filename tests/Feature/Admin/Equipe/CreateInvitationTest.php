<?php

namespace Tests\Feature\Admin\Equipe;

use App\Mail\InvitationMail;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateInvitationTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['email' => 'neues-mitglied@equipe.com'];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/invitation', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayInvitation() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/invitation', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/equipe');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['email']);
    }

    public function test_shouldSendInvitationEmail() {
        // given
        Mail::fake();

        // when
        $this->post('/course/' . $this->courseId . '/admin/invitation', $this->payload);

        // then
        Mail::assertSent(InvitationMail::class, function (InvitationMail $mail) {
            return $mail->hasTo($this->payload['email']);
        });
    }

    public function test_shouldValidateNewInvitationData_noEmail() {
        // given
        $payload = $this->payload;
        unset($payload['email']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/invitation', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('E-Mail muss ausgefüllt sein.', $exception->validator->errors()->first('email'));
    }

    public function test_shouldValidateNewInvitationData_longEmail() {
        // given
        $payload = $this->payload;
        $payload['email'] = 'extrem_lange_email_adresse.extrem_lange_email_adresse@example.com';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/invitation', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('E-Mail darf maximal 50 Zeichen haben.', $exception->validator->errors()->first('email'));
    }

    public function test_shouldValidateNewInvitationData_invalidEmail() {
        // given
        $payload = $this->payload;
        $payload['email'] = 'so en chabis';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/invitation', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('E-Mail muss eine gültige E-Mail-Adresse sein.', $exception->validator->errors()->first('email'));
    }

    public function test_shouldShowMessage_whenNoInvitationInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/equipe');

        // then
        $response->assertStatus(200);
        $response->assertSee('Momentan sind keine Einladungen offen.');
    }

    public function test_shouldNotShowMessage_whenSomeInvitationsInCourse() {
        // given
        $this->post('/course/' . $this->courseId . '/admin/invitation', $this->payload);

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/equipe');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Momentan sind keine Einladungen offen.');
    }
}
