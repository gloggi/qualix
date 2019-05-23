<?php

namespace Tests\Feature\Beobachtung;

use App\Models\Beobachtung;
use App\Models\Block;
use App\Models\TN;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Tests\TestCaseWithBasicData;

class ReadBeobachtungTest extends TestCaseWithBasicData {

    private $beobachtungId;

    public function setUp(): void {
        parent::setUp();

        $this->beobachtungId = $this->createBeobachtung('hat gut mitgemacht');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayBeobachtung() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId);

        // then
        $response->assertOk();
        $response->assertSee('hat gut mitgemacht');
    }

    public function test_shouldNotDisplayBeobachtung_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/beobachtungen/' . $this->beobachtungId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayBeobachtung_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherTNId = TN::create(['kurs_id' => $otherKursId, 'pfadiname' => 'Pflock'])->id;
        $otherBlockId = Block::create(['kurs_id' => $otherKursId, 'full_block_number' => '1.1', 'blockname' => 'Block 1', 'datum' => '01.01.2019', 'ma_ids' => null])->id;
        $otherUserId = $this->createUser(['name' => 'Lindo'])->id;
        $otherBeobachtungId = Beobachtung::create(['block_id' => $otherBlockId, 'tn_id' => $otherTNId, 'user_id' => $otherUserId, 'kommentar' => 'hat gut mitgemacht', 'bewertung' => '1', 'ma_ids' => '', 'qk_ids' => ''])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/beobachtungen/' . $otherBeobachtungId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldRenderNewlinesInBeobachtung() {
        // given
        $this->createBeobachtung("Mehrzeilige Beobachtungen\n- nützlich\n- wichtig\n- erlauben Strukturierung");

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId);

        // then
        $response->assertOk();
        $response->assertSee("Mehrzeilige Beobachtungen<br />\n- nützlich<br />\n- wichtig<br />\n- erlauben Strukturierung");
    }

    public function test_shouldOrderBeobachtungenByBlockOrder() {
        // given
        $this->createBlock('later date', '1.1', '02.01.2019');
        $this->createBlock('earlier date', '1.1', '31.12.2018');
        $this->createBlock('later day number', '2.1', '01.01.2019');
        $this->createBlock('earlier day number', '0.1', '01.01.2019');
        $this->createBlock('later block number', '1.2', '01.01.2019');
        $this->createBlock('earlier block number', '1.0', '01.01.2019');
        $this->createBlock('Block 2 later block name', '1.1', '01.01.2019');
        $this->createBlock('Block 0 earlier block name', '1.1', '01.01.2019');
        /** @var Collection $blockIds */
        $blockIds = $this->user()->lastAccessedKurs->bloecke->map(function (Block $block) { return $block->id; });
        $blockIdsToCreateBeobachtungen = $blockIds->sort();
        $blockIdsToCreateBeobachtungen->shift();
        foreach ($blockIdsToCreateBeobachtungen as $blockId) {
            $this->createBeobachtung(Block::find($blockId)->blockname, 1, [], [], $blockId);
        }

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId);

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards td[data-label^="Beobachtung"]', [
          'earlier date',
          'earlier day number',
          'earlier block number',
          'Block 0 earlier block name',
          'hat gut mitgemacht',
          'Block 2 later block name',
          'later block number',
          'later day number',
          'later date',
        ]);
    }
}
