<?php

namespace Tests\Feature\Admin\Bloecke;

use App\Models\Block;
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
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/bloecke/' . $this->blockId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayBlock_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherBlockId = Block::create(['kurs_id' => $otherKursId, 'full_block_number' => '1.1', 'blockname' => 'later date', 'datum' => '02.01.2019', 'ma_ids' => null])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/bloecke/' . $otherBlockId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldOrderBloecke() {
        // given
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'later date', 'datum' => '02.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'earlier date', 'datum' => '31.12.2018', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '2.1', 'blockname' => 'later day number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '0.1', 'blockname' => 'earlier day number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.2', 'blockname' => 'later block number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.0', 'blockname' => 'earlier block number', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 2 later block name', 'datum' => '01.01.2019', 'ma_ids' => null]);
        $this->post('/kurs/' . $this->kursId . '/admin/bloecke', ['full_block_number' => '1.1', 'blockname' => 'Block 0 earlier block name', 'datum' => '01.01.2019', 'ma_ids' => null]);

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/admin/bloecke');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards td[data-label^="Blockname"]', [
          'earlier date',
          'earlier day number',
          'earlier block number',
          'Block 0 earlier block name',
          'Block 1',
          'Block 2 later block name',
          'later block number',
          'later day number',
          'later date',
        ]);
    }
}
