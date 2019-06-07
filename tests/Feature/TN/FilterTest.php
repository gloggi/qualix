<?php

namespace Tests\Feature\TN;

use Tests\TestCaseWithBasicData;

class FilterTest extends TestCaseWithBasicData {

    protected $qkId;
    protected $qkId2;
    protected $maId;
    protected $maId2;

    public function setUp(): void {
        parent::setUp();

        $this->qkId = $this->createQK('Qualikategorie 1');
        $this->qkId2 = $this->createQK('Qualikategorie 2');

        $this->maId = $this->createMA('Mindestanforderung 1', true);
        $this->maId2 = $this->createMA('Mindestanforderung 2', true);

        $this->createBeobachtung('hat QK und MA', 1, [$this->maId], [$this->qkId]);
        $this->createBeobachtung('nur QK', 1, [], [$this->qkId]);
        $this->createBeobachtung('nur MA', 1, [$this->maId], []);
        $this->createBeobachtung('ohne QK oder MA', 1, [], []);
        $this->createBeobachtung('andere QK', 1, [], [$this->qkId2]);
        $this->createBeobachtung('andere MA', 1, [$this->maId2], []);
        $this->createBeobachtung('alle QK', 1, [], [$this->qkId, $this->qkId2]);
        $this->createBeobachtung('alle MA', 1, [$this->maId, $this->maId2], []);
    }

    public function test_shouldDisplayAllObservations_whenNoFilter() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat QK und MA');
        $response->assertSee('nur QK');
        $response->assertSee('nur MA');
        $response->assertSee('ohne QK oder MA');
        $response->assertSee('andere QK');
        $response->assertSee('andere MA');
        $response->assertSee('alle QK');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByQK() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?qk=' . $this->qkId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat QK und MA');
        $response->assertSee('nur QK');
        $response->assertDontSee('nur MA');
        $response->assertDontSee('ohne QK oder MA');
        $response->assertDontSee('andere QK');
        $response->assertDontSee('andere MA');
        $response->assertSee('alle QK');
        $response->assertDontSee('alle MA');
    }

    public function test_shouldFilterByNoQK() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?qk=0');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('hat QK und MA');
        $response->assertDontSee('nur QK');
        $response->assertSee('nur MA');
        $response->assertSee('ohne QK oder MA');
        $response->assertDontSee('andere QK');
        $response->assertSee('andere MA');
        $response->assertDontSee('alle QK');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?ma=' . $this->maId);

        // then
        $response->assertStatus(200);
        $response->assertSee('hat QK und MA');
        $response->assertDontSee('nur QK');
        $response->assertSee('nur MA');
        $response->assertDontSee('ohne QK oder MA');
        $response->assertDontSee('andere QK');
        $response->assertDontSee('andere MA');
        $response->assertDontSee('alle QK');
        $response->assertSee('alle MA');
    }

    public function test_shouldFilterByNoMA() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->kursId . '/tn/' . $this->tnId . '?ma=0');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('hat QK und MA');
        $response->assertSee('nur QK');
        $response->assertDontSee('nur MA');
        $response->assertSee('ohne QK oder MA');
        $response->assertSee('andere QK');
        $response->assertDontSee('andere MA');
        $response->assertSee('alle QK');
        $response->assertDontSee('alle MA');
    }
}
