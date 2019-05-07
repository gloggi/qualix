<?php

namespace Tests\Feature\Beobachtung;

use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithBasicData;

class DeleteBeobachtungTest extends TestCaseWithBasicData {

    private $beobachtungId;

    public function setUp(): void {
        parent::setUp();

        /** @var User $user */
        $user = Auth::user();

        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $this->tnId, 'kommentar' => 'hat gut mitgemacht', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '', 'qk_ids' => '']);
        $this->beobachtungId = $user->last_accessed_kurs->bloecke()->first()->beobachtungen()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteBeobachtung() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/tn/' . $this->tnId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateDeletedBeobachtungUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/kurs/' . $this->kursId . '/beobachtungen/' . ($this->beobachtungId + 1));

        // then
        $response->assertStatus(404);
    }
}
