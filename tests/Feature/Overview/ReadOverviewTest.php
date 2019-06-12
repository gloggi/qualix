<?php

namespace Tests\Feature\Overview;

use App\Models\Block;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Tests\TestCaseWithBasicData;

class ReadOverviewTest extends TestCaseWithBasicData {

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
            $this->createObservation(Block::find($blockId)->name, 1, [], [], $blockId);
        }
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayUeberblick() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN', 'Total', $this->user()->name, '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '9', '9', '' ]);
    }

    public function test_shouldDisplayUeberblick_observationsByMultiplePeople_andMultipleParticipants() {
        // given
        $name = $this->user()->name;

        // Create another participant
        $participantId2 = $this->createParticipant('Pfnörch');

        $this->createObservation(Block::find($this->blockIds[0])->name, 1, [], [], $this->blockIds[0], $participantId2);
        $this->createObservation(Block::find($this->blockIds[1])->name, 1, [], [], $this->blockIds[1], $participantId2);

        // create another leader in the course
        $user2 = $this->createUser(['name' => 'Lindo']);
        $user2->courses()->attach($this->courseId);

        $this->createObservation(Block::find($this->blockIds[0])->name, 1, [], [], $this->blockIds[0], $this->participantId, $user2->id);
        $this->createObservation(Block::find($this->blockIds[1])->name, 1, [], [], $this->blockIds[1], $this->participantId, $user2->id);

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('table.table-responsive-cards th', [ 'TN',     'Total', $name, 'Lindo', '' ]);
        $this->assertSeeAllInOrder('table.table-responsive-cards td', [ 'Pflock', '11',    '9',         '2',     '', 'Pfnörch', '2',    '2',         '0',     '' ]);
    }

    public function test_shouldDisplayMessage_whenNoParticipantsInKurs() {
        // given
        Participant::find($this->participantId)->delete();

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

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
        $response = $this->get('/course/' . $otherKursId . '/overview');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
