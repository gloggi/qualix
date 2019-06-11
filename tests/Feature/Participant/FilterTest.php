<?php

namespace Tests\Feature\Participant;

use Tests\TestCaseWithBasicData;

class FilterTest extends TestCaseWithBasicData {

    protected $categoryId;
    protected $categoryId2;
    protected $requirementId;
    protected $requirementId2;

    public function setUp(): void {
        parent::setUp();

        $this->categoryId = $this->createCategory('Kategorie 1');
        $this->categoryId2 = $this->createCategory('Kategorie 2');

        $this->requirementId = $this->createRequirement('Mindestanforderung 1', true);
        $this->requirementId2 = $this->createRequirement('Mindestanforderung 2', true);

        $this->createObservation('hat Kategorie und MA', 1, [$this->requirementId], [$this->categoryId]);
        $this->createObservation('nur Kategorie', 1, [], [$this->categoryId]);
        $this->createObservation('nur MA', 1, [$this->requirementId], []);
        $this->createObservation('ohne Kategorie oder MA', 1, [], []);
        $this->createObservation('andere Kategorie', 1, [], [$this->categoryId2]);
        $this->createObservation('andere MA', 1, [$this->requirementId2], []);
        $this->createObservation('alle Kategorien', 1, [], [$this->categoryId, $this->categoryId2]);
        $this->createObservation('alle MA', 1, [$this->requirementId, $this->requirementId2], []);
    }

    public function test_shouldDisplayAllObservations_whenNoFilter() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat Kategorie und MA');
        $response->assertSee('nur Kategorie');
        $response->assertSee('nur MA');
        $response->assertSee('ohne Kategorie oder MA');
        $response->assertSee('andere Kategorie');
        $response->assertSee('andere MA');
        $response->assertSee('alle Kategorien');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByCategory() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId . '?category=' . $this->categoryId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat Kategorie und MA');
        $response->assertSee('nur Kategorie');
        $response->assertDontSee('nur MA');
        $response->assertDontSee('ohne Kategorie oder MA');
        $response->assertDontSee('andere Kategorie');
        $response->assertDontSee('andere MA');
        $response->assertSee('alle Kategorien');
        $response->assertDontSee('alle MA');
    }

    public function test_shouldFilterByNoCategory() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId . '?category=0');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('hat Kategorie und MA');
        $response->assertDontSee('nur Kategorie');
        $response->assertSee('nur MA');
        $response->assertSee('ohne Kategorie oder MA');
        $response->assertDontSee('andere Kategorie');
        $response->assertSee('andere MA');
        $response->assertDontSee('alle Kategorien');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId . '?requirement=' . $this->requirementId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat Kategorie und MA');
        $response->assertDontSee('nur Kategorie');
        $response->assertSee('nur MA');
        $response->assertDontSee('ohne Kategorie oder MA');
        $response->assertDontSee('andere Kategorie');
        $response->assertDontSee('andere MA');
        $response->assertDontSee('alle Kategorien');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByNoMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/tn/' . $this->participantId . '?requirement=0');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('hat Kategorie und MA');
        $response->assertSee('nur Kategorie');
        $response->assertDontSee('nur MA');
        $response->assertSee('ohne Kategorie oder MA');
        $response->assertSee('andere Kategorie');
        $response->assertDontSee('andere MA');
        $response->assertSee('alle Kategorien');
        $response->assertDontSee('alle MA');
    }
}
