<?php

namespace Tests\Feature\Observation;

use App\Models\HitobitoUser;
use Closure;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Feature\Auth\HitobitoOAuthTest;
use Tests\TestCaseWithBasicData;

class RestoreFormDataWhenSessionExpiredTest extends TestCaseWithBasicData {

    private $payload;
    private $email = 'bari@example.com';

    public function setUp(): void {
        parent::setUp();

        $this->createObservation('hat gut mitgemacht', 1, [], [], $this->blockId);

        $this->payload = ['participant_ids' => '' . $this->participantId, 'content' => 'this text will be restored', 'impression' => '1', 'block_id' => '' . $this->blockId, 'requirement_ids' => '', 'category_ids' => ''];
    }

    public function test_shouldRestoreSubmittedFormData_whenLoggingBackInNormally() {
        $this->checkRestorationOfFormData(function () {
            // the user logs back in
            return $this->post('/login', ['email' => $this->email, 'password' => '87654321'], ['referer' => '/login']);
        });
    }

    public function test_shouldRestoreSubmittedFormData_whenLoginFailsOnce() {
        $this->checkRestorationOfFormData(function () {
            // the user at first fails to log back in
            $response = $this->post('/login', ['email' => $this->email, 'password' => 'wrong-password'], ['referer' => '/login']);
            $response->assertStatus(302);
            $response->assertRedirect('/login');

            // then the user manages to log back in
            return $this->post('/login', ['email' => $this->email, 'password' => '87654321'], ['referer' => '/login']);
        });
    }

    public function test_shouldRestoreSubmittedFormData_whenLoggingInAsADifferentUser_inSameCourse() {
        $otherUser = $this->createUser(['name' => 'Lindo', 'password' => bcrypt('12345678'), 'email' => 'another@user.com'], false);
        $otherUser->courses()->attach($this->courseId);

        $this->checkRestorationOfFormData(function () {
            // log in as a different user
            return $this->post('/login', ['email' => 'another@user.com', 'password' => '12345678'], ['referer' => '/login']);
        });
    }

    public function test_shouldNotRestoreSubmittedFormData_whenLoggingInAsADifferentUser_whoIsNotPartOfTheCourse() {
        $this->createUser(['name' => 'Lindo', 'password' => bcrypt('12345678'), 'email' => 'another@user.com'], false);

        $this->checkRestorationOfFormData(function () {
            // log in as a different user
            return $this->post('/login', ['email' => 'another@user.com', 'password' => '12345678'], ['referer' => '/login']);
        }, false);
    }

    public function test_shouldRestoreSubmittedFormData_whenLoggingInViaHitobito() {
        $otherUser = factory(HitobitoUser::class)->create(['hitobito_id' => 123, 'name' => 'Cosinus', 'email' => 'cosinus@hitobito.com']);
        HitobitoOAuthTest::mockHitobitoResponses(123, 'cosinus@hitobito.com', 'Cosinus');
        $otherUser->courses()->attach($this->courseId);

        $this->checkRestorationOfFormData(function () {
            $state = HitobitoOAuthTest::extractRedirectQueryParams($this->get('/login/hitobito'))['state'];
            return $this->get('/login/hitobito/callback?code=1234&state=' . $state);
        });
    }

    public function test_shouldRestoreSubmittedFormData_whenChangingLanguageOnLoginScreen() {
        $this->checkRestorationOfFormData(function () {
            // the user switches the language
            $this->get('/locale/fr');

            // the user logs back in
            return $this->post('/login', ['email' => $this->email, 'password' => '87654321'], ['referer' => '/login']);
        }, true, 'Tes données saisies ont étés restaurées. N&#039;oublie pas a sauvegarder!');
    }

    public function checkRestorationOfFormData(Closure $logBackIn, bool $shouldRestore = true, $restoredFlashMessage = 'Deine eingegebenen Daten wurden wiederhergestellt. Speichern nicht vergessen!') {
        // given
        $this->get('/course/' . $this->courseId . '/observation/new');

        // simulate the user session expiring
        auth()->logout();

        // when
        // simulate the user clicking the stale submit button
        $response = $this->post('/course/' . $this->courseId . '/observation/new', $this->payload);
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // then
        // check that the flash message is displayed
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeText('Ups, du bist inzwischen nicht mehr eingeloggt. Bitte logge dich nochmals ein, deine Eingaben werden dann wiederhergestellt.');

        // when
        $response = $logBackIn();

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/observation/new');

        /** @var TestResponse $response */
        $response = $response->followRedirects();

        // check that restoration works as intended
        if ($shouldRestore) {
            $response->assertSeeText($restoredFlashMessage);
            $response->assertSee('this text will be restored');
        } else {
            $response->assertDontSeeText($restoredFlashMessage);
            $response->assertDontSee('this text will be restored');
        }

        // when
        // Refresh the page
        $response = $this->get('/course/' . $this->courseId . '/observation/new');

        // then
        // data should not be restored a second time
        $response->assertDontSeeText($restoredFlashMessage);
        $response->assertDontSee('this text will be restored');
    }

}
