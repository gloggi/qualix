<?php

namespace Tests\Feature\Admin\Bloecke;

use App\Models\Kurs;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithKurs;

class ReadBlockTest extends TestCaseWithKurs {

    private $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 1', 'datum' => '01.01.2019', 'ma_ids' => null]);
        /** @var User $user */
        $user = Auth::user();
        $this->blockId = $user->lastAccessedKurs->bloecke()->first()->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/bloecke/' . $this->blockId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayBlock() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/bloecke/' . $this->blockId);

        // then
        $response->assertOk();
        $response->assertSee('Block 1');
    }

    public function test_shouldNotDisplayBlock_fromOtherCourseOfSameUser() {
        // given
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => ''])->followRedirects();
        $otherKursId = Kurs::where('name', '=', 'Zweiter Kurs')->firstOrFail()->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/bloecke/' . $this->blockId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayBlock_fromOtherUser() {
        // given
        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();
        $this->be($otherUser);
        $this->post('/neuerkurs', ['name' => 'Zweiter Kurs', 'kursnummer' => '']);

        // when
        $response = $this->get('/kurs/' . $otherUser->lastAccessedKurs->id . '/admin/bloecke/' . $this->blockId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
