<?php

namespace Tests\Feature\Auth;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordTest extends TestCase {

    public function setUp(): void {
        parent::setUp();
        auth()->logout();
    }

    public function test_resetPassword_step1_shouldDisplayEmailForm() {
        // given

        // when
        $response = $this->get('/password/reset');

        // then
        $response->assertSeeText('Link zum Passwort zurücksetzen schicken');
    }

    public function test_resetPassword_step1_shouldSendEmail() {
        // given
        Notification::fake();
        $user = $this->createUser(['email' => 'test@qualix.com']);
        $this->get('/password/reset');

        // when
        $response = $this->post('/password/email', ['email' => 'test@qualix.com']);

        // then
        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $response->assertRedirect('/password/reset');
        $response = $response->followRedirects();
        $response->assertSeeText('Wir haben dir einen Link zum zurücksetzen des Passworts gesendet.');
    }

    public function test_resetPassword_step1_shouldNotSendEmail_whenUserNotFound() {
        // given
        Notification::fake();
        $this->get('/password/reset');

        // when
        $response = $this->post('/password/email', ['email' => 'test@qualix.com']);

        // then
        Notification::assertNothingSent();
        $response->assertRedirect('/password/reset');
        $response = $response->followRedirects();
        $response->assertSeeText('Wir können keinen Benutzer mit dieser E-Mail-Adresse finden. Meldest du dich vielleicht normalerweise mit MiData an?');
    }

    public function test_resetPassword_step2_shouldDisplayForm() {
        // given
        $user = $this->createUser(['email' => 'test@qualix.com']);
        $token = Password::broker()->createToken($user);

        // when
        $response = $this->get('/password/reset/' . $token);

        // then
        $response->assertSeeText('Passwort zurücksetzen');
    }

    public function test_resetPassword_step2_shouldSetPassword() {
        // given
        $user = $this->createUser(['email' => 'test@qualix.com']);
        $token = Password::broker()->createToken($user);
        $newPassword = Str::random(16);

        // when
        $response = $this->post('/password/reset', ['token' => $token, 'email' => 'test@qualix.com', 'password' => $newPassword, 'password_confirmation' => $newPassword]);

        // then
        $response->assertRedirect('/');
        $response = $response->followRedirects();
        $response->assertSeeText('Dein Passwort wurde zurückgesetzt!');
        $hasher = app('hash');
        $this->assertTrue($hasher->check($newPassword, $user->refresh()->password));
    }

    public function test_resetPassword_step2_shouldNotSetPassword_whenWrongToken() {
        // given
        $user = $this->createUser(['email' => 'test@qualix.com']);
        $token = Password::broker()->createToken($user);
        $newPassword = Str::random(16);
        $this->get('/password/reset/' . $token . 'a');

        // when
        $response = $this->post('/password/reset', ['token' => $token . 'a', 'email' => 'test@qualix.com', 'password' => $newPassword, 'password_confirmation' => $newPassword]);

        // then
        $response->assertRedirect('/password/reset/' . $token . 'a');
        $response = $response->followRedirects();
        $response->assertSeeText('Dieses Token ist zum Zurücksetzen des Passworts ungültig.');
        $hasher = app('hash');
        $this->assertFalse($hasher->check($newPassword, $user->refresh()->password));
    }

    public function test_resetPassword_step2_shouldNotSetPassword_whenWrongEmail() {
        // given
        $user = $this->createUser(['email' => 'test@qualix.com']);
        $token = Password::broker()->createToken($user);
        $newPassword = Str::random(16);
        $this->get('/password/reset/' . $token);

        // when
        $response = $this->post('/password/reset', ['token' => $token, 'email' => 'test2@qualix.com', 'password' => $newPassword, 'password_confirmation' => $newPassword]);

        // then
        $response->assertRedirect('/password/reset/' . $token);
        $response = $response->followRedirects();
        $response->assertSeeText('Wir können keinen Benutzer mit dieser E-Mail-Adresse finden. Meldest du dich vielleicht normalerweise mit MiData an?');
        $hasher = app('hash');
        $this->assertFalse($hasher->check($newPassword, $user->refresh()->password));
    }

    public function test_resetPassword_step2_shouldNotSetPassword_whenPasswordsDontMatch() {
        // given
        $user = $this->createUser(['email' => 'test@qualix.com']);
        $token = Password::broker()->createToken($user);
        $newPassword = Str::random(16);
        $this->get('/password/reset/' . $token);

        // when
        $response = $this->post('/password/reset', ['token' => $token, 'email' => 'test@qualix.com', 'password' => $newPassword, 'password_confirmation' => $newPassword . 'a']);

        // then
        $response->assertRedirect('/password/reset/' . $token);
        $response = $response->followRedirects();
        $response->assertSeeText('Passwort stimmt nicht mit der Bestätigung überein.');
        $hasher = app('hash');
        $this->assertFalse($hasher->check($newPassword, $user->refresh()->password));
    }
}
