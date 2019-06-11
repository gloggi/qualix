<?php

namespace Tests\Feature\Ueberblick;

use App\Models\Block;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Tests\TestCaseWithBasicData;

class ReadUeberblickTest extends TestCaseWithBasicData {

    protected $blockIds;

    public function setUp(): void {
        parent::setUp();

        // one block is already created in parent setup, create some more

        $this->createBlock('later date', '1.1', '02.01.2019');
        $this->createBlock('earlier date', '1.1', '31.12.2018');
        $this->createBlock('later day number', '2.1', '01.01.2019');
        $this->createBlock('earlier day number', '0.1', '01.01.2019');
        $this->createBlock('later block number', '1.2', '01.01.2019');
        $this->createBlock('earlier block number', '1.0', '01.01.2019');
        $this->createBlock('Block 2 later block name', '1.1', '01.01.2019');
        $this->createBlock('Block 0 earlier block name', '1.1', '01.01.2019');
        /** @var Collection $blockIds */
        $this->blockIds = $this->user()->lastAccessedCourse->blocks->map(function (Block $block) { return $block->id; });

        foreach ($this->blockIds as $blockId) {
            $this->createBeobachtung(Block::find($blockId)->name, 1, [], [], $blockId);
        }
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/ueberblick');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayUeberblick() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/ueberblick');

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

        $this->createBeobachtung(Block::find($this->blockIds[0])->name, 1, [], [], $this->blockIds[0], $tnId2);
        $this->createBeobachtung(Block::find($this->blockIds[1])->name, 1, [], [], $this->blockIds[1], $tnId2);

        // create another leader in the course
        $user2 = $this->createUser(['name' => 'Lindo']);
        $user2->courses()->attach($this->courseId);

        $this->createBeobachtung(Block::find($this->blockIds[0])->name, 1, [], [], $this->blockIds[0], $this->tnId, $user2->id);
        $this->createBeobachtung(Block::find($this->blockIds[1])->name, 1, [], [], $this->blockIds[1], $this->tnId, $user2->id);

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/ueberblick');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN',     'Total', $name, 'Lindo', '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '11',    '9',         '2',     '', 'Pfnörch', '2',    '2',         '0',     '' ]);
    }

    public function test_shouldDisplayMessage_whenNoTNInKurs() {
        // given
        Participant::find($this->tnId)->delete();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/ueberblick');

        // then
        $response->assertOk();
        $response->assertDontSee('Pflock');
        $response->assertSee('Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie');
    }

    public function test_shouldNotDisplayUeberblick_toOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock']);

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/ueberblick');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
