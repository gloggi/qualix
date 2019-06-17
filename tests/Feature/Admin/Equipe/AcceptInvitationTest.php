<?php

namespace Tests\Feature\Admin\Equipe;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class AcceptInvitationTest extends TestCaseWithCourse {

    private $token;
    private $email = 'neues-mitglied@equipe.com';

    public function setUp(): void {
        parent::setUp();

        $payload = ['email' => $this->email];
        $this->post('/course/' . $this->courseId . '/admin/invitation', $payload);
        $this->token = Invitation::where('course_id', '=', $this->courseId)->where('email', '=', $payload['email'])->first()->token;
    }

    public function test_invitationClaimForm_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/invitation/' . $this->token);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayMessage_whenAlreadyMemberInCourse() {
        // given

        // when
        $response = $this->get('/invitation/' . $this->token);

        // then
        $response->assertStatus(200);
        $response->assertSee('Du bist schon in der Equipe von Kursname. Du kannst diese Einladung nicht annehmen.');
    }

    public function test_shouldDisplayInvitationClaimForm() {
        // given
        $this->createUser(['name' => 'Lindo'], true);

        // when
        $response = $this->get('/invitation/' . $this->token);

        // then
        $response->assertStatus(200);
        $response->assertSee($this->token);
        $response->assertSeeText($this->email);
    }

    public function test_claimInvitation_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/invitation/', ['token' => $this->token]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldNotClaimInvitation_whenAlreadyMemberInCourse() {
        // given

        // when
        $response = $this->post('/invitation/', ['token' => $this->token]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/equipe');
    }

    public function test_claimInvitation_shouldWork() {
        // given
        $this->createUser(['name' => 'Lindo'], true);

        // when
        $response = $this->post('/invitation/', ['token' => $this->token]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Kursname');
    }

    public function test_shouldValidateClaimedInvitation_noToken() {
        // given
        $this->createUser(['name' => 'Lindo'], true);

        // when
        $response = $this->post('/invitation/', []);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldRedirectToInvitationClaimPage_afterCompletingLogin() {
        // given
        $this->createUser(['name' => 'Lindo', 'password' => bcrypt('12345678'), 'email' => $this->email], true);
        auth()->logout();
        $this->get('/invitation/' . $this->token)->followRedirects();

        // when
        $response = $this->post('/login', ['email' => $this->email, 'password' => '12345678']);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/invitation/' . $this->token);
    }

    public function test_shouldRedirectToInvitationClaimPage_afterCompletingRegistrationAndEmailVerification() {
        // given
        Notification::fake();

        // Simulate invitation viewing and registration flow
        auth()->logout();
        $this->get('/invitation/' . $this->token)->followRedirects();
        $this->get('/register')->followRedirects();
        $response = $this->post('/register', ['name' => 'Lindo', 'email' => $this->email, 'password' => '12345678', 'password_confirmation' => '12345678'])->followRedirects();
        $response->assertSee('Du chasch de Link i dine E-Mails zur Verifizierig benutze.');

        // Get action URL from verification email
        $actionUrl = '';
        $user = User::where('email', '=', $this->email)->first();
        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification, $channels) use(&$actionUrl, $user) {
            $actionUrl = $notification->toMail($user)->actionUrl;
            return true;
        });

        // when
        $response = $this->get($actionUrl);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/invitation/' . $this->token);
    }
}
