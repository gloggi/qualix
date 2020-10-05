<?php

namespace Tests\Feature\Crib;

use App\Models\Observation;
use App\Models\ObservationAssignment;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCaseWithCourse;

class ReadCribTest extends TestCaseWithCourse {

    protected $safetyRequirementId;
    protected $basicsRequirementId;
    protected $beNiceRequirementId;

    public function setUp(): void {
        parent::setUp();

        $this->safetyRequirementId = $this->createRequirement('Sicherheitsüberlegungen', true);
        $this->basicsRequirementId = $this->createRequirement('Beziehungen und Methoden', true);
        $this->beNiceRequirementId = $this->createRequirement('Nett sein', false);
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayMessage_whenNoBlocksInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertOk();
        $response->assertSee('Bisher sind keine Blöcke erfasst. Bitte erfasse und verbinde sie ');
    }

    public function test_shouldDisplay_blockWithNoConnectedRequirement() {
        // given
        $this->createBlock('Block 1', '1.1', '01.01.2019');

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertOk();
        $response->assertSee('Block 1');
        $response->assertDontSee('Sicherheitsüberlegungen');
        $response->assertDontSee('Beziehungen und Methoden');
        $response->assertDontSee('Nett sein');
    }

    public function test_shouldDisplay_blockWithConnectedRequirement() {
        // given
        $this->createBlock('Block 1', '1.1', '01.01.2019', [$this->safetyRequirementId]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertOk();
        $response->assertSee('Block 1');
        $this->assertSeeAllInOrder('span.badge.badge-warning', ['Sicherheitsüberlegungen']);
        $response->assertDontSee('Beziehungen und Methoden');
        $response->assertDontSee('Nett sein');
    }

    public function test_shouldDisplay_blockWithMultipleConnectedRequirements() {
        // given
        $this->createBlock('Block 1', '1.1', '01.01.2019', [$this->safetyRequirementId, $this->basicsRequirementId, $this->beNiceRequirementId]);

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertOk();
        $response->assertSee('Block 1');
        $this->assertSeeAllInOrder('span.badge.badge-warning', ['Sicherheitsüberlegungen', 'Beziehungen und Methoden']);
        $this->assertSeeAllInOrder('span.badge.badge-info', ['Nett sein']);
    }

    public function test_shouldDisplayMessage_whenBlocksInCourse() {
        // given
        $this->createBlock('Block 1', '1.1', '01.01.2019');

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertOk();
        $response->assertSee('Siehst du nur leere Blöcke ohne Anforderungen?');
    }

    public function test_shouldNotDisplayCrib_toOtherUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '', false);
        Participant::create(['course_id' => $otherKursId, 'scout_name' => 'Pflock']);

