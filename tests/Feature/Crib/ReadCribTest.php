<?php

namespace Tests\Feature\Crib;

use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
}
