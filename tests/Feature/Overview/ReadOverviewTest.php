<?php

namespace Tests\Feature\Overview;

use App\Models\Block;
use App\Models\Course;
use App\Models\Feedback;
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
        $this->createBlock('two-digit day number', '11.1', '01.01.2019');
        $this->createBlock('later block number', '1.2', '01.01.2019');
        $this->createBlock('earlier block number', '1.0', '01.01.2019');
        $this->createBlock('two-digit block number', '1.12', '01.01.2019');
        $this->createBlock('Block 2 later block name', '1.1', '01.01.2019');
        $this->createBlock('Block 0 earlier block name', '1.1', '01.01.2019');
        /** @var Collection $blockIds */
        $this->blockIds = $this->user()->lastAccessedCourse->blocks->map(function (Block $block) { return $block->id; });

        foreach ($this->blockIds as $blockId) {
            $this->createObservation(Block::find($blockId)->name, 1, [], [], $blockId);
        }

        $this->createRequirementStatus();
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

    public function test_shouldRequireNonArchivedCourse() {
        // given
        Course::find($this->courseId)->update(['archived' => true]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.course', ['course' => $this->courseId]));
    }

    public function test_shouldDisplayUeberblick() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertSeeInOrder([ 'TN', $this->user()->name ]);
        $response->assertSeeInOrder([ 'Pflock', '11' ]);
    }

    public function test_shouldDisplayUeberblick_observationsByMultiplePeople_andMultipleParticipants() {
        // given
        $name = $this->user()->name;

        // Create another participant
        $participantId2 = $this->createParticipant('Pfnörch');

        $this->createObservation(Block::find($this->blockIds[0])->name, 1, [], [], $this->blockIds[0], $participantId2);
        $this->createObservation(Block::find($this->blockIds[1])->name, 1, [], [], $this->blockIds[1], $participantId2);

        // create another trainer in the course
        $user2 = $this->createUser(['name' => 'Lindo']);
        $user2->courses()->attach($this->courseId);

        $this->createObservation(Block::find($this->blockIds[0])->name, 1, [], [], $this->blockIds[0], $this->participantId, $user2->id);
        $this->createObservation(Block::find($this->blockIds[1])->name, 1, [], [], $this->blockIds[1], $this->participantId, $user2->id);

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertSeeInOrder([ 'TN', $name, 'Lindo' ]);
        $response->assertSeeInOrder([ 'Pflock', '11', '2', 'Pfn\u00f6rch', '2', /*'0'*/ ]); // a value of 0 isn't present in the JSON at all
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
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock']);

        // when
        $response = $this->get('/course/' . $otherKursId . '/overview');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldEscapeHTML_whenDisplayingOverview() {
        // given
        $participantName = '<b>Bar</b>i\'"';
        $this->createParticipant($participantName);
        $userName = 'Co<i>si</i>nus\'"';
        $this->createUser(['name' => $userName])->courses()->attach($this->courseId);

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertDontSee($participantName, false);
        $response->assertSee('&quot;&lt;b&gt;Bar&lt;\/b&gt;i&#039;\&quot;&quot;', false);
        $response->assertDontSee($userName, false);
        $response->assertSee('&quot;Co&lt;i&gt;si&lt;\/i&gt;nus&#039;\&quot;&quot;', false);
    }

    public function test_shouldNotShowFeedbackDropdown_whenNoFeedbacksInCourse() {
        // given
        Course::find($this->courseId)->feedback_datas()->delete();

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertDontSee('Rückmeldung anzeigen:');
        $response->assertDontSee('keines');
    }

    public function test_shouldSelectNoFeedbackByDefault() {
        // given
        $feedbackDataId = Feedback::find($this->createFeedback('Zwischenquali'))->feedback_data_id;

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertSee('Rückmeldung anzeigen:');
        $response->assertSee('keines');
        $response->assertSee(':value="&quot;0&quot;"', false);
        $response->assertSee('Zwischenquali');
        $response->assertDontSee(':value="&quot;'. $feedbackDataId .'&quot;"', false);
    }

    public function test_shouldNotUseWideLayout_whenNoFeedbackIsSelected() {
        // given
        $feedbackDataId = Feedback::find($this->createFeedback())->feedback_data_id;

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertDontSee('<b-container :fluid="true">', false);
    }

    public function test_shouldNotPassFeedbackToOverviewTable_whenNoFeedbackIsSelected() {
        // given
        $feedbackDataId = Feedback::find($this->createFeedback())->feedback_data_id;

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview');

        // then
        $response->assertOk();
        $response->assertDontSee(':feedback-data="{', false);
        $response->assertSee(':feedback-data="null"', false);
    }

    public function test_shouldShowSelectedFeedback_whenFeedbackIsSelected() {
        // given
        $feedbackDataId = Feedback::find($this->createFeedback('Zwischenquali'))->feedback_data_id;

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview/' . $feedbackDataId);

        // then
        $response->assertOk();
        $response->assertSee('Rückmeldung anzeigen:');
        $response->assertSee('keines');
        $response->assertDontSee(':value="&quot;0&quot;"', false);
        $response->assertSee('Zwischenquali');
        $response->assertSee(':value="&quot;'. $feedbackDataId .'&quot;"', false);
    }

    public function test_shouldUseWideLayout_whenFeedbackIsSelected() {
        // given
        $feedbackDataId = Feedback::find($this->createFeedback())->feedback_data_id;

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview/' . $feedbackDataId);

        // then
        $response->assertOk();
        $response->assertSee('<b-container :fluid="true">', false);
    }

    public function test_shouldPassFeedbackToOverviewTable_whenFeedbackIsSelected() {
        // given
        $feedbackDataId = Feedback::find($this->createFeedback())->feedback_data_id;

        // when
        $response = $this->get('/course/' . $this->courseId . '/overview/' . $feedbackDataId);

        // then
        $response->assertOk();
        $response->assertSee(':feedback-data="{', false);
        $response->assertDontSee(':feedback-data="null"', false);
    }
}