        // when
        $response = $this->get('/course/' . $otherKursId . '/crib');

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldCalculateObservationAssignmentsCorrectly() {
        // given
        $block1 = $this->createBlock('Block 1', '1.1', '01.01.2019');
        $block2 = $this->createBlock('Block 2', '1.2', '01.01.2019');
        $participant1 = $this->createParticipant('One');
        $participant2 = $this->createParticipant('Two');
        $observation = Observation::create(['content' => 'test', 'block' => $block1, 'user_id' => Auth::id()]);
        $observation->participants()->attach($participant1);
        $multiObservation = Observation::create(['content' => 'haben die ganze Zeit nur geschnädert', 'block' => $block1, 'user_id' => Auth::id()]);
        $multiObservation->participants()->attach([$participant1, $participant2]);

        $observationAssignment = ObservationAssignment::create(['name' => 'Assignment', 'course_id' => $this->courseId]);
        $observationAssignment->blocks()->attach([$block1, $block2]);
        $observationAssignment->participants()->attach([$participant1, $participant2]);
        $observationAssignment->users()->attach(Auth::id());

        $userId = Auth::id();

        // when
        $response = $this->get('/course/' . $this->courseId . '/crib');

        // then
        $response->assertOk();
        $response->assertSee('Block 1');
        $response->assertSee('Block 2');
        $data = $response->getOriginalContent()->getData()['trainerObservationAssignments'];
        $this->assertEquals([$block2, $block1], array_keys($data->all()));
        $block1Participant1 = collect($data[$block1])->first(function ($entry) use($participant1) { return $entry['id'] === $participant1; });
        $this->assertEquals($block1Participant1['user_id'], $userId);
        $this->assertEquals($block1Participant1['block_id'], $block1);
        $this->assertEquals($block1Participant1['observation_count'], 2);
        $this->assertEquals($block1Participant1['id'], $participant1);
        $this->assertEquals($block1Participant1['scout_name'], 'One');
        $block1Participant2 = collect($data[$block1])->first(function ($entry) use($participant2) { return $entry['id'] === $participant2; });
        $this->assertEquals($block1Participant2['user_id'], $userId);
        $this->assertEquals($block1Participant2['block_id'], $block1);
        $this->assertEquals($block1Participant2['observation_count'], 1);
        $this->assertEquals($block1Participant2['id'], $participant2);
        $this->assertEquals($block1Participant2['scout_name'], 'Two');
        $block2Participant1 = collect($data[$block2])->first(function ($entry) use($participant1) { return $entry['id'] === $participant1; });
        $this->assertEquals($block2Participant1['user_id'], $userId);
        $this->assertEquals($block2Participant1['block_id'], $block2);
        $this->assertEquals($block2Participant1['observation_count'], 0);
        $this->assertEquals($block2Participant1['id'], $participant1);
        $this->assertEquals($block2Participant1['scout_name'], 'One');
        $block2Participant2 = collect($data[$block2])->first(function ($entry) use($participant2) { return $entry['id'] === $participant2; });
        $this->assertEquals($block2Participant2['user_id'], $userId);
        $this->assertEquals($block2Participant2['block_id'], $block2);
        $this->assertEquals($block2Participant2['observation_count'], 0);
        $this->assertEquals($block2Participant2['id'], $participant2);
        $this->assertEquals($block2Participant2['scout_name'], 'Two');
    }

    public function test_shouldReturnToCrib_afterAddingObservationInAssignment() {
        // given
        $block1 = $this->createBlock('Block 1', '1.1', '01.01.2019');
        $block2 = $this->createBlock('Block 2', '1.2', '01.01.2019');
        $participant1 = $this->createParticipant('One');
        $participant2 = $this->createParticipant('Two');

        $observationAssignment = ObservationAssignment::create(['name' => 'Assignment', 'course_id' => $this->courseId]);
        $observationAssignment->blocks()->attach([$block1, $block2]);
        $observationAssignment->participants()->attach([$participant1, $participant2]);
        $observationAssignment->users()->attach(Auth::id());

        $this->get('/course/' . $this->courseId . '/crib');

        // when
        $this->get('/course/' . $this->courseId . '/observation/new?participant=' . $participant1 . '&block=' . $block1);
        $response = $this->post('/course/' . $this->courseId . '/observation/new', [
            'participants' => '' . $participant1,
            'content' => 'hat gut mitgemacht',
            'impression' => '1',
            'block' => '' . $block1,
            'requirements' => '',
            'categories' => ''
        ]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/crib');
    }

    public function test_shouldReturnToCrib_afterAddingObservationInBlock() {
        // given
        $block1 = $this->createBlock('Block 1', '1.1', '01.01.2019');
        $participant1 = $this->createParticipant('One');

        $this->get('/course/' . $this->courseId . '/crib');

        // when
        $this->get('/course/' . $this->courseId . '/observation/new');
        $response = $this->post('/course/' . $this->courseId . '/observation/new', [
            'participants' => '' . $participant1,
            'content' => 'hat gut mitgemacht',
            'impression' => '1',
            'block' => '' . $block1,
            'requirements' => '',
            'categories' => ''
        ]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/crib');
    }
}
