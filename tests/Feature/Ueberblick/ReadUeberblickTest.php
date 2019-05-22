<?php

namespace Tests\Feature\Ueberblick;

use App\Models\Block;
use App\Models\TN;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
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
        /** @var Collection $blockIds */
        $this->blockIds = $this->user()->lastAccessedKurs->bloecke->map(function (Block $block) { return $block->id; });

        foreach ($this->blockIds as $blockId) {
            $this->createBeobachtung($blockId, $this->tnId);
        }
    }

    private function createBeobachtung($blockId, $tnId) {
        $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', ['tn_ids' => '' . $tnId, 'kommentar' => Block::find($blockId)->blockname, 'bewertung' => '1', 'block_id' => '' . $blockId, 'ma_ids' => '', 'qk_ids' => '']);
        Session::forget('alert-success');
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
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN', 'Total', $this->user()->name, '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '9', '9', '' ]);
    }

    public function test_shouldDisplayUeberblick_observationsByMultiplePeople_andMultipleTN() {
        // given
        $name = $this->user()->name;

        // Create another TN
        $tnId2 = $this->createTN('Pfnörch');

        $this->createBeobachtung($this->blockIds[0], $tnId2);
        $this->createBeobachtung($this->blockIds[1], $tnId2);

        // create another leader in the course
        $user2 = $this->createUser(['name' => 'Lindo'], true);
        $user2->kurse()->attach($this->kursId);

        $this->createBeobachtung($this->blockIds[0], $this->tnId);
        $this->createBeobachtung($this->blockIds[1], $this->tnId);

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/ueberblick');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN',     'Total', $name, 'Lindo', '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '11',    '9',         '2',     '', 'Pfnörch', '2',    '2',         '0',     '' ]);
    }

    public function test_shouldDisplayMessage_whenNoTNInKurs() {
        // given
        TN::find($this->tnId)->delete();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/ueberblick');

        // then
        $response->assertOk();
        $response->assertDontSee('Pflock');
        $response->assertSee('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie');
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
