<?php

namespace Tests\Feature\Ueberblick;

use App\Models\Block;
use App\Models\TN;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithBasicData;

class ReadUeberblickTest extends TestCaseWithBasicData {

    protected $blockIds;

    public function setUp(): void {
        parent::setUp();

        // one block is already created in parent setup, create some more

        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block1', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.2', 'blockname' => 'Block2', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.3', 'blockname' => 'Block3', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.4', 'blockname' => 'Block4', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.5', 'blockname' => 'Block5', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.6', 'blockname' => 'Block6', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.7', 'blockname' => 'Block7', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.8', 'blockname' => 'Block8', 'datum' => '01.01.2019', 'ma_ids' => null]);
        /** @var User $user */
        $user = Auth::user();
        /** @var Collection $blockIds */
        $this->blockIds = $user->lastAccessedKurs->bloecke->map(function (Block $block) { return $block->id; });

        foreach ($this->blockIds as $blockId) {
            $this->createBeobachtung($blockId, $this->tnId);
        }
    }

    private function createBeobachtung($blockId, $tnId) {
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $tnId, 'kommentar' => Block::find($blockId)->blockname, 'bewertung' => '1', 'block_id' => '' . $blockId, 'ma_ids' => '', 'qk_ids' => '']);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/ueberblick');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayUeberblick() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/ueberblick');

        // then
        $response->assertOk();
        /** @var User $user */
        $user = Auth::user();
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN', 'Total', $user->name, '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '9', '9', '' ]);
    }

    public function test_shouldDisplayUeberblick_observationsByMultiplePeople_andMultipleTN() {
        // given

        // Create another TN
        $this->post('/kurs/' . $this->kursId . '/admin/tn', ['pfadiname' => 'Pfnörch']);
        /** @var User $user */
        $user = Auth::user();
        $tnId2 = $user->lastAccessedKurs->tns()->get()[1]->id;

        $this->createBeobachtung($this->blockIds[0], $tnId2);
        $this->createBeobachtung($this->blockIds[1], $tnId2);

        // create another leader in the course
        $user2 = factory(User::class)->create(['name' => 'Lindo']);
        $user2->kurse()->attach($this->kursId);
        $this->be($user2);

        $this->createBeobachtung($this->blockIds[0], $this->tnId);
        $this->createBeobachtung($this->blockIds[1], $this->tnId);

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/ueberblick');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN',     'Total', $user->name, 'Lindo', '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '11',    '9',         '2',     '', 'Pfnörch', '2',    '2',         '0',     '' ]);
    }

    public function test_shouldNotDisplayUeberblick_toOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        TN::create(['kurs_id' => $otherKursId, 'pfadiname' => 'Pflock']);

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/ueberblick');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
